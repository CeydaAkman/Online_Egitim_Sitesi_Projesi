<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineegitimsitesi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adSoyad = htmlspecialchars($_POST['name']);
    $kartNumarasi = htmlspecialchars($_POST['card-number']);
    $sonKullanmaTarihi = htmlspecialchars($_POST['expiry']);
    $cvv = htmlspecialchars($_POST['cvv']);

    if (!isset($_SESSION['kullaniciID'])) {
        die("Oturum bilgisi bulunamadı.Lütfen giriş yapınız.");
        exit();
    }

    $student_id = $_SESSION['kullaniciID'];

    $check_sql = "SELECT * FROM OdemeBilgileri WHERE KartNumarasi = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $kartNumarasi);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $odemeID = null;
    } else {
        $stmt = $conn->prepare("INSERT INTO OdemeBilgileri (AdSoyad, KartNumarasi, SonKullanmaTarihi, CVV) 
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $adSoyad, $kartNumarasi, $sonKullanmaTarihi, $cvv);

        if (!$stmt->execute()) {
            die("Kart bilgisi kaydedilirken hata: " . $stmt->error);
        }

        $odemeID = $stmt->insert_id;
        $stmt->close();
    }

    if ($odemeID !== null) {
        $update_sql = "UPDATE Ogrenciler SET OdemeID = ? WHERE OgrenciID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $odemeID, $student_id);
        $update_stmt->execute();
        $update_stmt->close();
    }

    $sepet_sql = "SELECT * FROM SepettekiEgitimler WHERE OgrenciID = ?";
    $sepet_stmt = $conn->prepare($sepet_sql);
    $sepet_stmt->bind_param("i", $student_id);
    $sepet_stmt->execute();
    $sepet_result = $sepet_stmt->get_result();

    if ($sepet_result->num_rows == 0) {
        die("Sepetiniz boş.Ödeme yapmadan önce sepetinize ürün ekleyin.");
        exit();
    }

    $insert_satin_alinan = $conn->prepare("INSERT INTO SatinAlinanEgitimler (OgrenciID, EgitimID) VALUES (?, ?)");
    $delete_sepet = $conn->prepare("DELETE FROM SepettekiEgitimler WHERE OgrenciID = ? AND EgitimID = ?");

    while ($row = $sepet_result->fetch_assoc()) {
        $egitimID = $row['EgitimID'];

        $insert_satin_alinan->bind_param("ii", $student_id, $egitimID);
        $insert_satin_alinan->execute();

        $delete_sepet->bind_param("ii", $student_id, $egitimID);
        $delete_sepet->execute();
    }

    $insert_satin_alinan->close();
    $delete_sepet->close();
    $sepet_stmt->close();

    echo "<script>alert('Ödeme ve satın alma işlemi başarıyla gerçekleşti.');window.location.href = 'GİRİŞ_YAPILMIŞ_ÖĞRENCİ.php';</script>";
    exit;
}

$conn->close();