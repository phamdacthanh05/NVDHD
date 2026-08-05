<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['id_nguoi_khao_sat'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập.']);
    exit;
}

$id_nguoi_khao_sat = (int)$_SESSION['id_nguoi_khao_sat'];

try {
    $stmt = $pdo->prepare("
        SELECT k.id_tieu_chi, t.ten_tieu_chi, t.icon, k.diem_tieu_chi 
        FROM ket_qua_tieu_chi k
        JOIN tieu_chi t ON k.id_tieu_chi = t.id
        WHERE k.id_nguoi_khao_sat = ?
        ORDER BY t.thu_tu ASC
    ");
    $stmt->execute([$id_nguoi_khao_sat]);
    $ds_tru_cot = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($ds_tru_cot)) {
        echo json_encode(['status' => 'error', 'message' => 'Chưa có dữ liệu đánh giá']);
        exit;
    }

    $tong_diem = 0;
    foreach ($ds_tru_cot as $tc) {
        $tong_diem += (float)$tc['diem_tieu_chi'];
    }
    $dms = count($ds_tru_cot) > 0 ? round($tong_diem / count($ds_tru_cot), 2) : 0;

    // === HÀM ĐÁNH GIÁ MỨC ĐỘ ===
    $muc_do = "";
    $badge_color = "";
    $mo_ta = "";

    if ($dms >= 4.5 && $dms <= 5.0) {
        $muc_do = "Mức 5 – Innovator / Leader";
        $badge_color = "#10b981";
    } elseif ($dms >= 4.0 && $dms < 4.5) {
        $muc_do = "Mức 4 – Integrated / Creator";
        $badge_color = "#3b82f6";
    } elseif ($dms >= 3.0 && $dms < 4.0) {
        $muc_do = "Mức 3 – Applied / Automated";
        $badge_color = "#f59e0b";
    } elseif ($dms >= 2.0 && $dms < 3.0) {
        $muc_do = "Mức 2 – Practitioner";
        $badge_color = "#f97316";
    } else {
        $muc_do = "Mức 1 – Ad-hoc / Basic User";
        $badge_color = "#ef4444";
    }

    echo json_encode([
        'status' => 'success',
        'dms_tong' => $dms,
        'muc_do' => $muc_do,
        'badge_color' => $badge_color,
        'mo_ta' => $mo_ta,
        'chi_tiet_tru_cot' => $ds_tru_cot
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>