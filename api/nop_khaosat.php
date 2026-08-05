<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['id_nguoi_khao_sat'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Bạn cần xác minh thông tin trước khi nộp khảo sát.']);
    exit;
}
$id_nguoi = (int)$_SESSION['id_nguoi_khao_sat'];

$input = json_decode(file_get_contents('php://input'), true);
$answers = $input['answers'] ?? null; // [{id_cau_hoi: 1, diem: 5}, ...]

if (!$answers || !is_array($answers) || count($answers) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Không có dữ liệu câu trả lời.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Khoá kiểm tra: chưa hoàn thành thì mới cho nộp
    $stmt = $pdo->prepare("SELECT da_hoan_thanh FROM nguoi_khao_sat WHERE id = ? FOR UPDATE");
    $stmt->execute([$id_nguoi]);
    $nguoi = $stmt->fetch();

    if (!$nguoi) {
        throw new Exception('Phiên làm việc không hợp lệ.');
    }
    if ((int)$nguoi['da_hoan_thanh'] === 1) {
        $pdo->rollBack();
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Bạn đã nộp khảo sát này trước đó rồi.']);
        exit;
    }

    // Tổng số câu hỏi thực tế trong hệ thống, để kiểm tra đã trả lời đủ chưa
    $tongCauHoi = (int)$pdo->query("SELECT COUNT(*) AS c FROM cau_hoi")->fetch()['c'];
    if (count($answers) < $tongCauHoi) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vui lòng trả lời đầy đủ tất cả câu hỏi trước khi nộp.']);
        exit;
    }

    $ins = $pdo->prepare(
        "INSERT INTO ket_qua_khao_sat (id_nguoi_khao_sat, id_cau_hoi, diem) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE diem = VALUES(diem)"
    );

    foreach ($answers as $a) {
        $id_cau_hoi = (int)($a['id_cau_hoi'] ?? 0);
        $diem = (int)($a['diem'] ?? 0);
        if ($id_cau_hoi <= 0 || $diem < 1 || $diem > 5) {
            throw new Exception('Dữ liệu câu trả lời không hợp lệ (điểm phải từ 1 đến 5).');
        }
        $ins->execute([$id_nguoi, $id_cau_hoi, $diem]);
    }

    $upd = $pdo->prepare("UPDATE nguoi_khao_sat SET da_hoan_thanh = 1, completed_at = NOW() WHERE id = ?");
    $upd->execute([$id_nguoi]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Nộp khảo sát thành công. Cảm ơn bạn đã tham gia!']);
} catch (\Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
