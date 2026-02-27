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

$egitimler = [];

if (isset($_SESSION['kullaniciID'])) {
    $ogrenciID = $_SESSION['kullaniciID'];

    $stmt = $conn->prepare("
        SELECT e.EgitimID, e.Baslik, e.Aciklama, e.GorselYolu
        FROM BegenilenEgitimler b
        INNER JOIN Egitimler e ON b.EgitimID = e.EgitimID
        WHERE b.OgrenciID = ?
    ");
    $stmt->bind_param("i", $ogrenciID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $egitimler[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Beğendiğim Eğitimlerim</title>
    <link rel="stylesheet" href="BEĞENDİĞİM_EĞİTİMLER.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>
    <main>
        <h2>Beğendiğim Eğitimlerim</h2>
        <div class="course-list">
            <?php if (!empty($egitimler)) : ?>
                <?php foreach ($egitimler as $egitim) : ?>
                    <div class="course-card">
                        <img src="<?= htmlspecialchars($egitim['GorselYolu']) ?>" alt="<?= htmlspecialchars($egitim['Baslik']) ?>">
                        <h4><?= htmlspecialchars($egitim['Baslik']) ?></h4>
                        <p><?= htmlspecialchars($egitim['Aciklama']) ?></p>
                        <a href="KURS_DETAY_SAYFALARI/KURS_DETAY.php?EgitimID=<?= urlencode($egitim['EgitimID']) ?>">Detaylı İncele</a>
                        <div class="right-links-2">
                            <a href="?begeni_id=<?= $egitim['EgitimID'] ?>">
                                <button class="like-button" data-egitim-id="<?= $egitim['EgitimID'] ?>"><span class="material-icons" title="Beğenme">favorite</span></button>
                            </a>
                            <a href="?sepet_id=<?= $egitim['EgitimID'] ?>">
                                <button class="shopping-button" data-egitim-id="<?= $egitim['EgitimID'] ?>"><span class="material-icons" title="Sepete Ekleme">add_shopping_cart</span></button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Henüz beğendiğiniz bir eğitim bulunmamaktadır.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Online Eğitim Sitesi. Tüm Hakları Saklıdır.</p>
    </footer>

    <script>
        document.querySelectorAll(".bookmark-button").forEach(button => {
            button.addEventListener("click", function() {
                const id = this.getAttribute("data-egitim-id");
                fetch("KAYDETME.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "EgitimID=" + encodeURIComponent(id)
                    })
                    .then(res => res.text())
                    .then(alert)
                    .catch(err => console.error("Hata:", err));
            });
        });

        document.querySelectorAll(".shopping-button").forEach(button => {
            button.addEventListener("click", function() {
                const id = this.getAttribute("data-egitim-id");
                fetch("SEPETE_EKLE.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "EgitimID=" + encodeURIComponent(id)
                    })
                    .then(res => res.text())
                    .then(alert)
                    .catch(err => console.error("Hata:", err));
            });
        });
    </script>
</body>

</html>