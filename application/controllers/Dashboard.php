<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->page_data['page']->title = 'Dashboard';
		$this->page_data['page']->titleUrl = 'dashboard';
		$this->page_data['page']->subtitle = 'Dashboard';
		$this->page_data['page']->subtitleUrl = 'dashboard';
		$this->page_data['page']->icon = 'solar:home-angle-2-linear';

		// 1. Total Seluruh Siswa Aktif
		$this->page_data['total_siswa'] = $this->db->where('status_keaktifan', 'Aktif')->count_all_results('siswa');

		// 2. Total Pegawai (PTK Aktif)
		$this->page_data['total_ptk'] = $this->db->where('status_keaktifan', 'Aktif')->count_all_results('ptk');

		// 3. Total Alumni
		$this->page_data['total_alumni'] = $this->db->table_exists('alumni') ? $this->db->count_all('alumni') : 0;

		// Query dasar untuk menghitung siswa per lembaga pada tahun pelajaran aktif
		$base_query = "SELECT COUNT(DISTINCT ps.peserta_didik_id) as total 
					   FROM pembelajaran_siswa ps 
					   JOIN pembelajaran p ON p.id_pembelajaran = ps.id_pembelajaran 
					   JOIN lembaga l ON l.id_lembaga = p.id_lembaga 
					   JOIN pembelajaran_tahun_pelajaran tp ON tp.id_tahun_pelajaran = p.id_tahun_pelajaran 
					   WHERE tp.status = 'Aktif'";

		$this->page_data['total_smp'] = $this->db->query("$base_query AND l.nama_lembaga LIKE '%SMP%'")->row()->total;
		$this->page_data['total_sma'] = $this->db->query("$base_query AND l.nama_lembaga LIKE '%SMA%'")->row()->total;
		$this->page_data['total_ponpes'] = $this->db->query("$base_query AND (l.nama_lembaga LIKE '%Ponpes%' OR l.nama_lembaga LIKE '%Pondok%')")->row()->total;

		// Query Tren Pendaftaran SMP vs SMA per Tahun
		$this->page_data['tren_pendaftaran'] = $this->db->query("
			SELECT YEAR(s.tanggal_pendaftaran) as tahun,
				   COUNT(DISTINCT CASE WHEN l.nama_lembaga LIKE '%SMP%' THEN s.id_siswa END) as total_smp,
				   COUNT(DISTINCT CASE WHEN l.nama_lembaga LIKE '%SMA%' THEN s.id_siswa END) as total_sma
			FROM siswa s
			LEFT JOIN pembelajaran_siswa ps ON ps.peserta_didik_id = s.id_siswa
			LEFT JOIN pembelajaran p ON p.id_pembelajaran = ps.id_pembelajaran
			LEFT JOIN lembaga l ON l.id_lembaga = p.id_lembaga
			WHERE s.tanggal_pendaftaran IS NOT NULL AND YEAR(s.tanggal_pendaftaran) > 2000
			GROUP BY YEAR(s.tanggal_pendaftaran)
			ORDER BY tahun ASC
		")->result();

		// Query Detail Siswa per Rombel per Lembaga
		$siswa_rombel_raw = $this->db->query("
			SELECT l.id_lembaga, l.nama_lembaga, CONCAT(t.nama_tingkat, ' - ', r.nama_rombel) as nama_rombel,
				   COUNT(DISTINCT CASE WHEN s.jenis_kelamin IN ('L', 'Laki-laki') THEN s.id_siswa END) as laki_laki,
				   COUNT(DISTINCT CASE WHEN s.jenis_kelamin IN ('P', 'Perempuan') THEN s.id_siswa END) as perempuan,
				   COUNT(DISTINCT s.id_siswa) as jumlah
			FROM pembelajaran p
			JOIN lembaga l ON l.id_lembaga = p.id_lembaga
			JOIN rombel r ON r.id_rombel = p.id_rombel
			JOIN master_tingkat_sekolah t ON t.id_tingkat_sekolah = p.id_tingkat_sekolah
			JOIN pembelajaran_tahun_pelajaran tp ON tp.id_tahun_pelajaran = p.id_tahun_pelajaran
			JOIN pembelajaran_siswa ps ON ps.id_pembelajaran = p.id_pembelajaran
			JOIN siswa s ON s.id_siswa = ps.peserta_didik_id
			WHERE tp.status = 'Aktif' AND s.status_keaktifan = 'Aktif'
			GROUP BY l.id_lembaga, r.id_rombel
			ORDER BY l.nama_lembaga ASC, t.tingkat_angka ASC, r.nama_rombel ASC
		")->result();

		$siswa_rombel = [];
		foreach ($siswa_rombel_raw as $row) {
			$siswa_rombel[$row->nama_lembaga][] = $row;
		}
		$this->page_data['siswa_rombel'] = $siswa_rombel;

		$this->load->view('dashboard', $this->page_data);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */