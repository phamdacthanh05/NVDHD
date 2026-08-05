<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$ho_ten    = trim($input['ho_ten'] ?? '');
$email_sdt = trim($input['email_sdt'] ?? '');
$id_khoa   = isset($input['id_khoa']) && $input['id_khoa'] !== '' ? (int)$input['id_khoa'] : null;
$lop       = trim($input['lop'] ?? ''); // Nhận tên lớp dưới dạng chuỗi text từ người dùng

// ===== VALIDATE =====
// Đổi điều kiện kiểm tra id_lop thành kiểm tra chuỗi $lop không được rỗng
if ($ho_ten === '' || $email_sdt === '' || !$id_khoa || $lop === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ Họ tên, Email/SĐT, Khoá và Lớp.']);
    exit;
}

// Cho phép email HOẶC số điện thoại VN
$isEmail = filter_var($email_sdt, FILTER_VALIDATE_EMAIL) !== false;
$isPhone = preg_match('/^(0|\+84)[0-9]{9,10}$/', $email_sdt);
if (!$isEmail && !$isPhone) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email hoặc số điện thoại không hợp lệ.']);
    exit;
}

try {
    // Kiểm tra đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT id, da_hoan_thanh FROM nguoi_khao_sat WHERE email_sdt = ?");
    $stmt->execute([$email_sdt]);
    $nguoi = $stmt->fetch();

    if ($nguoi) {
        if ((int)$nguoi['da_hoan_thanh'] === 1) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Email/SĐT này đã hoàn thành khảo sát trước đó. Mỗi người chỉ được khảo sát 1 lần.']);
            exit;
        }
        // Đã tồn tại nhưng chưa hoàn thành -> cập nhật lại thông tin (dùng cột 'lop' dạng chuỗi)
        $upd = $pdo->prepare("UPDATE nguoi_khao_sat SET ho_ten = ?, id_khoa = ?, lop = ? WHERE id = ?");
        $upd->execute([$ho_ten, $id_khoa, $lop, $nguoi['id']]);
        $id_nguoi = $nguoi['id'];
    } else {
        // Thêm mới người khảo sát với cột 'lop' dạng chuỗi
        $ins = $pdo->prepare("INSERT INTO nguoi_khao_sat (ho_ten, email_sdt, id_khoa, lop) VALUES (?, ?, ?, ?)");
        $ins->execute([$ho_ten, $email_sdt, $id_khoa, $lop]);
        $id_nguoi = $pdo->lastInsertId();
    }

    // Lưu định danh vào session để các bước sau (lấy câu hỏi / nộp bài) xác thực lại
    $_SESSION['id_nguoi_khao_sat'] = $id_nguoi;
    $_SESSION['ho_ten'] = $ho_ten;

    echo json_encode([
        'success' => true,
        'message' => 'Xác minh thành công.',
        'id_nguoi_khao_sat' => $id_nguoi,
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}