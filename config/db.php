<?php
/**
 * Kết nối cơ sở dữ liệu (PDO - MySQL / phpMyAdmin)
 * Chỉnh lại $host, $db, $user, $pass cho đúng với môi trường của bạn.
 */

$host = 'localhost';
$db   = 'nvdhd';
$user = 'root';   // đổi user MySQL của bạn
$pass = '';       // đổi mật khẩu MySQL của bạn
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối cơ sở dữ liệu: ' . $e->getMessage()]);
    exit;
}
