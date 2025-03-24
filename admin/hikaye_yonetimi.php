<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}

require '../config/db.php'; // Veritabanı bağlantısı

// Hikayeleri listele
try {
    $hikayeler = $pdo->query("
        SELECT h.*, u.kullanici_adi 
        FROM hikayeler h
        JOIN users u ON h.kullanici_id = u.id
        ORDER BY h.olusturulma_tarihi DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

// Hikaye ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ekle'])) {
    $kullanici_id = $_SESSION['yonetici_id'];
    $dosya_yolu = ''; // Dosya yolu yükleme işlemi aşağıda yapılacak

    // Dosya yükleme
    if (!empty($_FILES['dosya']['name'])) {
        $hedef_dizin = __DIR__ . '/uploads/';
        if (!file_exists($hedef_dizin)) {
            mkdir($hedef_dizin, 0777, true);
        }
        $dosya_adi = uniqid() . '_' . basename($_FILES['dosya']['name']);
        $hedef_yol = $hedef_dizin . $dosya_adi;

        if (move_uploaded_file($_FILES['dosya']['tmp_name'], $hedef_yol)) {
            $dosya_yolu = '/uploads/' . $dosya_adi;
        } else {
            die("Dosya yüklenirken bir hata oluştu!");
        }
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO hikayeler (kullanici_id, dosya_yolu)
            VALUES (:kullanici_id, :dosya_yolu)
        ");
        $stmt->execute([
            ':kullanici_id' => $kullanici_id,
            ':dosya_yolu' => $dosya_yolu
        ]);
        header("Location: hikaye_yonetimi.php"); // Sayfayı yeniden yükle
        exit();
    } catch (PDOException $e) {
        die("Veritabanı hatası: " . $e->getMessage());
    }
}

// Hikaye silme
if (isset($_GET['sil'])) {
    $id = intval($_GET['sil']);

    try {
        $stmt = $pdo->prepare("DELETE FROM hikayeler WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: hikaye_yonetimi.php"); // Sayfayı yeniden yükle
        exit();
    } catch (PDOException $e) {
        die("Veritabanı hatası: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hikaye Yönetimi</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1>Hikaye Yönetimi</h1>
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
            <h2>Hikaye Yönetimi</h2>

            <!-- Hikaye Ekleme Formu -->
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="file-input">
                    <label for="dosya">Dosya Seçin (Fotoğraf/Video):</label>
                    <input type="file" name="dosya" id="dosya" required>
                </div>
                <button type="submit" name="ekle" class="btn btn-primary">Hikaye Ekle</button>
            </form>

            <!-- Hikaye Listesi -->
            <h3>Hikaye Listesi</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı Adı</th>
                        <th>Dosya Yolu</th>
                        <th>Oluşturulma Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hikayeler as $hikaye): ?>
                        <tr>
                            <td><?= $hikaye['id'] ?></td>
                            <td><?= $hikaye['kullanici_adi'] ?></td>
                            <td>
                                <?php if (pathinfo($hikaye['dosya_yolu'], PATHINFO_EXTENSION) === 'mp4'): ?>
                                    <video controls width="150">
                                        <source src="<?= $hikaye['dosya_yolu'] ?>" type="video/mp4">
                                        Tarayıcınız video etiketini desteklemiyor.
                                    </video>
                                <?php else: ?>
                                    <img src="<?= $hikaye['dosya_yolu'] ?>" alt="Hikaye Fotoğrafı" width="150">
                                <?php endif; ?>
                            </td>
                            <td><?= $hikaye['olusturulma_tarihi'] ?></td>
                            <td>
                                <a href="?sil=<?= $hikaye['id'] ?>" class="btn btn-danger" onclick="return confirm('Bu hikayeyi silmek istediğinize emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>