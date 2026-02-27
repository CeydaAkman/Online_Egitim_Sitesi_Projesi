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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Eğitim Sitesi - Profil Güncelleme</title>
    <link rel="stylesheet" href="PROFİL_GÜNCELLEME_SAYFASI.css">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>

    <main>
        <h2>Profil Güncelleme</h2>
        <form action="PROFİL_BİLGİLERİNİ_GÜNCELLE.php" method="POST">
            <label for="name">Ad-Soyad:</label>
            <input type="text" id="name" name="name" placeholder="Adınızı ve soyadınızı giriniz" value="<?php echo htmlspecialchars($kullanici['Ad'] . ' ' . $kullanici['Soyad']); ?>"
                required>

            <label for="birthDate">Doğum Tarihi:</label>
            <input type="date" id="birthDate" name="birthDate" value="<?php echo htmlspecialchars($kullanici['DogumTarihi']); ?>" required>

            <label for="gender">Cinsiyet:</label>
            <select id="gender" name="gender" required>
                <option value="Kadın" <?php if ($kullanici['Cinsiyet'] === 'Kadın') echo 'selected'; ?>>Kadın</option>
                <option value="Erkek" <?php if ($kullanici['Cinsiyet'] === 'Erkek') echo 'selected'; ?>>Erkek</option>
                <option value="Diğer" <?php if ($kullanici['Cinsiyet'] === 'Diğer') echo 'selected'; ?>>Diğer</option>
            </select>

            <label for="userType">Kullanıcı Tipi:</label>
            <select id="userType" name="userType" disabled>
                <option value="Öğrenci" <?php if ($kullanici_tipi_tr === 'Öğrenci') echo 'selected'; ?>>Öğrenci</option>
                <option value="Eğitmen" <?php if ($kullanici_tipi_tr === 'Eğitmen') echo 'selected'; ?>>Eğitmen</option>
            </select>

            <label for="phone">Telefon Numarası:</label>
            <input type="text" id="phone" name="phone" placeholder="Telefon numaranızı giriniz" value="<?php echo htmlspecialchars($kullanici['Telefon']); ?>"
                required>

            <label for="email">E-Posta:</label>
            <input type="email" id="email" name="email" placeholder="E-posta adresinizi giriniz"
                value="<?php echo htmlspecialchars($kullanici['Eposta']); ?>" required>

            <button type="submit">Bilgileri Güncelle</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Online Eğitim Sitesi. Tüm Hakları Saklıdır.</p>
    </footer>

</body>

</html>