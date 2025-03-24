<?php
session_start();
require '../config/db.php';
require '../config/security.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici_adi = sanitizeInput($_POST['kullanici_adi']);
    $sifre = $_POST['sifre'];

    try {
        // Kullanıcıyı veritabanında bul
        $stmt = $pdo->prepare("SELECT id, sifre FROM admin WHERE kullanici_adi = ?");
        $stmt->execute([$kullanici_adi]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && verifyPassword($sifre, $admin['sifre'])) {
            // Oturum başlat
            $_SESSION['yonetici_id'] = $admin['id'];
            $_SESSION['yonetici_adi'] = $kullanici_adi;
            header("Location: index.php");
            exit();
        } else {
            $error = "Kullanıcı adı veya şifre hatalı!";
        }
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
    <title>Yönetici Girişi</title>
    <style>
         body {
    background: url('../img/yönetici.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height:90vh;
    margin: 0;
    font-family: 'Arial', sans-serif;
}
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 1.5rem;
            color: #333;
        }
        .login-container input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        .login-container button {
            width: 100%;
            padding: 0.75rem;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
        }
        .login-container button:hover {
            background: #0b5ed7;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 1rem;
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
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Yönetici Girişi</h2>
        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" required>
            <input type="password" name="sifre" placeholder="Şifre" required>
            <button type="submit">Giriş Yap</button>
            <a href="../on.php" class="geri-don-link">Web sitesine geri dön</a>
        </form>
    </div>
</body>
</html>