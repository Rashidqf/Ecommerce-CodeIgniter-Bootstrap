<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends ADMIN_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->login_check();
        $this->load->model('Ledger_model');
        $this->load->library(array('Order_quote', 'Safepay'));
    }

    public function index()
    {
        $this->renderPage('ecommerce/finance_payments', 'Payments', array(
            'environment' => $this->safepay->environment(),
            'configured' => $this->safepay->isConfigured(),
            'intent' => $this->safepay->intent(),
            'transactions' => $this->Ledger_model->transactions(50),
            'bankPayments' => $this->Ledger_model->pendingBankPayments()
        ));
    }

    public function commission()
    {
        if ($this->input->method(true) === 'POST') {
            if (!$this->Ledger_model->saveCommissionPercent($this->input->post('commission_rate_percent'))) {
                $this->session->set_flashdata('error', 'Enter a commission percentage from 0 to 100.');
            } else {
                $this->session->set_flashdata('result', 'Commission rate saved. New orders use this rate. Existing orders keep their saved rate.');
            }
            redirect('admin/finance/commission');
        }
        $stored = $this->Home_admin_model->getValueStore('commission_rate_percent');
        $this->renderPage('ecommerce/finance_commission', 'Commission', array(
            'rate' => $stored === null ? '0.00' : $stored
        ));
    }

    public function balances()
    {
        if ($this->input->method(true) === 'POST' && $this->input->post('mark_bank_paid') !== null) {
            $paymentId = (int) $this->input->post('payment_id');
            $payment = $this->db->where('id', $paymentId)->where('provider', 'bank')->where('status', 'pending')->get('payment_transactions')->row_array();
            if (empty($payment) || !$this->Ledger_model->recordVerifiedSale($paymentId, (string) $this->session->userdata('logged_in'))) {
                $this->session->set_flashdata('error', 'That bank payment is not waiting for confirmation.');
            } else {
                $this->session->set_flashdata('result', 'Bank payment marked received. Vendor earnings were added from the saved order items.');
            }
            redirect('admin/finance/balances');
        }
        if ($this->input->method(true) === 'POST' && $this->input->post('refund_item') !== null) {
            $result = $this->Ledger_model->recordRefund(
                (int) $this->input->post('order_item_id'),
                Order_quote::toMinor($this->input->post('refund_amount')),
                $this->input->post('reference'),
                (string) $this->session->userdata('logged_in')
            );
            $this->session->set_flashdata($result === true ? 'result' : 'error', $result === true ? 'Refund recorded in the vendor ledger.' : $result);
            redirect('admin/finance/balances');
        }
        if ($this->input->method(true) === 'POST' && $this->input->post('adjust_balance') !== null) {
            $result = $this->Ledger_model->recordAdjustment(
                (int) $this->input->post('vendor_id'),
                Order_quote::toMinor($this->input->post('amount')),
                $this->input->post('reference'),
                $this->input->post('note'),
                (string) $this->session->userdata('logged_in')
            );
            $this->session->set_flashdata($result === true ? 'result' : 'error', $result === true ? 'Adjustment recorded.' : $result);
            redirect('admin/finance/balances');
        }
        $this->renderPage('ecommerce/finance_balances', 'Vendor balances', array(
            'balances' => $this->Ledger_model->vendorBalances(),
            'items' => $this->Ledger_model->payableItems(),
            'bankPayments' => $this->Ledger_model->pendingBankPayments()
        ));
    }

    public function payouts()
    {
        if ($this->input->method(true) === 'POST') {
            $result = $this->Ledger_model->recordPayout(
                (int) $this->input->post('vendor_id'),
                Order_quote::toMinor($this->input->post('amount')),
                (string) $this->input->post('method'),
                (string) $this->input->post('reference'),
                (string) $this->input->post('paid_on'),
                (string) $this->input->post('notes'),
                (string) $this->session->userdata('logged_in')
            );
            $this->session->set_flashdata($result === true ? 'result' : 'error', $result === true ? 'Manual payout recorded. Buyora did not send the money.' : $result);
            redirect('admin/finance/payouts');
        }
        $this->renderPage('ecommerce/finance_payouts', 'Vendor payouts', array(
            'balances' => $this->Ledger_model->vendorBalances(),
            'payouts' => $this->Ledger_model->allPayouts()
        ));
    }

    private function renderPage($view, $title, array $data)
    {
        $head = array('title' => 'Administration - ' . $title, 'description' => '', 'keywords' => '');
        $this->load->view('_parts/header', $head);
        $this->load->view($view, $data);
        $this->load->view('_parts/footer');
        $this->saveHistory('Go to ' . $title);
    }
}
