<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Activity_logs extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->page_data['page']->title       = 'Log Aktivitas Pengguna';
		$this->page_data['page']->titleUrl    = 'activity_logs';
		$this->page_data['page']->subtitle    = 'Catatan Jejak Aktivitas dan Riwayat Perubahan Sistem';
		$this->page_data['page']->subtitleUrl = 'activity_logs';
		$this->page_data['page']->icon        = 'solar:history-bold-duotone';
	}

	public function index()
	{
		if (!hasPermissions('activity_log_list') && logged('role') != 1) {
			show_404();
		}

		$ip   = !empty(get('ip')) ? urldecode(get('ip')) : false;
		$user = !empty(get('user')) ? urldecode(get('user')) : false;

		$arg = [];
		if ($ip)   $arg['ip_address'] = $ip;
		if ($user) $arg['user']       = $user;

		$this->page_data['activity_logs'] = $this->activity_model->getByWhere($arg, [
			'order' => ['id', 'desc']
		]);
		$this->page_data['filter_ip']   = $ip;
		$this->page_data['filter_user'] = $user;
		$this->load->view('activity_logs/list', $this->page_data);
	}

	public function view($id)
	{
		if (!hasPermissions('activity_log_view') && logged('role') != 1) {
			show_404();
		}
		$this->page_data['activity'] = $this->activity_model->getById($id);
		$this->load->view('activity_logs/view', $this->page_data);
	}
}

/* End of file Activity_logs.php */
/* Location: ./application/controllers/Activity_logs.php */