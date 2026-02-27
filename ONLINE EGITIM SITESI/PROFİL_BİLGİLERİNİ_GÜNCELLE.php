<?php
session_start();

if (!isset($_SESSION['kullaniciID']) || !isset($_SESSION['kullaniciTipi'])) {
    header("Location: OTURUM_AÇ.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineegitimsitesi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$kullanici_id = $_SESSION['kullaniciID'];
$kullanici_tipi = $_SESSION['kullaniciTipi'];

$ad_soyad = trim($_POST['name']);
$dogum_tarihi = $_POST['birthDate'];
$cinsiyet = $_POST['gender'];
$telefon = trim($_POST['phone']);
$eposta = trim($_POST['email']);

$ad_soyad_parcala = explode(" ", $ad_soyad, 2);
$ad = $ad_soyad_parcala[0];
$soyad = isset($ad_soyad_parcala[1]) ? $ad_soyad_parcala[1] : "";

if ($kullanici_tipi === 'student') {
    $sql = "UPDATE Ogrenciler SET Ad = ?, Soyad = ?, DogumTarihi = ?, Cinsiyet = ?, Telefon = ?, Eposta = ? WHERE OgrenciID = ?";
} elseif ($kullanici_tipi === 'instructor') {
    $sql = "UPDATE Egitmenler SET Ad = ?, Soyad = ?, DogumTarihi = ?, Cinsiyet = ?, Telefon = ?, Eposta = ? WHERE EgitmenID = ?";
} else {
    die("Geçersiz kullanıcı tipi.");
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", $ad, $soyad, $dogum_tarihi, $cinsiyet, $telefon, $eposta, $kullanici_id);

if ($stmt->execute()) {
    echo "<script>
    alert('Profil başarıyla güncellendi.');window.location.href = 'PROFİL_SAYFASI.php';</script>";
} else {
    echo "Güncelleme hatası: " . $stmt->error;
}

$stmt->close();
$conn->close();
