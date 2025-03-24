<?php
session_start();

// Oturumu sonlandır
session_unset();
session_destroy();

// Giriş sayfasına yönlendir
header("Location: yonetici_giris.php");
exit();
?>