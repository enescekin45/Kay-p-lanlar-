<?php
$host = 'localhost'; // Veritabanı sunucusu
$db   = 'kayıp';     // Veritabanı adı
$user = 'root';      // Veritabanı kullanıcı adı
$pass = '';          // Veritabanı şifresi
$charset = 'utf8mb4'; // Karakter seti

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options); // $pdo değişkeni tanımlanıyor
} catch (\PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}
?>