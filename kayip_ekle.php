<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al ve temizle
    $ad_soyad = filter_input(INPUT_POST, 'ad_soyad', FILTER_SANITIZE_STRING);
    $yas = filter_input(INPUT_POST, 'yas', FILTER_SANITIZE_NUMBER_INT);
    $kaybolma_tarihi = $_POST['kaybolma_tarihi'];
    $konum_metni = filter_input(INPUT_POST, 'konum_metni', FILTER_SANITIZE_STRING);
    $fotograf = $_FILES['fotograf'];

    // Resim yükleme işlemi
    $hedef_dizin = "uploads/";
    if (!is_dir($hedef_dizin)) {
        mkdir($hedef_dizin, 0755, true);
    }

    // Benzersiz dosya adı oluştur
    $dosya_adi = uniqid() . '_' . basename($fotograf['name']);
    $hedef_dosya = $hedef_dizin . $dosya_adi;

    // Resim format kontrolü
    $izinli_uzantilar = ['jpg', 'jpeg', 'png', 'gif'];
    $dosya_uzantisi = strtolower(pathinfo($hedef_dosya, PATHINFO_EXTENSION));
    
    if (!in_array($dosya_uzantisi, $izinli_uzantilar)) {
        die("Sadece JPG, JPEG, PNG ve GIF dosyaları yüklenebilir!");
    }

    if (move_uploaded_file($fotograf['tmp_name'], $hedef_dosya)) {
        try {
            // SQL sorgusunu düzelt
            $stmt = $conn->prepare(
                "INSERT INTO kayip_ilani 
                (ad_soyad, yas, kaybolma_tarihi, konum_metni, fotograf, ekleyen_kullanici_id) 
                VALUES (:ad_soyad, :yas, :kaybolma_tarihi, :konum_metni, :fotograf, :ekleyen_kullanici_id)"
            );
            
            $stmt->execute([
                ':ad_soyad' => $ad_soyad,
                ':yas' => $yas,
                ':kaybolma_tarihi' => $kaybolma_tarihi,
                ':konum_metni' => $konum_metni,
                ':fotograf' => $dosya_adi,
                ':ekleyen_kullanici_id' => $_SESSION['user_id']
            ]);
            
            header("Location: index.php?success=1");
            exit();
            
        } catch (PDOException $e) {
            die("Hata: " . $e->getMessage());
        }
    } else {
        die("Resim yüklenirken hata oluştu!");
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni İlan Ekle</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
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
    <h1>Yeni İlan Ekle</h1>
    <form action="kayip_ekle.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="ad_soyad">Ad Soyad:</label>
            <input type="text" id="ad_soyad" name="ad_soyad" required>
        </div>
        <div class="form-group">
            <label for="yas">Yaş:</label>
            <input type="number" id="yas" name="yas" required>
        </div>
        <div class="form-group">
            <label for="kaybolma_tarihi">Kaybolma Tarihi:</label>
            <input type="date" id="kaybolma_tarihi" name="kaybolma_tarihi" required>
        </div>
        <div class="form-group">
            <label for="konum_metni">Kaybolduğu Yer:</label>
            <input type="text" id="konum_metni" name="konum_metni" required>
        </div>
        <div class="form-group">
            <label for="fotograf">Fotoğraf:</label>
            <input type="file" id="fotograf" name="fotograf" required>
        </div>
        <button type="submit" class="btn btn-primary">İlanı Ekle</button>
    </form>
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