<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->page_data['page']->title = 'Settings';
		$this->page_data['page']->menu = 'settings';
		$this->page_data['page']->titleUrl = 'settings/general';
		$this->page_data['page']->subtitle = 'System Settings';
		$this->page_data['page']->subtitleUrl = 'settings/general';
		$this->page_data['page']->icon = 'solar:settings-linear';
	}

	public function index()
	{
		$this->general();
	}

	public function general()
	{
		ifPermissions('general_settings');
		$this->page_data['page']->submenu = 'general';
		$this->load->view('settings/general', $this->page_data);
	}

	public function generalUpdate()
	{

		ifPermissions('general_settings');

		postAllowed();
		
		$this->settings_model->updateByKey('date_format', post('date_format'));
		$this->settings_model->updateByKey('datetime_format', post('datetime_format'));
		$this->settings_model->updateByKey('google_recaptcha_enabled', post('google_recaptcha_enabled') == 'ok' ? 1 : 0 );
		$this->settings_model->updateByKey('google_recaptcha_sitekey', post('google_recaptcha_sitekey'));
		$this->settings_model->updateByKey('google_recaptcha_secretkey', post('google_recaptcha_secretkey'));
		$this->settings_model->updateByKey('timezone', post('timezone'));
		$this->settings_model->updateByKey('default_lang', post('default_lang'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Settings has been Updated Successfully');

		$this->activity_model->add("Company Settings Updated by User: #".logged('id'));
		
		redirect('settings/general');
	}

	public function company()
	{
		ifPermissions('company_settings');
		$this->page_data['page']->submenu = 'company';
		$this->load->view('settings/company', $this->page_data);
	}

	public function companyUpdate()
	{

		ifPermissions('company_settings');

		postAllowed();
		
		$this->settings_model->updateByKey('company_name', post('company_name'));
		$this->settings_model->updateByKey('company_email', post('company_email'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Settings has been Updated Successfully');

		$this->activity_model->add("Company Settings Updated by User: #".logged('id'));
		
		redirect('settings/company');
	}

	public function login_theme()
	{
		ifPermissions('login_theme');
		$this->page_data['page']->submenu = 'login_theme';
		$this->load->view('settings/login_theme', $this->page_data);
	}

	public function loginthemeUpdate()
	{

		ifPermissions('login_theme');

		postAllowed();
		
		$this->settings_model->updateByKey('login_theme', post('login_theme'));

		if (!empty($_FILES['image']['name'])) {

			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$this->uploadlib->initialize([
				'file_name' => 'login-bg.'.$ext
			]);
			$image = $this->uploadlib->uploadImage('image');

			if($image['status']){
				$this->settings_model->updateByKey('bg_img_type', $ext);
			}

			$this->activity_model->add("User #$id Updated his/her Profile Image.");

			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Profile Image has been Updated Successfully');

		}
		else{

			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Server Error Occured while Uploading Image !');

		}

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Settings has been Updated Successfully');

		$this->activity_model->add("Login Theme Updated by User: #".logged('id'));
		
		redirect('settings/login_theme');
	}

	public function email_templates()
	{
		ifPermissions('email_templates');
		$this->page_data['page']->submenu = 'email_templates';
		$this->load->view('settings/email_templates/list', $this->page_data);
	}

	public function edit_email_templates($id)
	{
		ifPermissions('email_templates');
		$this->page_data['page']->submenu = 'email_templates';
		$this->page_data['template'] = $this->templates_model->getById($id);
		$this->load->view('settings/email_templates/edit', $this->page_data);
	}

	public function update_email_templates($id)
	{

		ifPermissions('login_theme');

		postAllowed();
		
		$this->templates_model->update($id, [
			// 'code'	=>	post('code'),
			'name'	=>	post('name'),
			'data'	=>	post('data'),
		]);

		// dd( post('data') );

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Email Template has been Updated Successfully');

		$this->activity_model->add("Email Template Updated by User: #".logged('id'));
		
		redirect('settings/email_templates');
	}

	public function api_settings()
	{
		ifPermissions('general_settings');
		$this->page_data['page']->subtitle = 'Integrasi API';
		$this->page_data['page']->subtitleUrl = 'settings/api_settings';
		$this->page_data['page']->submenu = 'api_settings';
		$this->load->view('settings/api_settings', $this->page_data);
	}

	public function apiSettingsUpdate()
	{
		ifPermissions('general_settings');
		postAllowed();

		$this->settings_model->updateByKey('company_name', post('company_name'));
		$this->settings_model->updateByKey('google_client_id', post('google_client_id'));
		$this->settings_model->updateByKey('google_client_secret', post('google_client_secret'));
		$this->settings_model->updateByKey('google_ai_api_key', post('google_ai_api_key'));
		$this->settings_model->updateByKey('google_ai_model', post('google_ai_model'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Pengaturan API berhasil diperbarui.');

		$this->activity_model->add("API Settings Updated by User: #".logged('id'));

		redirect('settings/api_settings');
	}

	public function feature_settings()
	{
		ifPermissions('general_settings');
		$this->page_data['page']->subtitle = 'Pengaturan Fitur';
		$this->page_data['page']->subtitleUrl = 'settings/feature_settings';
		$this->page_data['page']->submenu = 'feature_settings';
		$this->load->view('settings/feature_settings', $this->page_data);
	}

	public function featureSettingsUpdate()
	{
		ifPermissions('general_settings');
		postAllowed();

		$this->settings_model->updateByKey('daftar_ulang_status', post('daftar_ulang_status'));

		$start_date = post('daftar_ulang_start_date');
		$end_date = post('daftar_ulang_end_date');
		
		// Convert empty or invalid date inputs to null/empty string
		$start_date = !empty($start_date) ? date('Y-m-d H:i:s', strtotime($start_date)) : '';
		$end_date = !empty($end_date) ? date('Y-m-d H:i:s', strtotime($end_date)) : '';

		$this->settings_model->updateByKey('daftar_ulang_start_date', $start_date);
		$this->settings_model->updateByKey('daftar_ulang_end_date', $end_date);

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Pengaturan Fitur berhasil diperbarui.');

		$this->activity_model->add("Feature Settings Updated by User: #".logged('id'));

		redirect('settings/feature_settings');
	}

}

/* End of file Settings.php */
/* Location: ./application/controllers/Settings.php */