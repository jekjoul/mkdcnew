<?php
require_once 'index.php';
$ci = &get_instance();
$ci->load->database();
$fields = $ci->db->list_fields('agenda_pembelajaran');
echo "<pre>";
print_r($fields);
echo "</pre>";
