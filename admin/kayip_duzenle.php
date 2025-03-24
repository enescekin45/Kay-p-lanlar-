<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}

require '../config/db.php'; // Veritabanı bağlantısı

// Kayıp kişi ID'sini al
if (!isset($_GET['id'])) {
    header("Location: kayip_yonetimi.php");
    exit();
}
$kayip_id = $_GET['id'];

// Kayıp kişi bilgilerini veritabanından çek
$query = "SELECT * FROM kayip_ilani WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $kayip_id]);
$kayip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kayip) {
    header("Location: kayip_yonetimi.php");
    exit();
}

// Form gönderildiğinde kayıp kişi bilgilerini güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = $_POST['ad_soyad'];
    $yas = $_POST['yas'];
    $kaybolma_tarihi = $_POST['kaybolma_tarihi'];
    $konum_metni = $_POST['konum_metni'];

    // Kayıp kişi bilgilerini güncelle
    $query = "UPDATE kayip_ilani SET ad_soyad = :ad_soyad, yas = :yas, kaybolma_tarihi = :kaybolma_tarihi, konum_metni = :konum_metni WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'ad_soyad' => $ad_soyad,
        'yas' => $yas,
        'kaybolma_tarihi' => $kaybolma_tarihi,
        'konum_metni' => $konum_metni,
        'id' => $kayip_id
    ]);

    // Başarı mesajı ve yönlendirme
    $_SESSION['mesaj'] = "Kayıp kişi bilgileri başarıyla güncellendi.";
    header("Location: kayip_yonetimi.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıp Kişi Düzenle</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1>Kayıp Kişi Düzenle</h1>
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
            <h2>Kayıp Kişi Bilgilerini Düzenle</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="ad_soyad">Ad Soyad</label>
                    <input type="text" id="ad_soyad" name="ad_soyad" value="<?= htmlspecialchars($kayip['ad_soyad']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="yas">Yaş</label>
                    <input type="number" id="yas" name="yas" value="<?= htmlspecialchars($kayip['yas']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="kaybolma_tarihi">Kaybolma Tarihi</label>
                    <input type="date" id="kaybolma_tarihi" name="kaybolma_tarihi" value="<?= htmlspecialchars($kayip['kaybolma_tarihi']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="konum_metni">Konum</label>
                    <textarea id="konum_metni" name="konum_metni" required><?= htmlspecialchars($kayip['konum_metni']) ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="save">Kaydet</button>
                    <a href="kayip_yonetimi.php" class="cancel">İptal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>