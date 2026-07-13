<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Surat extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTables();
    }

    public function index()
    {
        ifPermissions('surat_list');
        redirect('surat/keluar');
    }

    public function masuk()
    {
        $this->setPage('Surat Masuk', 'surat/masuk', 'solar:inbox-in-linear');
        $this->db->order_by('tanggal_surat', 'DESC');
        $this->db->order_by('id_surat_masuk', 'DESC');
        $this->page_data['surat'] = $this->db->get('surat_masuk')->result();
        $this->load->view('surat/masuk_list', $this->page_data);
    }

    public function masuk_tambah()
    {
        $this->setPage('Tambah Surat Masuk', 'surat/masuk_tambah', 'solar:inbox-in-linear');
        $this->page_data['row'] = null;
        $this->load->view('surat/masuk_form', $this->page_data);
    }

    public function masuk_edit($id)
    {
        $this->setPage('Edit Surat Masuk', 'surat/masuk_edit/' . $id, 'solar:inbox-in-linear');
        $this->page_data['row'] = $this->db->get_where('surat_masuk', ['id_surat_masuk' => $id])->row();
        if (!$this->page_data['row']) {
            show_404();
        }
        $this->load->view('surat/masuk_form', $this->page_data);
    }

    public function masuk_simpan()
    {
        postAllowed();
        $id = (int) post('id_surat_masuk');
        $data = [
            'tujuan_surat' => post('tujuan_surat'),
            'pengirim' => post('pengirim'),
            'tanggal_surat' => post('tanggal_surat'),
            'nomor_surat' => post('nomor_surat') ?: null,
            'perihal' => post('perihal') ?: null,
            'status_disposisi' => post('status_disposisi'),
            'catatan_disposisi' => post('catatan_disposisi') ?: null,
        ];

        if ($id > 0) {
            $this->db->where('id_surat_masuk', $id);
            $this->db->update('surat_masuk', $data);
        } else {
            $this->db->insert('surat_masuk', $data);
            $id = $this->db->insert_id();
        }

        $upload = $this->uploadFile('scan_surat', 'surat_masuk', 'surat-masuk-' . $id);
        if ($upload === false) {
            redirect($id > 0 ? 'surat/masuk_edit/' . $id : 'surat/masuk_tambah');
            return;
        }
        if ($upload !== null) {
            $this->db->where('id_surat_masuk', $id);
            $this->db->update('surat_masuk', ['scan_file' => $upload]);
        }

        $this->flashSuccess('Surat masuk berhasil disimpan');
        redirect('surat/masuk');
    }

    public function kode()
    {
        $this->setPage('Kode Surat', 'surat/kode', 'solar:hashtag-linear');
        $this->db->select('sk.*, l.nama_lembaga');
        $this->db->from('surat_kode sk');
        $this->db->join('lembaga l', 'l.id_lembaga = sk.id_lembaga', 'left');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('sk.kode_jenis', 'ASC');
        $this->page_data['kode'] = $this->db->get()->result();
        $this->load->view('surat/kode_list', $this->page_data);
    }

    public function kode_tambah()
    {
        $this->setPage('Tambah Kode Surat', 'surat/kode_tambah', 'solar:hashtag-linear');
        $this->setKodeOptions();
        $this->page_data['row'] = null;
        $this->load->view('surat/kode_form', $this->page_data);
    }

    public function kode_edit($id)
    {
        $this->setPage('Edit Kode Surat', 'surat/kode_edit/' . $id, 'solar:hashtag-linear');
        $this->setKodeOptions();
        $this->page_data['row'] = $this->db->get_where('surat_kode', ['id_kode_surat' => $id])->row();
        if (!$this->page_data['row']) {
            show_404();
        }
        $this->load->view('surat/kode_form', $this->page_data);
    }

    public function kode_simpan()
    {
        postAllowed();
        $id = (int) post('id_kode_surat');
        $data = [
            'id_lembaga' => post('id_lembaga'),
            'kode_jenis' => post('kode_jenis'),
            'nama_jenis' => post('nama_jenis'),
            'kode_lembaga' => post('kode_lembaga'),
            'lokasi' => post('lokasi') ?: 'PANJALU',
            'format_nomor' => post('format_nomor'),
            'status' => post('status') ?: 'Aktif',
        ];

        if ($id > 0) {
            $this->db->where('id_kode_surat', $id);
            $this->db->update('surat_kode', $data);
        } else {
            $this->db->insert('surat_kode', $data);
        }

        $this->flashSuccess('Kode surat berhasil disimpan');
        redirect('surat/kode');
    }

    public function template()
    {
        $this->setPage('Template Surat', 'surat/template', 'solar:document-text-linear');
        $this->db->select('st.*, sk.kode_jenis, sk.nama_jenis, l.nama_lembaga');
        $this->db->from('surat_template st');
        $this->db->join('surat_kode sk', 'sk.id_kode_surat = st.id_kode_surat');
        $this->db->join('lembaga l', 'l.id_lembaga = sk.id_lembaga', 'left');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('sk.kode_jenis', 'ASC');
        $this->db->order_by('st.nama_template', 'ASC');
        $this->page_data['template'] = $this->db->get()->result();
        $this->load->view('surat/template_list', $this->page_data);
    }

    public function template_tambah()
    {
        $this->setPage('Tambah Template Surat', 'surat/template_tambah', 'solar:document-text-linear');
        $this->setTemplateOptions();
        $this->page_data['row'] = null;
        $this->load->view('surat/template_form', $this->page_data);
    }

    public function template_edit($id)
    {
        $this->setPage('Edit Template Surat', 'surat/template_edit/' . $id, 'solar:document-text-linear');
        $this->setTemplateOptions();
        $this->page_data['row'] = $this->db->get_where('surat_template', ['id_template_surat' => $id])->row();
        if (!$this->page_data['row']) {
            show_404();
        }
        $this->load->view('surat/template_form', $this->page_data);
    }

    public function template_simpan()
    {
        postAllowed();
        $id = (int) post('id_template_surat');
        $data = [
            'id_kode_surat' => post('id_kode_surat'),
            'nama_template' => post('nama_template'),
            'perihal_default' => post('perihal_default') ?: null,
            'isi_template' => post('isi_template'),
            'status' => post('status') ?: 'Aktif',
        ];

        if ($id > 0) {
            $this->db->where('id_template_surat', $id);
            $this->db->update('surat_template', $data);
        } else {
            $this->db->insert('surat_template', $data);
        }

        $this->flashSuccess('Template surat berhasil disimpan');
        redirect('surat/template');
    }

    public function kop()
    {
        ifPermissions('surat_list'); // Menggunakan permission surat_list yang relevan untuk modul ini
        $this->setPage('Pengaturan Kop Surat', 'surat/kop', 'solar:document-text-linear');
        $this->db->order_by('id_kop_surat', 'DESC');
        $this->page_data['kop'] = $this->db->get('surat_kop')->result();
        $this->load->view('surat/kop_list', $this->page_data);
    }

    public function kop_tambah()
    {
        ifPermissions('surat_list');
        $this->setPage('Tambah Kop Surat', 'surat/kop_tambah', 'solar:document-text-linear');
        $this->page_data['row'] = null;
        $this->load->view('surat/kop_form', $this->page_data);
    }

    public function kop_edit($id)
    {
        ifPermissions('surat_list');
        $this->setPage('Edit Kop Surat', 'surat/kop_edit/' . $id, 'solar:document-text-linear');
        $this->page_data['row'] = $this->db->get_where('surat_kop', ['id_kop_surat' => $id])->row();
        if (!$this->page_data['row']) {
            show_404();
        }
        $this->load->view('surat/kop_form', $this->page_data);
    }

    public function kop_simpan()
    {
        postAllowed();
        ifPermissions('surat_list');

        $id = (int) post('id_kop_surat');
        $data = [
            'nama_kop' => post('nama_kop'),
            'naungan' => post('naungan') ?: null,
            'naungan_2' => post('naungan_2') ?: null,
            'nama_lembaga' => post('nama_lembaga'),
            'sub_nama' => post('sub_nama') ?: null,
            'alamat' => post('alamat') ?: null,
            'kontak' => post('kontak') ?: null,
            'font_size_naungan' => (int) post('font_size_naungan') ?: 11,
            'font_size_naungan_2' => (int) post('font_size_naungan_2') ?: 11,
            'font_size_lembaga' => (int) post('font_size_lembaga') ?: 18,
            'font_size_sub' => (int) post('font_size_sub') ?: 13,
            'font_size_alamat' => (int) post('font_size_alamat') ?: 9,
            'layout_style' => post('layout_style') ?: 'center',
            'status' => post('status') ?: 'Aktif',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($id > 0) {
            $this->db->where('id_kop_surat', $id);
            $this->db->update('surat_kop', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('surat_kop', $data);
            $id = $this->db->insert_id();
        }

        // Upload Logo Kop Surat (Kiri)
        if (!empty($_FILES['logo']['name'])) {
            $path = './uploads/kop_logo/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $upload = $this->uploadFile('logo', 'kop_logo', 'kop-logo-' . $id);
            if ($upload !== false && $upload !== null) {
                $this->db->where('id_kop_surat', $id);
                $this->db->update('surat_kop', ['logo' => $upload]);
            }
        }

        // Upload Logo Kop Surat Kanan (jika ada)
        if (!empty($_FILES['logo_kanan']['name'])) {
            $path = './uploads/kop_logo/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $upload_kanan = $this->uploadFile('logo_kanan', 'kop_logo', 'kop-logo-kanan-' . $id);
            if ($upload_kanan !== false && $upload_kanan !== null) {
                $this->db->where('id_kop_surat', $id);
                $this->db->update('surat_kop', ['logo_kanan' => $upload_kanan]);
            }
        }

        $this->flashSuccess('Pengaturan kop surat berhasil disimpan');
        redirect('surat/kop');
    }

    public function kop_hapus($id)
    {
        ifPermissions('surat_list');
        $this->db->where('id_kop_surat', $id);
        $this->db->delete('surat_kop');
        $this->flashSuccess('Kop surat berhasil dihapus');
        redirect('surat/kop');
    }

    public function keluar()
    {
        $this->setPage('Surat Keluar', 'surat/keluar', 'solar:inbox-out-linear');
        $this->db->select('skel.*, l.nama_lembaga, sk.kode_jenis, sk.nama_jenis');
        $this->db->from('surat_keluar skel');
        $this->db->join('lembaga l', 'l.id_lembaga = skel.id_lembaga', 'left');
        $this->db->join('surat_kode sk', 'sk.id_kode_surat = skel.id_kode_surat', 'left');
        $this->db->order_by('skel.tanggal_surat', 'DESC');
        $this->db->order_by('skel.id_surat_keluar', 'DESC');
        $this->page_data['surat'] = $this->db->get()->result();
        $this->load->view('surat/keluar_list', $this->page_data);
    }

    public function keluar_tambah()
    {
        $this->setPage('Buat Surat Keluar', 'surat/keluar_tambah', 'solar:inbox-out-linear');
        $this->setKeluarOptions();
        $this->page_data['row'] = null;
        $this->page_data['prediksi_nomor'] = '';
        $this->load->view('surat/keluar_form', $this->page_data);
    }

    public function keluar_edit($id)
    {
        $this->setPage('Edit Surat Keluar', 'surat/keluar_edit/' . $id, 'solar:inbox-out-linear');
        $this->setKeluarOptions();
        $this->page_data['row'] = $this->db->get_where('surat_keluar', ['id_surat_keluar' => $id])->row();
        if (!$this->page_data['row']) {
            show_404();
        }
        $this->page_data['prediksi_nomor'] = $this->page_data['row']->nomor_surat;
        $this->load->view('surat/keluar_form', $this->page_data);
    }

    public function keluar_simpan()
    {
        postAllowed();
        $id = (int) post('id_surat_keluar');
        $kode = $this->db->get_where('surat_kode', ['id_kode_surat' => post('id_kode_surat')])->row();
        if (!$kode) {
            $this->flashDanger('Kode surat belum dipilih');
            redirect('surat/keluar_tambah');
            return;
        }

        $tanggal = post('tanggal_surat') ?: date('Y-m-d');
        $tahun = (int) date('Y', strtotime($tanggal));
        $nomor_custom = post('nomor_custom');
        $nomor_urut = post('nomor_urut') !== '' ? (int) post('nomor_urut') : $this->nextNomorUrut($kode->id_kode_surat, $tahun, $id);
        $nomor_surat = $nomor_custom ?: $this->formatNomorSurat($kode, $nomor_urut, $tahun);
        $token = post('token_validasi') ?: bin2hex(random_bytes(16));

        $data = [
            'id_lembaga' => $kode->id_lembaga,
            'id_kode_surat' => $kode->id_kode_surat,
            'id_template_surat' => post('id_template_surat') ?: null,
            'id_kop_surat' => post('id_kop_surat') ?: null,
            'tanggal_surat' => $tanggal,
            'nomor_urut' => $nomor_urut,
            'nomor_surat' => $nomor_surat,
            'nomor_custom' => $nomor_custom ?: null,
            'tujuan_surat' => post('tujuan_surat'),
            'perihal' => post('perihal'),
            'isi_surat' => post('isi_surat'),
            'penandatangan_nama' => post('penandatangan_nama') ?: null,
            'penandatangan_jabatan' => post('penandatangan_jabatan') ?: null,
            'status' => post('status') ?: 'Draft',
            'token_validasi' => $token,
        ];

        if ($id > 0) {
            $this->db->where('id_surat_keluar', $id);
            $this->db->update('surat_keluar', $data);
        } else {
            $this->db->insert('surat_keluar', $data);
            $id = $this->db->insert_id();
        }

        $this->flashSuccess('Surat keluar berhasil disimpan');
        redirect('surat/keluar_preview/' . $id);
    }

    public function keluar_preview($id)
    {
        $surat = $this->getSuratKeluar($id);
        if (!$surat) {
            show_404();
        }

        $this->page_data['surat'] = $surat;
        $this->page_data['validasi_url'] = url('surat/validasi/' . $surat->token_validasi);
        $this->page_data['isi_render'] = $this->renderTemplate($surat->isi_surat, $surat, $this->page_data['validasi_url']);
        $this->load->view('surat/keluar_preview', $this->page_data);
    }

    public function validasi($token)
    {
        $this->db->select('skel.*, l.nama_lembaga, sk.kode_jenis, sk.nama_jenis');
        $this->db->from('surat_keluar skel');
        $this->db->join('lembaga l', 'l.id_lembaga = skel.id_lembaga', 'left');
        $this->db->join('surat_kode sk', 'sk.id_kode_surat = skel.id_kode_surat', 'left');
        $this->db->where('skel.token_validasi', $token);
        $this->page_data['surat'] = $this->db->get()->row();
        $this->load->view('surat/validasi', $this->page_data);
    }

    public function template_json($id)
    {
        $row = $this->db->get_where('surat_template', ['id_template_surat' => $id])->row();
        $this->output->set_content_type('application/json')->set_output(json_encode($row ?: []));
    }

    private function setPage($subtitle, $subtitleUrl, $icon)
    {
        $this->page_data['page']->title = 'Surat Menyurat';
        $this->page_data['page']->titleUrl = 'surat';
        $this->page_data['page']->subtitle = $subtitle;
        $this->page_data['page']->subtitleUrl = $subtitleUrl;
        $this->page_data['page']->icon = $icon;
    }

    private function setKodeOptions()
    {
        $this->db->order_by('nama_lembaga', 'ASC');
        $this->page_data['lembaga'] = $this->db->get('lembaga')->result();
    }

    private function setTemplateOptions()
    {
        $this->db->select('sk.*, l.nama_lembaga');
        $this->db->from('surat_kode sk');
        $this->db->join('lembaga l', 'l.id_lembaga = sk.id_lembaga', 'left');
        $this->db->where('sk.status', 'Aktif');
        $this->db->order_by('l.nama_lembaga', 'ASC');
        $this->db->order_by('sk.kode_jenis', 'ASC');
        $this->page_data['kode'] = $this->db->get()->result();
    }

    private function setKeluarOptions()
    {
        $this->setTemplateOptions();
        $this->db->where('status', 'Aktif');
        $this->db->order_by('id_kode_surat', 'ASC');
        $this->page_data['template'] = $this->db->get('surat_template')->result();
        $this->db->where('status_keaktifan', 'Aktif');
        $this->db->order_by('nama_ptk', 'ASC');
        $this->page_data['ptk'] = $this->db->get('ptk')->result();
        
        // Ambil list Kop Surat Aktif untuk dropdown
        $this->db->where('status', 'Aktif');
        $this->db->order_by('nama_kop', 'ASC');
        $this->page_data['kop_list'] = $this->db->get('surat_kop')->result();
    }

    private function getSuratKeluar($id)
    {
        $this->db->select('skel.*, l.nama_lembaga, l.alamat, l.telepon, l.email, l.logo, l.id_ptk_kepsek, ptk.nama_ptk AS nama_kepsek, sk.kode_jenis, sk.nama_jenis, sk.kode_lembaga, sk.lokasi, kp.nama_kop, kp.logo as kop_logo, kp.logo_kanan, kp.naungan, kp.naungan_2, kp.nama_lembaga as kop_nama_lembaga, kp.sub_nama, kp.alamat as alamat_kop, kp.kontak, kp.font_size_naungan, kp.font_size_naungan_2, kp.font_size_lembaga, kp.font_size_sub, kp.font_size_alamat, kp.layout_style');
        $this->db->from('surat_keluar skel');
        $this->db->join('lembaga l', 'l.id_lembaga = skel.id_lembaga', 'left');
        $this->db->join('ptk', 'ptk.id_ptk = l.id_ptk_kepsek', 'left');
        $this->db->join('surat_kode sk', 'sk.id_kode_surat = skel.id_kode_surat', 'left');
        $this->db->join('surat_kop kp', 'kp.id_kop_surat = skel.id_kop_surat', 'left');
        $this->db->where('skel.id_surat_keluar', $id);
        return $this->db->get()->row();
    }

    private function nextNomorUrut($id_kode_surat, $tahun, $exclude_id = 0)
    {
        $this->db->select_max('nomor_urut', 'max_nomor');
        $this->db->where('id_kode_surat', $id_kode_surat);
        $this->db->where('YEAR(tanggal_surat) = ' . (int) $tahun, null, false);
        if ($exclude_id > 0) {
            $this->db->where('id_surat_keluar !=', $exclude_id);
        }
        $row = $this->db->get('surat_keluar')->row();
        return ((int) ($row ? $row->max_nomor : 0)) + 1;
    }

    private function formatNomorSurat($kode, $nomor_urut, $tahun)
    {
        $nomor = str_pad((int) $nomor_urut, 3, '0', STR_PAD_LEFT);
        $format = $kode->format_nomor ?: '{kode_jenis}/{nomor}-{kode_lembaga}/{lokasi}/{tahun}';
        return str_replace(
            ['{kode_jenis}', '{nomor}', '{kode_lembaga}', '{lokasi}', '{tahun}'],
            [$kode->kode_jenis, $nomor, $kode->kode_lembaga, $kode->lokasi, $tahun],
            $format
        );
    }

    private function renderTemplate($content, $surat, $validasi_url)
    {
        $tanggal = date('d-m-Y', strtotime($surat->tanggal_surat));
        $map = [
            '{{nomor_surat}}' => $surat->nomor_surat,
            '{{tanggal_surat}}' => $tanggal,
            '{{tujuan_surat}}' => $surat->tujuan_surat,
            '{{perihal}}' => $surat->perihal,
            '{{nama_lembaga}}' => $surat->nama_lembaga,
            '{{tahun}}' => date('Y', strtotime($surat->tanggal_surat)),
            '{{validasi_url}}' => $validasi_url,
        ];
        return str_replace(array_keys($map), array_values($map), (string) $content);
    }

    private function uploadFile($field, $folder, $name)
    {
        if (empty($_FILES[$field]['name'])) {
            return null;
        }

        $path = FCPATH . 'uploads/' . trim($folder, '/') . '/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $this->load->library('upload');
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'pdf|jpg|jpeg|png',
            'file_name' => $name . '-' . time(),
            'overwrite' => false,
        ]);

        if (!$this->upload->do_upload($field)) {
            $this->session->set_flashdata('alert-type', 'danger');
            $this->session->set_flashdata('alert', strip_tags($this->upload->display_errors()));
            return false;
        }

        return $this->upload->data('file_name');
    }

    private function flashSuccess($message)
    {
        $this->session->set_flashdata('alert-type', 'success');
        $this->session->set_flashdata('alert', $message);
    }

    private function flashDanger($message)
    {
        $this->session->set_flashdata('alert-type', 'danger');
        $this->session->set_flashdata('alert', $message);
    }

    private function ensureTables()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists('surat_kode')) {
            $this->dbforge->add_field([
                'id_kode_surat' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_lembaga' => ['type' => 'INT', 'constraint' => 11],
                'kode_jenis' => ['type' => 'VARCHAR', 'constraint' => 50],
                'nama_jenis' => ['type' => 'VARCHAR', 'constraint' => 150],
                'kode_lembaga' => ['type' => 'VARCHAR', 'constraint' => 50],
                'lokasi' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'PANJALU'],
                'format_nomor' => ['type' => 'VARCHAR', 'constraint' => 180],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Aktif'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_kode_surat', true);
            $this->dbforge->create_table('surat_kode', true);
        }

        if (!$this->db->table_exists('surat_template')) {
            $this->dbforge->add_field([
                'id_template_surat' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_kode_surat' => ['type' => 'INT', 'constraint' => 11],
                'nama_template' => ['type' => 'VARCHAR', 'constraint' => 150],
                'perihal_default' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
                'isi_template' => ['type' => 'TEXT'],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Aktif'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_template_surat', true);
            $this->dbforge->create_table('surat_template', true);
        }

        if (!$this->db->table_exists('surat_masuk')) {
            $this->dbforge->add_field([
                'id_surat_masuk' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'tujuan_surat' => ['type' => 'VARCHAR', 'constraint' => 150],
                'pengirim' => ['type' => 'VARCHAR', 'constraint' => 150],
                'tanggal_surat' => ['type' => 'DATE'],
                'nomor_surat' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'perihal' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
                'scan_file' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
                'status_disposisi' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'Belum Disposisi'],
                'catatan_disposisi' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_surat_masuk', true);
            $this->dbforge->create_table('surat_masuk', true);
        }

        if (!$this->db->table_exists('surat_keluar')) {
            $this->dbforge->add_field([
                'id_surat_keluar' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_lembaga' => ['type' => 'INT', 'constraint' => 11],
                'id_kode_surat' => ['type' => 'INT', 'constraint' => 11],
                'id_template_surat' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'id_kop_surat' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'tanggal_surat' => ['type' => 'DATE'],
                'nomor_urut' => ['type' => 'INT', 'constraint' => 11],
                'nomor_surat' => ['type' => 'VARCHAR', 'constraint' => 180],
                'nomor_custom' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
                'tujuan_surat' => ['type' => 'VARCHAR', 'constraint' => 180],
                'perihal' => ['type' => 'VARCHAR', 'constraint' => 180],
                'isi_surat' => ['type' => 'TEXT'],
                'penandatangan_nama' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'penandatangan_jabatan' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'Draft'],
                'token_validasi' => ['type' => 'VARCHAR', 'constraint' => 80],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_surat_keluar', true);
            $this->dbforge->create_table('surat_keluar', true);
        } else {
            // Cek jika kolom id_kop_surat belum ada di table surat_keluar
            if (!$this->db->field_exists('id_kop_surat', 'surat_keluar')) {
                $this->dbforge->add_column('surat_keluar', [
                    'id_kop_surat' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'id_template_surat']
                ]);
            }
        }

        if (!$this->db->table_exists('surat_kop')) {
            $this->dbforge->add_field([
                'id_kop_surat' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'nama_kop' => ['type' => 'VARCHAR', 'constraint' => 100],
                'logo' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'logo_kanan' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'naungan' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'naungan_2' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'nama_lembaga' => ['type' => 'VARCHAR', 'constraint' => 150],
                'sub_nama' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'alamat' => ['type' => 'TEXT', 'null' => true],
                'kontak' => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
                'font_size_naungan' => ['type' => 'INT', 'constraint' => 3, 'default' => 12],
                'font_size_naungan_2' => ['type' => 'INT', 'constraint' => 3, 'default' => 12],
                'font_size_lembaga' => ['type' => 'INT', 'constraint' => 3, 'default' => 18],
                'font_size_sub' => ['type' => 'INT', 'constraint' => 3, 'default' => 14],
                'font_size_alamat' => ['type' => 'INT', 'constraint' => 3, 'default' => 10],
                'layout_style' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'center'], // center, left_logo, double_logo
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Aktif'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_kop_surat', true);
            $this->dbforge->create_table('surat_kop', true);
        } else {
            // Cek jika kolom logo_kanan belum ada di table surat_kop
            if (!$this->db->field_exists('logo_kanan', 'surat_kop')) {
                $this->dbforge->add_column('surat_kop', [
                    'logo_kanan' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'logo']
                ]);
            }
            // Cek jika kolom naungan_2 belum ada di table surat_kop
            if (!$this->db->field_exists('naungan_2', 'surat_kop')) {
                $this->dbforge->add_column('surat_kop', [
                    'naungan_2' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'naungan'],
                    'font_size_naungan_2' => ['type' => 'INT', 'constraint' => 3, 'default' => 12, 'after' => 'font_size_naungan']
                ]);
            }
        }
    }
}
