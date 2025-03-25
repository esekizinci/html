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

// FAN VERISI
$fan_raw = shell_exec("sensors | grep -i 'fan' | head -n 1");
if ($fan_raw && preg_match('/\s+(\d+)\s+RPM/i', $fan_raw, $matches)) {
    $data["fan_rpm"] = $matches[1] . " RPM";
} else {
    $data["fan_rpm"] = "Fan bilgisi yok";
}


/*
// TCMB VERILERI (Doviz) – ARTIK KULLANILMIYOR
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
*/

// DOVIZ.COM VERILERI (Canli)
$html = @file_get_contents('https://www.doviz.com/');
if ($html !== false) {
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $xpath = new DOMXPath($doc);

    $dolar = $xpath->query('//span[@data-socket-key="USD"]/text()')->item(0);
    $euro = $xpath->query('//span[@data-socket-key="EUR"]/text()')->item(0);
    $altin = $xpath->query('//span[@data-socket-key="gram-altin"]/text()')->item(0);

    $data['usd_try'] = $dolar ? trim($dolar->nodeValue) : 'Veri yok';
    $data['eur_try'] = $euro ? trim($euro->nodeValue) : 'Veri yok';
    $data['altin_try'] = $altin ? trim($altin->nodeValue) : 'Veri yok';
} else {
    $data['usd_try'] = 'Baglanti hatasi';
    $data['eur_try'] = 'Baglanti hatasi';
    $data['altin_try'] = 'Baglanti hatasi';
}

// JSON olarak döndür
header('Content-Type: application/json');
echo json_encode($data);
?>
