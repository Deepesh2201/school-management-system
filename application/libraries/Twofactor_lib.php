<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Twofactor.in SMS Gateway Library
 *
 * Single SMS gateway used across the application. Credentials are hardcoded
 * here (no UI settings required). Replace the constants below with the values
 * from your 2factor.in account.
 *
 * API reference: https://2factor.in/
 * Transactional SMS endpoint: /API/V1/{api_key}/ADDON_SERVICES/SEND/TSMS
 */
class Twofactor_lib
{
    private const API_KEY = 'YOUR_2FACTOR_API_KEY';

    private const SENDER_ID = 'ACAMP';

    public function sendSMS($send_to, $message)
    {
        if (self::API_KEY === '' || self::API_KEY === 'YOUR_2FACTOR_API_KEY') {
            return false;
        }

        $phoneNumber = $this->formatPhoneNumber($send_to);
        if ($phoneNumber === '') {
            return false;
        }

        $apiUrl = 'https://2factor.in/API/V1/' . self::API_KEY . '/ADDON_SERVICES/SEND/TSMS';

        $postData = http_build_query(array(
            'From' => self::SENDER_ID,
            'To'   => $phoneNumber,
            'Msg'  => $message,
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        if (isset($result['Status']) && $result['Status'] === 'Success') {
            return true;
        }

        log_message('error', '2factor.in SMS failed: ' . $response);
        return false;
    }

    private function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (strlen($number) === 10) {
            $number = '91' . $number;
        }

        if (strlen($number) === 12 && substr($number, 0, 2) === '91') {
            return $number;
        }

        return '';
    }
}
