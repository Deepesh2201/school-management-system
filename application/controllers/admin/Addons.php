<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Addons extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function _remap($method, $params = array())
    {
        return call_user_func_array(array($this, 'dev_only'), $params);
    }

    public function dev_only()
    {
        $this->load->view('admin/addons/dev_only');
    }
}
