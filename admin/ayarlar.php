<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}

require '../config/db.php'; // Veritabanı bağlantısı
require '../config/security.php'; // Güvenlik fonksiyonları

$message = '';

// Ayarları güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ana_baslik = sanitizeInput($_POST['ana_baslik']);
    $ana_aciklama = sanitizeInput($_POST['ana_aciklama']);
    $iletisim_adres = sanitizeInput($_POST['iletisim_adres']);
    $iletisim_telefon = sanitizeInput($_POST['iletisim_telefon']);
    $iletisim_email = sanitizeInput($_POST['iletisim_email']);
    $footer_metin = sanitizeInput($_POST['footer_metin']);

    try {
        $stmt = $pdo->prepare("
            UPDATE site_ayarlari 
            SET ana_baslik = :ana_baslik, 
                ana_aciklama = :ana_aciklama, 
                iletisim_adres = :iletisim_adres, 
                iletisim_telefon = :iletisim_telefon, 
                iletisim_email = :iletisim_email, 
                footer_metin = :footer_metin 
            WHERE id = 1
        ");
        $stmt->execute([
            ':ana_baslik' => $ana_baslik,
            ':ana_aciklama' => $ana_aciklama,
            ':iletisim_adres' => $iletisim_adres,
            ':iletisim_telefon' => $iletisim_telefon,
            ':iletisim_email' => $iletisim_email,
            ':footer_metin' => $footer_metin
        ]);
        $message = "Ayarlar başarıyla güncellendi!";
    } catch (PDOException $e) {
        $message = "Ayarlar güncellenirken bir hata oluştu: " . $e->getMessage();
    }
}

// Ayarları çek
try {
    $stmt = $pdo->query("SELECT * FROM site_ayarlari WHERE id = 1");
    $ayarlar = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1>Ayarlar</h1>
        <div class="admin-info">
            <span>Giriş Yapan: <?= htmlspecialchars($_SESSION['yonetici_adi']) ?></span>
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-btn">Çıkış Yap</button>
            </form>
        </div>
    </div>

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
            <h2>Site Ayarları</h2>

            <!-- Mesaj Göster -->
            <?php if ($message): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>

            <!-- Ayarlar Formu -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="ana_baslik">Ana Başlık:</label>
                    <input type="text" name="ana_baslik" id="ana_baslik" value="<?= htmlspecialchars($ayarlar['ana_baslik']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="ana_aciklama">Ana Açıklama:</label>
                    <textarea name="ana_aciklama" id="ana_aciklama" rows="5" required><?= htmlspecialchars($ayarlar['ana_aciklama']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="iletisim_adres">İletişim Adresi:</label>
                    <input type="text" name="iletisim_adres" id="iletisim_adres" value="<?= htmlspecialchars($ayarlar['iletisim_adres']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="iletisim_telefon">İletişim Telefonu:</label>
                    <input type="text" name="iletisim_telefon" id="iletisim_telefon" value="<?= htmlspecialchars($ayarlar['iletisim_telefon']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="iletisim_email">İletişim E-posta:</label>
                    <input type="email" name="iletisim_email" id="iletisim_email" value="<?= htmlspecialchars($ayarlar['iletisim_email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="footer_metin">Footer Metni:</label>
                    <textarea name="footer_metin" id="footer_metin" rows="3" required><?= htmlspecialchars($ayarlar['footer_metin']) ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>