<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = htmlspecialchars($_POST["ad"]);
    $soyad = htmlspecialchars($_POST["soyad"]);
    $posta = htmlspecialchars($_POST["posta"]);

    $veri = ["ad" => $ad, "soyad" => $soyad, "posta" => $posta];

    $dosya = __DIR__ . "/formdb.json";  // TAM DOSYA YOLU KULLANILDI

    if (file_exists($dosya)) {
        $mevcut_veriler = json_decode(file_get_contents($dosya), true);
        if (!is_array($mevcut_veriler)) {
            $mevcut_veriler = [];
        }
        $mevcut_veriler[] = $veri;
    } else {
        $mevcut_veriler = [$veri];
    }

    file_put_contents($dosya, json_encode($mevcut_veriler, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "Form başarıyla kaydedildi!";

    echo '<br><br><a href="index.html" style="
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: background 0.3s ease;">
    Ana Sayfaya Dön</a>';
}
?>