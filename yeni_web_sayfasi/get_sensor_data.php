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

// TCMB VERILERI (Doviz)
$tcmb_url = 'https://www.tcmb.gov.tr/kurlar/today.xml';
$tcmb_xml = @simplexml_load_file($tcmb_url);
if ($tcmb_xml !== false) {
    $usd = $tcmb_xml->Currency[0]->BanknoteSelling;
    $eur = $tcmb_xml->Currency[3]->BanknoteSelling;

    $data["usd_try"] = number_format((float)$usd, 4, '.', '');
    $data["eur_try"] = number_format((float)$eur, 4, '.', '');
} else {
    $data["usd_try"] = "Veri cekilemedi.";
    $data["eur_try"] = "Veri cekilemedi.";
}

// LOTO VERISI
$loto_url = "https://www.nosyapi.com/apiv2/service/lotto/getResult?type=1&apiKey=vdpCOL0rGrOYFYZJmGkWjtMbHnTlxcJ5hlzrxtSwNQHgPoFjcKO0IzdfB415";

$loto_response = @file_get_contents($loto_url);
$loto_json = json_decode($loto_response, true);

if (
    isset($loto_json['data']) &&
    isset($loto_json['data']['numbers']) &&
    isset($loto_json['data']['prizes'])
) {
    $numbers = $loto_json['data']['numbers'];
    $prizes = $loto_json['data']['prizes'];

    $data["loto_numbers"] = array_values($numbers);
    $data["loto_6_bilen"] = $prizes[0]['winner'];
    $data["loto_6_odul"] = $prizes[0]['prizeTL'];
    $data["loto_5_bilen"] = $prizes[2]['winner'];
    $data["loto_5_odul"] = $prizes[2]['prizeTL'];
} else {
    $data["loto_error"] = "Loto verisi cekilemedi.";
}


// JSON olarak döndür
header('Content-Type: application/json');
echo json_encode($data);
?>
