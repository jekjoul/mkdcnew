<?php
$file = 'C:/xampp/apache/logs/error.log';
$lines = file($file);
foreach ($lines as $line) {
    if (strpos($line, 'PHP') !== false) {
        echo $line . "<br>";
    }
}
