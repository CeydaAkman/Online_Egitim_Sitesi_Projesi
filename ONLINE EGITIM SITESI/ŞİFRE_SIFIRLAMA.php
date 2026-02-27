<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineegitimsitesi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$eposta = $_POST['email'];

$sql_ogrenci = "SELECT * FROM Ogrenciler WHERE Eposta = ?";
$stmt = $conn->prepare($sql_ogrenci);
$stmt->bind_param("s", $eposta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Şifre sıfırlama maili gönderildi. Lütfen e-postanızı kontrol ediniz.'); window.location.href='OTURUM_AÇ.html';</script>";
} else {
    $sql_egitmen = "SELECT * FROM Egitmenler WHERE Eposta = ?";
    $stmt = $conn->prepare($sql_egitmen);
    $stmt->bind_param("s", $eposta);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Şifre sıfırlama maili gönderildi. Lütfen e-postanızı kontrol ediniz.'); window.location.href='OTURUM_AÇ.html';</script>";
    } else {
        echo "<script>alert('Bu e-posta adresi sistemde bulunamadı.'); window.location.href='ŞİFRE_SIFIRLAMA.html';</script>";
    }
}

$stmt->close();
$conn->close();
?>