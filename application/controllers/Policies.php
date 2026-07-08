<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Policies extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->page_data['url'] = (object) [
            'assets' => assets_url() . '/'
        ];
        $this->page_data['app'] = (object) [
            'site_title' => setting('company_name') ?: 'Miftahul Khoer Boarding School'
        ];
    }

    public function privacy_policy()
    {
        $this->page_data['page_title'] = 'Kebijakan Privasi (Privacy Policy)';
        $this->load->view('policies/privacy', $this->page_data);
    }

    public function terms_of_service()
    {
        $this->page_data['page_title'] = 'Syarat & Ketentuan Layanan (Terms of Service)';
        $this->load->view('policies/terms', $this->page_data);
    }
}
