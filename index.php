<?php
session_start();
require 'config/db.php'; // Veritabanı bağlantısını dahil et

// Hata mesajlarını görünür yap
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Kayıp ilanlarını çek
    $kayiplar = $pdo->query(
        "SELECT *, DATE_FORMAT(kaybolma_tarihi, '%d.%m.%Y') as tarih FROM kayip_ilani ORDER BY kaybolma_tarihi DESC"
    )->fetchAll(PDO::FETCH_ASSOC);

    // Hikayeleri çek
    $hikayeler = $pdo->query(
        "SELECT h.*, u.kullanici_adi, u.profil_resmi 
         FROM hikayeler h
         JOIN users u ON h.kullanici_id = u.id
         ORDER BY h.olusturulma_tarihi DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıp İlanları</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Hikayeler için stil */
        .hikaye-container {
            display: flex;
            gap: 15px;
            padding: 20px;
            overflow-x: auto;
            margin-bottom: 30px;
        }
        .hikaye-karti {
            flex: 0 0 auto;
            width: 100px;
            text-align: center;
            cursor: pointer;
            position: relative;
        }
        .hikaye-karti img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }
        .hikaye-karti p {
            margin-top: 5px;
            font-size: 14px;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .hikaye-ekle {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px dashed #007bff;
            cursor: pointer;
        }
        .hikaye-ekle i {
            font-size: 24px;
            color: #007bff;
        }

        /* Diğer stiller */
        .ilan-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .ilan-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .ilan-karti {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .ilan-karti:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .ilan-resim img {
            width: 100%;
            height: 250px;
            object-fit: cover;
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
        <?php endif; ?>
    </ul>
</nav>

<div class="container">
    <h1>Kayıp İlanları</h1>
    
    <!-- Hikayeler Bölümü -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="hikaye-container">
            <!-- Hikaye Ekle Butonu -->
            <div class="hikaye-karti hikaye-ekle" onclick="window.location.href='hikaye_ekle.php'">
                <i class="fas fa-plus"></i>
                <p>Hikaye Ekle</p>
            </div>

            <!-- Kullanıcı Hikayeleri -->
            <?php foreach($hikayeler as $hikaye): ?>
                <div class="hikaye-karti" onclick="window.location.href='hikaye_detay.php?id=<?= $hikaye['id'] ?>'">
                    <?php
                    $profil_resmi = "uploads/" . $hikaye['profil_resmi'];
                    if (file_exists($profil_resmi)) {
                        echo '<img src="' . $profil_resmi . '" alt="' . htmlspecialchars($hikaye['kullanici_adi']) . '">';
                    } else {
                        echo '<img src="uploads/default_profile.jpg" alt="Profil Resmi">';
                    }
                    ?>
                    <p><?= htmlspecialchars($hikaye['kullanici_adi']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Kayıp İlanları Bölümü -->
    <?php if (count($kayiplar) > 0): ?>
        <div class="ilan-container">
            <?php foreach ($kayiplar as $kayip): ?>
                <a href="ilan_detay.php?id=<?= $kayip['id'] ?>" class="ilan-link">
                    <div class="ilan-karti">
                        <div class="ilan-resim">
                            <?php
                            $resim_yolu = "uploads/" . $kayip['fotograf'];
                            if (file_exists($resim_yolu)) {
                                echo '<img src="' . $resim_yolu . '" alt="' . htmlspecialchars($kayip['ad_soyad']) . '">';
                            } else {
                                echo '<p>Resim bulunamadı!</p>';
                            }
                            ?>
                        </div>
                        <div class="ilan-detay">
                            <h3><?= htmlspecialchars($kayip['ad_soyad']) ?></h3>
                            <p><i class="fas fa-birthday-cake"></i> <?= $kayip['yas'] ?> Yaş</p>
                            <p><i class="fas fa-calendar-day"></i> <?= $kayip['tarih'] ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($kayip['konum_metni']) ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-data">Henüz kayıp ilanı bulunmamaktadır. 😔</div>
    <?php endif; ?>
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
