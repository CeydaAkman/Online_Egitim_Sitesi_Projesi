<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineegitimsitesi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$ad = $_POST['name'];
$soyad = $_POST['lastname'];
$dogumTarihi = $_POST['birthdate'];
$cinsiyet = $_POST['gender'];
$kullaniciTipi = $_POST['user-type'];
$telefon = $_POST['phone'];
$eposta = $_POST['email'];
$plainPassword = $_POST['password'];

$salt = bin2hex(random_bytes(16));

$hashedPassword = hash('sha512', $salt . $plainPassword);

if ($kullaniciTipi == 'Öğrenci') {
    $sql = "INSERT INTO Ogrenciler (Ad, Soyad, DogumTarihi, Cinsiyet, KullaniciTipi, Telefon, Eposta, Sifre, Salt) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
} elseif ($kullaniciTipi == 'Eğitmen') {
    $sql = "INSERT INTO Egitmenler (Ad, Soyad, DogumTarihi, Cinsiyet, KullaniciTipi, Telefon, Eposta, Sifre, Salt) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
} else {
    die("Geçersiz kullanıcı tipi seçildi.");
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $ad, $soyad, $dogumTarihi, $cinsiyet, $kullaniciTipi, $telefon, $eposta, $hashedPassword, $salt);

if ($stmt->execute()) {
    echo "<script>alert('Kayıt başarılı! Giriş yapabilirsiniz.'); window.location.href='OTURUM_AÇ.html';</script>";
} else {
    echo "Hata: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>