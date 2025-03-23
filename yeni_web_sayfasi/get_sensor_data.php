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

// Loglamak istersen
// file_put_contents("/tmp/loto_log.json", $loto_response);

if (
    isset($loto_json['data']['numbers']) &&
    isset($loto_json['data']['prizes']) &&
    is_array($loto_json['data']['numbers']) &&
    is_array($loto_json['data']['prizes'])
) {
    // Sayiları sırayla al
    $numbers_raw = $loto_json['data']['numbers'];
    $numbers = [
        $numbers_raw['number1'],
        $numbers_raw['number2'],
        $numbers_raw['number3'],
        $numbers_raw['number4'],
        $numbers_raw['number5'],
        $numbers_raw['number6']
    ];

    // Ödül bilgileri
    $prizes = $loto_json['data']['prizes'];

    $data["loto_numbers"] = $numbers;
    $data["loto_6_bilen"] = $prizes[0]['winner'] ?? "0";
    $data["loto_6_odul"] = $prizes[0]['prizeTL'] ?? "0,00 TL";
    $data["loto_5_bilen"] = $prizes[2]['winner'] ?? "0";
    $data["loto_5_odul"] = $prizes[2]['prizeTL'] ?? "0,00 TL";
} else {
    $data["loto_error"] = "Loto verisi format hatali veya eksik.";
}

// JSON olarak döndür
header('Content-Type: application/json');
echo json_encode($data);
?>
