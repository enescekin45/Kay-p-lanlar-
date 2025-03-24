<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}

require '../config/db.php'; // Veritabanı bağlantısı

// Kayıp kişi silme işlemi
if (isset($_GET['sil'])) {
    $kayip_id = $_GET['sil'];
    $query = "DELETE FROM kayip_kisiler WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $kayip_id]);
    header("Location: kayip_yonetimi.php"); // Sayfayı yeniden yönlendir
    exit();
}

// Kayıp kişileri veritabanından çek
$query = "SELECT * FROM kayip_ilani";
$stmt = $pdo->query($query);
$kayip_kisiler = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıp Yönetimi</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1>Kayıp Yönetimi</h1>
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
            <h2>Kayıp Kişi Listesi</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad Soyad</th>
                        <th>Yaş</th>
                        <th>Kaybolma Tarihi</th>
                        <th>Konum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($kayip_kisiler)): ?>
                        <?php foreach ($kayip_kisiler as $kayip): ?>
                            <tr>
                                <td><?= htmlspecialchars($kayip['id']) ?></td>
                                <td><?= htmlspecialchars($kayip['ad_soyad']) ?></td>
                                <td><?= htmlspecialchars($kayip['yas']) ?></td>
                                <td><?= htmlspecialchars($kayip['kaybolma_tarihi']) ?></td>
                                <td><?= htmlspecialchars($kayip['konum_metni']) ?></td>
                                <td class="action-buttons">
                                    <a href="kayip_duzenle.php?id=<?= $kayip['id'] ?>" class="edit">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </a>
                                    <a href="kayip_yonetimi.php?sil=<?= $kayip['id'] ?>" class="delete" onclick="return confirm('Bu kayıp kişiyi silmek istediğinizden emin misiniz?');">
                                        <i class="fas fa-trash"></i> Sil
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Kayıp kişi bulunamadı.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>