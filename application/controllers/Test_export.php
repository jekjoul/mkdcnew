<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test_export extends CI_Controller
{
    public function index()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        // Get one active pembelajaran/rombel
        $p = $this->db->get_where('pembelajaran', ['status' => 'Aktif'])->row();
        if (!$p) {
            echo "No active pembelajaran found.";
            return;
        }

        // Get student in that pembelajaran
        $this->db->select('peserta_didik_id');
        $ps = $this->db->get_where('pembelajaran_siswa', ['id_pembelajaran' => $p->id_pembelajaran])->result();
        
        $students = [];
        foreach ($ps as $row) {
            $students[] = $row->peserta_didik_id;
        }

        if (empty($students)) {
            echo "No students found in pembelajaran ID: " . $p->id_pembelajaran;
            return;
        }

        // Simulate POST data
        $_POST['id_pembelajaran'] = [$p->id_pembelajaran];
        $_POST['fields'] = ['nama_siswa', 'nisn', 'nipd'];
        $_POST['students'] = $students;

        // Call Export_siswa/export_excel
        require_once APPPATH . 'controllers/Export_siswa.php';
        $controller = new Export_siswa();
        $controller->export_excel();
    }
}
