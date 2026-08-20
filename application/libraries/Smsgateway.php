<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Smsgateway
{

    private $_CI;
    private $sch_setting;

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->model('setting_model');
        $this->_CI->load->model('student_model');
        $this->_CI->load->model('teacher_model');
        $this->_CI->load->model('studentfeemaster_model');
        $this->_CI->load->model('studentfee_model');
        $this->_CI->load->model('staff_model');
        $this->_CI->load->model('librarian_model');
        $this->_CI->load->model('accountant_model');
        $this->_CI->load->model('smsconfig_model');
        $this->_CI->load->model('notificationsetting_model');
        $this->sch_setting = $this->_CI->setting_model->get();
    }

    private function sendSmstoTwofactor($send_to, $msg)
    {
        if (empty($send_to)) {
            return true;
        }
        $this->_CI->load->library('twofactor_lib');
        return $this->_CI->twofactor_lib->sendSMS($send_to, $msg);
    }

    public function sentNotification($send_to, $detail, $subject, $template = '')
    {
        $this->_CI->load->library('pushnotification');
        $msg        = $this->getContent($detail, $template);
        $push_array = array(
            'title' => $subject,
            'body'  => $msg,
        );
        if ($send_to != "") {
            $this->_CI->pushnotification->send($send_to, $push_array, "mail_sms");
        }
    }

    public function sendSMS($send_to, $detail, $template_id, $template = '')
    {
        if ($template != "") {
            $msg = $this->getContent($detail, $template);
        } else {
            $msg = $detail;
        }

        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sentRegisterSMS($id, $send_to, $template, $template_id)
    {
        $msg = $this->getStudentRegistrationContent($id, $template, 'twofactor');
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sentFeeProcessingSMS($detail, $template, $template_id, $send_to)
    {
        $msg = $this->getFeeProcessingContent($detail, $template, 'twofactor');
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sentAddFeeSMS($detail, $template, $template_id, $send_to)
    {
        if (is_array($detail) && array_key_exists("send_type", $detail)) {
            $msg = $this->getGroupAddFeeContent($detail, $template, 'twofactor');
        } else {
            $msg = $this->getAddFeeContent($detail, $template, 'twofactor');
        }
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sentPresentStudentSMS($detail, $template, $template_id, $send_to)
    {
        $msg = $this->getPresentStudentContent($detail, $template, 'twofactor');
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sentPresentStudentNotification($detail, $template, $subject)
    {
        $msg        = $this->getPresentStudentContent($detail, $template);
        $push_array = array(
            'title' => $subject,
            'body'  => $msg,
        );
        if ($detail['app_key'] != "") {
            $this->_CI->pushnotification->send($detail['app_key'], $push_array, "mail_sms");
        }
    }

    public function getPresentStudentContent($student_detail, $template, $sms_detail_type = null)
    {
        foreach ($student_detail as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {
                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }
            }
                $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

    public function sentAbsentStudentSMS($detail, $template, $template_id, $send_to)
    {
        $msg = $this->getAbsentStudentContent($detail, $template, 'twofactor');
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sentAbsentStudentNotification($detail, $template, $subject)
    {
        $msg        = $this->getAbsentStudentContent($detail, $template);
        $push_array = array(
            'title' => $subject,
            'body'  => $msg,
        );
        if ($detail['app_key'] != "") {
            $this->_CI->pushnotification->send($detail['app_key'], $push_array, "mail_sms");
        }
    }

    public function sentExamResultNotification($detail, $template, $subject)
    {
        foreach ($detail['contact_numbers'] as $key => $contact_numbersvalue) {
            $msg        = $this->getStudentResultContent($detail, $template);
            $pushkey    = $detail['app_key'];
            $push_array = array(
                'title' => $subject,
                'body'  => $msg,
            );
            if ($pushkey != "") {
                $this->_CI->pushnotification->send($pushkey, $push_array, "mail_sms");
            }
        }
    }

    public function sentExamResultSMS($detail, $template, $template_id)
    {
        $msg = $this->getStudentResultContent($detail, $template, 'twofactor');
        foreach ($detail['contact_numbers'] as $key => $contact_numbersvalue) {
            $send_to = $contact_numbersvalue;
            $this->sendSmstoTwofactor($send_to, $msg);
        }
        return true;
    }

    public function sendLoginCredential($chk_mail_sms, $sender_details, $template, $template_id)
    {
        $msg    = $this->getLoginCredentialContent($sender_details['credential_for'], $sender_details, $template, 'twofactor');
        $send_to = $sender_details['contact_no'];
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sentHomeworkStudentNotification($detail, $template, $subject)
    {
        foreach ($detail as $student_key => $student_value) {
            $msg        = $this->getHomeworkStudentContent($detail[$student_key], $template);
            $push_array = array(
                'title' => $subject,
                'body'  => $msg,
            );
            if ($student_value['app_key'] != "") {
                $this->_CI->pushnotification->send($student_value['app_key'], $push_array, "mail_sms");
            }
        }
    }

    public function sentOnlineexamStudentNotification($detail, $template, $subject)
    {
        foreach ($detail as $student_key => $student_value) {
            $msg        = $this->getOnlineexamStudentContent($detail[$student_key], $template);
            $push_array = array(
                'title' => $subject,
                'body'  => $msg,
            );

            if ($student_value['app_key'] != "") {
                $this->_CI->pushnotification->send($student_value['app_key'], $push_array, "mail_sms");
            }
        }
    }

    public function sentOnlineClassStudentNotification($detail, $template)
    {
        foreach ($detail as $student_key => $student_value) {
            $msg = $this->getOnlineClassStudentContent($detail[$student_key], $template);

            $push_array = array(
                'title' => 'Online Class',
                'body'  => $msg,
            );

            if ($student_value['app_key'] != "") {
                $this->_CI->pushnotification->send($student_value['app_key'], $push_array, "mail_sms");
            }
        }
    }

    public function sentAddFeeNotification($detail, $template, $subject)
    {
        if (is_array($detail) && array_key_exists("send_type", $detail)) {
            $app_key = $detail['app_key'];
            $msg     = $this->getGroupAddFeeContent($detail, $template);
        } else {
            $app_key = $detail->app_key;
            $msg     = $this->getAddFeeContent($detail, $template);
        }

        $push_array = array(
            'title' => $subject,
            'body'  => $msg,
        );

        if ($app_key != "") {
            $this->_CI->pushnotification->send($app_key, $push_array, "mail_sms");
        }
    }

    public function sentFeeProcessingNotification($detail, $template, $subject)
    {
        $msg        = $this->getFeeProcessingContent($detail, $template);
        $push_array = array(
            'title' => $subject,
            'body'  => $msg,
        );

        if ($detail->app_keys != "") {
            $this->_CI->pushnotification->send($detail->app_keys, $push_array, "mail_sms");
        }
    }

    public function sentHomeworkStudentSMS($detail, $template, $template_id)
    {
        foreach ($detail as $student_key => $student_value) {
            $send_to = $student_key;
            if ($send_to != "") {
                $msg     = $this->getHomeworkStudentContent($detail[$student_key], $template, 'twofactor');
                $subject = "HomeWork Notice";
                $this->sendSmstoTwofactor($send_to, $msg);
            }
        }
        return true;
    }

    public function sentOnlineexamStudentSMS($detail, $template, $template_id)
    {
        foreach ($detail as $student_key => $student_value) {
            $send_to = $student_key;
            if ($send_to != "") {
                $msg     = $this->getOnlineexamStudentContent($detail[$student_key], $template, 'twofactor');
                $subject = "Online Exam";
                $this->sendSmstoTwofactor($send_to, $msg);
            }
        }
        return true;
    }

    public function sentOnlineadmissionStudentSMS($detail, $template, $template_id, $send_to)
    {
        if ($send_to != "") {
            $msg     = $this->getOnlineadmissionStudentContent($detail, $template);
            $subject = "Online Admission Confirmation";
            $this->sendSmstoTwofactor($send_to, $msg);
        }
        return true;
    }

    public function getOnlineadmissionStudentContent($student_detail, $template)
    {
        foreach ($student_detail as $key => $value) {
              $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

    /* end online admission sms */
    public function getStudentRegistrationContent($id, $template, $sms_detail_type)
    {
        $session_name                    = $this->_CI->setting_model->getCurrentSessionName();
        $student                         = $this->_CI->student_model->get($id);
        $student['current_session_name'] = $session_name;
        $student['student_name']         = $student['firstname'] . " " . $student['lastname'];

        foreach ($student as $key => $value) {
            if ($sms_detail_type == 'msg_nineone') {
                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }
            }
               $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }

        return $template;
    }

    public function getAddFeeContent($data, $template, $sms_detail_type = null)
    {
        $currency_symbol      = $this->sch_setting[0]['currency_symbol'];
        $school_name          = $this->sch_setting[0]['name'];
        $invoice_data         = json_decode($data->invoice);
        $data->invoice_id     = $invoice_data->invoice_id;
        $data->sub_invoice_id = $invoice_data->sub_invoice_id;
        $data->payment_id     = $data->invoice_id."/".$data->sub_invoice_id;
        $data->amount         = $currency_symbol . $data->amount;

        if ($data->fee_category == "transport") {
            $fee = $this->_CI->studentfeemaster_model->getTransportFeeByInvoice($data->invoice_id, $data->sub_invoice_id);
        } else {
            $fee = $this->_CI->studentfeemaster_model->getFeeByInvoice($data->invoice_id, $data->sub_invoice_id);
        }

        $a                    = json_decode($fee->amount_detail);
        $record               = $a->{$data->sub_invoice_id};
        $fee_amount           = number_format((($record->amount + $record->amount_fine)), 2, '.', ',');
        $data->firstname      = $fee->firstname;
        $data->lastname       = $fee->lastname;
        $data->class          = $fee->class;
        $data->section        = $fee->section;
        $data->fee_amount     = $currency_symbol . $fee_amount;
        $data->student_name   = $this->_CI->customlib->getFullName($fee->firstname, $fee->middlename, $fee->lastname, $this->sch_setting[0]['middlename'], $this->sch_setting[0]['lastname']);

        foreach ($data as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {
                if ($key != 'url') {

                    if (strlen($value) > 30) {
                        $value = substr($value, 0, 29);
                    }
                }
            }

             $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }

        return $template;
    }

    public function getGroupAddFeeContent($data, $template, $sms_detail_type = null)
    {
     $currency_symbol      = $this->sch_setting[0]['currency_symbol'];
        $school_name          = $this->sch_setting[0]['name'];
        $fee_amount=0;
        $data['payment_id']="";
        $payment_id=[];
        foreach ($data['invoice'] as $invoice_key => $invoice_value) {
            # code...

        $payment_id[]=$invoice_value['invoice_id']."/".$invoice_value['sub_invoice_id'];

        if ($invoice_value['fee_category'] == "transport") {

            $fee = $this->_CI->studentfeemaster_model->getTransportFeeByInvoice($invoice_value['invoice_id'], $invoice_value['sub_invoice_id']);

        } else {

        $fee = $this->_CI->studentfeemaster_model->getFeeByInvoice($invoice_value['invoice_id'], $invoice_value['sub_invoice_id']);
        }

        $a          = json_decode($fee->amount_detail);
        $record     = $a->{$invoice_value['sub_invoice_id']};
        $fee_amount += ($record->amount + $record->amount_fine);
        }

        $data['payment_id']            = "(".implode(',', $payment_id).")";
        $data['class']        = $fee->class;
        $data['section']      = $fee->section;
        $data['fee_amount']   = $currency_symbol . amountFormat($fee_amount);
        $data['student_name'] = $this->_CI->customlib->getFullName($fee->firstname, $fee->middlename, $fee->lastname, $this->sch_setting[0]['middlename'], $this->sch_setting[0]['lastname']);

        unset($data['invoice']);

        foreach ($data as $key => $value) {
              if ($sms_detail_type == 'msg_nineone') {
                if ($key != 'url') {

                    if (strlen($value) > 30) {
                        $value = substr($value, 0, 29);
                    }
                }
            }

            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }

        return $template;
    }


    public function getFeeProcessingContent($data, $template, $sms_detail_type = null)
    {
        $currency_symbol  = $this->sch_setting[0]['currency_symbol'];
        $school_name      = $this->sch_setting[0]['name'];
        $fee_amount       = number_format((float)(($data->fee_amount)), 2, '.', ',');
        $data->fee_amount = $currency_symbol . $fee_amount;

        foreach ($data as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {
                if ($key != 'url') {

                    if (strlen($value) > 30) {
                        $value = substr($value, 0, 29);
                    }
                }
            }

            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }

        return $template;
    }

    public function sentOnlineClassStudentSMS($detail, $template)
    {
        foreach ($detail as $student_key => $student_value) {
            $send_to = $student_key;
            if ($send_to != "") {
                $msg     = $this->getOnlineClassStudentContent($detail[$student_key], $template);
                $subject = "Online Class";
                $this->sendSmstoTwofactor($send_to, $msg);
            }
        }
        return true;
    }

    public function sentOnlineMeetingStaffSMS($detail, $template)
    {
        foreach ($detail as $staff_key => $staff_value) {
            $send_to = $staff_key;
            if ($send_to != "") {
                $msg     = $this->getOnlineMeetingStaffContent($detail[$staff_key], $template);
                $subject = "Online Meeting";
                $this->sendSmstoTwofactor($send_to, $msg);
            }
        }
        return true;
    }

    public function sentOnlineadmissionFeesSMS($detail, $template, $template_id)
    {
        $send_to = $detail['mobileno'];
        if ($send_to != "") {
            $msg     = $this->getOnlineadmissionFeesContent($detail, $template, 'twofactor');
            $subject = "Online Admission Confirmation";
            $this->sendSmstoTwofactor($send_to, $msg);
        }
        return true;
    }

    public function getOnlineadmissionFeesContent($student_detail, $template, $sms_detail_type = null)
    {

        foreach ($student_detail as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {

                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }

            }
                 $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }

        return $template;
    }

    public function getLoginCredentialContent($credential_for, $sender_details, $template, $sms_detail_type)
    {
        if ($credential_for == "student") {
            $student                        = $this->_CI->student_model->get($sender_details['id']);
            $sender_details['url']          = base_url();
            $sender_details['display_name'] = $student['firstname'] . " " . $student['lastname'];
        } elseif ($credential_for == "parent") {
            $parent                         = $this->_CI->student_model->get($sender_details['id']);
            $sender_details['url']          = base_url();
            $sender_details['display_name'] = $parent['guardian_name'];
        } elseif ($credential_for == "staff") {
            $staff                          = $this->_CI->staff_model->get($sender_details['id']);
            $sender_details['url']          = base_url();
            $sender_details['display_name'] = $staff['name'];
        }

        foreach ($sender_details as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {
                if ($key != 'url') {

                    if (strlen($value) > 30) {
                        $value = substr($value, 0, 29);
                    }
                }
            }

             $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }

        return $template;
    }

    public function getAbsentStudentContent($student_detail, $template, $sms_detail_type = null)
    {

        $session_name                           = $this->_CI->setting_model->getCurrentSessionName();
        $student_detail['current_session_name'] = $session_name;
        foreach ($student_detail as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {

                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }
            }
             $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

    public function getStudentResultContent($student_result_detail, $template, $sms_detail_type = null)
    {

        foreach ($student_result_detail as $key => $value) {

            if ($key != 'contact_numbers') {

                if ($sms_detail_type == 'msg_nineone') {

                    if (strlen($value) > 30) {
                        $value = substr($value, 0, 29);
                    }

                }

                        $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
            }

        }

        return $template;
    }

    public function getContent($sender_details, $template, $sms_detail_type = null)
    {

        foreach ($sender_details as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {

                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }

            }

               $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }

        return $template;
    }

    public function getHomeworkStudentContent($student_detail, $template, $sms_detail_type = null)
    {

        foreach ($student_detail as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {

                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }

            }

               $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

    public function getOnlineexamStudentContent($student_detail, $template, $sms_detail_type = null)
    {

        foreach ($student_detail as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {

                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }

            }

                $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

    public function getOnlineClassStudentContent($student_detail, $template)
    {

        foreach ($student_detail as $key => $value) {

                $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

    public function getOnlineMeetingStaffContent($student_detail, $template)
    {

        foreach ($student_detail as $key => $value) {

          $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

    public function sentSMSToAlumni($sender_details, $template_id = null)
    {
        $msg = $sender_details['subject'] . " - Event From " . $sender_details['from_date'] . " To " . $sender_details['to_date'] . "\n" .
            $sender_details['body'];
        $send_to = $sender_details['contact_no'];

        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sendStudentLoginCredential($chk_mail_sms, $sender_details, $template, $template_id)
    {
        $msg   = $this->getLoginCredentialContent($sender_details['credential_for'], $sender_details, $template, 'twofactor');
        $send_to = $sender_details['contact_no'];
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function sendStaffLoginCredential($chk_mail_sms, $sender_details, $template, $template_id)
    {
        $msg   = $this->getLoginCredentialContent($sender_details['credential_for'], $sender_details, $template, 'twofactor');
        $send_to = $sender_details['contact_no'];
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function StudentAddFeesMSG($inserted_id)
    {
        $fee_list = $this->_CI->studentfee_model->getFeeByInvoice($inserted_id);

        if (empty($fee_list)) {
            return true;
        }

        $fee               = $fee_list[0];
        $currency_symbol   = $this->sch_setting[0]['currency_symbol'];
        $fee_amount        = number_format((float)($fee['amount'] + $fee['amount_fine']), 2, '.', ',');
        $sender_details    = array(
            'amount'       => $currency_symbol . $fee['amount'],
            'amount_fine'  => $currency_symbol . $fee['amount_fine'],
            'fee_amount'   => $currency_symbol . $fee_amount,
            'student_name' => $fee['firstname'] . " " . $fee['middlename'] . " " . $fee['lastname'],
            'class'        => $fee['class'],
            'section'      => $fee['section'],
            'type'         => $fee['type'],
            'date'         => $fee['date'],
        );

        $chk_mail_sms = $this->_CI->customlib->sendMailSMS('fee_submission');

        if (!empty($chk_mail_sms) && $chk_mail_sms['sms'] && !empty($chk_mail_sms['template'])) {
            $msg     = $this->getContent($sender_details, $chk_mail_sms['template'], 'twofactor');
            $send_to = $fee['mobileno'];
            $this->sendSmstoTwofactor($send_to, $msg);

            if (!empty($fee['guardian_phone'])) {
                $this->sendSmstoTwofactor($fee['guardian_phone'], $msg);
            }
        }

        return true;
    }

    public function student_apply_leave($chk_mail_sms, $sender_details, $template, $template_id)
    {
        $msg    = $this->getstudent_apply_leaveContent($sender_details, $template, 'twofactor');
        $send_to = $sender_details['contact_no'];
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function getstudent_apply_leaveContent($sender_details, $template, $sms_detail_type)
    {

        foreach ($sender_details as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {
                if ($key != 'url') {

                    if (strlen($value) > 30) {
                        $value = substr($value, 0, 29);
                    }
                }
            }

             $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }

        return $template;
    }

    public function getmailsubject($id, $subject)
    {
        $student                 = $this->_CI->student_model->get($id);
        $student['student_name'] = $student['firstname'] . ' ' . $student['middlename'] . '' . $student['lastname'];
        foreach ($student as $key => $value) {

            $subject = $value ? str_replace('{{' . $key . '}}', $value, $subject) : $subject;
        }

        return $subject;
    }

    //send staff attendance sms and notification on app present
    public function sentPresentStaffSMS($detail, $template, $template_id, $send_to)
    {
        $msg = $this->getPresentStaffContent($detail, $template, 'twofactor');
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function getPresentStaffContent($staff_detail, $template, $sms_detail_type = null)
    {
        foreach ($staff_detail as $key => $value) {
            if ($sms_detail_type == 'msg_nineone') {
                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }
            }
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

    //send staff attendance sms and notification on app present
    public function sentAbsentStaffSMS($detail, $template, $template_id, $send_to)
    {
        $msg = $this->getAbsentStaffContent($detail, $template, 'twofactor');
        return $this->sendSmstoTwofactor($send_to, $msg);
    }

    public function getAbsentStaffContent($staff_detail, $template, $sms_detail_type = null)
    {
        foreach ($staff_detail as $key => $value) {
            if ($sms_detail_type == 'msg_nineone') {
                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }
            }
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) :  str_replace('{{' . $key . '}}', $key, $template);
        }
        return $template;
    }

}