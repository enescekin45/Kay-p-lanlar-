<?php
session_start();
require 'config/db.php'; // Veritabanı bağlantısını dahil et

// Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Hikaye ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hikaye_resmi'])) {
    $kullanici_id = $_SESSION['user_id'];

    // Klasör yolu
    $klasor_yolu = "uploads/hikayeler/";

    // Klasör yoksa oluştur
    if (!file_exists($klasor_yolu)) {
        mkdir($klasor_yolu, 0777, true); // 0777: Tam erişim izni
    }

    // Dosya yolu
    $dosya_adi = basename($_FILES['hikaye_resmi']['name']);
    $dosya_yolu = $klasor_yolu . $dosya_adi;

    // Dosya yükleme işlemi
    if (move_uploaded_file($_FILES['hikaye_resmi']['tmp_name'], $dosya_yolu)) {
        try {
            // Hikayeyi veritabanına kaydet
            $stmt = $pdo->prepare("
                INSERT INTO hikayeler (kullanici_id, dosya_yolu)
                VALUES (?, ?)
            ");
            $stmt->execute([$kullanici_id, $dosya_yolu]);

            // Başarılı mesajı
            $_SESSION['success'] = "Hikaye başarıyla paylaşıldı!";
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            die("Hikaye kaydedilirken bir hata oluştu: " . $e->getMessage());
        }
    } else {
        $error = "Dosya yüklenirken bir hata oluştu. Lütfen tekrar deneyin.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hikaye Ekle</title>
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
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="file"],
        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .form-group button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 20px;
            text-align: center;
        }
        .geri-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .geri-btn:hover {
            background: #5a6268;
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
        .uyari-mesaji {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
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
    <h1><i class="fas fa-plus-circle"></i> Hikaye Ekle</h1>

    <!-- Uyarı Mesajı -->
    <div class="uyari-mesaji">
        <p><i class="fas fa-exclamation-triangle"></i> Görenler hemen <strong>155</strong>'i arasın!</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="kayboldugu_yer">Kaybolduğu Yer:</label>
            <input type="text" name="kayboldugu_yer" id="kayboldugu_yer" placeholder="Örneğin: Ankara, Kızılay" required>
        </div>
        <div class="form-group">
            <label for="hikaye_resmi">Hikaye Resmi Seçin:</label>
            <input type="file" name="hikaye_resmi" id="hikaye_resmi" accept="image/*" required>
        </div>
        <div class="form-group">
            <button type="submit"><i class="fas fa-share"></i> Hikayeyi Paylaş</button>
        </div>
    </form>

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