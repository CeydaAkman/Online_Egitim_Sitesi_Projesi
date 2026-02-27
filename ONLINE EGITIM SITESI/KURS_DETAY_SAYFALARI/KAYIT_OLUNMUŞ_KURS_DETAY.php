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

$stmt = $conn->prepare("SELECT Baslik FROM Egitimler WHERE EgitimID = ?");
$stmt->bind_param("i", $egitimID);
$stmt->execute();
$result_egitim = $stmt->get_result();
if ($result_egitim->num_rows !== 1) {
    die("Eğitim bulunamadı.");
}
$egitim = $result_egitim->fetch_assoc();

$stmt = $conn->prepare("SELECT Baslik, VideoYolu FROM EgitimBolumleri WHERE EgitimID = ?");
$stmt->bind_param("i", $egitimID);
$stmt->execute();
$bolumler = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("
    SELECT Y.YorumIcerigi, Y.YorumTarihi, CONCAT(O.Ad, ' ', O.Soyad) AS AdSoyad
    FROM Yorumlar Y
    JOIN Ogrenciler O ON Y.OgrenciID = O.OgrenciID
    WHERE Y.EgitimID = ?
    ORDER BY Y.YorumTarihi DESC
");
$stmt->bind_param("i", $egitimID);
$stmt->execute();
$yorumlar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();

function videoPath($videoYolu) {
    return htmlspecialchars($videoYolu, ENT_QUOTES);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($egitim["Baslik"]) ?> Kursu</title>
    <link rel="stylesheet" href="KAYIT_OLUNMUŞ_KURS_SAYFALARI.css" />
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($egitim["Baslik"]) ?> Kursu</h1>
    </header>

    <main>
        <section class="course-details">
            <div class="container">
                <h2>Kurs Bölümleri</h2>
                <ul class="module-list">
                    <?php foreach ($bolumler as $bolum): ?>
                        <li>
                            <button 
                                onclick="loadVideo('<?= videoPath($bolum['VideoYolu']) ?>')"
                            >
                                <?= htmlspecialchars($bolum["Baslik"]) ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="video-section">
            <div id="video-container" class="container">
                <video id="course-video" controls preload="auto" width="100%" height="480px" style="border-radius: 30px;">
                    <source 
                        id="video-source" 
                        src="<?= videoPath($bolumler[0]['VideoYolu']) ?>" 
                        type="video/mp4" 
                    />
                    Tarayıcınız video etiketini desteklemiyor.
                </video>
            </div>
        </section>

        <section class="comments-section">
            <div class="container">
                <h2>Yorumlar</h2>
                <div class="comment-form">
                    <textarea id="comment-box" placeholder="Yorumunuzu buraya yazın..."></textarea>
                    <button id="submit-comment">Yorum Yap</button>
                </div>
                <div id="comments-list">
                    <?php foreach ($yorumlar as $yorum): ?>
                        <div class="comment">
                            <span class="comment-author"><?= htmlspecialchars($yorum["AdSoyad"]) ?>:</span>
                            <div class="comment-text"><?= nl2br(htmlspecialchars($yorum["YorumIcerigi"])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Online Eğitim Sitesi</p>
        </div>
    </footer>

    <script>
        const video = document.getElementById('course-video');
        const videoSource = document.getElementById('video-source');

        function loadVideo(src) {
            if (!src) return;
            video.pause();
            videoSource.src = src;
            video.load();
            video.play().catch(() => {
                video.muted = true;
                video.play().catch(err => console.error("Video oynatılamıyor:", err));
            });
        }

        document.getElementById('submit-comment').addEventListener('click', function () {
            const comment = document.getElementById('comment-box').value.trim();
            if (!comment) return;

            fetch('../YORUM_EKLE.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'egitimID=<?= $egitimID ?>&yorum=' + encodeURIComponent(comment)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const newComment = document.createElement('div');
                    newComment.className = 'comment';
                    newComment.innerHTML = `<span class="comment-author">${data.adSoyad}:</span>
                                            <div class="comment-text">${data.yorumIcerigi}</div>`;
                    document.getElementById('comments-list').prepend(newComment);
                    document.getElementById('comment-box').value = '';
                } else {
                    alert('Yorum eklenemedi.');
                }
            });
        });
    </script>
</body>
</html>