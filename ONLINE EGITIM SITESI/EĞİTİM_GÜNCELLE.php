<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['kullaniciTipi']) || $_SESSION['kullaniciTipi'] !== 'instructor') {
    die("Bu işlemi sadece eğitmenler gerçekleştirebilir.");
}

$conn = new mysqli("localhost", "root", "", "onlineegitimsitesi");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$egitimID = intval($_GET['EgitimID'] ?? 0);
$egitmenID = $_SESSION['EgitmenID'];

$egitimSorgu = $conn->prepare("SELECT * FROM Egitimler WHERE EgitimID = ? AND EgitmenID = ?");
$egitimSorgu->bind_param("ii", $egitimID, $egitmenID);
$egitimSorgu->execute();
$egitimSonuc = $egitimSorgu->get_result();

if ($egitimSonuc->num_rows === 0) {
    die("Bu eğitimi düzenleme yetkiniz yok veya eğitim bulunamadı.");
}

$egitim = $egitimSonuc->fetch_assoc();

$bolumSorgu = $conn->prepare("SELECT * FROM EgitimBolumleri WHERE EgitimID = ?");
$bolumSorgu->bind_param("i", $egitimID);
$bolumSorgu->execute();
$bolumSonuc = $bolumSorgu->get_result();
$bolumler = [];
while ($row = $bolumSonuc->fetch_assoc()) {
    $bolumler[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baslik = $_POST['course-title'] ?? $egitim['Baslik'];
    $aciklama = $_POST['course-description'] ?? $egitim['Aciklama'];
    $kategoriAdi = $_POST['course-category'] ?? $egitim['KategoriAdi'];
    $egitmenAdi = $_POST['instructor-name'] ?? $egitim['EgitmenAdi'];
    $egitmenBiyografi = $_POST['instructor-bio'] ?? $egitim['EgitmenBiyografi'];
    $fiyat = floatval(str_replace(',', '.', $_POST['course-price'] ?? $egitim['Fiyat']));

    $gorselYolu = $egitim['GorselYolu'];
    if (!empty($_FILES['course-image']['tmp_name'])) {
        $hedefKlasor = "uploads/gorseller/";
        if (!is_dir($hedefKlasor)) mkdir($hedefKlasor, 0777, true);
        $uzanti = pathinfo($_FILES['course-image']['name'], PATHINFO_EXTENSION);
        $gorselAdi = time() . '_' . uniqid() . '.' . $uzanti;
        $gorselTmp = $_FILES['course-image']['tmp_name'];
        $gorselYolu = $hedefKlasor . $gorselAdi;
        move_uploaded_file($gorselTmp, $gorselYolu);
    }

    $guncelle = $conn->prepare("UPDATE Egitimler SET Baslik=?, Aciklama=?, GorselYolu=?, KategoriAdi=?, EgitmenAdi=?, EgitmenBiyografi=?, Fiyat=? WHERE EgitimID=? AND EgitmenID=?");
    $guncelle->bind_param("ssssssdii", $baslik, $aciklama, $gorselYolu, $kategoriAdi, $egitmenAdi, $egitmenBiyografi, $fiyat, $egitimID, $egitmenID);
    $guncelle->execute();

    $bolumBasliklari = $_POST['section-title'] ?? [];
    $bolumIDs = $_POST['section-id'] ?? [];
    $videoKlasoru = "uploads/videolar/";
    if (!is_dir($videoKlasoru)) mkdir($videoKlasoru, 0777, true);

    for ($i = 0; $i < count($bolumBasliklari); $i++) {
        $bolumID = intval($bolumIDs[$i] ?? 0);
        $bolumBaslik = trim($bolumBasliklari[$i] ?? '');

        $videoYolu = '';
        if (!empty($_FILES['section-video']['tmp_name'][$i])) {
            $videoUzanti = pathinfo($_FILES['section-video']['name'][$i], PATHINFO_EXTENSION);
            $videoAdi = time() . '_' . uniqid() . '.' . $videoUzanti;
            $videoTmp = $_FILES['section-video']['tmp_name'][$i];
            $videoYolu = $videoKlasoru . $videoAdi;
            move_uploaded_file($videoTmp, $videoYolu);
        }

        if ($bolumID > 0) {
            if ($videoYolu) {
                $updateBolum = $conn->prepare("UPDATE EgitimBolumleri SET Baslik=?, VideoYolu=? WHERE BolumID=? AND EgitimID=?");
                $updateBolum->bind_param("ssii", $bolumBaslik, $videoYolu, $bolumID, $egitimID);
            } else {
                $updateBolum = $conn->prepare("UPDATE EgitimBolumleri SET Baslik=? WHERE BolumID=? AND EgitimID=?");
                $updateBolum->bind_param("sii", $bolumBaslik, $bolumID, $egitimID);
            }
            $updateBolum->execute();
        } else {
            if ($bolumBaslik && $videoYolu) {
                $insertBolum = $conn->prepare("INSERT INTO EgitimBolumleri (EgitimID, Baslik, VideoYolu) VALUES (?, ?, ?)");
                $insertBolum->bind_param("iss", $egitimID, $bolumBaslik, $videoYolu);
                $insertBolum->execute();
            }
        }
    }

    echo "<script>alert('Eğitim ve bölümler başarıyla güncellendi.'); window.location.href='EĞİTMEN_EĞİTİMLERİM.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Eğitim Sitesi - Admin Paneli</title>
    <link rel="stylesheet" href="ADMİN_PANELİ.css">
</head>

<body>
    <header>
        <h1>Online Eğitim Sitesi</h1>
    </header>

    <main>
        <section class="upload-form">
            <h2>Eğitimi Güncelle</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="course-title">Eğitim Başlığı:</label>
                    <input type="text" id="course-title" name="course-title" placeholder="Eğitim başlığı giriniz" required
                        value="<?php echo htmlspecialchars($egitim['Baslik'], ENT_QUOTES); ?>">
                </div>
                <div class="form-group">
                    <label for="course-description">Eğitim Bilgisi:</label>
                    <input type="text" id="course-description" name="course-description" placeholder="Eğitim açıklaması yazınız" required
                        value="<?php echo htmlspecialchars($egitim['Aciklama'], ENT_QUOTES); ?>">
                </div>
                <div class="form-group">
                    <label for="course-image">Eğitim Görseli:</label>
                    <input type="file" id="course-image" name="course-image" accept="image/*">
                    <?php if (!empty($egitim['GorselYolu'])): ?>
                        <br><small>Mevcut görsel: <a href="<?php echo htmlspecialchars($egitim['GorselYolu'], ENT_QUOTES); ?>" target="_blank">Görüntüle</a></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="course-category">Eğitim Kategorisi:</label>
                    <select id="course-category" name="course-category" required>
                        <option value="">Kategori Seçin</option>
                        <?php
                        $kategoriler = [
                            "Web Geliştirme",
                            "Programlama Dilleri",
                            "Veri Bilimi",
                            "Mobil Uygulama Geliştirme",
                            "Oyun Geliştirme",
                            "Yapay Zeka",
                            "Veritabanı Yönetimi",
                            "Siber Güvenlik"
                        ];
                        foreach ($kategoriler as $kategori) {
                            $selected = ($egitim['KategoriAdi'] === $kategori) ? "selected" : "";
                            echo "<option value=\"" . htmlspecialchars($kategori, ENT_QUOTES) . "\" $selected>$kategori</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="instructor-name">Eğitmen Adı Soyadı:</label>
                    <input type="text" id="instructor-name" name="instructor-name" placeholder="Eğitmen adı giriniz" required
                        value="<?php echo htmlspecialchars($egitim['EgitmenAdi'], ENT_QUOTES); ?>">
                </div>
                <div class="form-group">
                    <label for="instructor-bio">Eğitmen Hakkında:</label>
                    <input type="text" id="instructor-bio" name="instructor-bio" placeholder="Eğitmen biyografisini yazınız" required
                        value="<?php echo htmlspecialchars($egitim['EgitmenBiyografi'], ENT_QUOTES); ?>">
                </div>
                <div class="form-group">
                    <label for="course-price">Kurs Fiyat Bilgisi:</label>
                    <input type="text" id="course-price" name="course-price" placeholder="Kurs fiyat bilgisini giriniz" required
                        value="<?php echo htmlspecialchars($egitim['Fiyat'], ENT_QUOTES); ?>">
                </div>
                <h3>Kurs Bölümleri</h3>
                <div id="course-sections">
                    <?php
                    if (count($bolumler) > 0) {
                        $sectionCount = 0;
                        foreach ($bolumler as $bolum) {
                            $sectionCount++;
                            ?>
                            <div class="course-section">
                                <input type="hidden" name="section-id[]" value="<?php echo intval($bolum['BolumID']); ?>">
                                <label for="section-title-<?php echo $sectionCount; ?>">Bölüm Başlığı:</label>
                                <input type="text" id="section-title-<?php echo $sectionCount; ?>" name="section-title[]" placeholder="Bölüm başlığı giriniz" required
                                    value="<?php echo htmlspecialchars($bolum['Baslik'], ENT_QUOTES); ?>">

                                <label for="section-video-<?php echo $sectionCount; ?>">Bölüm Videosu:</label>
                                <input type="file" id="section-video-<?php echo $sectionCount; ?>" name="section-video[]" accept="video/*">
                                <?php if (!empty($bolum['VideoYolu'])): ?>
                                    <br><small>Mevcut video: <a href="<?php echo htmlspecialchars($bolum['VideoYolu'], ENT_QUOTES); ?>" target="_blank">Görüntüle</a></small>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="course-section">
                            <input type="hidden" name="section-id[]" value="0">
                            <label for="section-title-1">Bölüm Başlığı:</label>
                            <input type="text" id="section-title-1" name="section-title[]" placeholder="Bölüm başlığı giriniz" required>

                            <label for="section-video-1">Bölüm Videosu:</label>
                            <input type="file" id="section-video-1" name="section-video[]" accept="video/*" required>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <button type="button" onclick="addSection()">+ Bölüm Ekle</button>

                <button type="submit">Eğitimi Güncelle</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Online Eğitim Sitesi. Tüm Hakları Saklıdır.</p>
    </footer>

    <script>
        let sectionCount = <?php echo isset($sectionCount) ? $sectionCount : 1; ?>;
        function addSection() {
            sectionCount++;
            const sectionsContainer = document.getElementById('course-sections');
            const newSection = document.createElement('div');
            newSection.classList.add('course-section');

            newSection.innerHTML = `
                <input type="hidden" name="section-id[]" value="0">
                <label for="section-title-${sectionCount}">Bölüm Başlığı:</label>
                <input type="text" id="section-title-${sectionCount}" name="section-title[]" placeholder="Bölüm başlığı girin..." required>

                <label for="section-video-${sectionCount}">Bölüm Videosu:</label>
                <input type="file" id="section-video-${sectionCount}" name="section-video[]" accept="video/*" required>
            `;

            sectionsContainer.appendChild(newSection);
        }
    </script>
</body>

</html>