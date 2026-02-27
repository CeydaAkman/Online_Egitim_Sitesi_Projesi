<?php
session_start();

$adSoyad = isset($_SESSION['adSoyad']) ? explode(' ', $_SESSION['adSoyad']) : ['Kullanıcı', ''];
$ad = $adSoyad[0];
$soyad = isset($adSoyad[1]) ? $adSoyad[1] : '';

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
    <title>Online Eğitim Sitesi - Giriş Yapılmış</title>
    <link rel="stylesheet" href="GİRİŞ_YAPILMIŞ.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>

    <nav>
        <div class="left-links">
            <a class="movement" href="GİRİŞ_YAPILMIŞ_ÖĞRENCİ.php">Ana Sayfa</a>
            <a class="movement" href="EĞİTİMLER_ÖĞRENCİ.php">Eğitimler</a>
        </div>

        <div class="search-container">
            <form action="GİRİŞ_YAPILMIŞ_ÖĞRENCİ.php" method="GET">
                <input type="text" name="arama" placeholder="Ara..." value="<?php echo htmlspecialchars($arama); ?>">
                <button type="submit">Ara</button>
            </form>
        </div>

        <div class="right-links">
            <span id="user-name" class="user-name"></span>
            <div class="dropdown">
                <a href="#"><span class="material-icons" title="Hesap">person</span></a>
                <div class="dropdown-menu">
                    <a href="PROFİL_SAYFASI.php">Profili gör</a>
                    <a href="KAYITLI_EĞİTİMLERİM.php">Kayıtlı Eğitimlerim</a>
                    <a href="KAYDEDİLEN_EĞİTİMLERİM.php">Kaydedilen Eğitimlerim</a>
                    <a href="BEĞENDİĞİM_EĞİTİMLERİM.php">Beğendiğim Eğitimlerim</a>
                    <a href="ALIŞVERİŞ_SEPETİ.php">Sepetim</a>
                    <a href="ANA_SAYFA.php">Oturumu kapat</a>
                </div>
            </div>
        </div>
    </nav>

    <section>
        <h3 id="welcome-message" class="welcome-message"></h3>
    </section>

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
                    echo '<div class="right-links-2">
                        <a href="?begeni_id=' . $row['EgitimID'] . '">
                        <button class="like-button" data-egitim-id="' . $row['EgitimID'] . '"><span class="material-icons" title="Beğenme">favorite</span></button>
                        </a>
                        <a href="?kaydetme_id=' . $row['EgitimID'] . '">
                        <button class="bookmark-button" data-egitim-id="' . $row['EgitimID'] . '"><span class="material-icons" title="Kaydetme">bookmark</span></button>
                        </a>
                        <a href="?sepet_id=' . $row['EgitimID'] . '">
                        <button class="shopping-button" data-egitim-id="' . $row['EgitimID'] . '"><span class="material-icons" title="Sepete Ekleme">add_shopping_cart</span></button>
                        </a>
                    </div>';
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

<script>
    const kullanici = {
        ad: "<?php echo $ad; ?>",
        soyad: "<?php echo $soyad; ?>"
    };

    document.getElementById("welcome-message").textContent = `Tekrardan Hoşgeldiniz, ${kullanici.ad} ${kullanici.soyad}!`;
    document.getElementById("user-name").textContent = `${kullanici.ad} ${kullanici.soyad}`;

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

</html>