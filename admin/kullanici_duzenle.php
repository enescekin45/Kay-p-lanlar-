<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}

require '../config/db.php'; // Veritabanı bağlantısı

// Kullanıcı ID'sini al
if (!isset($_GET['id'])) {
    header("Location: kullanici_yonetimi.php");
    exit();
}
$kullanici_id = $_GET['id'];

// Kullanıcı bilgilerini veritabanından çek
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $kullanici_id]);
$kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kullanici) {
    header("Location: kullanici_yonetimi.php");
    exit();
}

// Form gönderildiğinde kullanıcı bilgilerini güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = $_POST['ad_soyad'];
    $kullanici_adi = $_POST['kullanici_adi'];
    $eposta = $_POST['eposta'];

    // Kullanıcı bilgilerini güncelle
    $query = "UPDATE users SET ad_soyad = :ad_soyad, kullanici_adi = :kullanici_adi, eposta = :eposta WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'ad_soyad' => $ad_soyad,
        'kullanici_adi' => $kullanici_adi,
        'eposta' => $eposta,
        'id' => $kullanici_id
    ]);

    // Başarı mesajı ve yönlendirme
    $_SESSION['mesaj'] = "Kullanıcı bilgileri başarıyla güncellendi.";
    header("Location: kullanici_yonetimi.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Düzenle</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1>Kullanıcı Düzenle</h1>
        <div class="admin-info">
            <span>Giriş Yapan: <?= htmlspecialchars($_SESSION['yonetici_adi']) ?></span>
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-btn">Çıkış Yap</button>
            </form>
        </div>
    </div>

    <!-- Ana İçerik -->
    <div class="admin-container">
        <!-- Sidebar (Yan Menü) -->
        <div class="sidebar">
            <ul>
                <li>
                    <a href="index.php">
                        <i class="fas fa-home"></i>
                        Ana Sayfa
                    </a>
                </li>
                <li>
                    <a href="hikaye_yonetimi.php">
                        <i class="fas fa-book"></i>
                        Hikaye Yönetimi
                    </a>
                </li>
                <li>
                    <a href="kullanici_yonetimi.php">
                        <i class="fas fa-users"></i>
                        Kullanıcı Yönetimi
                    </a>
                </li>
                <li>
                    <a href="raporlar_analizler.php">
                        <i class="fas fa-chart-line"></i>
                        Raporlar ve Analizler
                    </a>
                </li>
                <li>
                    <a href="kayip_yonetimi.php">
                        <i class="fas fa-search"></i>
                        Kayıp Yönetimi
                    </a>
                </li>
                <li>
    <a href="hakkımız.php">
        <i class="fas fa-info-circle"></i> <!-- İkon güncellendi -->
        Hakkımızda
    </a>
</li>
                <li>
                    <a href="ayarlar.php">
                        <i class="fas fa-cog"></i>
                        Ayarlar
                    </a>
                </li>
            </ul>
        </div>

        <!-- Ana İçerik -->
        <div class="main-content">
            <h2>Kullanıcı Bilgilerini Düzenle</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="ad_soyad">Ad Soyad</label>
                    <input type="text" id="ad_soyad" name="ad_soyad" value="<?= htmlspecialchars($kullanici['ad_soyad']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="kullanici_adi">Kullanıcı Adı</label>
                    <input type="text" id="kullanici_adi" name="kullanici_adi" value="<?= htmlspecialchars($kullanici['kullanici_adi']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="eposta">E-posta</label>
                    <input type="email" id="eposta" name="eposta" value="<?= htmlspecialchars($kullanici['eposta']) ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="save">Kaydet</button>
                    <a href="kullanici_yonetimi.php" class="cancel">İptal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>