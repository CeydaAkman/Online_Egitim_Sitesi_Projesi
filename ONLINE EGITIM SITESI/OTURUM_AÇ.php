<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineegitimsitesi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$kullaniciTipi = $_POST['user-type'];
$eposta = $_POST['email'];
$gelenSifre = $_POST['password'];

if ($kullaniciTipi === 'student') {
    $sql = "SELECT OgrenciID AS ID, Ad, Soyad, Sifre, Salt FROM Ogrenciler WHERE Eposta = ?";
} elseif ($kullaniciTipi === 'instructor') {
    $sql = "SELECT EgitmenID AS ID, Ad, Soyad, Sifre, Salt FROM Egitmenler WHERE Eposta = ?";
} else {
    die("Geçersiz kullanıcı tipi.");
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $eposta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $dbSifre = $row['Sifre'];
    $dbSalt = $row['Salt'];

    $gelenSifreHash = hash('sha512', $dbSalt . $gelenSifre);

    if ($gelenSifreHash === $dbSifre) {
        $_SESSION['kullaniciID'] = $row['ID'];
        $_SESSION['adSoyad'] = $row['Ad'] . ' ' . $row['Soyad'];
        $_SESSION['kullaniciTipi'] = $kullaniciTipi;

        if ($kullaniciTipi === 'student') {
            header("Location: GİRİŞ_YAPILMIŞ_ÖĞRENCİ.php");
        } else {
            $_SESSION['EgitmenID'] = $row['ID'];
            header("Location: GİRİŞ_YAPILMIŞ_EĞİTMEN.php");
        }
        exit();
    } else {
        echo "<script>alert('Şifre yanlış!'); window.location.href='OTURUM_AÇ.html';</script>";
    }
} else {
    echo "<script>alert('Kullanıcı bulunamadı!'); window.location.href='OTURUM_AÇ.html';</script>";
}

$stmt->close();
$conn->close();
?>
