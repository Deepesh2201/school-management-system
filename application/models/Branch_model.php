<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Branch_model extends CI_Model
{

    const MAIN_BRANCH_ID = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function db_group_for_branch($branch_id)
    {
        return 'default';
    }

    public function get_branches($only_active = false)
    {
        $this->db->select('*')->from('multi_branch');
        if ($only_active) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('name', 'asc');
        return $this->db->get()->result();
    }

    public function get_branch($id)
    {
        return $this->db->select('*')->from('multi_branch')->where('id', (int) $id)->get()->row();
    }

    public function add_branch($data)
    {
        $insert = array(
            'name'          => $data['name'],
            'hostname'      => isset($data['hostname']) ? $data['hostname'] : '127.0.0.1',
            'username'      => isset($data['username']) ? $data['username'] : '',
            'password'      => isset($data['password']) ? $data['password'] : '',
            'database_name' => $data['database_name'],
            'base_url'      => isset($data['base_url']) ? $data['base_url'] : '',
            'folder_path'   => isset($data['folder_path']) ? $data['folder_path'] : '',
            'is_verified'   => isset($data['is_verified']) ? (int) $data['is_verified'] : 1,
            'is_active'     => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            'created_at'    => date('Y-m-d H:i:s'),
        );
        $this->db->insert('multi_branch', $insert);
        return $this->db->insert_id();
    }

    public function update_branch($id, $data)
    {
        $update = array(
            'name'          => $data['name'],
            'hostname'      => isset($data['hostname']) ? $data['hostname'] : '127.0.0.1',
            'username'      => isset($data['username']) ? $data['username'] : '',
            'password'      => isset($data['password']) ? $data['password'] : '',
            'database_name' => $data['database_name'],
            'base_url'      => isset($data['base_url']) ? $data['base_url'] : '',
            'folder_path'   => isset($data['folder_path']) ? $data['folder_path'] : '',
            'is_verified'   => isset($data['is_verified']) ? (int) $data['is_verified'] : 1,
            'is_active'     => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            'updated_at'    => date('Y-m-d H:i:s'),
        );
        $this->db->where('id', (int) $id);
        return $this->db->update('multi_branch', $update);
    }

    public function remove_branch($id)
    {
        $id = (int) $id;
        $this->db->where('branch_id', $id)->delete('branch_user');
        $this->db->where('id', $id)->delete('multi_branch');
        return true;
    }

    public function get_user_branch_map($username)
    {
        return $this->db->select('*')->from('branch_user')
            ->where('username', $username)
            ->order_by('is_default', 'desc')
            ->order_by('branch_id', 'asc')
            ->get()->result();
    }

    /**
     * Returns every branch a user may use, main branch first, as
     * array( array('id'=>int,'name'=>string,'db_group'=>string,'is_main'=>bool) )
     */
    public function get_user_branches($username, $main_branch_name = null, $main_base_url = '', $main_folder_path = '')
    {
        $map      = $this->get_user_branch_map($username);
        $branches = array();

        foreach ($map as $row) {
            $bid = (int) $row->branch_id;
            if ($bid === self::MAIN_BRANCH_ID) {
                $branches[] = array(
                    'id'          => self::MAIN_BRANCH_ID,
                    'name'        => $main_branch_name ? $main_branch_name : 'Main Branch',
                    'db_group'    => 'default',
                    'base_url'    => $main_base_url,
                    'folder_path' => $main_folder_path,
                    'is_main'     => true,
                );
            } else {
                $branch = $this->get_branch($bid);
                if ($branch && $branch->is_active) {
                    $branches[] = array(
                        'id'          => $bid,
                        'name'        => $branch->name,
                        'db_group'    => $this->db_group_for_branch($bid),
                        'base_url'    => $branch->base_url,
                        'folder_path' => $branch->folder_path,
                        'is_main'     => false,
                    );
                }
            }
        }

        return $branches;
    }

    public function get_default_branch_id($username)
    {
        $map = $this->get_user_branch_map($username);
        if (empty($map)) {
            return self::MAIN_BRANCH_ID;
        }
        foreach ($map as $row) {
            if ($row->is_default) {
                return (int) $row->branch_id;
            }
        }
        return (int) $map[0]->branch_id;
    }

    public function is_branch_allowed($username, $branch_id)
    {
        $branch_id = (int) $branch_id;
        $count     = $this->db->from('branch_user')
            ->where('username', $username)
            ->where('branch_id', $branch_id)
            ->count_all_results();
        return ($count > 0);
    }

    public function assign_user($username, $branch_ids, $default_branch_id = null)
    {
        $this->db->where('username', $username)->delete('branch_user');

        if (!is_array($branch_ids)) {
            $branch_ids = array($branch_ids);
        }
        $branch_ids = array_unique(array_map('intval', $branch_ids));
        if (empty($branch_ids)) {
            return false;
        }
        if ($default_branch_id === null || !in_array((int) $default_branch_id, $branch_ids)) {
            $default_branch_id = $branch_ids[0];
        }

        foreach ($branch_ids as $bid) {
            $this->db->insert('branch_user', array(
                'username'   => $username,
                'branch_id'  => $bid,
                'is_default' => ((int) $bid === (int) $default_branch_id) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
        return true;
    }

    public function get_branch_users()
    {
        return $this->db->select('*')->from('branch_user')->order_by('username', 'asc')->get()->result();
    }

    public function get_usernames_for_branch($branch_id)
    {
        $rows = $this->db->select('username')->from('branch_user')
            ->where('branch_id', (int) $branch_id)->get()->result();
        $out = array();
        foreach ($rows as $r) {
            $out[] = $r->username;
        }
        return $out;
    }
}
