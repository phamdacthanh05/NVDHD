<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['id_nguoi_khao_sat'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Chưa xác minh thông tin.']);
    exit;
}

$id_nguoi = $_SESSION['id_nguoi_khao_sat'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            tc.id, 
            tc.ten_tieu_chi, 
            tc.icon,
            COALESCE(ktt.diem_tieu_chi, 0) as diem_tieu_chi
        FROM tieu_chi tc
        LEFT JOIN ket_qua_tieu_chi ktt ON tc.id = ktt.id_tieu_chi AND ktt.id_nguoi_khao_sat = ?
        ORDER BY tc.thu_tu ASC
    ");
    $stmt->execute([$id_nguoi]);
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'tieu_chi' => $list
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
}