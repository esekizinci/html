<?php
$file = '/var/www/html/sensor_log.txt';

if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    echo end($lines);
} else {
    echo "Veri bulunamadi.";
}
?>