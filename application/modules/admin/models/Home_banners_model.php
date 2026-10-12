<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_banners_model extends CI_Model
{
    public function getVisibleBanners()
    {
        $this->db->where('visible', 1);
        $this->db->order_by('position', 'asc');
        $this->db->order_by('id', 'asc');
        return $this->db->get('home_banners')->result_array();
    }

    public function getAllBanners()
    {
        $this->db->order_by('placement', 'asc');
        $this->db->order_by('position', 'asc');
        $this->db->order_by('id', 'asc');
        return $this->db->get('home_banners')->result_array();
    }

    public function getBanner($id)
    {
        return $this->db->where('id', (int) $id)->get('home_banners')->row_array();
    }

    public function saveBanner($data, $id = 0)
    {
        if ($id > 0) {
            $this->db->where('id', (int) $id);
            return $this->db->update('home_banners', $data);
        }
        return $this->db->insert('home_banners', $data);
    }

    public function deleteBanner($id)
    {
        return $this->db->where('id', (int) $id)->delete('home_banners');
    }
}
