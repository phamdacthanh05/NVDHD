<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$ho_ten = $_POST['ho_ten'] ?? '';
$email_sdt = $_POST['email_sdt'] ?? '';
$password = $_POST['password'] ?? '';
$id_khoa = $_POST['id_khoa'] ?? '';
$lop = $_POST['lop'] ?? '';

if (empty($ho_ten) || empty($email_sdt) || empty($password) || empty($id_khoa) || empty($lop)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
}

try {
    // Kiểm tra tồn tại
    $stmtCheck = $pdo->prepare("SELECT id FROM nguoi_khao_sat WHERE email_sdt = ?");
    $stmtCheck->execute([$email_sdt]);
    if ($stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email/SĐT này đã được đăng ký! Vui lòng đăng nhập.']);
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // QUAN TRỌNG: Sửa tên cột thành 'password' để khớp Database
    $stmt = $pdo->prepare("INSERT INTO nguoi_khao_sat (ho_ten, email_sdt, password, id_khoa, lop, da_hoan_thanh) VALUES (?, ?, ?, ?, ?, 0)");
    
    if ($stmt->execute([$ho_ten, $email_sdt, $password_hash, $id_khoa, $lop])) {
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['id_nguoi_khao_sat'] = $pdo->lastInsertId();
        $_SESSION['role'] = 'user'; 
        echo json_encode(['success' => true, 'role' => 'user', 'message' => 'Đăng ký thành công!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống khi tạo tài khoản.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>