<?php
session_start();
require __DIR__ . '/../config/db.php'; // Göreli yolu düzeltin
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
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hakkımızda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #0d6efd;
            --hover-blue: #0b5ed7;
            --dark-bg: #212529;
        }
        
        /* Navbar Özelleştirme */
        .navbar {
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-blue) !important;
        }
        
        .nav-link {
            color: var(--dark-bg) !important;
            font-weight: 500;
            margin: 0 15px;
            transition: all 0.3s;
        }
        
        .nav-link.active {
            color: var(--primary-blue) !important;
            border-bottom: 2px solid var(--primary-blue);
        }
        
        .nav-link:hover {
            color: var(--hover-blue) !important;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 4rem 0;
            border-radius: 15px;
            margin-top: 2rem;
        }
        
        /* Buton Özelleştirme */
        .btn-custom {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            transition: transform 0.2s;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
        }

        /* Footer Tasarımı */
        .site-footer {
            background: var(--dark-bg);
            color: white;
            padding: 3rem 0;
            margin-top: 4rem;
        }
        
        .footer-section h4 {
            color: var(--primary-blue);
            margin-bottom: 1.5rem;
        }
        
        .social-links a {
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            transition: color 0.3s;
        }
        
        .social-links a:hover {
            color: var(--primary-blue);
        }
        
        .copyright {
            background: rgba(0,0,0,0.2);
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
<header class="bg-white">
    <div class="container">
        <nav class="navbar navbar-expand-lg">
            <a class="navbar-brand" href="../on.php">Kayıpİlan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="../on.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../önkısımı/hakkimizda.php">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../login.php">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../register.php">Kayıt Ol</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../admin/yonetici_giris.php">Yönetici</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
      
<div class="container mt-5">
    <h2><?= htmlspecialchars($hakkimizda['baslik']) ?></h2>
    <p class="mt-3">
        <?= nl2br(htmlspecialchars($hakkimizda['icerik'])) ?>
    </p>
</div>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="footer-section">
                    <h4>İletişim</h4>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i> İstanbul, Türkiye</li>
                        <li><i class="fas fa-phone me-2"></i> 0850 123 45 67</li>
                        <li><i class="fas fa-envelope me-2"></i> iletisim@kayipilan.com</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="footer-section">
                    <h4>Bağlantılar</h4>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white text-decoration-none">Ana Sayfa</a></li>
                        <li><a href="hakkimizda.php" class="text-white text-decoration-none">Hakkımızda</a></li>
                        <li><a href="gizlilik.php" class="text-white text-decoration-none">Gizlilik Politikası</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="footer-section">
                    <h4>Sosyal Medya</h4>
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
        </div>
        
        <div class="copyright mt-5">
            <p class="mb-0">© 2025 Kayıpİlan. Tüm hakları saklıdır.</p>
            <p class="mb-0">Developed with <i class="fas fa-heart text-danger"></i> by [Şirket Adı]</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>