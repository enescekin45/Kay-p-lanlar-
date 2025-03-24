<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}

require '../config/db.php'; // Veritabanı bağlantısını dahil et

// Hakkımızda bilgilerini veritabanından çek
$query = "SELECT * FROM hakkimizda LIMIT 1";
$stmt = $pdo->query($query);
if (!$stmt) {
    die("Veritabanı sorgu hatası: " . $pdo->errorInfo()[2]); // Hata mesajını göster
}
$hakkimizda = $stmt->fetch(PDO::FETCH_ASSOC);

// Eğer hakkımızda bilgisi yoksa, varsayılan değerlerle doldur
if (!$hakkimizda) {
    $hakkimizda = [
        'baslik' => 'Hakkımızda',
        'icerik' => 'Buraya hakkımızda bilgileri gelecek.'
    ];
}

// Yönetici girişi yapıldıysa ve form gönderildiyse, hakkımızda bilgilerini güncelle
if (isset($_SESSION['yonetici_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $baslik = htmlspecialchars($_POST['baslik']);
    $icerik = htmlspecialchars($_POST['icerik']);

    try {
        // Hakkımızda bilgilerini güncelle
        $query = "UPDATE hakkimizda SET baslik = :baslik, icerik = :icerik WHERE id = 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'baslik' => $baslik,
            'icerik' => $icerik
        ]);

        // Başarı mesajı ve sayfayı yenile
        $_SESSION['mesaj'] = "Hakkımızda bilgileri başarıyla güncellendi.";
        header("Location: hakkımız.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['mesaj'] = "Veritabanı hatası: " . $e->getMessage(); // Hata mesajını göster
        header("Location: hakkımız.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli - Hakkımızda</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1> Hakkımız</h1>
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
                    <a href="hakkımızda.php">
                        <i class="fas fa-info-circle"></i>
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
            <h2>Hakkımızda</h2>
            <div class="container">
                <h1><?= htmlspecialchars($hakkimizda['baslik']) ?></h1>
                <div class="content">
                    <?= nl2br(htmlspecialchars($hakkimizda['icerik'])) ?>
                </div>

                <!-- Yönetici girişi yapıldıysa, düzenleme formunu göster -->
                <?php if (isset($_SESSION['yonetici_id'])): ?>
                    <div class="edit-form">
                        <h2>Hakkımızda Bilgilerini Düzenle</h2>
                        <?php if (isset($_SESSION['mesaj'])): ?>
                            <div class="message success"><?= $_SESSION['mesaj'] ?></div>
                            <?php unset($_SESSION['mesaj']); ?>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="baslik">Başlık:</label>
                                <input type="text" id="baslik" name="baslik" value="<?= htmlspecialchars($hakkimizda['baslik']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="icerik">İçerik:</label>
                                <textarea id="icerik" name="icerik" rows="10" required><?= htmlspecialchars($hakkimizda['icerik']) ?></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="save">Kaydet</button>
                                <a href="index.php" class="cancel">İptal</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>