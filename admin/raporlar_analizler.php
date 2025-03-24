<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}

require '../config/db.php'; // Veritabanı bağlantısı

// İstatistikleri çek
try {
    // Toplam kullanıcı sayısı
    $query = "SELECT COUNT(*) as total_users FROM users";
    $stmt = $pdo->query($query);
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Toplam hikaye sayısı
    $query = "SELECT COUNT(*) as total_hikayeler FROM hikayeler";
    $stmt = $pdo->query($query);
    $total_hikayeler = $stmt->fetch(PDO::FETCH_ASSOC)['total_hikayeler'];

    // Toplam kayıp ilanı sayısı
    $query = "SELECT COUNT(*) as total_kayip FROM kayip_ilani";
    $stmt = $pdo->query($query);
    $total_kayip = $stmt->fetch(PDO::FETCH_ASSOC)['total_kayip'];

    // Son 7 günde eklenen hikayeler
    $query = "SELECT COUNT(*) as last_7_days_hikayeler FROM hikayeler WHERE olusturulma_tarihi >= NOW() - INTERVAL 7 DAY";
    $stmt = $pdo->query($query);
    $last_7_days_hikayeler = $stmt->fetch(PDO::FETCH_ASSOC)['last_7_days_hikayeler'];

    // Son 30 günde kayıt olan kullanıcılar
    $query = "SELECT COUNT(*) as last_30_days_users FROM users WHERE kayit_tarihi >= NOW() - INTERVAL 30 DAY";
    $stmt = $pdo->query($query);
    $last_30_days_users = $stmt->fetch(PDO::FETCH_ASSOC)['last_30_days_users'];
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporlar ve Analizler</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1>Raporlar ve Analizler</h1>
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
            <h2>Raporlar ve Analizler</h2>

            <!-- İstatistikler -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Toplam Kullanıcı</h3>
                    <p><?= $total_users ?></p>
                </div>
                <div class="stat-card">
                    <h3>Toplam Hikaye</h3>
                    <p><?= $total_hikayeler ?></p>
                </div>
                <div class="stat-card">
                    <h3>Toplam Kayıp İlanı</h3>
                    <p><?= $total_kayip ?></p>
                </div>
                <div class="stat-card">
                    <h3>Son 7 Günde Eklenen Hikayeler</h3>
                    <p><?= $last_7_days_hikayeler ?></p>
                </div>
                <div class="stat-card">
                    <h3>Son 30 Günde Kayıt Olan Kullanıcılar</h3>
                    <p><?= $last_30_days_users ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>