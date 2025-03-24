<?php
// CSRF Token Oluşturma
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token Doğrulama
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// XSS Önleme
function sanitizeInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Şifre Hash'leme
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Şifre Doğrulama
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Dosya Yükleme Güvenliği
function validateUploadedFile($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    if (!in_array($file['type'], $allowedTypes)) {
        return false; // Geçersiz dosya türü
    }

    if ($file['size'] > $maxFileSize) {
        return false; // Dosya boyutu çok büyük
    }

    return true;
}
?>