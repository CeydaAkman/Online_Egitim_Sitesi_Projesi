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

$egitimID = isset($_GET["EgitimID"]) ? intval($_GET["EgitimID"]) : 0;
if ($egitimID <= 0) {
    die("Geçersiz eğitim ID'si.");
}

$sql = "SELECT * FROM Egitimler WHERE EgitimID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $egitimID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $egitim = $result->fetch_assoc();
} else {
    die("Eğitim bulunamadı.");
}

$sqlBolumler = "SELECT * FROM EgitimBolumleri WHERE EgitimID = ?";
$stmtBolum = $conn->prepare($sqlBolumler);
$stmtBolum->bind_param("i", $egitimID);
$stmtBolum->execute();
$resultBolum = $stmtBolum->get_result();

$bolumler = [];
while ($row = $resultBolum->fetch_assoc()) {
    $bolumler[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Online Eğitim Sitesi - <?= htmlspecialchars($egitim["Baslik"]) ?></title>
    <link rel="stylesheet" href="KURS_SAYFALARI.css">
</head>

<body>
    <header>
        <h1><?= htmlspecialchars($egitim["Baslik"]) ?></h1>
    </header>
    <main>
        <h2>Eğitim Açıklaması</h2>
        <section class="course-overview">
            <p><?= nl2br(htmlspecialchars($egitim["Aciklama"])) ?></p>
        </section>

        <section class="course-modules">
            <h2>Kurs Bölümleri</h2>
            <?php if (count($bolumler) > 0): ?>
                <ul>
                    <?php foreach ($bolumler as $bolum): ?>
                        <li><?= htmlspecialchars($bolum["Baslik"]) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Bu eğitime ait bölüm bulunmamaktadır.</p>
            <?php endif; ?>
        </section>

        <h2>Eğitmen Bilgisi</h2>
        <section class="instructor-info">
            <p><strong>Adı:</strong> <?= htmlspecialchars($egitim["EgitmenAdi"]) ?></p>
            <p><strong>Biyografi:</strong> <?= nl2br(htmlspecialchars($egitim["EgitmenBiyografi"])) ?></p>
        </section>

        <h2>Fiyat Bilgisi</h2>
        <section class="price-overview">
            <p><strong>Fiyat:</strong> <?= htmlspecialchars($egitim["Fiyat"]) ?> TL</p>
        </section>

        <section class="enroll-section">
            <?php if (isset($_SESSION["kullaniciTipi"]) && $_SESSION["kullaniciTipi"] === "student"): ?>
                <form action="../SEPETE_EKLE.php" method="POST">
                    <input type="hidden" name="EgitimID" value="<?= $egitimID ?>">
                    <button type="submit" class="enroll-button">Sepete Ekle</button>
                </form>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Online Eğitim Sitesi. Tüm Hakları Saklıdır.</p>
    </footer>
</body>

</html>