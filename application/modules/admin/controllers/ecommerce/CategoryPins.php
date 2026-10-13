<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryPins extends ADMIN_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Categories_model', 'Home_admin_model'));
    }

    public function index()
    {
        $this->login_check();
        $categories = $this->Categories_model->getShopCategories();

        if ($this->input->method(TRUE) === 'POST') {
            $validIds = array();
            foreach ($categories as $id => $category) {
                $validIds[(int) $id] = true;
            }
            $header = $this->selectedIds('pin_header', $validIds);
            $footer = $this->selectedIds('pin_footer', $validIds);
            $this->Home_admin_model->setValueStore('pin_header_categories', implode(',', $header));
            $this->Home_admin_model->setValueStore('pin_footer_categories', implode(',', $footer));
            $this->saveHistory('Update pinned header and footer categories');
            $this->session->set_flashdata('result', 'Pinned categories saved. The store header, footer, and All Categories menu use these choices.');
            redirect('admin/categorypins');
        }

        $headerRaw = $this->Home_admin_model->getValueStore('pin_header_categories');
        $footerRaw = $this->Home_admin_model->getValueStore('pin_footer_categories');
        $data = array(
            'categories' => $categories,
            'headerPins' => $headerRaw === null ? $this->defaultTopLevelIds($categories, 6) : $this->storedIds($headerRaw),
            'footerPins' => $footerRaw === null ? $this->defaultTopLevelIds($categories, 6) : $this->storedIds($footerRaw)
        );
        $head = array(
            'title' => 'Administration - Pin categories',
            'description' => '',
            'keywords' => ''
        );
        $this->load->view('_parts/header', $head);
        $this->load->view('ecommerce/category_pins', $data);
        $this->load->view('_parts/footer');
        $this->saveHistory('Go to pinned categories');
    }

    private function selectedIds($field, $validIds)
    {
        $posted = $this->input->post($field);
        if (!is_array($posted)) {
            return array();
        }
        $ids = array();
        foreach ($posted as $id) {
            if (ctype_digit((string) $id) && isset($validIds[(int) $id])) {
                $ids[] = (int) $id;
            }
        }
        return $ids;
    }

    private function storedIds($raw)
    {
        $ids = array();
        foreach (explode(',', (string) $raw) as $part) {
            if (ctype_digit($part) && (int) $part > 0) {
                $ids[] = (int) $part;
            }
        }
        return $ids;
    }

    private function defaultTopLevelIds($categories, $limit)
    {
        $ids = array();
        foreach ($categories as $id => $category) {
            $parent = isset($category['sub'][0]) ? $category['sub'][0] : '';
            if ($parent !== null && $parent !== '') {
                continue;
            }
            $ids[] = (int) $id;
            if (count($ids) >= $limit) {
                break;
            }
        }
        return $ids;
    }
}
