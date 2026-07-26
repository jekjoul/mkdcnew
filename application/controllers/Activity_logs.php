<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Activity_logs extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		$this->page_data['page']->title       = 'Log Aktivitas Pengguna';
		$this->page_data['page']->titleUrl    = 'activity_logs';
		$this->page_data['page']->subtitle    = 'Catatan Jejak Aktivitas & Riwayat Sistem (WIB / GMT+7)';
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

		$this->db->select('a.*, u.name as user_name, u.username as user_username, u.role as user_role');
		$this->db->from('activity_logs a');
		$this->db->join('users u', 'a.user = u.id', 'left');

		if ($ip) {
			$this->db->where('a.ip_address', $ip);
		}
		if ($user) {
			$this->db->where('a.user', $user);
		}

		// Urutkan dari yang paling terbaru (ID DESC & created_at DESC)
		$this->db->order_by('a.id', 'DESC');
		$this->db->limit(1000);

		$activity_logs = $this->db->get()->result();

		$this->page_data['activity_logs'] = $activity_logs;
		$this->page_data['filter_ip']     = $ip;
		$this->page_data['filter_user']   = $user;
		$this->load->view('activity_logs/list', $this->page_data);
	}

	public function view($id)
	{
		if (!hasPermissions('activity_log_view') && logged('role') != 1) {
			show_404();
		}

		$this->db->select('a.*, u.name as user_name, u.username as user_username, u.email as user_email, u.role as user_role');
		$this->db->from('activity_logs a');
		$this->db->join('users u', 'a.user = u.id', 'left');
		$this->db->where('a.id', $id);
		$activity = $this->db->get()->row();

		if (!$activity) {
			show_404();
		}

		$this->page_data['activity'] = $activity;
		$this->load->view('activity_logs/view', $this->page_data);
	}
}

/* End of file Activity_logs.php */
/* Location: ./application/controllers/Activity_logs.php */