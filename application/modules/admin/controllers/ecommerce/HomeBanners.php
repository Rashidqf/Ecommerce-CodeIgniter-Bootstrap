<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeBanners extends ADMIN_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Home_banners_model');
    }

    public function index()
    {
        $this->login_check();

        if ($this->input->method(TRUE) === 'POST') {
            $id = $this->input->post('banner_id');
            $id = ctype_digit((string) $id) ? (int) $id : 0;
            $existing = $id > 0 ? $this->Home_banners_model->getBanner($id) : array();
            if ($id > 0 && empty($existing)) {
                show_404();
            }

            if ($this->input->post('delete_banner') !== null && $id > 0) {
                if (!$this->Home_banners_model->deleteBanner($id)) {
                    show_error('The banner could not be deleted.');
                }
                $this->saveHistory('Delete homepage banner id - ' . $id);
                $this->session->set_flashdata('result', 'Homepage banner removed.');
                redirect('admin/homebanners');
            }

            $placement = $this->input->post('placement', TRUE);
            $altText = trim((string) $this->input->post('alt_text', TRUE));
            $targetUrl = trim((string) $this->input->post('target_url', TRUE));
            $position = $this->input->post('position');
            $visible = $this->input->post('visible');
            if (!in_array($placement, array('hero', 'promo'), true)
                || $altText === ''
                || strlen($altText) > 255
                || !$this->isSafeLocalTarget($targetUrl)
                || !ctype_digit((string) $position)
                || !in_array((string) $visible, array('0', '1'), true)) {
                $this->session->set_flashdata('error', 'Check the banner placement, description, destination, order, and visibility.');
                redirect('admin/homebanners');
            }

            $image = $this->saveUpload('image', !isset($existing['image']) ? '' : $existing['image'], true);
            if ($image === false) {
                redirect('admin/homebanners');
            }
            $mobileImage = $this->saveUpload(
                'mobile_image',
                isset($existing['mobile_image']) ? $existing['mobile_image'] : '',
                false
            );
            if ($mobileImage === false) {
                redirect('admin/homebanners');
            }

            $data = array(
                'placement' => $placement,
                'image' => $image,
                'mobile_image' => $mobileImage !== '' ? $mobileImage : null,
                'alt_text' => $altText,
                'target_url' => $targetUrl,
                'position' => (int) $position,
                'visible' => (int) $visible
            );
            if (!$this->Home_banners_model->saveBanner($data, $id)) {
                show_error('The homepage banner could not be saved.');
            }
            $this->saveHistory(($id > 0 ? 'Update' : 'Add') . ' homepage banner ' . $altText);
            $this->session->set_flashdata('result', 'Homepage banner saved.');
            redirect('admin/homebanners');
        }

        $data = array('banners' => $this->Home_banners_model->getAllBanners());
        $head = array(
            'title' => 'Administration - Homepage banners',
            'description' => '',
            'keywords' => ''
        );
        $this->load->view('_parts/header', $head);
        $this->load->view('ecommerce/home_banners', $data);
        $this->load->view('_parts/footer');
        $this->saveHistory('Go to homepage banners');
    }

    private function isSafeLocalTarget($target)
    {
        if ($target === '') {
            return false;
        }
        if (strpos($target, '..') !== false || strpos($target, '\\') !== false) {
            return false;
        }
        return preg_match('~^(?:/(?!/)[A-Za-z0-9_?=&%#./-]*|#[A-Za-z0-9_-]+)$~', $target) === 1;
    }

    private function saveUpload($field, $existingPath, $required)
    {
        if (!isset($_FILES[$field]) || (int) $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            if ($required && $existingPath === '') {
                $this->session->set_flashdata('error', 'Choose an image for this banner.');
                return false;
            }
            return $existingPath;
        }

        $uploadPath = './attachments/home_banners/';
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
            $this->session->set_flashdata('error', 'The banner image directory is unavailable.');
            return false;
        }

        $this->load->library('upload');
        $this->upload->initialize(array(
            'upload_path' => $uploadPath,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 5120,
            'encrypt_name' => true,
            'file_ext_tolower' => true
        ));
        if (!$this->upload->do_upload($field)) {
            $this->session->set_flashdata('error', trim(strip_tags($this->upload->display_errors())));
            return false;
        }

        return 'attachments/home_banners/' . $this->upload->data('file_name');
    }
}
