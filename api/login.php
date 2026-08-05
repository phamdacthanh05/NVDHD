<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); 

try {
    session_start();
    require_once '../config/db.php';
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($pdo)) {
        throw new Exception('Lỗi kết nối Database: Biến $pdo không tồn tại.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ', 405);
    }

    $username = $_POST['username'] ?? '';
    $password_input = $_POST['password'] ?? '';

    if (empty($username) || empty($password_input)) {
        throw new Exception('Vui lòng nhập đầy đủ tài khoản và mật khẩu');
    }

    // Lấy cả 2 cột
    $stmt = $pdo->prepare("SELECT id, ho_ten, password, password_hash, role, da_hoan_thanh FROM nguoi_khao_sat WHERE email_sdt = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    
    if ($row) {
        $is_valid = false;

        // 1. Ưu tiên password_hash
        if (!empty($row['password_hash'])) {
            if (password_verify($password_input, $row['password_hash'])) {
                $is_valid = true;
            }
        } 
        // 2. Nếu không có password_hash, kiểm tra cột password
        elseif (!empty($row['password'])) {
            // Nếu cột password là dạng hash (bắt đầu $2y$...)
            if (substr($row['password'], 0, 4) === '$2y$') {
                if (password_verify($password_input, $row['password'])) {
                    $is_valid = true;
                }
            } else {
                // Nếu là văn bản thuần (plain text), so sánh trực tiếp
                if ($password_input === $row['password']) {
                    $is_valid = true;
                }
            }
        }

        if ($is_valid) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['id_nguoi_khao_sat'] = $row['id'];
            $_SESSION['ho_ten'] = $row['ho_ten'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                echo json_encode(['success' => true, 'role' => 'admin', 'message' => 'Đăng nhập Admin thành công!']);
                exit;
            }

            // User đã hoàn thành
            if ($row['da_hoan_thanh'] == 1) {
                echo json_encode(['success' => true, 'role' => 'user', 'is_completed' => true, 'message' => 'Tài khoản đã hoàn thành khảo sát.']);
                exit;
            }

            echo json_encode(['success' => true, 'role' => 'user', 'message' => 'Đăng nhập User thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sai mật khẩu!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại trong hệ thống!']);
    }

} catch (Exception $e) {
    $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống PHP: ' . $e->getMessage()]);
}
?>