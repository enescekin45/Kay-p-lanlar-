<?php
session_start();
require 'config/db.php'; // Veritabanı bağlantısını dahil et

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$ilan_id = $_GET['id'];

try {
    // İlan bilgilerini çek
    $stmt = $pdo->prepare("SELECT * FROM kayip_ilani WHERE id = ?");
    $stmt->execute([$ilan_id]);
    $ilan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ilan) die("<div class='error'>İlan bulunamadı!</div>");

    // Yorumları çek
    $yorumlar = $pdo->prepare("
        SELECT y.*, u.kullanici_adi 
        FROM yorumlar y
        JOIN users u ON y.kullanici_id = u.id
        WHERE y.ilan_id = ?
    ");
    $yorumlar->execute([$ilan_id]);
    $yorumlar = $yorumlar->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Yorumlar çekilemedi: " . $e->getMessage());
}

// Yorum gönderimi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['yorum'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    $yorum_metni = filter_input(INPUT_POST, 'yorum', FILTER_SANITIZE_STRING);
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO yorumlar (ilan_id, kullanici_id, yorum_metni)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$ilan_id, $_SESSION['user_id'], $yorum_metni]);
        
        header("Location: ilan_detay.php?id=$ilan_id");
        exit();
        
    } catch (PDOException $e) {
        die("<div class='error'>Yorum eklenemedi: " . $e->getMessage() . "</div>");
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($ilan['ad_soyad']) ?> Detay</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .detay-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .ilan-resim img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        .acil-durum-bildirimi {
            background: #ff4444;
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            display: flex;
            align-items: center;
            gap: 20px;
            animation: pulse 2s infinite;
        }

        .acil-ikon {
            font-size: 2.5rem;
        }

        .acil-icerik h3 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
        }

        .acil-ara-btn {
            background: white;
            color: #ff4444;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .acil-ara-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .yorum-form {
            margin-top: 40px;
        }

        .yorum-form textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            resize: vertical;
        }

        .yorum-form button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .yorum-form button:hover {
            background: #0056b3;
        }

        .yorum-listesi {
            margin-top: 30px;
        }

        .yorum-karti {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
        }

        .yorum-baslik {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .yorum-baslik strong {
            font-size: 1.1rem;
        }

        .yorum-baslik span {
            color: #666;
            font-size: 0.9rem;
        }

        .yorum-icerik {
            color: #444;
            line-height: 1.6;
        }
        .ilan-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .ilan-karti {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
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
                        <img src="<?= htmlspecialchars($_SESSION['profil_resmi']) ?>" alt="Profil Resmi" class="profile-icon">
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

<div class="detay-container">
    <div class="ilan-detay">
        <div class="ilan-resim">
            <img src="uploads/<?= $ilan['fotograf'] ?>" alt="<?= htmlspecialchars($ilan['ad_soyad']) ?>">
        </div>
        <h1><?= htmlspecialchars($ilan['ad_soyad']) ?></h1>
        <p><i class="fas fa-birthday-cake"></i> <?= $ilan['yas'] ?> Yaş</p>
        <p><i class="fas fa-calendar-day"></i> <?= date('d.m.Y', strtotime($ilan['kaybolma_tarihi'])) ?></p>
        <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($ilan['konum_metni']) ?></p>
    </div>

    <!-- Acil Durum Bildirimi -->
    <div class="acil-durum-bildirimi">
        <div class="acil-ikon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="acil-icerik">
            <h3>Acil Durumda 155 Polis İhbar Hattını Arayınız!</h3>
            <a href="tel:155" class="acil-ara-btn">
                <i class="fas fa-phone"></i> Hemen Ara
            </a>
        </div>
    </div>

    <!-- Yorum Formu -->
    <div class="yorum-form">
        <?php if(isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <textarea name="yorum" rows="4" placeholder="Yorumunuzu yazın..." required></textarea>
                <button type="submit" class="btn-primary">Yorum Gönder</button>
            </form>
        <?php else: ?>
            <p>Yorum yapmak için <a href="login.php">giriş yapın</a></p>
        <?php endif; ?>
    </div>

    <!-- Yorum Listesi -->
    <div class="yorum-listesi">
        <h3>Yorumlar (<?= count($yorumlar) ?>)</h3>
        <?php foreach($yorumlar as $yorum): ?>
            <div class="yorum-karti">
                <div class="yorum-baslik">
                    <strong><?= htmlspecialchars($yorum['kullanici_adi']) ?></strong>
                    <span><?= date('d.m.Y H:i', strtotime($yorum['yorum_tarihi'])) ?></span>
                </div>
                <p><?= htmlspecialchars($yorum['yorum_metni']) ?></p>
            </div>
        <?php endforeach; ?>
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