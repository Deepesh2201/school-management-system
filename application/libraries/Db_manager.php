<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Db_manager
{
    public $connections = array();
    public $CI;
    public $db;

    public function __construct()
    {
         $this->CI = &get_instance();

        if ($this->CI->session->has_userdata('admin')) {

            $database_session = $this->CI->session->userdata('admin');
            $database_group   = $database_session['db_array']['db_group'];
            
            $this->CI->db=$this->CI->load->database($database_group, TRUE); 
        } else {
           
            $this->CI->db=$this->CI->load->database('default', TRUE); 
        }
             
    }

    public function get_connection($db_name)
    {

        // create connection. return it.
        $this->connections[$db_name] = $this->CI->load->database($db_name, true);
        return $this->connections[$db_name];

    }

    /**
     * Switch the active database connection to a configured group
     * (e.g. 'default' or 'branch_{id}') and make it visible to models.
     */
    public function switch_group($db_group)
    {
        if (empty($db_group)) {
            return $this->CI->db;
        }

        $new_db = $this->CI->load->database($db_group, true);

        if ($new_db) {
            $this->CI->db = $new_db;
            $this->db     = $new_db;
        }

        return $new_db;
    }

}
