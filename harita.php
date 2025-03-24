<?php
session_start(); // Oturumu başlat
require 'config/db.php'; // Veritabanı bağlantısını dahil et

// Hata mesajlarını görünür yap
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harita</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        #map {
            height: 500px;
            width: 100%;
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
        <li><a href="index.php"><i class="fas fa-home"></i> Ev</a></li>
        <li><a href="harita.php"><i class="fas fa-map-marked"></i> Harita</a></li>
        <li><a href="hakkimizda.php"><i class="fas fa-info-circle"></i> Hakkımızda</a></li>
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
    </ul>
</nav>

    <h3>Harita</h3>
    <div id="map"></div>
    

    <script>
        function initMap() {
            var location = { lat: 39.92077, lng: 32.85411 }; // Örnek koordinatlar (Ankara, Türkiye)
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: location
            });

            var marker = new google.maps.Marker({
                position: location,
                map: map,
                title: "Buradasınız"
            });
        }
    </script>

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

    <script async defer 
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCT7G_qtqVSF-NhgbvZ01Qo8dhWN4ZojOI&callback=initMap">
    </script>

</body>
</html>
