<?php
session_start();
require 'config/db.php'; // Veritabanı bağlantısını dahil et

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // $_POST anahtarlarının tanımlı olup olmadığını kontrol et
    $eposta = isset($_POST['eposta']) ? $_POST['eposta'] : '';
    $sifre = isset($_POST['sifre']) ? $_POST['sifre'] : '';

    // Form validation
    if (empty($eposta) || empty($sifre)) {
        $error = "Lütfen tüm alanları doldurun!";
    } else {
        // Kullanıcıyı veritabanında bul
        $sql = "SELECT * FROM users WHERE eposta = ?";
        $stmt = $pdo->prepare($sql); // $pdo kullanılıyor
        $stmt->execute([$eposta]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($sifre, $user['sifre'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['ad_soyad'] = $user['ad_soyad'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Hatalı email veya şifre!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
    background: url('./img/arka.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    font-family: 'Arial', sans-serif;
}

.login-container {
    background: rgba(255, 255, 255, 0.9); /* Şeffaf beyaz arka plan */
    padding: 2.5rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
    backdrop-filter: blur(10px); /* Bulanık arka plan efekti */
}

.login-container h2 {
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
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: #0d6efd;
    outline: none;
}

.btn-login {
    width: 100%;
    padding: 0.75rem;
    background-color: #0d6efd;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-login:hover {
    background-color: #0b5ed7;
}

.register-link {
    margin-top: 1rem;
    color: #555;
}

.register-link a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 500;
}

.register-link a:hover {
    text-decoration: underline;
}

.error-message {
    color: #dc3545;
    margin-bottom: 1rem;
    font-weight: 500;
}

.success-animation {
    animation: fadeIn 1s ease-in-out;
}

.geri-don-link {
            display: block;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .geri-don-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Giriş Yap</h2>
        
        <?php if ($error): ?>
            <div class="error-message animate__animated animate__shakeX"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="eposta" required>
            </div>
            <div class="form-group">
                <label>Şifre:</label>
                <input type="password" name="sifre" required>
            </div>
            <button type="submit" class="btn-login">Giriş Yap</button>
        </form>

        <div class="register-link">
            Hesabın yok mu? <a href="register.php">Kayıt Ol</a>
        </div>
        <a href="./on.php" class="geri-don-link">Web sitesine geri dön</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>