<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Update_log extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->page_data['page']->title = 'Update Log';
        $this->page_data['page']->titleUrl = 'update_log';
        $this->page_data['page']->subtitle = 'Log Pembaruan Aplikasi';
        $this->page_data['page']->subtitleUrl = 'update_log';
        $this->page_data['page']->icon = 'solar:history-linear';

        $logs = [];
        $cache_file = APPPATH . 'config/git_log_cache.json';

        // 1. Ambil log commit murni dari Git (hanya commit resmi, max 50 commit)
        $git_output = [];
        $return_var = -1;
        @exec('git log --pretty=format:"%h|%ad|%an|%s" --date=short -n 50 2>&1', $git_output, $return_var);

        if ($return_var === 0 && !empty($git_output)) {
            foreach ($git_output as $line) {
                $parts = explode('|', $line, 4);
                if (count($parts) === 4) {
                    $logs[] = [
                        'step'    => $parts[0],
                        'date'    => $parts[1],
                        'author'  => $parts[2],
                        'message' => $parts[3],
                    ];
                }
            }

            // Simpan cache log commit ke file JSON agar server tanpa .git dapat membacanya
            if (!empty($logs)) {
                @file_put_contents($cache_file, json_encode($logs, JSON_PRETTY_PRINT));
            }
        } 
        // 2. Fallback: Baca dari file cache JSON jika perintah Git tidak tersedia/gagal
        elseif (file_exists($cache_file)) {
            $cache_data = @file_get_contents($cache_file);
            if ($cache_data) {
                $logs = json_decode($cache_data, true) ?: [];
            }
        }

        $this->page_data['logs'] = $logs;
        $this->load->view('update_log/index', $this->page_data);
    }
}
