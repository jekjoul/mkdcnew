<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public $data;

	public function __construct()
	{
		parent::__construct();
		$this->load->config('google_oauth');
		$this->ensureGoogleOauthColumns();

		date_default_timezone_set( setting('timezone') );

		if( !empty($this->db->username) && !empty($this->db->hostname) && !empty($this->db->database) ){ }else{
			die('Database is not configured');
		}

		if(is_logged()){
			redirect('dashboard','refresh');
		}

		$this->data = [
			'assets' => assets_url(),
			'body_classes'	=> setting('login_theme') == '1' ? 'login-page login-background' : 'login-page-side login-background'
		];

	}

	public function index()
	{
		$this->load->view('account/login', $this->data, FALSE);
	}

	public function google()
	{
		$client = $this->googleClient();
		if (!$client['client_id']) {
			$this->data['message'] = 'Konfigurasi Google OAuth belum lengkap.';
			$this->data['message_type'] = 'danger';
			$this->index();
			return;
		}

		$state = bin2hex(random_bytes(16));
		$this->session->set_userdata('google_oauth_state', $state);

		$params = [
			'client_id' => $client['client_id'],
			'redirect_uri' => $this->googleRedirectUri(),
			'response_type' => 'code',
			'scope' => 'openid email profile',
			'state' => $state,
			'access_type' => 'online',
			'prompt' => 'select_account',
		];

		redirect($client['auth_uri'] . '?' . http_build_query($params), 'refresh');
	}

	public function google_callback()
	{
		$state = $this->input->get('state', true);
		$code = $this->input->get('code', true);
		$error = $this->input->get('error', true);

		if ($error) {
			$this->data['message'] = 'Login Google dibatalkan atau ditolak.';
			$this->data['message_type'] = 'danger';
			$this->index();
			return;
		}

		if (!$code || !$state || $state !== $this->session->userdata('google_oauth_state')) {
			$this->data['message'] = 'Sesi login Google tidak valid. Silakan coba lagi.';
			$this->data['message_type'] = 'danger';
			$this->index();
			return;
		}
		$this->session->unset_userdata('google_oauth_state');

		$token = $this->exchangeGoogleCode($code);
		if (!$token || empty($token['access_token'])) {
			$this->data['message'] = 'Gagal mengambil token Google. Pastikan Redirect URI sudah benar.';
			$this->data['message_type'] = 'danger';
			$this->index();
			return;
		}

		$profile = $this->fetchGoogleProfile($token['access_token']);
		if (!$profile || empty($profile['email']) || empty($profile['sub'])) {
			$this->data['message'] = 'Gagal membaca profil Google.';
			$this->data['message_type'] = 'danger';
			$this->index();
			return;
		}

		if (empty($profile['email_verified'])) {
			$this->data['message'] = 'Email Google belum terverifikasi.';
			$this->data['message_type'] = 'danger';
			$this->index();
			return;
		}

		$allowed_domain = trim((string) $this->config->item('google_oauth_allowed_domain'));
		if ($allowed_domain !== '') {
			$domain = strtolower(substr(strrchr($profile['email'], '@'), 1));
			if ($domain !== strtolower($allowed_domain)) {
				$this->data['message'] = 'Domain email Google tidak diizinkan.';
				$this->data['message_type'] = 'danger';
				$this->index();
				return;
			}
		}

		$user = $this->findOrCreateGoogleUser($profile);
		if (!$user) {
			$this->data['message'] = 'Email Google belum terdaftar atau belum cocok dengan data PTK. Hubungi admin.';
			$this->data['message_type'] = 'danger';
			$this->index();
			return;
		}

		if ((string) $user->status !== '1') {
			$this->data['message'] = 'Akun Anda tidak aktif. Hubungi admin.';
			$this->data['message_type'] = 'danger';
			$this->index();
			return;
		}

		$this->users_model->login($user, false);
		redirect('/', 'refresh');
	}


	public function check()
	{

        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|xss_clean|callback_validate_username');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|xss_clean');

        $is_recaptcha_enabled = (setting('google_recaptcha_enabled') == '1');

        if($is_recaptcha_enabled)
        	$this->form_validation->set_rules('g-recaptcha-response', 'Google Recaptcha', 'callback_validate_recaptcha');

        if ($this->form_validation->run() == FALSE)
        {
            $this->index();
            return;
        }

        $username = post('username');
        $password = post('password');

        $attempt = $this->users_model->attempt( compact('username', 'password') );

        if( $attempt=='valid' ){

        	// If Allowed, then retreive user row and login the user
			$user = $this->db->where( 'username', $username )->or_where( 'email', $username )->get( $this->users_model->table )->row();
        	$this->users_model->login( $user, post('remember_me') );

        }elseif( $attempt=='invalid_password' ){

        	// Show Message if invalid password

            $this->data['message'] = 'Password salah!';
            $this->data['message_type'] = 'danger';

            $this->index();
            return;
        }elseif( $attempt=='not_allowed' ){

        	// Show Message if invalid password

            $this->data['message'] = 'Anda tidak diizinkan login ! Hubungi Admin';
            $this->data['message_type'] = 'danger';

            $this->index();
            return;
        }else{
        	
        	// if invalid value or false returned by $attempt
            
            $this->data['message'] = 'Terjadi Kesalahan !';
            $this->data['message_type'] = 'danger';

            $this->index();
            return;

        }

        redirect('/','refresh');

	}

	public function validate_recaptcha($recaptchaResponse)
	{
		
		$userIp=$this->input->ip_address();
        $secret = setting('google_recaptcha_secretkey');

        $url="https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$recaptchaResponse."&remoteip=".$userIp;
 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);      
         
        $status= json_decode($output, true);
 
        if ($status['success']) {
			return true;
		}else{
			$this->form_validation->set_message('validate_recaptcha', 'Google Recaptcha not valid !');  
			return false;
		}
	}

	public function validate_username($username)
	{
		$table = $this->users_model->table;
		$this->db->where('username', $username);
		$this->db->or_where('email', $username);

		$exists = $this->db->get($table)->num_rows();

		if($exists > 0){
			return true;
		}else{
			$this->form_validation->set_message('validate_username', 'Invalid Username/Email');  
			return false;
		}
	}

	public function forget()
	{
		$this->load->view('account/forget', $this->data, FALSE);
	}

	public function reset_password()
	{
		
		postAllowed();

		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|xss_clean|callback_validate_username');

		if($this->form_validation->run() == FALSE){
			$this->forget();
			return;
		}

		$reset = $this->users_model->resetPassword( [ 'username' => post('username') ] );

		$this->data['message']	=	'Reset Link Sent to <a href="#">'.obfuscate_email($reset).'</a> ! Please check your email';
		$this->data['message_type']	=	'info';

		if($reset==='invalid'){
			$this->data['message']	=	'Invalid Email/Username';
			$this->data['message_type']	=	'danger';
		}

		$this->forget();

	}

	public function new_password()
	{
		$reset_token = !empty(get('token')) ? get('token') : false;

		$user = $this->users_model->getByWhere(['reset_token' => $reset_token]);

		if(!$reset_token || !$user || empty($user)){
			echo 'Invalid Request';
			redirect('login/forget', 'refresh'); return;
		}

		$user = $user[0];

		$this->data['user']	=	$user;

		$this->load->view('account/reset_password', $this->data, FALSE);

	}

	public function set_new_password()
	{

		postAllowed();

		$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
		$this->form_validation->set_rules('password_confirm', 'Password Confirm', 'required|matches[password]');

		if($this->form_validation->run() == FALSE){
			$this->data['user']	=	$this->users_model->getByWhere(['reset_token' => post('token')])[0];
			$this->load->view('account/reset_password', $this->data, FALSE);
			return;
		}

		$reset_token = post('token');

		$user	=	$this->users_model->getByWhere(compact('reset_token'))[0];

		$this->users_model->update($user->id, [
			'password'	=>	hash( "sha256", post('password') ),
			'reset_token'	=>	'',
		]);

		$this->session->set_flashdata('message', 'New Password has been Updated, You can login now');
		$this->session->set_flashdata('message_type', 'success');
		redirect('login', 'refresh');

	}

	private function googleClient()
	{
		$config = [
			'client_id' => setting('google_client_id') ? setting('google_client_id') : $this->config->item('google_oauth_client_id'),
			'client_secret' => setting('google_client_secret') ? setting('google_client_secret') : '',
			'auth_uri' => 'https://accounts.google.com/o/oauth2/v2/auth',
			'token_uri' => 'https://oauth2.googleapis.com/token',
		];

		if (empty($config['client_secret'])) {
			$file = $this->config->item('google_oauth_client_secret_file');
			if ($file && is_file($file)) {
				$json = json_decode(file_get_contents($file), true);
				$item = isset($json['web']) ? $json['web'] : (isset($json['installed']) ? $json['installed'] : []);
				$config['client_id'] = !empty($item['client_id']) ? $item['client_id'] : $config['client_id'];
				$config['client_secret'] = !empty($item['client_secret']) ? $item['client_secret'] : '';
				$config['auth_uri'] = !empty($item['auth_uri']) ? $item['auth_uri'] : $config['auth_uri'];
				$config['token_uri'] = !empty($item['token_uri']) ? $item['token_uri'] : $config['token_uri'];
			}
		}

		return $config;
	}

	private function googleRedirectUri()
	{
		return site_url('login/google_callback');
	}

	private function exchangeGoogleCode($code)
	{
		$client = $this->googleClient();
		if (empty($client['client_secret'])) {
			return null;
		}

		return $this->httpPostJson($client['token_uri'], [
			'code' => $code,
			'client_id' => $client['client_id'],
			'client_secret' => $client['client_secret'],
			'redirect_uri' => $this->googleRedirectUri(),
			'grant_type' => 'authorization_code',
		]);
	}

	private function fetchGoogleProfile($access_token)
	{
		$ch = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$output = curl_exec($ch);
		curl_close($ch);

		return $output ? json_decode($output, true) : null;
	}

	private function httpPostJson($url, $data)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$output = curl_exec($ch);
		curl_close($ch);

		return $output ? json_decode($output, true) : null;
	}

	private function findOrCreateGoogleUser($profile)
	{
		$email = strtolower(trim((string) $profile['email']));
		$google_id = (string) $profile['sub'];

		$user = $this->db->get_where('users', ['google_id' => $google_id])->row();
		if (!$user) {
			$this->db->where('LOWER(email)', $email);
			$user = $this->db->get('users')->row();
		}

		if ($user) {
			$update = [
				'google_id' => $google_id,
				'auth_provider' => 'google',
				'email_verified' => 1,
			];
			if (empty($user->id_ptk)) {
				$ptk = $this->findPtkByEmail($email);
				if ($ptk) {
					$update['id_ptk'] = $ptk->id_ptk;
				}
			}
			$this->db->where('id', $user->id);
			$this->db->update('users', $update);
			return $this->db->get_where('users', ['id' => $user->id])->row();
		}

		$ptk = $this->findPtkByEmail($email);
		if (!$ptk) {
			return null;
		}

		$role_guru = $this->ensureGuruRole();
		$email_parts = explode('@', $email);
		$username = $this->uniqueUsername($email_parts[0]);
		$this->db->insert('users', [
			'name' => $ptk->nama_ptk ?: (!empty($profile['name']) ? $profile['name'] : $email),
			'username' => $username,
			'email' => $email,
			'password' => hash('sha256', bin2hex(random_bytes(16))),
			'phone' => $ptk->telepon ?: '',
			'address' => $ptk->alamat ?: '',
			'role' => $role_guru,
			'id_ptk' => $ptk->id_ptk,
			'google_id' => $google_id,
			'auth_provider' => 'google',
			'email_verified' => 1,
			'status' => 1,
			'img_type' => 'png',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
		$id = $this->db->insert_id();
		if (is_file(FCPATH . 'uploads/users/default.png') && !is_file(FCPATH . 'uploads/users/' . $id . '.png')) {
			copy(FCPATH . 'uploads/users/default.png', FCPATH . 'uploads/users/' . $id . '.png');
		}

		return $this->db->get_where('users', ['id' => $id])->row();
	}

	private function findPtkByEmail($email)
	{
		$this->db->where('LOWER(email)', strtolower($email));
		$this->db->where('status_keaktifan', 'Aktif');
		return $this->db->get('ptk')->row();
	}

	private function uniqueUsername($base)
	{
		$base = preg_replace('/[^a-z0-9_]/i', '_', strtolower($base));
		$base = trim($base, '_') ?: 'google_user';
		$username = $base;
		$index = 1;
		while ($this->db->get_where('users', ['username' => $username])->row()) {
			$username = $base . $index;
			$index++;
		}
		return $username;
	}

	private function ensureGuruRole()
	{
		$this->db->where('LOWER(title)', 'guru');
		$row = $this->db->get('roles')->row();
		if ($row) {
			return $row->id;
		}

		$this->db->insert('roles', ['title' => 'Guru']);
		return $this->db->insert_id();
	}

	private function ensureGoogleOauthColumns()
	{
		$this->load->dbforge();
		if (!$this->db->field_exists('id_ptk', 'users')) {
			$this->dbforge->add_column('users', [
				'id_ptk' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'role'],
			]);
		}
		if (!$this->db->field_exists('google_id', 'users')) {
			$this->dbforge->add_column('users', [
				'google_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'id_ptk'],
			]);
		}
		if (!$this->db->field_exists('auth_provider', 'users')) {
			$this->dbforge->add_column('users', [
				'auth_provider' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'after' => 'google_id'],
			]);
		}
		if (!$this->db->field_exists('email_verified', 'users')) {
			$this->dbforge->add_column('users', [
				'email_verified' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'auth_provider'],
			]);
		}
	}

}

/* End of file Login.php */
/* Location: ./application/controllers/Admin/Login.php */
