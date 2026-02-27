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

if (!empty($arama)) {
    $sql = "SELECT * FROM Egitimler WHERE Baslik LIKE '%$arama%'";
} else {
    $sql = "SELECT * FROM Egitimler";
}

$sql = "SELECT * FROM Egitimler WHERE KategoriAdi = 'Veri Bilimi'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Eğitim Sitesi - Veri Bilimi Kategorisi</title>
    <link rel="stylesheet" href="KATEGORİ_SAYFALARI.css">
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
                <a href="WEB_GELİŞTİRME.php">Web Geliştirme</a>
                <a href="PROGRAMLAMA_DİLLERİ.php">Programlama Dilleri</a>
                <a href="VERİ_BİLİMİ.php">Veri Bilimi</a>
                <a href="MOBİL_UYGULAMA_GELİŞTİRME.php">Mobil Uygulama Geliştirme</a>
                <a href="OYUN_GELİŞTİRME.php">Oyun Geliştirme</a>
                <a href="YAPAY_ZEKA.php">Yapay Zeka</a>
                <a href="VERİTABANI_YÖNETİMİ.php">Veritabanı Yönetimi</a>
                <a href="SİBER_GÜVENLİK.php">Siber Güvenlik</a>
            </div>
        </div>
        <div class="search-container">
            <form action="EĞİTİMLER_ÖĞRENCİ.php" method="GET">
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
                    echo '<img src="../' . htmlspecialchars($row["GorselYolu"]) . '" alt="' . htmlspecialchars($row["Baslik"]) . '">';
                    echo '<h4>' . htmlspecialchars($row["Baslik"]) . '</h4>';
                    echo '<p>' . htmlspecialchars($row["Aciklama"]) . '</p>';
                    echo '<a href="../KURS_DETAY_SAYFALARI/KURS_DETAY.php?EgitimID=' . $row["EgitimID"] . '">Detaylı İncele</a>';
                    if (isset($_SESSION["kullaniciTipi"]) && $_SESSION["kullaniciTipi"] === 'student') {
                        echo '<div class="right-links-2">
                            <a href="?begeni_id=' . $row['EgitimID'] . '">
                                <button class="like-button" data-egitim-id="' . $row['EgitimID'] . '">
                                    <span class="material-icons" title="Beğenme">favorite</span>
                                </button>
                            </a>
                            <a href="?kaydetme_id=' . $row['EgitimID'] . '">
                                <button class="bookmark-button" data-egitim-id="' . $row['EgitimID'] . '">
                                    <span class="material-icons" title="Kaydetme">bookmark</span>
                                </button>
                            </a>
                            <a href="?sepet_id=' . $row['EgitimID'] . '">
                                <button class="shopping-button" data-egitim-id="' . $row['EgitimID'] . '">
                                    <span class="material-icons" title="Sepete Ekleme">add_shopping_cart</span>
                                </button>
                            </a>
                        </div>';
                    }

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
    <script>
        document.querySelectorAll(".like-button").forEach(button => {
            button.addEventListener("click", function() {
                const EgitimID = this.getAttribute("data-egitim-id");

                fetch("BEĞENME.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "EgitimID=" + encodeURIComponent(EgitimID)
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                    })
                    .catch(error => {
                        console.error("Hata:", error);
                    });
            });
        });

        document.querySelectorAll(".bookmark-button").forEach(button => {
            button.addEventListener("click", function() {
                const EgitimID = this.getAttribute("data-egitim-id");

                fetch("KAYDETME.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "EgitimID=" + encodeURIComponent(EgitimID)
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                    })
                    .catch(error => {
                        console.error("Hata:", error);
                    });
            });
        });

        document.querySelectorAll(".shopping-button").forEach(button => {
            button.addEventListener("click", function() {
                const EgitimID = this.getAttribute("data-egitim-id");

                fetch("SEPETE_EKLE.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "EgitimID=" + encodeURIComponent(EgitimID)
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                    })
                    .catch(error => {
                        console.error("Hata:", error);
                    });
            });
        });
    </script>
</body>

</html>