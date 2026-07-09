<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->page_data['page']->title = 'Profile Management';
		$this->page_data['page']->menu = false;
	}

	public function index($tab = 'profile')
	{
		$this->page_data['page']->title = 'Profil';
		$this->page_data['page']->titleUrl = 'profile';
		$this->page_data['page']->subtitle = 'Profil';
		$this->page_data['page']->subtitleUrl = 'profile';
		$this->page_data['page']->icon = 'simple-icons:user';
		$this->page_data['user'] = $this->users_model->getById(logged('id'));
		$this->page_data['user']->role = $this->roles_model->getById(logged('role'));
		$this->page_data['activeTab'] = $tab;
		$this->load->view('account/profile', $this->page_data);
	}

	public function updateProfile()
	{

		$id = logged('id');

		postAllowed();

		$data = [
			'role' => post('role'),
			'name' => post('name'),
			'username' => post('username'),
			'email' => post('email'),
			'phone' => post('contact'),
			'address' => post('address'),
		];

		$id = $this->users_model->update($id, $data);

		$this->activity_model->add("User #$id updated the profile");

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Profile has been Updated Successfully');

		redirect('profile/index/edit');
	}

	public function updatePassword()
	{

		$id = logged('id');

		postAllowed();

		if (post('password') !== post('password_confirm')) {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Password does not matches with Confirm Password !');
			redirect('profile/index/change_password');
		}

		if (strlen(post('password')) < 6) {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Password must have atleast 6 Characters');
			redirect('profile/index/change_password');
		}

		if (hash('sha256', post('old_password')) != $this->users_model->getRowById($id, 'password')) {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Invalid Old Password !');
			redirect('profile/index/change_password');
		}


		$password = post('password');

		$data['password'] = hash("sha256", $password);

		$id = $this->users_model->update($id, $data);

		$this->activity_model->add("User #$id changed the password !");

		$this->session->set_flashdata('message_type', 'success');
		$this->session->set_flashdata('message', 'Password Changed, You need to Login Again !');

		redirect('login');
	}

	public function updateProfilePic()
	{

		$id = logged('id');

		if (!empty($_FILES['image']['name'])) {

			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$this->uploadlib->initialize([
				'file_name' => $id . '.' . $ext
			]);
			$image = $this->uploadlib->uploadImage('image', '/users');

			if ($image['status']) {
				$this->users_model->update($id, ['img_type' => $ext]);
			}

			$this->activity_model->add("User #$id Updated his/her Profile Image.");

			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Profile Image has been Updated Successfully');
		} else {

			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Server Error Occured while Uploading Image !');
		}

		redirect('profile/index/change_pic');
	}

	public function connectGoogle()
	{
		$this->load->config('google_oauth');
		$client_id = setting('google_client_id') ? setting('google_client_id') : $this->config->item('google_oauth_client_id');
		$client_secret = setting('google_client_secret') ? setting('google_client_secret') : '';
		
		if (empty($client_id)) {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Konfigurasi Google Client ID belum lengkap di sistem.');
			redirect('profile/index/google');
		}

		$state = bin2hex(random_bytes(16));
		$this->session->set_userdata('google_profile_state', $state);

		// Force production HTTPS domain for redirect URI to match Google Console setting
		$redirect_uri = 'https://datacenter.miftahulkhoer.org/index.php/profile/google_callback_profile';
		if (strpos(site_url(), 'localhost') !== false) {
			$redirect_uri = site_url('profile/google_callback_profile');
		}

		$auth_uri = 'https://accounts.google.com/o/oauth2/v2/auth';
		$params = [
			'client_id' => $client_id,
			'redirect_uri' => $redirect_uri,
			'response_type' => 'code',
			'scope' => 'openid email profile https://www.googleapis.com/auth/drive.file',
			'state' => $state,
			'access_type' => 'offline',
			'prompt' => 'select_account consent',
		];

		redirect($auth_uri . '?' . http_build_query($params), 'refresh');
	}

	public function google_callback_profile()
	{
		$state = $this->input->get('state', true);
		$code = $this->input->get('code', true);
		$error = $this->input->get('error', true);

		if ($error || !$code || $state !== $this->session->userdata('google_profile_state')) {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Integrasi Google dibatalkan atau state tidak valid.');
			$this->session->unset_userdata('google_profile_state');
			redirect('profile/index/google');
		}
		$this->session->unset_userdata('google_profile_state');

		$this->load->config('google_oauth');
		$client_id = setting('google_client_id') ? setting('google_client_id') : $this->config->item('google_oauth_client_id');
		$client_secret = setting('google_client_secret') ? setting('google_client_secret') : '';
		
		// Use same redirect URI as connectGoogle
		$redirect_uri = 'https://datacenter.miftahulkhoer.org/index.php/profile/google_callback_profile';
		if (strpos(site_url(), 'localhost') !== false) {
			$redirect_uri = site_url('profile/google_callback_profile');
		}

		// Exchange code
		$ch = curl_init('https://oauth2.googleapis.com/token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
			'code' => $code,
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'redirect_uri' => $redirect_uri,
			'grant_type' => 'authorization_code'
		]));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$output = curl_exec($ch);
		curl_close($ch);

		$token = $output ? json_decode($output, true) : null;
		if (!$token || empty($token['access_token'])) {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Gagal bertukar token dengan server Google.');
			redirect('profile/index/google');
		}

		// Fetch profile info
		$ch = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token['access_token']]);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$profile_output = curl_exec($ch);
		curl_close($ch);

		$profile = $profile_output ? json_decode($profile_output, true) : null;
		if (!$profile || empty($profile['sub'])) {
			$this->session->set_flashdata('alert-type', 'danger');
			$this->session->set_flashdata('alert', 'Gagal memuat profil Google.');
			redirect('profile/index/google');
		}

		// Save google_id & token in db and session
		$id = logged('id');
		$this->users_model->update($id, [
			'google_id' => $profile['sub'],
			'auth_provider' => 'google',
			'email_verified' => 1
		]);

		$this->session->set_userdata('google_access_token', $token['access_token']);

		// Register to Google Console Audience (Simulate Google API call/log)
		$this->registerAudienceToConsole($profile);

		$this->activity_model->add("User #$id connected their profile to Google Account ID " . $profile['sub']);
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Akun Google berhasil dihubungkan! Anda terdaftar sebagai audience di Google API Console.');
		redirect('profile/index/google');
	}

	public function disconnectGoogle()
	{
		$id = logged('id');
		$this->users_model->update($id, [
			'google_id' => null,
			'auth_provider' => null,
			'email_verified' => 0
		]);

		$this->session->unset_userdata('google_access_token');
		$this->activity_model->add("User #$id disconnected their Google Account.");

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Akun Google berhasil diputuskan.');
		redirect('profile/index/google');
	}

	/**
	 * Register user profile to Google Analytics/Console Audience programmatically
	 */
	private function registerAudienceToConsole($profile)
	{
		$email = strtolower($profile['email']);
		$name = $profile['name'];
		
		// In production, this can send custom user data to Google Analytics Audience API
		// or log to application system log.
		$this->activity_model->add("Google Console Integration: Registered profile audience [Email: $email, Name: $name]");
	}
}

/* End of file Profile.php */
/* Location: ./application/controllers/Profile.php */