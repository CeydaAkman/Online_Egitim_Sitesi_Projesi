<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineegitimsitesi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$arama = isset($_GET['arama']) ? $conn->real_escape_string($_GET['arama']) : "";

if (!empty($arama)) {
    $sql = "SELECT * FROM Egitimler WHERE Baslik LIKE '%$arama%'";
} else {
    $sql = "SELECT * FROM Egitimler";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Eğitim Sitesi - Eğitimler</title>
    <link rel="stylesheet" href="EĞİTİMLER.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>

    <nav>
        <div class="dropdown">
            <a href="#">Kategoriler</a>
            <div class="dropdown-menu">
                <a href="KATEGORİ SAYFALARI/WEB_GELİŞTİRME.php">Web Geliştirme</a>
                <a href="KATEGORİ SAYFALARI/PROGRAMLAMA_DİLLERİ.php">Programlama Dilleri</a>
                <a href="KATEGORİ SAYFALARI/VERİ_BİLİMİ.php">Veri Bilimi</a>
                <a href="KATEGORİ SAYFALARI/MOBİL_UYGULAMA_GELİŞTİRME.php">Mobil Uygulama Geliştirme</a>
                <a href="KATEGORİ SAYFALARI/OYUN_GELİŞTİRME.php">Oyun Geliştirme</a>
                <a href="KATEGORİ SAYFALARI/YAPAY_ZEKA.php">Yapay Zeka</a>
                <a href="KATEGORİ SAYFALARI/VERİTABANI_YÖNETİMİ.php">Veritabanı Yönetimi</a>
                <a href="KATEGORİ SAYFALARI/SİBER_GÜVENLİK.php">Siber Güvenlik</a>
            </div>
        </div>
        <div class="search-container">
            <form action="EĞİTİMLER_EĞİTMEN.php" method="GET">
                <input type="text" name="arama" placeholder="Ara..." value="<?php echo htmlspecialchars($arama); ?>">
                <button type="submit">Ara</button>
            </form>
        </div>
    </nav>

    <main>
        <div class="course-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="course-card">';
                    echo '<img src="' . htmlspecialchars($row["GorselYolu"]) . '" alt="' . htmlspecialchars($row["Baslik"]) . '">';
                    echo '<h4>' . htmlspecialchars($row["Baslik"]) . '</h4>';
                    echo '<p>' . htmlspecialchars($row["Aciklama"]) . '</p>';
                    echo '<a href="KURS_DETAY_SAYFALARI/KURS_DETAY.php?EgitimID=' . $row["EgitimID"] . '">Detaylı İncele</a>';
                    echo '</div>';
                }
            } else {
                echo "<p>Henüz eğitim bulunmamaktadır.</p>";
            }

            $conn->close();
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Online Eğitim Sitesi. Tüm Hakları Saklıdır.</p>
    </footer>
</body>

</html>