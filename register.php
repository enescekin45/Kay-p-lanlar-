<?php
require 'config/db.php'; // Veritabanı bağlantısını dahil et

$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // $_POST anahtarlarının tanımlı olup olmadığını kontrol et
    $ad_soyad = isset($_POST['ad_soyad']) ? $_POST['ad_soyad'] : '';
    $kullanici_adi = isset($_POST['kullanici_adi']) ? $_POST['kullanici_adi'] : '';
    $eposta = isset($_POST['eposta']) ? $_POST['eposta'] : '';
    $sifre = isset($_POST['sifre']) ? $_POST['sifre'] : '';

    // Form validation
    if (empty($ad_soyad) || empty($kullanici_adi) || empty($eposta) || empty($sifre)) {
        $error = "Lütfen tüm alanları doldurun!";
    } else {
        try {
            // Kullanıcıyı veritabanına ekleyelim
            $sql = "INSERT INTO users (ad_soyad, kullanici_adi, eposta, sifre) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql); // $conn yerine $pdo kullanıldı
            
            if ($stmt->execute([$ad_soyad, $kullanici_adi, $eposta, password_hash($sifre, PASSWORD_DEFAULT)])) {
                $success = "Kayıt başarılı! <a href='login.php' class='text-decoration-none'>Giriş Yap</a>";
            } else {
                $error = "Kayıt başarısız. Lütfen tekrar deneyin.";
            }
        } catch (PDOException $e) {
            $error = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background: url('./img/arka_kayıt.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.9); /* Şeffaf beyaz arka plan */
            padding: 2rem; /* Padding'i biraz küçülttük */
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            backdrop-filter: blur(10px); /* Bulanık arka plan efekti */
        }

        .register-container h2 {
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            font-weight: 500;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem; /* Padding'i küçülttük */
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem; /* Font boyutunu küçülttük */
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #0d6efd;
            outline: none;
        }

        .btn-register {
            width: 100%;
            padding: 0.5rem; /* Padding'i küçülttük */
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem; /* Font boyutunu küçülttük */
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-register:hover {
            background-color: #0b5ed7;
        }

        .login-link {
            margin-top: 1rem;
            color: #555;
        }

        .login-link a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem; /* Font boyutunu küçülttük */
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 1rem;
            font-weight: 500;
            font-size: 0.9rem; /* Font boyutunu küçülttük */
        }

        .success-message {
            color: #28a745;
            margin-bottom: 1rem;
            font-weight: 500;
            font-size: 0.9rem; /* Font boyutunu küçülttük */
        }

        .animate__animated {
            animation-duration: 1s;
        }

        .geri-don-link {
            display: block;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem; /* Font boyutunu küçülttük */
            transition: color 0.3s;
        }

        .geri-don-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Kayıt Ol</h2>
        
        <?php if ($error): ?>
            <div class="error-message animate__animated animate__shakeX"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message animate__animated animate__fadeIn"><?= $success ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Ad Soyad:</label>
                <input type="text" name="ad_soyad" required>
            </div>
            <div class="form-group">
                <label>Kullanıcı Adı:</label>
                <input type="text" name="kullanici_adi" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="eposta" required>
            </div>
            <div class="form-group">
                <label>Şifre:</label>
                <input type="password" name="sifre" required>
            </div>
            <button type="submit" class="btn-register">Kayıt Ol</button>
        </form>

        <div class="login-link">
            Zaten hesabın var mı? <a href="login.php">Giriş Yap</a>
            <a href="./on.php" class="geri-don-link">Web sitesine geri dön</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>