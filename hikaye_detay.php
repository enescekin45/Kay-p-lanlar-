<?php
session_start();
require 'config/db.php'; // Veritabanı bağlantısını dahil et

// Hikaye ID'sini al
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$hikaye_id = $_GET['id'];

try {
    // Hikaye bilgilerini çek
    $stmt = $pdo->prepare("
        SELECT h.*, u.kullanici_adi 
        FROM hikayeler h
        JOIN users u ON h.kullanici_id = u.id
        WHERE h.id = ?
    ");
    $stmt->execute([$hikaye_id]);
    $hikaye = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$hikaye) {
        die("Hikaye bulunamadı!");
    }
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hikaye Detayı</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .hikaye-resim img {
            width: 100%;
            border-radius: 10px;
        }
        .hikaye-detay {
            margin-top: 20px;
        }
        .hikaye-detay h2 {
            margin-bottom: 10px;
        }
        .kullanici-bilgi {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }
        .kullanici-bilgi img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .kullanici-bilgi p {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .geri-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .geri-btn:hover {
            background: #0056b3;
        }
        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            margin: 20px;
            border-radius: 5px;
        }
        .user-profile {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .user-profile a {
            color: #333;
            text-decoration: none;
            margin-right: 15px;
        }
        .user-profile a:hover {
            color: #000;
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="index.php" class="active"><i class="fas fa-home"></i> Ev</a></li>
        <li><a href="harita.php"><i class="fas fa-map-marked"></i> Harita</a></li>
        <li><a href="hakkimizda.php"><i class="fas fa-info-circle"></i> Hakkımızda</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="kayip_ekle.php"><i class="fas fa-plus-circle"></i> Yeni İlan Ekle</a></li>
            <li class="user-profile">
                <a href="profile.php">
                    <?php if (!empty($_SESSION['profil_resmi'])): ?>
                        <!-- Profil resmi varsa göster -->
                        <img src="<?= htmlspecialchars($_SESSION['profil_resmi']) ?>" alt="Profil Resmi" class="profile-icon">
                    <?php else: ?>
                        <!-- Profil resmi yoksa varsayılan ikon göster -->
                        <i class="fas fa-user-circle"></i>
                    <?php endif; ?>
                    Profil
                </a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
            </li>
        <?php else: ?>
            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Giriş</a></li>
            <li><a href="register.php"><i class="fas fa-user-plus"></i> Kayıt Ol</a></li>
        <?php endif; ?>
    </ul>
</nav>
<div class="container">
    <div class="hikaye-resim">
        <?php
        $resim_yolu = $hikaye['dosya_yolu'];
        if (file_exists($resim_yolu)) {
            echo '<img src="' . $resim_yolu . '" alt="Hikaye Resmi">';
        } else {
            echo '<p>Resim bulunamadı!</p>';
        }
        ?>
    </div>
    <div class="hikaye-detay">
        <h2>Hikaye Detayı</h2>
        <p>Bu hikaye, <?= htmlspecialchars($hikaye['kullanici_adi']) ?> tarafından paylaşıldı.</p>
    </div>
    <div class="kullanici-bilgi">
        <?php
        $profil_resmi = "uploads/default_profile.jpg";
        if (file_exists($profil_resmi)) {
            echo '<img src="' . $profil_resmi . '" alt="Profil Resmi">';
        } else {
            echo '<p>Profil resmi bulunamadı!</p>';
        }
        ?>
        <p><?= htmlspecialchars($hikaye['kullanici_adi']) ?></p>
    </div>
    <a href="index.php" class="geri-btn"><i class="fas fa-arrow-left"></i> Geri Dön</a>
</div>

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4>Kayıp İlanları Platformu</h4>
            <p>Kaybolan sevdiklerinizi bulmak için güvenli iletişim ağı.</p>
            <div class="contact-info">
                <p><i class="fas fa-envelope"></i> iletisim@kayipilanlari.com</p>
                <p><i class="fas fa-phone"></i> 0850 123 45 67</p>
            </div>
        </div>
        <div class="footer-section">
            <h4>Hızlı Erişim</h4>
            <ul>
                <li><a href="index.php"><i class="fas fa-chevron-right"></i> Anasayfa</a></li>
                <li><a href="harita.php"><i class="fas fa-chevron-right"></i> Harita</a></li>
                <li><a href="hakkimizda.php"><i class="fas fa-chevron-right"></i> Hakkımızda</a></li>
                <li><a href="gizlilik.php"><i class="fas fa-chevron-right"></i> Gizlilik Politikası</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Sosyal Ağlar</h4>
            <div class="social-links">
    <a href="#"><i class="fab fa-facebook"></i></a>
    <a href="#">
        <!-- "X" ikonu için özel SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817-5.957 6.817H2.25l7.73-8.835L1.5 2.25h7.914l4.708 6.231 5.372-6.231zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/>
        </svg>
    </a>
    <a href="#"><i class="fab fa-instagram"></i></a>
    <a href="#"><i class="fab fa-youtube"></i></a>
</div>
        </div>
    </div>
    <div class="copyright">
        <p>© 2025 Kayıp İlanları. Tüm hakları saklıdır.</p>
        <p>Developed by [Şirket Adı]</p>
    </div>
</footer>

</body>
</html>