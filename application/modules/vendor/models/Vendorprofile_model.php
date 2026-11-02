<?php

class Vendorprofile_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getVendorInfoFromEmail($email)
    {
        $this->db->where('email', $email);
        $result = $this->db->get('vendors');
        return $result->row_array();
    }

    public function setRememberToken($email, $token_hash)
    {
        $this->db->where('email', $email);
        $this->db->update('vendors', ['remember_token' => $token_hash]);
    }

    public function getEmailByRememberToken($token_hash)
    {
        $this->db->where('remember_token', $token_hash);
        $result = $this->db->get('vendors');
        $row = $result->row_array();
        return $row ? $row['email'] : null;
    }

    public function clearRememberToken($email)
    {
        $this->db->where('email', $email);
        $this->db->update('vendors', ['remember_token' => null]);
    }

    public function getVendorByUrlAddress($urlAddr)
    {
        $this->db->where('url', $urlAddr);
        $result = $this->db->get('vendors');
        return $result->row_array();
    }

    public function saveNewVendorDetails($post, $vendor_id)
    {
        if (!$this->db->where('id', $vendor_id)->update('vendors', array(
                    'name' => $post['vendor_name'],
                    'url' => $post['vendor_url']
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
    }

    public function isVendorUrlFree($vendorUrl)
    {
        $this->db->where('url', $vendorUrl);
        $num = $this->db->count_all_results('vendors');
        if ($num > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function ensureBankingColumns()
    {
        try {
            $fields = $this->db->list_fields('vendors');
            if (empty($fields) || !is_array($fields)) {
                return;
            }
            if (!in_array('bank_name', $fields, true)) {
                @$this->db->query("ALTER TABLE `vendors` ADD COLUMN `bank_name` VARCHAR(100) NULL");
            }
            if (!in_array('account_title', $fields, true)) {
                @$this->db->query("ALTER TABLE `vendors` ADD COLUMN `account_title` VARCHAR(150) NULL");
            }
            if (!in_array('account_number', $fields, true)) {
                @$this->db->query("ALTER TABLE `vendors` ADD COLUMN `account_number` VARCHAR(50) NULL");
            }
            if (!in_array('iban', $fields, true)) {
                @$this->db->query("ALTER TABLE `vendors` ADD COLUMN `iban` VARCHAR(34) NULL");
            }
            if (!in_array('raast_id', $fields, true)) {
                @$this->db->query("ALTER TABLE `vendors` ADD COLUMN `raast_id` VARCHAR(50) NULL");
            }
            if (!in_array('cnic', $fields, true)) {
                @$this->db->query("ALTER TABLE `vendors` ADD COLUMN `cnic` VARCHAR(20) NULL");
            }
        } catch (\Throwable $e) {
            log_message('error', 'ensureBankingColumns error: ' . $e->getMessage());
        }
    }

    public function getVendorFullInfo($vendor_id)
    {
        $this->ensureBankingColumns();
        $this->db->where('id', (int) $vendor_id);
        $result = $this->db->get('vendors');
        return $result->row_array();
    }

    public function getVendorBankingDetails($vendor_id)
    {
        $this->ensureBankingColumns();
        $this->db->select('id, name, email, bank_name, account_title, account_number, iban, raast_id, cnic');
        $this->db->where('id', (int) $vendor_id);
        $row = $this->db->get('vendors')->row_array();
        return $row ? $row : array(
            'bank_name' => '',
            'account_title' => '',
            'account_number' => '',
            'iban' => '',
            'raast_id' => '',
            'cnic' => ''
        );
    }

    public function saveVendorBankingDetails($vendor_id, array $data)
    {
        $this->ensureBankingColumns();
        $update = array(
            'bank_name'      => isset($data['bank_name']) ? substr(trim($data['bank_name']), 0, 100) : null,
            'account_title'  => isset($data['account_title']) ? substr(trim($data['account_title']), 0, 150) : null,
            'account_number' => isset($data['account_number']) ? substr(trim($data['account_number']), 0, 50) : null,
            'iban'           => isset($data['iban']) ? strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $data['iban']), 0, 34)) : null,
            'raast_id'       => isset($data['raast_id']) ? substr(trim($data['raast_id']), 0, 50) : null,
            'cnic'           => isset($data['cnic']) ? substr(trim($data['cnic']), 0, 20) : null,
        );
        $this->db->where('id', (int) $vendor_id);
        return $this->db->update('vendors', $update);
    }

    public function getOrdersByMonth($vendor_id)
    {
        $vendor_id = (int)$vendor_id;
        $result = $this->db->query("SELECT YEAR(FROM_UNIXTIME(date)) as year, MONTH(FROM_UNIXTIME(date)) as month, COUNT(id) as num FROM vendors_orders WHERE vendor_id = $vendor_id GROUP BY YEAR(FROM_UNIXTIME(date)), MONTH(FROM_UNIXTIME(date)) ORDER BY year, month ASC");
        $result = $result->result_array();
        $orders = array();
        $years = array();
        if(!empty($result)) {
            foreach ($result as $res) {
                if (!isset($orders[$res['year']])) {
                    for ($i = 1; $i <= 12; $i++) {
                        $orders[$res['year']][$i] = 0;
                    }
                }
                $years[] = $res['year'];
                $orders[$res['year']][$res['month']] = $res['num'];
            }
        }
        return array(
            'years' => count($years) > 0 ? array_unique($years): [],
            'orders' => count($orders) > 0 ? $orders : [],
        );
    }

}
