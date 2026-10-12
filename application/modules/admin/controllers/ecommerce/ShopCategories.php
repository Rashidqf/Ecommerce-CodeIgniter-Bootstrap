<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ShopCategories extends ADMIN_Controller
{

    private $num_rows = 20;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Categories_model', 'Languages_model'));
    }

    public function index($page = 0)
    {
        $this->login_check();
        $data = array();
        $head = array();
        $head['title'] = 'Administration - Home Categories';
        $head['description'] = '!';
        $head['keywords'] = '';
        $data['shop_categories'] = $this->Categories_model->getShopCategories($this->num_rows, $page);
        $data['languages'] = $this->Languages_model->getLanguages();
        $rowscount = $this->Categories_model->categoriesCount();
        $data['links_pagination'] = pagination('admin/shopcategories', $rowscount, $this->num_rows, 3);
        if (isset($_POST['update_category_image'])) {
            $categoryId = $this->input->post('category_id');
            if (!ctype_digit((string) $categoryId) || (int) $categoryId < 1 || !$this->Categories_model->categoryIdExists($categoryId)) {
                show_error('A valid category must be selected.', 400);
            }
            $image = $this->uploadCategoryImage();
            if ($image !== false) {
                if (!$this->Categories_model->updateCategoryImage($categoryId, $image)) {
                    show_error('The category image could not be saved.');
                }
                $this->session->set_flashdata('result_add', 'Category image updated.');
                $this->saveHistory('Update category image id - ' . (int) $categoryId);
            }
            redirect('admin/shopcategories');
        }
        if (isset($_GET['delete'])) {
            if (!ctype_digit((string) $_GET['delete']) || (int) $_GET['delete'] < 1
                || !$this->Categories_model->categoryIdExists($_GET['delete'])) {
                show_404();
            }
            $this->saveHistory('Delete a shop categorie');
            if (!$this->Categories_model->deleteShopCategorie($_GET['delete'])) {
                show_error('The category could not be deleted.');
            }
            $this->session->set_flashdata('result_delete', 'Shop Categorie is deleted!');
            redirect('admin/shopcategories');
        }
        if (isset($_POST['submit'])) {
            $translations = $this->input->post('translations');
            $names = $this->input->post('categorie_name');
            $englishName = '';
            if (is_array($translations) && is_array($names)) {
                foreach ($translations as $index => $language) {
                    if ($language === MY_DEFAULT_LANGUAGE_ABBR && isset($names[$index])) {
                        $englishName = trim($names[$index]);
                        break;
                    }
                }
            }
            if ($englishName === '') {
                $this->session->set_flashdata('result_add', 'Enter a category name in the default language.');
                redirect('admin/shopcategories');
            }
            if ($this->Categories_model->categoryExists($englishName)) {
                $this->session->set_flashdata('result_add', 'A category with that name already exists.');
                redirect('admin/shopcategories');
            }
            $image = '';
            if (!empty($_FILES['category_image']['name'])) {
                $image = $this->uploadCategoryImage();
                if ($image === false) {
                    redirect('admin/shopcategories');
                }
            }
            $parentId = $this->input->post('sub_for');
            if (!ctype_digit((string) $parentId) || ((int) $parentId > 0 && !$this->Categories_model->categoryIdExists($parentId))) {
                $this->session->set_flashdata('result_add', 'Select an available parent category.');
                redirect('admin/shopcategories');
            }
            $_POST['sub_for'] = (int) $parentId;
            $this->Categories_model->setShopCategorie($_POST, $image);
            $this->session->set_flashdata('result_add', 'Shop category added.');
            redirect('admin/shopcategories');
        }
        if (isset($_POST['editSubId'])) {
            $result = $this->Categories_model->editShopCategorieSub($_POST);
            if ($result === true) {
                $this->session->set_flashdata('result_add', 'Subcategory changed!');
                $this->saveHistory('Change subcategory for category id - ' . $_POST['editSubId']);
            } else {
                $this->session->set_flashdata('result_add', 'Problem with Shop category change!');
            }
            redirect('admin/shopcategories');
        }
        $this->load->view('_parts/header', $head);
        $this->load->view('ecommerce/shopcategories', $data);
        $this->load->view('_parts/footer');
        $this->saveHistory('Go to shop categories');
    }

    private function uploadCategoryImage()
    {
        $uploadPath = './attachments/shop_images/';
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
            $this->session->set_flashdata('result_add', 'The category image directory is unavailable.');
            return false;
        }

        $config = array(
            'upload_path' => $uploadPath,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 5120,
            'encrypt_name' => true,
            'file_ext_tolower' => true
        );
        $this->load->library('upload');
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('category_image')) {
            $this->session->set_flashdata('result_add', trim(strip_tags($this->upload->display_errors())));
            return false;
        }

        return $this->upload->data('file_name');
    }

    /*
     * Called from ajax
     */

    public function editShopCategorie()
    {
        $this->login_check();
        $categoryId = $this->input->post('for_id');
        $language = $this->input->post('abbr');
        $name = trim((string) $this->input->post('name', TRUE));
        if (!ctype_digit((string) $categoryId) || (int) $categoryId < 1
            || !$this->Categories_model->categoryIdExists($categoryId)
            || !is_string($language) || $language === ''
            || $name === '' || strlen($name) > 255) {
            show_error('Enter a valid category and name.', 400);
        }
        if (!$this->Categories_model->editShopCategorie(array(
            'for_id' => (int) $categoryId,
            'abbr' => $language,
            'name' => $name
        ))) {
            show_error('A category with that name already exists in this language.', 409);
        }
        $this->saveHistory('Edit shop categorie to ' . $name);
    }

    /*
     * Called from ajax
     */

    public function changePosition()
    {
        $this->login_check();
        $result = $this->Categories_model->editShopCategoriePosition($_POST);
        $this->saveHistory('Edit shop categorie position ' . $_POST['name']);
    }

}
