<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = htmlspecialchars($_POST["ad"]);
    #$soyad = htmlspecialchars($_POST["soyad"]);
    $posta = htmlspecialchars($_POST["posta"]);
    #$kardes = htmlspecialchars($_POST["kardes"]);
    #$cinsiyet = htmlspecialchars($_POST["cinsiyet"]);
    #$sigorta = htmlspecialchars($_POST["sigorta"]);
    #$ok = htmlspecialchars($_POST["ok"]);

    $veri = ["ad" => $ad, "posta" => $posta];

    $dosya = "formdb.json";

    if (file_exists($dosya)) {
        $mevcut_veriler = json_decode(file_get_contents($dosya), true);
        $mevcut_veriler[] = $veri;
    } else {
        $mevcut_veriler = [$veri];
    }

    file_put_contents($dosya, json_encode($mevcut_veriler, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "Form basariyla kaydedildi!";
    phpinfo();
}
?>