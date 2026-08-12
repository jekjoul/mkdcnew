<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fingerprint_bridge_model extends MY_Model
{
    private $table_machines = 'fingerprint_machines';
    private $table_settings = 'fingerprint_settings';

    public function ensureTables()
    {
        $this->load->dbforge();

        // 1. Table fingerprint_machines
        if (!$this->db->table_exists($this->table_machines)) {
            $this->dbforge->add_field([
                'id_machine'    => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'nama_mesin'    => ['type' => 'VARCHAR', 'constraint' => 100],
                'serial_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'ip_address'    => ['type' => 'VARCHAR', 'constraint' => 45],
                'port'          => ['type' => 'INT', 'constraint' => 11, 'default' => 4370],
                'comm_key'      => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => '0'],
                'tipe_mesin'    => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'ZK_TCP'],
                'lokasi'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Unknown'],
                'last_sync'     => ['type' => 'DATETIME', 'null' => true],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id_machine', true);
            $this->dbforge->create_table($this->table_machines, true);

            // Insert sample initial machine if empty
            $this->db->insert($this->table_machines, [
                'nama_mesin'    => 'Mesin Utama Gerbang',
                'serial_number' => 'FS-101010101',
                'ip_address'    => '192.168.1.201',
                'port'          => 4370,
                'comm_key'      => '0',
                'tipe_mesin'    => 'ZK_TCP',
                'lokasi'        => 'Gerbang Depan',
                'status'        => 'Unknown',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        } else {
            if (!$this->db->field_exists('serial_number', $this->table_machines)) {
                $this->dbforge->add_column($this->table_machines, [
                    'serial_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true]
                ]);
            }
            if (!$this->db->field_exists('kode_aktivasi', $this->table_machines)) {
                $this->dbforge->add_column($this->table_machines, [
                    'kode_aktivasi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true]
                ]);
            }
            if (!$this->db->field_exists('total_user', $this->table_machines)) {
                $this->dbforge->add_column($this->table_machines, [
                    'total_user' => ['type' => 'INT', 'constraint' => 11, 'default' => 0]
                ]);
            }
            if (!$this->db->field_exists('total_fp', $this->table_machines)) {
                $this->dbforge->add_column($this->table_machines, [
                    'total_fp' => ['type' => 'INT', 'constraint' => 11, 'default' => 0]
                ]);
            }
            if (!$this->db->field_exists('total_history_presensi', $this->table_machines)) {
                $this->dbforge->add_column($this->table_machines, [
                    'total_history_presensi' => ['type' => 'INT', 'constraint' => 11, 'default' => 0]
                ]);
            }
        }

        // 2. Table fingerprint_settings
        if (!$this->db->table_exists($this->table_settings)) {
            $this->dbforge->add_field([
                'id'                 => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'env_mode'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'development'],
                'dev_endpoint_url'   => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'http://localhost/mkdcnew/api/presensi/sync'],
                'prod_endpoint_url'  => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'https://domain-sekolah.sch.id/api/presensi/sync'],
                'api_token'          => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'MKDC_FINGERPRINT_SECRET_KEY_2026'],
                'auto_sync_interval' => ['type' => 'INT', 'constraint' => 11, 'default' => 10],
                'auto_sync_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'updated_at'         => ['type' => 'DATETIME', 'null' => true]
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table($this->table_settings, true);

            // Insert default row
            $this->db->insert($this->table_settings, [
                'env_mode'           => 'development',
                'dev_endpoint_url'   => url('api/presensi/sync'),
                'prod_endpoint_url'  => 'https://domain-sekolah.sch.id/api/presensi/sync',
                'api_token'          => 'MKDC_FINGERPRINT_SECRET_KEY_2026',
                'auto_sync_interval' => 10,
                'auto_sync_active'   => 1,
                'updated_at'         => date('Y-m-d H:i:s')
            ]);
        }

        // 3. Table presensi_machine_users
        if (!$this->db->table_exists('presensi_machine_users')) {
            $this->dbforge->add_field([
                'id'              => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'pin'             => ['type' => 'VARCHAR', 'constraint' => 50],
                'nama'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'password'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'rfid'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'privilege'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'jumlah_template' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('pin');
            $this->dbforge->create_table('presensi_machine_users', true);
        }

        // 4. Table presensi_machine_templates
        if (!$this->db->table_exists('presensi_machine_templates')) {
            $this->dbforge->add_field([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'pin'        => ['type' => 'VARCHAR', 'constraint' => 50],
                'finger_idx' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'alg_ver'    => ['type' => 'INT', 'constraint' => 11, 'default' => 10],
                'template'   => ['type' => 'LONGTEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('pin');
            $this->dbforge->create_table('presensi_machine_templates', true);
        }
    }

    public function getSettings()
    {
        $settings = $this->db->get($this->table_settings)->row();
        if (!$settings) {
            $this->ensureTables();
            $settings = $this->db->get($this->table_settings)->row();
        }
        return $settings;
    }

    public function saveSettings($data)
    {
        $settings = $this->getSettings();
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($settings) {
            $this->db->where('id', $settings->id);
            return $this->db->update($this->table_settings, $data);
        } else {
            return $this->db->insert($this->table_settings, $data);
        }
    }

    public function getActiveEndpointUrl()
    {
        $s = $this->getSettings();
        if (!$s) return url('api/presensi/sync');

        return ($s->env_mode === 'production') ? $s->prod_endpoint_url : $s->dev_endpoint_url;
    }

    public function getAllMachines()
    {
        $this->db->order_by('id_machine', 'ASC');
        return $this->db->get($this->table_machines)->result();
    }

    public function getMachine($id)
    {
        return $this->db->get_where($this->table_machines, ['id_machine' => (int)$id])->row();
    }

    public function saveMachine($data, $id = null)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($id) {
            $this->db->where('id_machine', (int)$id);
            return $this->db->update($this->table_machines, $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert($this->table_machines, $data);
        }
    }

    public function deleteMachine($id)
    {
        return $this->db->delete($this->table_machines, ['id_machine' => (int)$id]);
    }

    public function updateMachineStatus($id, $status)
    {
        $this->db->where('id_machine', (int)$id);
        return $this->db->update($this->table_machines, [
            'status'    => $status,
            'last_sync' => date('Y-m-d H:i:s')
        ]);
    }
}
