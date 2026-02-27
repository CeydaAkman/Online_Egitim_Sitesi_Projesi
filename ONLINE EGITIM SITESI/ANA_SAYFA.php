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
    $sql = "SELECT * FROM Egitimler WHERE Baslik LIKE '%$arama%' AND Fiyat > 460.00";
} else {
    $sql = "SELECT * FROM Egitimler WHERE Fiyat > 460.00";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Eğitim Sitesi - Ana Sayfa</title>
    <link rel="stylesheet" href="ANA_SAYFA.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>

    <nav>
        <div class="left-links">
            <a href="ANA_SAYFA.php">Ana Sayfa</a>
            <a href="EĞİTİMLER_EĞİTMEN.php">Eğitimler</a>
        </div>

        <div class="search-container">
            <form action="ANA_SAYFA.php" method="GET">
                <input type="text" name="arama" placeholder="Ara..." value="<?php echo htmlspecialchars($arama); ?>">
                <button type="submit">Ara</button>
            </form>
        </div>

        <div class="right-links">
            <a href="OTURUM_AÇ.html">Oturum Aç</a>
            <a href="KAYIT_OL.html">Kayıt Ol</a>
        </div>
    </nav>

    <section>
        <h2>Hakkımızda</h2>
        <p>
            Online Eğitim Platformu, size en iyi öğrenme deneyimini sunmak için tasarlanmış bir sistemdir.
            Alanında uzman eğitmenler tarafından hazırlanan içeriklerle öğrenme yolculuğunuzda başarıya ulaşın.
        </p>
    </section>

    <main>
        <h2>Öne Çıkan Eğitimler</h2>
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
                echo "<p>Aradığınız kriterlere uygun eğitim bulunamadı.</p>";
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