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

if ($kullanici_tipi === 'student') {
    $sql = "SELECT * FROM Ogrenciler WHERE OgrenciID = ?";
} elseif ($kullanici_tipi === 'instructor') {
    $sql = "SELECT * FROM Egitmenler WHERE EgitmenID = ?";
} else {
    die("Geçersiz kullanıcı tipi.");
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $kullanici_id);
$stmt->execute();
$result = $stmt->get_result();
$kullanici = $result->fetch_assoc();

if (!$kullanici) {
    die("Kullanıcı bilgileri bulunamadı.");
    exit();
}
$kullanici_tipi_tr = $kullanici_tipi === 'student' ? 'Öğrenci' : 'Eğitmen';
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Online Eğitim Sitesi - Profil Sayfası</title>
    <link rel="stylesheet" href="PROFİL_SAYFASI.css">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>
    <main>
        <h2>Profil Sayfası</h2>
        <section>
            <img id="Resim" src="https://www.ozbrandatekstil.com/include/resize.php?path=images/urunler/Beyaz-650-Gr-Polyester-Branda-resim-245.jpg&width=700" alt="Profil Resmi">
        </section>
        <section> <br>
            <h3>Ad-Soyad: <span><?php echo htmlspecialchars($kullanici['Ad'] . ' ' . $kullanici['Soyad']); ?></span></h3>
            <h3>Doğum Tarihi: <span><?php echo htmlspecialchars($kullanici['DogumTarihi']); ?></span></h3>
            <h3>Cinsiyet: <span><?php echo htmlspecialchars($kullanici['Cinsiyet']); ?></span></h3>
            <h3>Kullanıcı Tipi: <span><?php echo ucfirst($kullanici_tipi_tr); ?></span></h3>
            <h3>Telefon Numarası: <span><?php echo htmlspecialchars($kullanici['Telefon']); ?></span></h3>
            <h3>E-Posta: <span><?php echo htmlspecialchars($kullanici['Eposta']); ?></span></h3>
        </section>
        <section class="buton">
            <a href="PROFİL_GÜNCELLEME_SAYFASI.php"><button class="enroll-button">Bilgileri Düzenle</button></a>
            <a href="OTURUM_KAPAT.php"><button class="enroll-button">Oturumu Kapat</button></a>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Online Eğitim Sitesi. Tüm Hakları Saklıdır.</p>
    </footer>
</body>

</html>

<?php
$conn->close();
?>