<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_api_db extends CI_Controller {

	public function index()
	{
		header('Content-Type: text/plain');
		$this->load->database();

		// 1. Cek tabel siswa
		$q_siswa = $this->db->query("SELECT COUNT(*) as total, SUM(CASE WHEN pin_fingerprint IS NOT NULL AND pin_fingerprint != '' AND pin_fingerprint != 0 THEN 1 ELSE 0 END) as mapped FROM siswa")->row();
		echo "=== DATA SISWA ONLINE ===\n";
		echo "Total Siswa: " . $q_siswa->total . "\n";
		echo "Siswa ter-mapping PIN: " . $q_siswa->mapped . "\n\n";

		// Sampel siswa ter-mapping
		$samples = $this->db->query("SELECT id_siswa, nama, pin_fingerprint FROM siswa WHERE pin_fingerprint IS NOT NULL AND pin_fingerprint != 0 LIMIT 10")->result();
		foreach ($samples as $s) {
			echo "- ID: {$s->id_siswa} | Nama: {$s->nama} | PIN: {$s->pin_fingerprint}\n";
		}
		echo "\n";

		// 2. Cek tabel ptk
		$q_ptk = $this->db->query("SELECT COUNT(*) as total, SUM(CASE WHEN pin_fingerprint IS NOT NULL AND pin_fingerprint != '' AND pin_fingerprint != 0 THEN 1 ELSE 0 END) as mapped FROM ptk")->row();
		echo "=== DATA PTK ONLINE ===\n";
		echo "Total PTK: " . $q_ptk->total . "\n";
		echo "PTK ter-mapping PIN: " . $q_ptk->mapped . "\n\n";

		$samples_ptk = $this->db->query("SELECT id_ptk, nama, pin_fingerprint FROM ptk WHERE pin_fingerprint IS NOT NULL AND pin_fingerprint != 0 LIMIT 10")->result();
		foreach ($samples_ptk as $p) {
			echo "- ID: {$p->id_ptk} | Nama: {$p->nama} | PIN: {$p->pin_fingerprint}\n";
		}
	}
}
