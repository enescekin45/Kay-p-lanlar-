<?php
session_start();

// Yönetici giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['yonetici_id'])) {
    header("Location: yonetici_giris.php");
    exit();
}

require '../config/db.php'; // Veritabanı bağlantısı

// Kullanıcı silme işlemi
if (isset($_GET['sil'])) {
    $kullanici_id = $_GET['sil'];
    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $kullanici_id]);
    header("Location: kullanici_yonetimi.php"); // Sayfayı yeniden yönlendir
    exit();
}

// Kullanıcıları veritabanından çek
$query = "SELECT * FROM users";
$stmt = $pdo->query($query);
$kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .admin-header {
            background: #0d6efd;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .admin-header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .admin-header .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-header .admin-info span {
            font-weight: 500;
        }

        .admin-header .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .admin-header .logout-btn:hover {
            background: #c82333;
        }

        .admin-container {
            display: flex;
            margin: 20px;
        }

        .sidebar {
            width: 250px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            margin-right: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 1rem;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: background 0.3s, color 0.3s;
        }

        .sidebar ul li a:hover {
            background: #0d6efd;
            color: white;
        }

        .sidebar ul li a i {
            font-size: 1.2rem;
        }

        .main-content {
            flex: 1;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        .main-content h2 {
            margin-top: 0;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        table th, table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #0d6efd;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-buttons a {
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .action-buttons a.edit {
            background: #28a745;
            color: white;
        }

        .action-buttons a.edit:hover {
            background: #218838;
        }

        .action-buttons a.delete {
            background: #dc3545;
            color: white;
        }

        .action-buttons a.delete:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                margin-right: 0;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <h1>Kullanıcı Yönetimi</h1>
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
            <h2>Kullanıcı Listesi</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad Soyad</th>
                        <th>Kullanıcı Adı</th>
                        <th>E-posta</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kullanicilar as $kullanici): ?>
                        <tr>
                            <td><?= htmlspecialchars($kullanici['id']) ?></td>
                            <td><?= htmlspecialchars($kullanici['ad_soyad']) ?></td>
                            <td><?= htmlspecialchars($kullanici['kullanici_adi']) ?></td>
                            <td><?= htmlspecialchars($kullanici['eposta']) ?></td>
                            <td><?= htmlspecialchars($kullanici['kayit_tarihi']) ?></td>
                            <td class="action-buttons">
                                <a href="kullanici_duzenle.php?id=<?= $kullanici['id'] ?>" class="edit">
                                    <i class="fas fa-edit"></i> Düzenle
                                </a>
                                <a href="kullanici_yonetimi.php?sil=<?= $kullanici['id'] ?>" class="delete" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">
                                    <i class="fas fa-trash"></i> Sil
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>