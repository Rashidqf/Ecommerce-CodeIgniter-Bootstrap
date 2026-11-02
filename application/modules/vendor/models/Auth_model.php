<?php

class Auth_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function registerVendor($post, $status = 1)
    {
        $this->load->model('Vendorprofile_model');
        @$this->Vendorprofile_model->ensureBankingColumns();

        $name = !empty($post['name']) ? trim($post['name']) : explode('@', trim($post['u_email']))[0];
        $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $name)));
        if ($slug === '' || $slug === '-') {
            $slug = 'store-' . time();
        }
        $url = $slug;
        $counter = 1;
        while ($this->db->where('url', $url)->count_all_results('vendors') > 0) {
            $url = $slug . '-' . $counter;
            $counter++;
        }

        $input = array(
            'name'     => $name,
            'url'      => $url,
            'email'    => trim($post['u_email']),
            'password' => password_hash($post['u_password'], PASSWORD_DEFAULT),
            'status'   => $status
        );

        $fields = $this->db->list_fields('vendors');
        if (in_array('bank_name', $fields, true) && !empty($post['bank_name'])) {
            $input['bank_name'] = trim($post['bank_name']);
        }
        if (in_array('account_title', $fields, true) && !empty($post['account_title'])) {
            $input['account_title'] = trim($post['account_title']);
        }
        if (in_array('iban', $fields, true) && !empty($post['iban'])) {
            $input['iban'] = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $post['iban']), 0, 34));
        }
        if (in_array('raast_id', $fields, true) && !empty($post['raast_id'])) {
            $input['raast_id'] = trim($post['raast_id']);
        }
        if (in_array('cnic', $fields, true) && !empty($post['cnic'])) {
            $input['cnic'] = trim($post['cnic']);
        }

        if (!$this->db->insert('vendors', $input)) {
            $err = $this->db->error();
            log_message('error', print_r($err, true));
            show_error('Database Insert Error: ' . json_encode($err) . ' Data: ' . json_encode($input));
        }
    }

    public function countVendorsWithEmail($email)
    {
        $this->db->where('email', $email);
        return $this->db->count_all_results('vendors');
    }

    public function checkVendorExsists($post)
    {
        $this->db->where('email', $post['u_email']);
        $query = $this->db->get('vendors');
        $row = $query->row_array();
        if (empty($row) || !password_verify($post['u_password'], $row['password'])) {
            return false;
        }
        return $row;
    }

    public function updateVendorPassword($email)
    {
        $newPass = str_shuffle(bin2hex(openssl_random_pseudo_bytes(4)));
        $this->db->where('email', $email);
        if (!$this->db->update('vendors', ['password' => password_hash($newPass, PASSWORD_DEFAULT)])) {
            log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
        return $newPass;
    }

}
