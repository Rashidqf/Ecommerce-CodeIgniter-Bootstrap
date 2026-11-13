<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Settings extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Languages_model', 'Settings_model']);
    }

    public function index()
    {
        $this->login_check();
        $data = array();
        $head = array();
        $head['title'] = 'Administration - Settings';
        $head['description'] = '';
        $head['keywords'] = '';

        $this->postChecker();

        $value_stores = $this->getValueStores();
        if(!is_null($value_stores)) {
            // Map to data
            foreach($value_stores as $value_s) {
                if (!array_key_exists($value_s['thekey'], $data)) {
                    $data[$value_s['thekey']] = $value_s['value'];
                }
            }
            unset($value_stores);
        }
        
        $data['cookieLawInfo'] = $this->getCookieLaw();
        $data['languages'] = $this->Languages_model->getLanguages();
        $data['law_themes'] = array_diff(scandir('.' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'imgs' . DIRECTORY_SEPARATOR . 'cookie-law-themes' . DIRECTORY_SEPARATOR), array('..', '.'));
        $data['cookieLawInfo'] = $this->getCookieLaw();
        $this->load->view('_parts/header', $head);
        $this->load->view('settings/settings', $data);
        $this->load->view('_parts/footer');
        $this->saveHistory('Go to Settings Page');
    }

    private function getValueStores()
    {
        $values = $this->Settings_model->getValueStores();
        if(is_array($values) && count($values) > 0) {
            return $values;
        }
        return null;
    }

    private function postChecker()
    {
        if (isset($_POST['uploadimage'])) {
            $config['upload_path'] = '.' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'site_logo' . DIRECTORY_SEPARATOR;
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 1500;
            $config['max_width'] = 1024;
            $config['max_height'] = 768;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('sitelogo')) {
                $this->session->set_flashdata('resultSiteLogoPublish', $this->upload->display_errors());
            } else {
                $data = array('upload_data' => $this->upload->data());
                $newImage = $data['upload_data']['file_name'];
                $this->Home_admin_model->setValueStore('sitelogo', $newImage);
                $this->saveHistory('Change site logo');
                $this->session->set_flashdata('resultSiteLogoPublish', 'New logo is set!');
            }
            redirect('admin/settings');
        }
        if (isset($_POST['naviText'])) {
            $this->Home_admin_model->setValueStore('navitext', $_POST['naviText']);
            $this->session->set_flashdata('resultNaviText', 'New navigation text is set!');
            $this->saveHistory('Change navigation text');
            redirect('admin/settings');
        }
        if (isset($_POST['footerCopyright'])) {
            $this->Home_admin_model->setValueStore('footercopyright', $_POST['footerCopyright']);
            $this->session->set_flashdata('resultFooterCopyright', 'New navigation text is set!');
            $this->saveHistory('Change footer copyright');
            redirect('admin/settings');
        }
        if (isset($_POST['contactsPage'])) {
            $this->Home_admin_model->setValueStore('contactspage', $_POST['contactsPage']);
            $this->session->set_flashdata('resultContactspage', 'Contacts page is updated!');
            $this->saveHistory('Change contacts page');
            redirect('admin/settings');
        }
        if (isset($_POST['footerContacts'])) {
            $this->Home_admin_model->setValueStore('footerContactAddr', $_POST['footerContactAddr']);
            $this->Home_admin_model->setValueStore('footerContactPhone', $_POST['footerContactPhone']);
            $this->Home_admin_model->setValueStore('footerContactEmail', $_POST['footerContactEmail']);
            $this->session->set_flashdata('resultfooterContacts', 'Contacts on footer are updated!');
            $this->saveHistory('Change footer contacts');
            redirect('admin/settings');
        }
        if (isset($_POST['footerSocial'])) {
            $this->Home_admin_model->setValueStore('footerSocialFacebook', $_POST['footerSocialFacebook']);
            $this->Home_admin_model->setValueStore('footerSocialTwitter', $_POST['footerSocialTwitter']);
            $this->Home_admin_model->setValueStore('footerSocialGooglePlus', $_POST['footerSocialGooglePlus']);
            $this->Home_admin_model->setValueStore('footerSocialPinterest', $_POST['footerSocialPinterest']);
            $this->Home_admin_model->setValueStore('footerSocialYoutube', $_POST['footerSocialYoutube']);
            $this->session->set_flashdata('resultfooterSocial', 'Social on footer are updated!');
            $this->saveHistory('Change footer contacts');
            redirect('admin/settings');
        }
        if (isset($_POST['googleMaps'])) {
            $this->Home_admin_model->setValueStore('googleMaps', $_POST['googleMaps']);
            $this->Home_admin_model->setValueStore('googleApi', $_POST['googleApi']);
            $this->session->set_flashdata('resultGoogleMaps', 'Google maps coordinates and api key are updated!');
            $this->saveHistory('Update Google Maps Coordinates and Api Key');
            redirect('admin/settings');
        }
        if (isset($_POST['footerAboutUs'])) {
            $this->Home_admin_model->setValueStore('footerAboutUs', $_POST['footerAboutUs']);
            $this->session->set_flashdata('resultFooterAboutUs', 'Footer about us text changed!');
            $this->saveHistory('Change footer about us info');
            redirect('admin/settings');
        }
        if (isset($_POST['contactsEmailTo'])) {
            $this->Home_admin_model->setValueStore('contactsEmailTo', $_POST['contactsEmailTo']);
            $this->session->set_flashdata('resultEmailTo', 'Email changed!');
            $this->saveHistory('Change where going emails from contact form');
            redirect('admin/settings');
        }
        if (isset($_POST['shippingOrder'])) {
            $this->Home_admin_model->setValueStore('shippingOrder', $_POST['shippingOrder']);
            $this->session->set_flashdata('shippingOrder', 'Shipping Order price chagned!');
            $this->saveHistory('Change Shipping free for order more than ' . $_POST['shippingOrder']);
            redirect('admin/settings');
        }
        if (isset($_POST['shippingAmount'])) {
            $this->Home_admin_model->setValueStore('shippingAmount', $_POST['shippingAmount']);
            $this->session->set_flashdata('shippingAmount', 'Shipping amount price chagned!');
            $this->saveHistory('Change Shipping amount for orders ' . $_POST['shippingAmount']);
            redirect('admin/settings');
        }
        if (isset($_POST['addJs'])) {
            $this->Home_admin_model->setValueStore('addJs', $_POST['addJs']);
            $this->session->set_flashdata('addJs', 'JavaScript code is added');
            $this->saveHistory('Add JS to website');
            redirect('admin/settings');
        }
        if (isset($_POST['publicQuantity'])) {
            $this->Home_admin_model->setValueStore('publicQuantity', $_POST['publicQuantity']);
            $this->session->set_flashdata('publicQuantity', 'Public quantity visibility changed');
            $this->saveHistory('Change publicQuantity visibility');
            redirect('admin/settings');
        }
        if (isset($_POST['publicDateAdded'])) {
            $this->Home_admin_model->setValueStore('publicDateAdded', $_POST['publicDateAdded']);
            $this->session->set_flashdata('publicDateAdded', 'Public date added visibility changed');
            $this->saveHistory('Change public date added visibility');
            redirect('admin/settings');
        }
        if (isset($_POST['outOfStock'])) {
            $this->Home_admin_model->setValueStore('outOfStock', $_POST['outOfStock']);
            $this->session->set_flashdata('outOfStock', 'Out of stock settings visibility change');
            $this->saveHistory('Change visibility of final checkout page');
            redirect('admin/settings');
        }
        if (isset($_POST['moreInfoBtn'])) {
            $this->Home_admin_model->setValueStore('moreInfoBtn', $_POST['moreInfoBtn']);
            $this->session->set_flashdata('moreInfoBtn', 'Button More Information visibility is changed');
            $this->saveHistory('Change visibility of More Information button in products list');
            redirect('admin/settings');
        }
        if (isset($_POST['showBrands'])) {
            $this->Home_admin_model->setValueStore('showBrands', $_POST['showBrands']);
            $this->session->set_flashdata('showBrands', 'Brands visibility changed');
            $this->saveHistory('Brands visibility changed');
            redirect('admin/settings');
        }
        if (isset($_POST['virtualProducts'])) {
            $this->Home_admin_model->setValueStore('virtualProducts', $_POST['virtualProducts']);
            $this->session->set_flashdata('virtualProducts', 'Virtual products visibility changed');
            $this->saveHistory('Virtual products visibility changed');
            redirect('admin/settings');
        }
        if (isset($_POST['showInSlider'])) {
            $this->Home_admin_model->setValueStore('showInSlider', $_POST['showInSlider']);
            $this->session->set_flashdata('showInSlider', 'In Slider products visibility changed');
            $this->saveHistory('In Slider products visibility changed');
            redirect('admin/settings');
        }
        if (isset($_POST['multiVendor'])) {
            $this->Home_admin_model->setValueStore('multiVendor', $_POST['multiVendor']);
            $this->session->set_flashdata('multiVendor', 'Multi Vendor Support changed');
            $this->saveHistory('Multi Vendor Support changed');
            redirect('admin/settings');
        }
        if (isset($_POST['requireVendorApproval'])) {
            $this->Home_admin_model->setValueStore('requireVendorApproval', $_POST['requireVendorApproval']);
            $this->session->set_flashdata('requireVendorApproval', 'Vendor Approval Required setting changed');
            $this->saveHistory('Vendor Approval Required setting changed');
            redirect('admin/settings');
        }
        if (isset($_POST['requireProductApproval'])) {
            $this->Home_admin_model->setValueStore('requireProductApproval', $_POST['requireProductApproval']);
            $this->session->set_flashdata('requireProductApproval', 'Product Approval Required setting changed');
            $this->saveHistory('Product Approval Required setting changed');
            redirect('admin/settings');
        }
        if (isset($_POST['setCookieLaw'])) {
            unset($_POST['setCookieLaw']);
            $this->setCookieLaw($_POST);
            $this->saveHistory('Cookie law information changed');
            redirect('admin/settings');
        }
        if (isset($_POST['hideBuyButtonsOfOutOfStock'])) {
            $this->Home_admin_model->setValueStore('hideBuyButtonsOfOutOfStock', $_POST['hideBuyButtonsOfOutOfStock']);
            $this->session->set_flashdata('hideBuyButtonsOfOutOfStock', 'Buy buttons of Out of stock products visibility changed');
            $this->saveHistory('Buy buttons visibility changed for out of stock products');
            redirect('admin/settings');
        }
        if (isset($_POST['refreshAfterAddToCart'])) {
            $this->Home_admin_model->setValueStore('refreshAfterAddToCart', $_POST['refreshAfterAddToCart']);
            $this->session->set_flashdata('refreshAfterAddToCart', 'Saved');
            $this->saveHistory('Option to open shopping cart after click add to cart button changed');
            redirect('admin/settings');
        }
        if (isset($_POST['save_bankalfalah'])) {
            $keys = array(
                'alfalah_enabled', 'alfalah_environment', 'alfalah_merchant_id',
                'alfalah_store_id', 'alfalah_channel_id', 'alfalah_merchant_username',
                'alfalah_merchant_password', 'alfalah_key1', 'alfalah_key2', 'alfalah_merchant_hash'
            );
            foreach ($keys as $k) {
                $val = isset($_POST[$k]) ? trim($_POST[$k]) : '';
                $this->Home_admin_model->setValueStore($k, $val);
            }
            $this->session->set_flashdata('bankalfalah_msg', 'Bank Alfalah APG settings saved successfully');
            $this->saveHistory('Updated Bank Alfalah APG Gateway settings');
            redirect('admin/settings');
        }
        if (isset($_POST['save_app_version'])) {
            $latestVersion = isset($_POST['app_latest_version']) && trim($_POST['app_latest_version']) !== '' ? trim($_POST['app_latest_version']) : '1.0.0';
            $minVersion = isset($_POST['app_min_supported_version']) && trim($_POST['app_min_supported_version']) !== '' ? trim($_POST['app_min_supported_version']) : '1.0.0';
            $forceUpdate = isset($_POST['app_force_update']) && $_POST['app_force_update'] == '1' ? '1' : '0';
            $updateMessage = isset($_POST['app_update_message']) ? trim($_POST['app_update_message']) : '';
            $playStoreUrl = isset($_POST['app_play_store_url']) ? trim($_POST['app_play_store_url']) : '';

            $this->Home_admin_model->setValueStore('app_latest_version', $latestVersion);
            $this->Home_admin_model->setValueStore('app_min_supported_version', $minVersion);
            $this->Home_admin_model->setValueStore('app_force_update', $forceUpdate);
            $this->Home_admin_model->setValueStore('app_update_message', $updateMessage);
            $this->Home_admin_model->setValueStore('app_play_store_url', $playStoreUrl);

            $this->session->set_flashdata('app_version_msg', 'Mobile App Version Control settings updated successfully!');
            $this->saveHistory('Updated Mobile App Version Control settings (Latest: ' . $latestVersion . ', Min: ' . $minVersion . ', Force: ' . $forceUpdate . ')');
            redirect('admin/settings');
        }
    }

    private function setCookieLaw($post)
    {
        $this->Home_admin_model->setCookieLaw($post);
    }

    private function getCookieLaw()
    {
        return $this->Home_admin_model->getCookieLaw();
    }

}
