<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Finance extends VENDOR_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ledger_model');
        $this->load->library('Order_quote');
    }

    public function index()
    {
        $vendorId = (int) $this->vendor_id;
        $data = array(
            'summary' => $this->Ledger_model->vendorSummary($vendorId),
            'payouts' => $this->Ledger_model->payoutsForVendor($vendorId),
            'ledger' => $this->Ledger_model->ledgerForVendor($vendorId)
        );
        $head = array('title' => 'Earnings', 'description' => 'Earnings', 'keywords' => '');
        $this->load->view('_parts/header', $head);
        $this->load->view('finance', $data);
        $this->load->view('_parts/footer');
    }
}
