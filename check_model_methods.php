<?php
header('Content-Type: text/plain');
define('BASEPATH', true);

require_once 'application/core/MY_Model.php';
require_once 'application/models/Perangkat_pembelajaran_model.php';

$class = new ReflectionClass('Perangkat_pembelajaran_model');
$methods = $class->getMethods();

echo "=== METHODS IN Perangkat_pembelajaran_model ===\n";
foreach ($methods as $m) {
    if ($m->class === 'Perangkat_pembelajaran_model') {
        echo $m->name . "\n";
    }
}
