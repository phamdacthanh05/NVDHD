<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

// Phải xác minh danh tính trước khi lấy câu hỏi
if (empty($_SESSION['id_nguoi_khao_sat'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Bạn cần xác minh thông tin trước khi làm khảo sát.']);
    exit;
}

try {
    // Nếu người này đã hoàn thành khảo sát rồi thì chặn luôn
    $stmt = $pdo->prepare("SELECT da_hoan_thanh FROM nguoi_khao_sat WHERE id = ?");
    $stmt->execute([$_SESSION['id_nguoi_khao_sat']]);
    $nguoi = $stmt->fetch();

    if (!$nguoi) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Phiên làm việc không hợp lệ.']);
        exit;
    }
    if ((int)$nguoi['da_hoan_thanh'] === 1) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Bạn đã hoàn thành khảo sát này rồi.']);
        exit;
    }

    $tieuChiList = $pdo->query("SELECT id, ten_tieu_chi, icon FROM tieu_chi ORDER BY thu_tu, id")->fetchAll();
    $cauHoiStmt = $pdo->prepare("SELECT id, noi_dung FROM cau_hoi WHERE id_tieu_chi = ? ORDER BY thu_tu, id");

    $result = [];
    foreach ($tieuChiList as $tc) {
        $cauHoiStmt->execute([$tc['id']]);
        $result[] = [
            'id' => $tc['id'],
            'ten_tieu_chi' => $tc['ten_tieu_chi'],
            'icon' => $tc['icon'] ?: 'fa-circle-exclamation',
            'cau_hoi' => $cauHoiStmt->fetchAll(),
        ];
    }

    echo json_encode(['success' => true, 'tieu_chi' => $result]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn: ' . $e->getMessage()]);
}
