<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        throw new Exception('Bạn chưa đăng nhập với quyền Admin!', 403);
    }

    if (!file_exists('../config/db.php')) {
        throw new Exception('KHÔNG TÌM THẤY FILE DB: Đường dẫn "../config/db.php" sai!');
    }
    require_once '../config/db.php';

    if (!isset($pdo)) {
        throw new Exception('LỖI DB: Biến $pdo không tồn tại trong file config/db.php.');
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // === HÀM XÁC ĐỊNH ĐÁNH GIÁ DỰA TRÊN ĐIỂM DMS ===
    function getDmsAssessment($dms) {
        if ($dms >= 4.5 && $dms <= 5.0) {
            return [
                'level' => 'Mức 5',
                'color' => '#10b981', // Màu xanh lục
            ];
        } elseif ($dms >= 4.0 && $dms < 4.5) {
            return [
                'level' => 'Mức 4',
                'color' => '#3b82f6', // Màu xanh dương
            ];
        } elseif ($dms >= 3.0 && $dms < 4.0) {
            return [
                'level' => 'Mức 3',
                'color' => '#f59e0b', // Màu vàng cam
            ];
        } elseif ($dms >= 2.0 && $dms < 3.0) {
            return [
                'level' => 'Mức 2',
                'color' => '#f97316', // Màu cam
            ];
        } else { // DMS < 2.0
            return [
                'level' => 'Mức 1',
                'color' => '#ef4444', // Màu đỏ
            ];
        }
    }

    $data = [];
    $sql = "SELECT n.id, n.ho_ten, n.email_sdt, n.lop, n.da_hoan_thanh, 
                   k.ten_khoa, k.id as khoa_id
            FROM nguoi_khao_sat n
            LEFT JOIN khoa k ON n.id_khoa = k.id
            WHERE n.role = 'user' 
            ORDER BY n.id DESC";
    $result = $pdo->query($sql);

    $tieu_chis = [];
    $sql_tc = "SELECT id, ten_tieu_chi FROM tieu_chi";
    $result_tc = $pdo->query($sql_tc);
    while ($tc = $result_tc->fetch()) {
        $tieu_chis[$tc['id']] = $tc;
    }
    $total_tieu_chi = count($tieu_chis);

    while ($row = $result->fetch()) {
        $user_id = $row['id'];
        $dms_total = 0;
        $chi_tiet_tieu_chi = [];

        if ($row['da_hoan_thanh'] == 1) {
            $sql_diem = "SELECT id_tieu_chi, diem_tieu_chi FROM ket_qua_tieu_chi WHERE id_nguoi_khao_sat = ?";
            $stmt = $pdo->prepare($sql_diem);
            $stmt->execute([$user_id]);
            $res_diem = $stmt->fetchAll();

            $sum_pi = 0;
            foreach ($res_diem as $d) {
                $tc_id = $d['id_tieu_chi'];
                $diem = (float)$d['diem_tieu_chi'];
                $sum_pi += $diem;
                $chi_tiet_tieu_chi[] = [
                    'ten_tieu_chi' => $tieu_chis[$tc_id]['ten_tieu_chi'] ?? 'Tiêu chí ' . $tc_id,
                    'diem_tieu_chi' => round($diem, 2)
                ];
            }
            $dms_total = $total_tieu_chi > 0 ? $sum_pi / $total_tieu_chi : 0;
        }

        // Lấy đánh giá dựa trên DMS
        $assessment = getDmsAssessment($dms_total);

        $data[] = [
            'id' => $row['id'],
            'ho_ten' => $row['ho_ten'],
            'email_sdt' => $row['email_sdt'],
            'lop' => $row['lop'],
            'ten_khoa' => $row['ten_khoa'],
            'da_hoan_thanh' => (int)$row['da_hoan_thanh'],
            'diem_tong_dms' => round($dms_total, 2),
            'danh_gia' => $assessment['level'],
            'danh_gia_color' => $assessment['color'],
            'mo_ta' => $assessment['desc'],
            'chi_tiet_tieu_chi' => $chi_tiet_tieu_chi
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $data]);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => '🚨 LỖI CHI TIẾT: ' . $e->getMessage() . ' tại dòng ' . $e->getLine() . ' trong file ' . basename($e->getFile())
    ]);
}
?>