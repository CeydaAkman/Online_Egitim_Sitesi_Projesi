<?php
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['kullaniciTipi']) || $_SESSION['kullaniciTipi'] !== 'student') {
    die("Sadece öğrenciler yorum yapabilir.");
    exit;
}

if (!isset($_SESSION['kullaniciID'])) {
    die("Oturum bilgisi bulunamadı.");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineegitimsitesi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Veritabanı bağlantı hatası."]);
    exit;
}

$ogrenciID = $_SESSION['kullaniciID'];
$egitimID = isset($_POST["egitimID"]) ? intval($_POST["egitimID"]) : 0;
$yorum = trim($_POST["yorum"] ?? "");

if ($yorum === "") {
    echo json_encode(["success" => false, "message" => "Yorum boş olamaz."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO Yorumlar (OgrenciID, EgitimID, YorumTarihi, YorumIcerigi) VALUES (?, ?, NOW(), ?)");
$stmt->bind_param("iis", $ogrenciID, $egitimID, $yorum);

if ($stmt->execute()) {
    $stmt2 = $conn->prepare("SELECT Ad, Soyad FROM Ogrenciler WHERE OgrenciID = ?");
    $stmt2->bind_param("i", $ogrenciID);
    $stmt2->execute();
    $result = $stmt2->get_result()->fetch_assoc();

    $adSoyad = htmlspecialchars($result["Ad"] . " " . $result["Soyad"]);
    $yorumIcerigi = nl2br(htmlspecialchars($yorum));

    echo json_encode([
        "success" => true,
        "adSoyad" => $adSoyad,
        "yorumIcerigi" => $yorumIcerigi
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Yorum eklenemedi."]);
}

$conn->close();