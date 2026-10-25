<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Branch extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('branch_model');
        $this->load->library('form_validation');
    }

    private function is_main_schema()
    {
        return ($this->customlib->getCurrentDbGroup() === 'default');
    }

    private function require_main_schema()
    {
        if (!$this->is_main_schema()) {
            $this->session->set_flashdata('branch_error', 'Please switch to the Main Branch to manage branches.');
            redirect('admin/admin/dashboard');
        }
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('branch', 'can_view')) {
            access_denied();
        }
        $this->require_main_schema();

        $data['title'] = 'Branches';

        $main_settings           = $this->setting_model->get();
        $data['main_branch_name'] = isset($main_settings[0]['name']) ? $main_settings[0]['name'] : 'Main Branch';
        $data['branches']        = $this->branch_model->get_branches();

        // group branch_user rows by username
        $branch_users = $this->branch_model->get_branch_users();
        $users        = array();
        foreach ($branch_users as $bu) {
            if (!isset($users[$bu->username])) {
                $users[$bu->username] = array('branches' => array(), 'default' => null);
            }
            $users[$bu->username]['branches'][] = (int) $bu->branch_id;
            if ($bu->is_default) {
                $users[$bu->username]['default'] = (int) $bu->branch_id;
            }
        }
        $data['branch_users'] = $users;
        $data['staff_list']   = $this->staff_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/branch/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('branch', 'can_add')) {
            access_denied();
        }
        $this->require_main_schema();

        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('database_name', 'Database Name', 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('branch_error', validation_errors());
            redirect('admin/branch');
        }

        $this->branch_model->add_branch(array(
            'name'          => $this->input->post('name'),
            'hostname'      => $this->input->post('hostname'),
            'username'      => $this->input->post('db_username'),
            'password'      => $this->input->post('db_password'),
            'database_name' => $this->input->post('database_name'),
            'base_url'      => $this->input->post('base_url'),
            'folder_path'   => $this->input->post('folder_path'),
            'is_verified'   => 1,
            'is_active'     => 1,
        ));

        $this->session->set_flashdata('branch_success', $this->lang->line('success_message'));
        redirect('admin/branch');
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('branch', 'can_edit')) {
            access_denied();
        }
        $this->require_main_schema();

        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('database_name', 'Database Name', 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('branch_error', validation_errors());
            redirect('admin/branch');
        }

        $this->branch_model->update_branch($id, array(
            'name'          => $this->input->post('name'),
            'hostname'      => $this->input->post('hostname'),
            'username'      => $this->input->post('db_username'),
            'password'      => $this->input->post('db_password'),
            'database_name' => $this->input->post('database_name'),
            'base_url'      => $this->input->post('base_url'),
            'folder_path'   => $this->input->post('folder_path'),
            'is_verified'   => 1,
            'is_active'     => 1,
        ));

        $this->session->set_flashdata('branch_success', $this->lang->line('success_message'));
        redirect('admin/branch');
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('branch', 'can_delete')) {
            access_denied();
        }
        $this->require_main_schema();

        $this->branch_model->remove_branch($id);
        $this->session->set_flashdata('branch_success', $this->lang->line('delete_message'));
        redirect('admin/branch');
    }

    public function assign()
    {
        if (!$this->rbac->hasPrivilege('branch', 'can_edit')) {
            access_denied();
        }
        $this->require_main_schema();

        $username    = $this->input->post('username');
        $branch_ids  = $this->input->post('branch_ids');
        $default_id  = $this->input->post('default_branch_id');

        if (empty($username)) {
            $this->session->set_flashdata('branch_error', 'Please select a user.');
            redirect('admin/branch');
        }

        if (empty($branch_ids)) {
            // no branch selected: remove all mappings
            $this->branch_model->assign_user($username, array());
            $this->session->set_flashdata('branch_success', $this->lang->line('success_message'));
            redirect('admin/branch');
        }

        $this->branch_model->assign_user($username, $branch_ids, $default_id);
        $this->session->set_flashdata('branch_success', $this->lang->line('success_message'));
        redirect('admin/branch');
    }

    /**
     * Switch the current session to another branch the user is allowed to use.
     */
    public function switch_branch($branch_id)
    {
        $admin = $this->session->userdata('admin');
        if (empty($admin)) {
            redirect('site/login');
        }

        $branch_id = (int) $branch_id;
        $allowed   = $this->customlib->getAllowedBranches();
        $target    = null;

        foreach ($allowed as $b) {
            if ((int) $b['id'] === $branch_id) {
                $target = $b;
                break;
            }
        }

        // Server-side enforcement: the branch must be in this user's allowed list.
        if ($target === null) {
            $this->session->set_flashdata('branch_error', 'You are not allowed to access that branch.');
            redirect('admin/admin/dashboard');
        }

        $admin['branch_id']            = (int) $target['id'];
        $admin['branch_name']          = $target['name'];
        $admin['db_array']['db_group'] = $target['db_group'];
        $admin['db_array']['base_url'] = isset($target['base_url']) ? $target['base_url'] : '';
        $admin['db_array']['folder_path'] = isset($target['folder_path']) ? $target['folder_path'] : '';

        $this->session->set_userdata('admin', $admin);

        redirect('admin/admin/dashboard');
    }
}
