<?php
session_start();

if (!isset($_SESSION['kullaniciTipi']) || $_SESSION['kullaniciTipi'] !== 'student') {
    die("Sadece öğrenciler sepete ekelyebilir.");
}

if (!isset($_SESSION['kullaniciID'])) {
    die("Oturum bilgisi bulunamadı.");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineegitimsitesi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

if (isset($_POST['EgitimID'])) {
    $ogrenciID = $_SESSION['kullaniciID'];
    $egitimID = intval($_POST['EgitimID']);

    $kontrol = $conn->prepare("SELECT * FROM SepettekiEgitimler WHERE OgrenciID = ? AND EgitimID = ?");
    $kontrol->bind_param("ii", $ogrenciID, $egitimID);
    $kontrol->execute();
    $sonuc = $kontrol->get_result();

    if ($sonuc->num_rows > 0) {
        die("Bu eğitimi zaten sepete eklediniz.");
        exit;
    }

    $ekle = $conn->prepare("INSERT INTO SepettekiEgitimler (OgrenciID, EgitimID) VALUES (?, ?)");
    $ekle->bind_param("ii", $ogrenciID, $egitimID);
    if ($ekle->execute()) {
        die("Eğitim sepete eklendi.");
    } else {
        die("Sepete eklerken hata oluştu.");
    }
} else {
    die("Geçersiz istek.");
}
?>