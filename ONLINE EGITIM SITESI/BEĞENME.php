<?php
session_start();
$ogrenci_id = $_SESSION['kullaniciID'] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['EgitimID']) && $ogrenci_id) {
    $conn = new mysqli("localhost", "root", "", "onlineegitimsitesi");

    if ($conn->connect_error) {
        die("Bağlantı hatası: " . $conn->connect_error);
    }

    $egitim_id = $conn->real_escape_string($_POST['EgitimID']);

    $checkSql = "SELECT * FROM BegenilenEgitimler WHERE OgrenciID = $ogrenci_id AND EgitimID = $egitim_id";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        $deleteSql = "DELETE FROM BegenilenEgitimler WHERE OgrenciID = $ogrenci_id AND EgitimID = $egitim_id";
        if ($conn->query($deleteSql) === TRUE) {
            echo "Beğeni kaldırıldı.";
        } else {
            echo "Hata oluştu: " . $conn->error;
        }
    } else {
        $insertSql = "INSERT INTO BegenilenEgitimler (OgrenciID, EgitimID) VALUES ($ogrenci_id, $egitim_id)";
        if ($conn->query($insertSql) === TRUE) {
            echo "Eğitim beğenildi.";
        } else {
            echo "Hata oluştu: " . $conn->error;
        }
    }

    $conn->close();
} else {
    echo "Geçersiz istek.";
}
?>
