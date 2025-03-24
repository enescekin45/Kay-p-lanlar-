<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}
require '../config/db.php'; // Veritabanı bağlantısı
require '../config/security.php';

// Toplam kullanıcı sayısını çek
$query = "SELECT COUNT(*) as total_users FROM users";
$stmt = $pdo->query($query);
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Toplam kayıp kişi sayısını çek
$query = "SELECT COUNT(*) as total_kayip FROM users"; // users tablosundaki kayıp kişileri say
$stmt = $pdo->query($query);
$total_kayip = $stmt->fetch(PDO::FETCH_ASSOC)['total_kayip'];

// Aktif kullanıcı sayısını çek (örneğin, son 30 gün içinde kayıt olanlar)
$query = "SELECT COUNT(*) as active_users FROM users WHERE kayit_tarihi >= NOW() - INTERVAL 30 DAY";
$stmt = $pdo->query($query);
$active_users = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'];

// Yeni kayıp kişi sayısını çek (örneğin, son 7 gün içinde eklenenler)
$query = "SELECT COUNT(*) as new_kayip FROM users WHERE kayit_tarihi >= NOW() - INTERVAL 7 DAY";
$stmt = $pdo->query($query);
$new_kayip = $stmt->fetch(PDO::FETCH_ASSOC)['new_kayip'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1>Yönetici Paneli</h1>
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
            <h2>Hoş Geldiniz, <?= htmlspecialchars($_SESSION['yonetici_adi']) ?></h2>
            <p>Yönetici paneli üzerinden sisteminizi yönetebilirsiniz.</p>

            <!-- İstatistikler -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Toplam Kullanıcı</h3>
                    <p><?= $total_users ?></p>
                </div>
                <div class="stat-card">
                    <h3>Toplam Kayıp Kişi</h3>
                    <p><?= $total_kayip ?></p>
                </div>
                <div class="stat-card">
                    <h3>Aktif Kullanıcı</h3>
                    <p><?= $active_users ?></p>
                </div>
                <div class="stat-card">
                    <h3>Yeni Kayıp Kişi</h3>
                    <p><?= $new_kayip ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>