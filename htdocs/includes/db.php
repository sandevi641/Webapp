<?php
$host = 'sql308.infinityfree.com';  // MySQL Host Name
$db   = 'if0_40336696_epiz_123456_blog';  // Database Name (check exact name)
$user = 'if0_40336696';  // MySQL Username (same as shown in your InfinityFree panel)
$pass = 'ODOMuoEcDj1';  
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
