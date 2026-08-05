<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json; charset=utf-8');

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Truy cập bị từ chối. Vui lòng đăng nhập Admin!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (empty($old_password) || empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ mật khẩu cũ và mới']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? $_SESSION['id_nguoi_khao_sat'] ?? 0;

try {
    // 2. Lấy mật khẩu hiện tại của admin từ DB (kiểm tra cả 2 cột password và password_hash)
    $stmt = $pdo->prepare("SELECT password, password_hash FROM nguoi_khao_sat WHERE id = ? AND role = 'admin'");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();

    if (!$row) {
        throw new Exception('Không tìm thấy thông tin Admin');
    }

    // 3. Xác minh mật khẩu cũ
    $is_valid = false;
    if (!empty($row['password_hash'])) {
        if (password_verify($old_password, $row['password_hash'])) $is_valid = true;
    } elseif (!empty($row['password'])) {
        if (password_verify($old_password, $row['password'])) $is_valid = true;
    }

    if (!$is_valid) {
        throw new Exception('Mật khẩu cũ không chính xác');
    }

    // 4. Hash mật khẩu mới và cập nhật DB (cập nhật luôn vào 2 cột để tương thích)
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $upd = $pdo->prepare("UPDATE nguoi_khao_sat SET password = ?, password_hash = ? WHERE id = ?");
    $upd->execute([$new_hash, $new_hash, $user_id]);

    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>