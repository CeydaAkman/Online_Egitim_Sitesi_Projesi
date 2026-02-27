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

if (isset($_GET['sil_id']) && isset($_SESSION['kullaniciID'])) {
    $sil_id = $_GET['sil_id'];
    $ogrenciID = $_SESSION['kullaniciID'];

    $stmtSil = $conn->prepare("DELETE FROM SepettekiEgitimler WHERE EgitimID = ? AND OgrenciID = ?");
    $stmtSil->bind_param("ii", $sil_id, $ogrenciID);
    $stmtSil->execute();
    $stmtSil->close();

    header("Location: ALIŞVERİŞ_SEPETİ.php");
    exit();
}

$egitimler = [];

if (isset($_SESSION['kullaniciID'])) {
    $ogrenciID = $_SESSION['kullaniciID'];

    $stmt = $conn->prepare("
        SELECT e.EgitimID, e.Baslik, e.Aciklama, e.GorselYolu, e.Fiyat
        FROM SepettekiEgitimler s
        INNER JOIN Egitimler e ON s.EgitimID = e.EgitimID
        WHERE s.OgrenciID = ?
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

$toplam = 0;
foreach ($egitimler as $egitim) {
    $toplam += $egitim['Fiyat'];
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Online Eğitim Sitesi - Alışveriş Sepeti</title>
    <link rel="stylesheet" href="ALIŞVERİŞ_SEPETİ.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>

    <main>
        <h2>Sepetimdeki Eğitimler</h2>
        <div class="course-list">
            <?php if (!empty($egitimler)) : ?>
                <?php foreach ($egitimler as $egitim) : ?>
                    <div class="course-card" data-id="<?= $egitim['EgitimID'] ?>">
                        <img src="<?= htmlspecialchars($egitim['GorselYolu']) ?>" alt="<?= htmlspecialchars($egitim['Baslik']) ?>">
                        <h4><?= htmlspecialchars($egitim['Baslik']) ?></h4>
                        <p><?= htmlspecialchars($egitim['Aciklama']) ?></p>
                        <a href="KURS_DETAY_SAYFALARI/<?= urlencode($egitim['Baslik']) ?>.html">Detaylı İncele</a>
                        <div class="right-links-2">
                            <a href="#" class="fiyat"><?= $egitim['Fiyat'] ?> TL</a>
                            <a href="?sil_id=<?= $egitim['EgitimID'] ?>" onclick="return confirm('Bu eğitimi sepetten silmek istediğinize emin misiniz?')">
                                <button class="delete-icon" title="Sil"><span class="material-icons">delete</span></button>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Sepetinizde henüz bir eğitim bulunmamaktadır.</p>
            <?php endif; ?>
        </div>
    </main>

    <section>
        <p><strong>Toplam Tutar: </strong><span id="total-price"><?= $toplam ?> TL</span></p>
        <button onclick="odemeKontrol()">Ödeme Yap</button>
    </section>

    <script>
        function odemeKontrol() {
            const toplamText = document.getElementById('total-price').innerText;
            const toplam = parseFloat(toplamText.replace('TL', '').trim());

            if (toplam <= 0) {
                alert("Sepetiniz boş. Lütfen ödeme yapmadan önce en az bir eğitim ekleyin.");
            } else {
                window.location.href = 'ÖDEME_SAYFASI.html';
            }
        }
    </script>

    <footer>
        <p>&copy; 2024 Online Eğitim Sitesi. Tüm Hakları Saklıdır.</p>
    </footer>
</body>

</html>