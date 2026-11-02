<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class VendorProfile extends VENDOR_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Vendorprofile_model');
    }

    public function index()
    {
        $data = array();
        $head = array();
        $head['title'] = lang('vendor_dashboard');
        $head['description'] = lang('vendor_home_page');
        $head['keywords'] = '';
        $data['ordersByMonth'] = $this->Vendorprofile_model->getOrdersByMonth($this->vendor_id);
        $data['banking'] = $this->Vendorprofile_model->getVendorBankingDetails($this->vendor_id);
        $this->load->model('Ledger_model');
        $data['summary'] = $this->Ledger_model->vendorSummary($this->vendor_id);
        $this->load->view('_parts/header', $head);
        $this->load->view('home', $data);
        $this->load->view('_parts/footer');
    }

    public function logout()
    {
        if (isset($_SESSION['logged_vendor']) && get_cookie('logged_vendor') != null) {
            $this->Vendorprofile_model->clearRememberToken($_SESSION['logged_vendor']);
        }
        unset($_SESSION['logged_vendor']);
        delete_cookie('logged_vendor');
        redirect(LANG_URL . '/vendor/login');
    }

}
