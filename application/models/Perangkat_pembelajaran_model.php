<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perangkat_pembelajaran_model extends MY_Model
{
    private $template_table = 'perangkat_template';
    private $perangkat_table = 'perangkat_pembelajaran';
    private $materi_table = 'perangkat_materi_harian';

    public function ensureTables()
    {
        $this->load->dbforge();

        if (!$this->db->table_exists($this->template_table)) {
            $this->dbforge->add_field([
                'id_template' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'jenjang' => ['type' => 'VARCHAR', 'constraint' => 20],
                'fase' => ['type' => 'VARCHAR', 'constraint' => 5],
                'mapel' => ['type' => 'VARCHAR', 'constraint' => 150],
                'cp' => ['type' => 'TEXT', 'null' => true],
                'atp' => ['type' => 'TEXT', 'null' => true],
                'modul_ajar' => ['type' => 'TEXT', 'null' => true],
                'materi_json' => ['type' => 'MEDIUMTEXT', 'null' => true],
                'sumber_url' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_template', true);
            $this->dbforge->create_table($this->template_table, true);
        }

        if (!$this->db->table_exists($this->perangkat_table)) {
            $this->dbforge->add_field([
                'id_perangkat' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_pembelajaran_mapel' => ['type' => 'INT', 'constraint' => 11],
                'id_template' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'cp' => ['type' => 'TEXT', 'null' => true],
                'atp' => ['type' => 'TEXT', 'null' => true],
                'modul_ajar' => ['type' => 'TEXT', 'null' => true],
                'hari_efektif' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'cadangan_hari' => ['type' => 'INT', 'constraint' => 11, 'default' => 28],
                'jumlah_pertemuan' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'sumber_url' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_perangkat', true);
            $this->dbforge->create_table($this->perangkat_table, true);
        }

        if (!$this->db->table_exists($this->materi_table)) {
            $this->dbforge->add_field([
                'id_materi_harian' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'id_perangkat' => ['type' => 'INT', 'constraint' => 11],
                'pertemuan_ke' => ['type' => 'INT', 'constraint' => 11],
                'tanggal' => ['type' => 'DATE', 'null' => true],
                'materi' => ['type' => 'TEXT'],
                'tujuan' => ['type' => 'TEXT', 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Belum'],
                'tanggal_diajarkan' => ['type' => 'DATE', 'null' => true],
                'catatan' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_materi_harian', true);
            $this->dbforge->create_table($this->materi_table, true);
        }

        $this->seedTemplates();
    }

    public function getPembelajaranMapel($id_pembelajaran_mapel)
    {
        $this->db->select('pm.*, p.id_tahun_pelajaran, l.nama_lembaga, t.nama_tingkat, t.tingkat_angka, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, m.mapel_singkat, ptk.nama_ptk');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->where('pm.id_pembelajaran_mapel', (int) $id_pembelajaran_mapel);
        return $this->db->get()->row();
    }

    public function getPerangkatByMapel($id_pembelajaran_mapel)
    {
        return $this->db->get_where($this->perangkat_table, ['id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel])->row();
    }

    public function getMateri($id_perangkat)
    {
        $this->db->order_by('pertemuan_ke', 'ASC');
        return $this->db->get_where($this->materi_table, ['id_perangkat' => (int) $id_perangkat])->result();
    }

    public function getTemplateFor($pembelajaran_mapel)
    {
        $jenjang = $this->detectJenjang($pembelajaran_mapel);
        $fase = $this->detectFase($pembelajaran_mapel);
        $mapel_key = $this->normalizeMapel($pembelajaran_mapel->nama_mapel);

        $templates = $this->db->get_where($this->template_table, ['jenjang' => $jenjang, 'fase' => $fase])->result();
        foreach ($templates as $template) {
            if ($this->normalizeMapel($template->mapel) === $mapel_key) {
                return $template;
            }
        }

        foreach ($templates as $template) {
            if (strpos($mapel_key, $this->normalizeMapel($template->mapel)) !== false || strpos($this->normalizeMapel($template->mapel), $mapel_key) !== false) {
                return $template;
            }
        }

        return $this->createFallbackTemplate($pembelajaran_mapel, $jenjang, $fase);
    }

    public function generate($id_pembelajaran_mapel, $cadangan_hari = 28)
    {
        $item = $this->getPembelajaranMapel($id_pembelajaran_mapel);
        if (!$item) {
            return false;
        }

        $existing = $this->getPerangkatByMapel($id_pembelajaran_mapel);
        if ($existing) {
            return $existing;
        }

        $template = $this->getTemplateFor($item);
        $dates = $this->getTanggalEfektif($item->id_tahun_pelajaran);
        $hari_efektif = count($dates);
        $cadangan_hari = max(0, (int) $cadangan_hari);
        $jumlah_pertemuan = max(1, $hari_efektif - $cadangan_hari);
        $materi = json_decode($template->materi_json ?: '[]', true);
        if (empty($materi)) {
            $materi = $this->defaultMateri($item->nama_mapel);
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert($this->perangkat_table, [
            'id_pembelajaran_mapel' => (int) $id_pembelajaran_mapel,
            'id_template' => $template->id_template,
            'cp' => $this->fillText($template->cp, $item),
            'atp' => $this->fillText($template->atp, $item),
            'modul_ajar' => $this->fillText($template->modul_ajar, $item),
            'hari_efektif' => $hari_efektif,
            'cadangan_hari' => $cadangan_hari,
            'jumlah_pertemuan' => $jumlah_pertemuan,
            'sumber_url' => $template->sumber_url,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $id_perangkat = $this->db->insert_id();

        for ($i = 1; $i <= $jumlah_pertemuan; $i++) {
            $topic = $materi[($i - 1) % count($materi)];
            $this->db->insert($this->materi_table, [
                'id_perangkat' => $id_perangkat,
                'pertemuan_ke' => $i,
                'tanggal' => isset($dates[$i - 1]) ? $dates[$i - 1] : null,
                'materi' => $topic['materi'],
                'tujuan' => $topic['tujuan'],
                'status' => 'Belum',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $this->db->get_where($this->perangkat_table, ['id_perangkat' => $id_perangkat])->row();
    }

    public function savePerangkat($id_perangkat, $data)
    {
        $allowed = ['cp', 'atp', 'modul_ajar'];
        $update = ['updated_at' => date('Y-m-d H:i:s')];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $update[$field] = $data[$field];
            }
        }

        $this->db->where('id_perangkat', (int) $id_perangkat);
        return $this->db->update($this->perangkat_table, $update);
    }

    public function saveMateri($rows)
    {
        foreach ((array) $rows as $id => $row) {
            $id = (int) $id;
            if (!$id) {
                continue;
            }

            $status = !empty($row['status']) ? 'Diajarkan' : 'Belum';
            $this->db->where('id_materi_harian', $id);
            $this->db->update($this->materi_table, [
                'materi' => isset($row['materi']) ? $row['materi'] : '',
                'tujuan' => isset($row['tujuan']) ? $row['tujuan'] : null,
                'status' => $status,
                'tanggal_diajarkan' => $status === 'Diajarkan' ? date('Y-m-d') : null,
                'catatan' => isset($row['catatan']) ? $row['catatan'] : null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function getAdminItems()
    {
        $this->db->select('pm.id_pembelajaran_mapel, pp.id_perangkat, pp.jumlah_pertemuan, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, ptk.nama_ptk, COUNT(mh.id_materi_harian) AS total_materi, SUM(CASE WHEN mh.status = "Diajarkan" THEN 1 ELSE 0 END) AS diajarkan');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join('ptk', 'ptk.id_ptk = pm.id_ptk', 'left');
        $this->db->join($this->perangkat_table . ' pp', 'pp.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        $this->db->join($this->materi_table . ' mh', 'mh.id_perangkat = pp.id_perangkat', 'left');
        $this->db->group_by('pm.id_pembelajaran_mapel');
        $this->db->order_by('tp.id_tahun_pelajaran', 'DESC');
        $this->db->order_by('m.nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    public function getGuruItems($id_ptk)
    {
        $this->db->select('pm.id_pembelajaran_mapel, pp.id_perangkat, pp.jumlah_pertemuan, l.nama_lembaga, t.nama_tingkat, r.nama_rombel, tp.tahun_pelajaran, tp.semester, m.nama_mapel, COUNT(mh.id_materi_harian) AS total_materi, SUM(CASE WHEN mh.status = "Diajarkan" THEN 1 ELSE 0 END) AS diajarkan');
        $this->db->from('pembelajaran_mapel pm');
        $this->db->join('pembelajaran p', 'p.id_pembelajaran = pm.id_pembelajaran');
        $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga');
        $this->db->join('master_tingkat_sekolah t', 't.id_tingkat_sekolah = p.id_tingkat_sekolah');
        $this->db->join('rombel r', 'r.id_rombel = p.id_rombel');
        $this->db->join('pembelajaran_tahun_pelajaran tp', 'tp.id_tahun_pelajaran = p.id_tahun_pelajaran');
        $this->db->join('mapel m', 'm.id_mapel = pm.id_mapel');
        $this->db->join($this->perangkat_table . ' pp', 'pp.id_pembelajaran_mapel = pm.id_pembelajaran_mapel', 'left');
        $this->db->join($this->materi_table . ' mh', 'mh.id_perangkat = pp.id_perangkat', 'left');
        $this->db->where('pm.id_ptk', (int) $id_ptk);
        $this->db->group_by('pm.id_pembelajaran_mapel');
        $this->db->order_by('m.nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    private function seedTemplates()
    {
        if ($this->db->count_all($this->template_table) > 0) {
            return;
        }

        $source = "https://guru.kemendikdasmen.go.id/kurikulum/referensi-penerapan/capaian-pembelajaran/\nhttps://kurikulum.kemendikdasmen.go.id/file/1718851677_manage_file.pdf";
        $subjects = [
            ['SMP', 'D', 'Pendidikan Agama Islam dan Budi Pekerti', ['Akidah dan akhlak', 'Al-Quran dan Hadis', 'Fikih ibadah', 'Sejarah peradaban Islam']],
            ['SMP', 'D', 'Pendidikan Pancasila', ['Pancasila', 'UUD NRI 1945', 'Bhinneka Tunggal Ika', 'NKRI dan gotong royong']],
            ['SMP', 'D', 'Bahasa Indonesia', ['Teks deskripsi', 'Teks narasi', 'Teks prosedur', 'Teks laporan hasil observasi', 'Literasi dan presentasi']],
            ['SMP', 'D', 'Matematika', ['Bilangan', 'Aljabar', 'Geometri', 'Pengukuran', 'Statistika dan peluang']],
            ['SMP', 'D', 'Ilmu Pengetahuan Alam', ['Makhluk hidup', 'Zat dan perubahannya', 'Energi', 'Bumi dan antariksa', 'Proyek sains']],
            ['SMP', 'D', 'Ilmu Pengetahuan Sosial', ['Interaksi sosial', 'Keruangan', 'Aktivitas ekonomi', 'Sejarah Indonesia', 'Proyek sosial']],
            ['SMP', 'D', 'Bahasa Inggris', ['Descriptive text', 'Procedure text', 'Recount text', 'Functional text', 'Speaking project']],
            ['SMP', 'D', 'PJOK', ['Kebugaran jasmani', 'Permainan bola besar', 'Permainan bola kecil', 'Atletik', 'Kesehatan']],
            ['SMP', 'D', 'Informatika', ['Berpikir komputasional', 'Sistem komputer', 'Jaringan internet', 'Analisis data', 'Algoritma dan pemrograman']],
            ['SMP', 'D', 'Seni Budaya', ['Seni rupa', 'Seni musik', 'Seni tari', 'Seni teater', 'Apresiasi karya']],
            ['SMP', 'D', 'Prakarya', ['Kerajinan', 'Rekayasa', 'Budidaya', 'Pengolahan', 'Kewirausahaan']],
            ['SMA', 'E', 'Pendidikan Agama Islam dan Budi Pekerti', ['Akidah', 'Akhlak', 'Fikih muamalah', 'Sejarah Islam', 'Proyek pengamalan']],
            ['SMA', 'E', 'Pendidikan Pancasila', ['Pancasila', 'Konstitusi', 'Hak dan kewajiban warga negara', 'Demokrasi', 'Proyek kewargaan']],
            ['SMA', 'E', 'Bahasa Indonesia', ['Teks laporan', 'Teks eksposisi', 'Teks anekdot', 'Hikayat', 'Karya ilmiah sederhana']],
            ['SMA', 'E', 'Matematika', ['Eksponen dan logaritma', 'Barisan dan deret', 'Fungsi', 'Trigonometri', 'Statistika']],
            ['SMA', 'E', 'Bahasa Inggris', ['Descriptive and recount text', 'Narrative text', 'Analytical exposition', 'Public speaking', 'Writing project']],
            ['SMA', 'E', 'Sejarah', ['Konsep sejarah', 'Peradaban awal', 'Kerajaan Nusantara', 'Kolonialisme', 'Sejarah lokal']],
            ['SMA', 'E', 'Fisika', ['Pengukuran', 'Gerak', 'Dinamika', 'Energi', 'Gelombang']],
            ['SMA', 'E', 'Kimia', ['Hakikat ilmu kimia', 'Struktur atom', 'Ikatan kimia', 'Stoikiometri', 'Larutan']],
            ['SMA', 'E', 'Biologi', ['Keanekaragaman hayati', 'Virus dan bakteri', 'Ekosistem', 'Perubahan lingkungan', 'Bioteknologi sederhana']],
            ['SMA', 'E', 'Ekonomi', ['Kebutuhan dan kelangkaan', 'Permintaan penawaran', 'Lembaga keuangan', 'Akuntansi dasar', 'Kewirausahaan']],
            ['SMA', 'E', 'Sosiologi', ['Individu dan masyarakat', 'Interaksi sosial', 'Nilai dan norma', 'Ragam gejala sosial', 'Penelitian sosial']],
            ['SMA', 'E', 'Geografi', ['Peta dan penginderaan jauh', 'Litosfer', 'Atmosfer', 'Hidrosfer', 'Mitigasi bencana']],
            ['SMA', 'E', 'Informatika', ['Berpikir komputasional', 'Teknologi informasi', 'Jaringan', 'Analisis data', 'Pemrograman']],
            ['SMA', 'E', 'PJOK', ['Kebugaran', 'Permainan', 'Atletik', 'Aktivitas ritmik', 'Kesehatan remaja']],
            ['SMA', 'E', 'Seni Budaya', ['Apresiasi seni', 'Kreasi seni rupa', 'Musik', 'Tari', 'Pameran/pertunjukan']],
            ['SMA', 'F', 'Bahasa Indonesia', ['Teks argumentasi', 'Kritik dan esai', 'Novel', 'Karya ilmiah', 'Presentasi akademik']],
            ['SMA', 'F', 'Matematika', ['Fungsi lanjutan', 'Limit', 'Turunan', 'Integral', 'Peluang lanjutan']],
            ['SMA', 'F', 'Bahasa Inggris', ['Discussion text', 'News item', 'Report text', 'Academic presentation', 'Writing portfolio']],
            ['SMA', 'F', 'Fisika', ['Fluida', 'Termodinamika', 'Listrik magnet', 'Optik', 'Fisika modern']],
            ['SMA', 'F', 'Kimia', ['Termokimia', 'Kesetimbangan', 'Asam basa', 'Elektrokimia', 'Kimia karbon']],
            ['SMA', 'F', 'Biologi', ['Sel', 'Genetika', 'Evolusi', 'Sistem organ', 'Bioteknologi']],
            ['SMA', 'F', 'Ekonomi', ['Pendapatan nasional', 'Inflasi', 'Kebijakan fiskal dan moneter', 'Perdagangan internasional', 'Akuntansi']],
            ['SMA', 'F', 'Sosiologi', ['Konflik sosial', 'Mobilitas sosial', 'Perubahan sosial', 'Globalisasi', 'Riset sosial']],
            ['SMA', 'F', 'Geografi', ['Kependudukan', 'Sumber daya alam', 'Wilayah dan tata ruang', 'Geografi regional', 'SIG']],
            ['SMA', 'F', 'Sejarah', ['Pergerakan nasional', 'Kemerdekaan', 'Orde lama dan baru', 'Reformasi', 'Sejarah dunia kontemporer']],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($subjects as $subject) {
            $this->db->insert($this->template_table, [
                'jenjang' => $subject[0],
                'fase' => $subject[1],
                'mapel' => $subject[2],
                'cp' => $this->templateCp($subject[2], $subject[1]),
                'atp' => $this->templateAtp($subject[2], $subject[3]),
                'modul_ajar' => $this->templateModul($subject[2]),
                'materi_json' => json_encode($this->topicsToMateri($subject[3])),
                'sumber_url' => $source,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function getTanggalEfektif($id_tahun_pelajaran)
    {
        if (!$this->db->table_exists('pembelajaran_hari_efektif')) {
            return [];
        }

        $this->db->select('tanggal');
        $this->db->where('id_tahun_pelajaran', (int) $id_tahun_pelajaran);
        $this->db->where_in('status', ['Efektif', 'Daring', 'Luar Kelas']);
        $this->db->order_by('tanggal', 'ASC');
        $rows = $this->db->get('pembelajaran_hari_efektif')->result();

        return array_map(function ($row) {
            return $row->tanggal;
        }, $rows);
    }

    private function detectJenjang($item)
    {
        return (int) $item->tingkat_angka >= 10 ? 'SMA' : 'SMP';
    }

    private function detectFase($item)
    {
        $tingkat = (int) $item->tingkat_angka;
        if ($tingkat >= 11) {
            return 'F';
        }
        if ($tingkat >= 10) {
            return 'E';
        }
        return 'D';
    }

    private function normalizeMapel($name)
    {
        $name = strtolower((string) $name);
        $name = str_replace(['&', '.', ',', '-'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    private function createFallbackTemplate($item, $jenjang, $fase)
    {
        $materi = $this->defaultMateri($item->nama_mapel);
        $now = date('Y-m-d H:i:s');
        $this->db->insert($this->template_table, [
            'jenjang' => $jenjang,
            'fase' => $fase,
            'mapel' => $item->nama_mapel,
            'cp' => $this->templateCp($item->nama_mapel, $fase),
            'atp' => $this->templateAtp($item->nama_mapel, array_column($materi, 'materi')),
            'modul_ajar' => $this->templateModul($item->nama_mapel),
            'materi_json' => json_encode($materi),
            'sumber_url' => 'https://guru.kemendikdasmen.go.id/kurikulum/referensi-penerapan/capaian-pembelajaran/',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->db->get_where($this->template_table, ['id_template' => $this->db->insert_id()])->row();
    }

    private function defaultMateri($mapel)
    {
        return $this->topicsToMateri(['Orientasi ' . $mapel, 'Konsep dasar', 'Latihan terpandu', 'Proyek/produk belajar', 'Refleksi dan asesmen']);
    }

    private function topicsToMateri($topics)
    {
        $rows = [];
        foreach ($topics as $topic) {
            $rows[] = [
                'materi' => $topic,
                'tujuan' => 'Peserta didik memahami, menerapkan, dan merefleksikan materi ' . $topic . ' sesuai fase pembelajaran.',
            ];
        }
        return $rows;
    }

    private function fillText($text, $item)
    {
        $replace = [
            '{{mapel}}' => $item->nama_mapel,
            '{{kelas}}' => trim($item->nama_tingkat . ' ' . $item->nama_rombel),
            '{{semester}}' => $item->semester,
            '{{tahun_pelajaran}}' => $item->tahun_pelajaran,
            '{{fase}}' => $this->detectFase($item),
        ];

        return strtr((string) $text, $replace);
    }

    private function templateCp($mapel, $fase)
    {
        return "Capaian Pembelajaran {{mapel}} Fase {{fase}}\n\nPeserta didik menunjukkan pemahaman konseptual, keterampilan proses, dan sikap belajar yang relevan dengan {{mapel}}. Guru perlu menyesuaikan narasi CP ini dengan dokumen resmi CP yang berlaku, karakteristik satuan pendidikan, dan kebutuhan kelas {{kelas}} semester {{semester}} tahun pelajaran {{tahun_pelajaran}}.";
    }

    private function templateAtp($mapel, $topics)
    {
        $lines = ["Alur Tujuan Pembelajaran {{mapel}}", ""];
        foreach ($topics as $i => $topic) {
            $lines[] = ($i + 1) . '. Peserta didik mempelajari ' . $topic . ' melalui eksplorasi konsep, latihan, diskusi, dan asesmen formatif.';
        }
        return implode("\n", $lines);
    }

    private function templateModul($mapel)
    {
        return "Modul Ajar {{mapel}}\n\nIdentitas: {{kelas}}, semester {{semester}}, tahun pelajaran {{tahun_pelajaran}}.\n\nTujuan: peserta didik mencapai tujuan pembelajaran harian sesuai ATP.\n\nKegiatan Pembelajaran:\n1. Pendahuluan: apersepsi, tujuan, dan kesepakatan belajar.\n2. Inti: eksplorasi materi, diskusi, latihan/proyek, dan umpan balik.\n3. Penutup: refleksi, rangkuman, dan tindak lanjut.\n\nAsesmen: diagnostik singkat, formatif selama proses, dan sumatif sesuai akhir lingkup materi.\n\nDiferensiasi: penyesuaian konten, proses, produk, atau dukungan belajar berdasarkan kesiapan peserta didik.";
    }
}
