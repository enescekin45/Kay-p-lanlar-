<?php
session_start();
require_once "config/db.php"; // Veritabanı bağlantısını dahil et

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id"); // $pdo kullanılıyor
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Kullanıcı bulunamadı!");
    }

    $_SESSION['profil_resmi'] = $user['profil_resmi'];
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $ad_soyad = htmlspecialchars(trim($_POST['ad_soyad']));
        $eposta = filter_var(trim($_POST['eposta']), FILTER_VALIDATE_EMAIL);
        $kullanici_adi = htmlspecialchars(trim($_POST['kullanici_adi']));
        
        if (!$eposta) {
            throw new Exception("Geçersiz e-posta formatı!");
        }

        // Profil resmi işlemleri
        $profil_resmi = $user['profil_resmi'];
        
        if (!empty($_FILES['profil_resmi']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($_FILES['profil_resmi']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed)) {
                throw new Exception("Sadece JPG, JPEG, PNG & GIF dosyaları yüklenebilir!");
            }
            
            if ($_FILES['profil_resmi']['size'] > 2000000) {
                throw new Exception("Dosya boyutu 2MB'ı geçemez!");
            }
            
            $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profil/';
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($_FILES['profil_resmi']['tmp_name'], $upload_path)) {
                // Eski resmi sil
                if ($user['profil_resmi'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $user['profil_resmi'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $user['profil_resmi']);
                }
                
                $profil_resmi = '/uploads/profil/' . $new_file_name;
                $_SESSION['profil_resmi'] = $profil_resmi;
            } else {
                throw new Exception("Dosya yüklenemedi: " . $_FILES['profil_resmi']['error']);
            }
        }

        // Veritabanı güncelleme
        $update_stmt = $pdo->prepare("
            UPDATE users 
            SET ad_soyad=:ad_soyad, eposta=:eposta, 
                kullanici_adi=:kullanici_adi, profil_resmi=:profil_resmi 
            WHERE id=:user_id
        ");
        
        $update_stmt->execute([
            ':ad_soyad' => $ad_soyad,
            ':eposta' => $eposta,
            ':kullanici_adi' => $kullanici_adi,
            ':profil_resmi' => $profil_resmi,
            ':user_id' => $user_id
        ]);

        // Yeni verileri çek
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['success'] = "Profil başarıyla güncellendi!";
        header("Location: profile.php?t=" . time());
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Yönetimi</title>
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
    .profile-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
        border: 2px solid #007bff;
    }
    .profil-resim {
        text-align: center;
        margin-bottom: 20px;
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
       /* Stil tanımlamaları aynı */
       .profil-resim img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }
    .profil-resim:hover img {
        transform: scale(1.05);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
    }
    .profil-resim input[type="file"] {
        display: none;
    }
    .profil-resim label {
        display: block;
        cursor: pointer;
        position: relative;
    }
    .upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%; /* Overlay'i yuvarlak yapar */
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        text-align: center;
    }
    .profil-resim:hover .upload-overlay {
        opacity: 1;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="tel"] {
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
    .success-msg {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        margin: 20px;
        border-radius: 5px;
        text-align: center;
    }
    .user-profile {
        display: flex;
        align-items: center;
        margin-left: auto;
    }
    .user-profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%; /* Profil resmini yuvarlak yapar */
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
    <script>
     function previewImage(event) {
        const reader = new FileReader();
        const image = document.getElementById('profile-image');
        
        reader.onload = function() {
            if (reader.readyState == 2) {
                image.src = reader.result;
            }
        }
        
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
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
                        <img src="<?= $_SESSION['profil_resmi'] ?>" alt="Profil Resmi" class="profile-icon">
                    <?php else: ?>
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
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-msg"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="profil-resim">
            <label for="profil-resmi">
                <img src="<?= !empty($user['profil_resmi']) ? $user['profil_resmi'] . '?t=' . time() : '/default_profile.jpg' ?>" 
                     id="profile-image" 
                     alt="Profil Resmi">
                <div class="upload-overlay">Resmi Değiştir</div>
            </label>
            <input type="file" id="profil-resmi" name="profil_resmi" accept="image/*" onchange="previewImage(event)">
        </div>



        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Kullanıcı Adı:</label>
                <input type="text" name="kullanici_adi" value="<?= htmlspecialchars($user['kullanici_adi']) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Ad Soyad:</label>
                <input type="text" name="ad_soyad" value="<?= htmlspecialchars($user['ad_soyad']) ?>" required>
            </div>
            <div class="form-group">
                <label>E-posta:</label>
                <input type="email" name="eposta" value="<?= htmlspecialchars($user['eposta']) ?>" required>
            </div>
            <button type="submit">Güncelle</button>
        </form>
    </div>
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