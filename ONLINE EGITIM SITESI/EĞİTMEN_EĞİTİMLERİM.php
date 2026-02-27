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

$egitmenID = isset($_SESSION['EgitmenID']) ? $_SESSION['EgitmenID'] : null;

$egitimler = [];

if ($egitmenID) {
    $stmt = $conn->prepare("
        SELECT e.EgitimID, e.Baslik, e.Aciklama, e.GorselYolu
        FROM Egitimler e
        WHERE e.EgitmenID = ?
    ");
    $stmt->bind_param("i", $egitmenID);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Eğitim Sitesi - Giriş Yapılmış</title>
    <link rel="stylesheet" href="GİRİŞ_YAPILMIŞ.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>

    <main>
        <h2>Eğitimlerim</h2>
        <div class="course-list">
            <?php if (!empty($egitimler)) : ?>
                <?php foreach ($egitimler as $egitim) : ?>
                    <div class="course-card">
                        <img src="<?php echo htmlspecialchars($egitim['GorselYolu']); ?>" alt="<?php echo htmlspecialchars($egitim['Baslik']); ?>">
                        <h4><?php echo htmlspecialchars($egitim['Baslik']); ?></h4>
                        <p><?php echo htmlspecialchars($egitim['Aciklama']); ?></p>
                        <a href="EĞİTİM_GÜNCELLE.php?EgitimID=<?php echo urlencode($egitim['EgitimID']); ?>">Düzenle</a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Henüz eğitiminiz yok.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Online Eğitim Sitesi. Tüm Hakları Saklıdır.</p>
    </footer>
</body>

</html>