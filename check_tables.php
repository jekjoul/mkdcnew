<?php
require_once 'index.php';
$ci = &get_instance();
$ci->load->database();
echo "<pre>";
echo "pembelajaran_mapel: ";
print_r($ci->db->list_fields('pembelajaran_mapel'));
echo "\nmaster_tingkat_sekolah: ";
print_r($ci->db->list_fields('master_tingkat_sekolah'));
echo "\nrombel: ";
print_r($ci->db->list_fields('rombel'));
echo "\npembelajaran: ";
print_r($ci->db->list_fields('pembelajaran'));
echo "</pre>";
