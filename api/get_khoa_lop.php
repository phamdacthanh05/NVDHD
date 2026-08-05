<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

try {
    // Lớp giờ là ô nhập text tự do (không còn bảng `lop`), nên chỉ cần lấy danh sách Khoá từ DB
    $khoa = $pdo->query("SELECT id, ten_khoa FROM khoa ORDER BY thu_tu, ten_khoa")->fetchAll();

    echo json_encode([
        'success' => true,
        'khoa' => $khoa,
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn: ' . $e->getMessage()]);
}
