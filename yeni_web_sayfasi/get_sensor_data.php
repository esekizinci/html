<?php
$data = [];

// SENSOR VERISI
$sensor_file = '/var/www/html/sensor_log.txt';
if (file_exists($sensor_file)) {
    $lines = file($sensor_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data["sensor"] = end($lines);
} else {
    $data["sensor"] = "Sensor verisi bulunamadi.";
}

// CPU SICAKLIK
$cpu_temp_raw = @file_get_contents("/sys/class/thermal/thermal_zone0/temp");
if ($cpu_temp_raw !== false) {
    $cpu_temp_c = round($cpu_temp_raw / 1000, 1);
    $data["cpu_temp"] = $cpu_temp_c . "°C";
} else {
    $data["cpu_temp"] = "Sicaklik bilgisi yok";
}

// CPU KULLANIM
$load = sys_getloadavg();
$cores = (int) shell_exec("nproc");
$cpu_usage = round(($load[0] / $cores) * 100, 1);
$data["cpu_usage"] = $cpu_usage . "%";

// TCMB VERILERI
$tcmb_url = 'https://www.tcmb.gov.tr/kurlar/today.xml';
$tcmb_xml = @simplexml_load_file($tcmb_url);
if ($tcmb_xml !== false) {
    // Dolar/TL
    $usd = $tcmb_xml->Currency[0]->BanknoteSelling;
    $data["usd_try"] = number_format((float)$usd, 4, '.', '');

    // Euro/TL
    $eur = $tcmb_xml->Currency[3]->BanknoteSelling;
    $data["eur_try"] = number_format((float)$eur, 4, '.', '');

    // Gram Altın/TL
    $gram_altin = $tcmb_xml->Currency[11]->BanknoteSelling;
    $data["gram_altin_try"] = number_format((float)$gram_altin, 4, '.', '');
} else {
    $data["usd_try"] = "Veri cekilemedi.";
    $data["eur_try"] = "Veri cekilemedi.";
    $data["gram_altin_try"] = "Veri cekilemedi.";
}

// JSON olarak döndür
header('Content-Type: application/json');
echo json_encode($data);
?>
