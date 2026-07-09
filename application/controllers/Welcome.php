<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set(setting('timezone'));
    }

    public function index()
    {
        // If user is already logged in, redirect them to dashboard directly
        if (is_logged()) {
            redirect('dashboard', 'refresh');
        }

        $page_data['assets'] = assets_url() . '/';
        $page_data['site_title'] = setting('company_name') ?: 'Miftahul Khoer Boarding School';
        $page_data['privacy_url'] = url('policies/privacy_policy');
        $page_data['terms_url'] = url('policies/terms_of_service');
        $page_data['login_url'] = url('login');

        $this->load->view('welcome', $page_data);
    }
}
