<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['kullaniciTipi']) || $_SESSION['kullaniciTipi'] !== 'instructor') {
    die("Bu işlemi sadece eğitmenler gerçekleştirebilir.");
    exit;
}

$egitmen_id = $_SESSION['kullaniciID'];

$conn = new mysqli("localhost", "root", "", "onlineegitimsitesi");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$baslik = $_POST['course-title'] ?? '';
$aciklama = $_POST['course-description'] ?? '';
$kategoriAdi = $_POST['course-category'] ?? '';
$egitmenAdi = $_POST['instructor-name'] ?? '';
$egitmenHakkinda = $_POST['instructor-bio'] ?? '';
$fiyat = floatval(str_replace(',', '.', $_POST['course-price'] ?? '0'));
$bolumBasliklari = $_POST['section-title'] ?? [];
$bolumVideolari = $_FILES['section-video'] ?? [];

$gorselYolu = '';
if (!empty($_FILES['course-image']['tmp_name'])) {
    $hedefKlasor = "uploads/gorseller/";
    if (!is_dir($hedefKlasor)) mkdir($hedefKlasor, 0777, true);

    $uzanti = pathinfo($_FILES['course-image']['name'], PATHINFO_EXTENSION);
    $gorselAdi = time() . '_' . uniqid() . '.' . $uzanti;
    $gorselTmp = $_FILES['course-image']['tmp_name'];
    $gorselYolu = $hedefKlasor . $gorselAdi;
    move_uploaded_file($gorselTmp, $gorselYolu);
}

$ekleEgitim = $conn->prepare("INSERT INTO Egitimler (Baslik, Aciklama, GorselYolu, KategoriAdi, EgitmenID, EgitmenAdi, EgitmenBiyografi, Fiyat) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$ekleEgitim->bind_param("sssssssd", $baslik, $aciklama, $gorselYolu, $kategoriAdi, $egitmen_id, $egitmenAdi, $egitmenHakkinda, $fiyat);

if ($ekleEgitim->execute()) {
    $egitim_id = $ekleEgitim->insert_id;

    function duzenleDosyaArray($dosya_inputi)
    {
        $duzenlenmis = [];
        $dosyaSayisi = count($dosya_inputi['name']);
        for ($i = 0; $i < $dosyaSayisi; $i++) {
            $duzenlenmis[] = [
                'name' => $dosya_inputi['name'][$i],
                'type' => $dosya_inputi['type'][$i],
                'tmp_name' => $dosya_inputi['tmp_name'][$i],
                'error' => $dosya_inputi['error'][$i],
                'size' => $dosya_inputi['size'][$i],
            ];
        }
        return $duzenlenmis;
    }

    $videoKlasoru = "uploads/videolar/";
    if (!is_dir($videoKlasoru)) mkdir($videoKlasoru, 0777, true);

    $bolumVideolariDuzenli = duzenleDosyaArray($bolumVideolari);

    for ($i = 0; $i < count($bolumBasliklari); $i++) {
        $bolumBaslik = trim($bolumBasliklari[$i] ?? '');
        $videoYolu = '';

        if (!empty($bolumVideolariDuzenli[$i]['tmp_name'])) {
            $videoUzanti = pathinfo($bolumVideolariDuzenli[$i]['name'], PATHINFO_EXTENSION);
            $videoAdi = time() . '_' . uniqid() . '.' . $videoUzanti;
            $videoTmp = $bolumVideolariDuzenli[$i]['tmp_name'];
            $videoYolu = $videoKlasoru . $videoAdi;
            move_uploaded_file($videoTmp, $videoYolu);
        }

        if (!empty($bolumBaslik) && !empty($videoYolu)) {
            $ekleBolum = $conn->prepare("INSERT INTO EgitimBolumleri (EgitimID, Baslik, VideoYolu) VALUES (?, ?, ?)");
            $ekleBolum->bind_param("iss", $egitim_id, $bolumBaslik, $videoYolu);
            $ekleBolum->execute();
        }
    }

    echo "<script>
    alert('Eğitim başarıyla yüklendi.');
    window.location.href = 'EĞİTMEN_EĞİTİMLERİM.php';
    </script>";
} else {
    echo "Hata: " . $ekleEgitim->error;
}

$conn->close();