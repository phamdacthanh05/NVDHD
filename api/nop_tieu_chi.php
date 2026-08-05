<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['id_nguoi_khao_sat'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Chưa xác minh thông tin người dùng.']);
    exit;
}

$id_nguoi = (int)$_SESSION['id_nguoi_khao_sat'];
$input = json_decode(file_get_contents('php://input'), true);

$id_tieu_chi = (int)($input['id_tieu_chi'] ?? 0);
$answers = $input['answers'] ?? [];

if (!$id_tieu_chi || empty($answers)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

try {
    // Kiểm tra đã hoàn thành chưa
    $checkDone = $pdo->prepare("SELECT da_hoan_thanh FROM nguoi_khao_sat WHERE id = ?");
    $checkDone->execute([$id_nguoi]);
    $row = $checkDone->fetch();
    if ($row && (int)$row['da_hoan_thanh'] === 1) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Bạn đã hoàn thành toàn bộ khảo sát.']);
        exit;
    }

    // Lấy danh sách câu hỏi thuộc tiêu chí này để kiểm tra hợp lệ
    $qStmt = $pdo->prepare("SELECT id FROM cau_hoi WHERE id_tieu_chi = ?");
    $qStmt->execute([$id_tieu_chi]);
    $validIds = $qStmt->fetchAll(PDO::FETCH_COLUMN);
    $receivedIds = array_column($answers, 'id_cau_hoi');

    $diff = array_diff($receivedIds, $validIds);
    if (!empty($diff)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Có câu hỏi không thuộc tiêu chí này.']);
        exit;
    }

    // Tính điểm trung bình cộng của tiêu chí
    $tongDiem = 0;
    foreach ($answers as $ans) {
        $diem = (int)$ans['diem'];
        if ($diem < 1 || $diem > 5) {
            throw new Exception('Điểm phải từ 1 đến 5.');
        }
        $tongDiem += $diem;
    }
    $soCau = count($answers);
    $diemTieuChi = $soCau > 0 ? $tongDiem / $soCau : 0;

    // Lưu vào bảng ket_qua_tieu_chi
    $stmt = $pdo->prepare("
        INSERT INTO ket_qua_tieu_chi (id_nguoi_khao_sat, id_tieu_chi, diem_tieu_chi) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE diem_tieu_chi = VALUES(diem_tieu_chi), created_at = NOW()
    ");
    $stmt->execute([$id_nguoi, $id_tieu_chi, $diemTieuChi]);

    // Kiểm tra đã đủ 6 tiêu chí chưa
    $countStmt = $pdo->prepare("SELECT COUNT(DISTINCT id_tieu_chi) as cnt FROM ket_qua_tieu_chi WHERE id_nguoi_khao_sat = ?");
    $countStmt->execute([$id_nguoi]);
    $count = $countStmt->fetch(PDO::FETCH_ASSOC)['cnt'];

    $dms = 0;
    $hoanThanh = false;

    if ($count == 6) {
        $allStmt = $pdo->prepare("SELECT diem_tieu_chi FROM ket_qua_tieu_chi WHERE id_nguoi_khao_sat = ?");
        $allStmt->execute([$id_nguoi]);
        $scores = $allStmt->fetchAll(PDO::FETCH_COLUMN);
        $dms = array_sum($scores) / count($scores);
        // Đánh dấu hoàn thành
        $upd = $pdo->prepare("UPDATE nguoi_khao_sat SET da_hoan_thanh = 1, completed_at = NOW(), diem_tong_dms = ? WHERE id = ?");
        $upd->execute([$dms, $id_nguoi]);
        $hoanThanh = true;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Đã lưu kết quả tiêu chí thành công!',
        'diem_tieu_chi' => round($diemTieuChi, 2),
        'da_hoan_tat_ca' => $hoanThanh,
        'dms' => round($dms, 2)
    ]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}