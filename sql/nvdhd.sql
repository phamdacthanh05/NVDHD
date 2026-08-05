-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th8 05, 2026 lúc 05:36 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `nvdhd`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cau_hoi`
--

CREATE TABLE `cau_hoi` (
  `id` int(11) NOT NULL,
  `id_tieu_chi` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `thu_tu` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cau_hoi`
--

INSERT INTO `cau_hoi` (`id`, `id_tieu_chi`, `noi_dung`, `thu_tu`) VALUES
(13, 1, 'Bạn có thể tìm kiếm tài liệu học tập hoặc nghiên cứu bằng các từ khóa phù hợp trên Internet.', 1),
(14, 1, 'Bạn biết cách đánh giá độ tin cậy của nguồn thông tin trước khi sử dụng.', 2),
(15, 1, 'Bạn có khả năng phân loại và lưu trữ tài liệu số một cách khoa học để dễ dàng tìm lại.', 3),
(16, 1, 'Bạn biết cách sử dụng các công cụ tìm kiếm học thuật (Google Scholar, IEEE, ScienceDirect,...).', 4),
(17, 1, 'Bạn biết cách trích dẫn và sử dụng thông tin đúng quy định, tránh đạo văn.', 5),
(18, 2, 'Bạn thường xuyên sử dụng email, LMS hoặc các nền tảng trực tuyến để trao đổi học tập.', 1),
(19, 2, 'Bạn có thể làm việc nhóm hiệu quả bằng các công cụ như Google Drive, Microsoft Teams hoặc các nền tảng tương tự.', 2),
(20, 2, 'Bạn tuân thủ các quy tắc ứng xử văn minh khi giao tiếp trên môi trường số.', 3),
(21, 2, 'Bạn có thể chia sẻ tài liệu số cho người khác một cách thuận tiện và đúng quyền truy cập.', 4),
(22, 2, 'Bạn chủ động sử dụng các công cụ số để phối hợp và hoàn thành công việc nhóm.', 5),
(23, 3, 'Bạn có thể tạo các tài liệu số (Word, PowerPoint, Excel...) phục vụ học tập.', 1),
(24, 3, 'Bạn có khả năng thiết kế hoặc chỉnh sửa hình ảnh, video hoặc infographic cơ bản.', 2),
(25, 3, 'Bạn biết sử dụng các công cụ AI hoặc phần mềm hỗ trợ để nâng cao chất lượng sản phẩm học tập.', 3),
(26, 3, 'Bạn tôn trọng bản quyền khi sử dụng hình ảnh, tài liệu hoặc phần mềm trên Internet.', 4),
(27, 3, 'Bạn có thể tạo ra sản phẩm số phục vụ học tập hoặc nghiên cứu theo yêu cầu.', 5),
(28, 4, 'Bạn sử dụng mật khẩu mạnh và thay đổi mật khẩu khi cần thiết.', 1),
(29, 4, 'Bạn biết cách bảo vệ thông tin cá nhân trên môi trường số.', 2),
(30, 4, 'Bạn có thể nhận biết các nguy cơ như lừa đảo trực tuyến, email giả mạo hoặc phần mềm độc hại.', 3),
(31, 4, 'Bạn sử dụng công nghệ và trí tuệ nhân tạo (AI) một cách trung thực và có trách nhiệm.', 4),
(32, 4, 'Bạn hiểu và tuân thủ các quy định về quyền riêng tư và bảo mật dữ liệu.', 5),
(33, 5, 'Bạn có thể lựa chọn công cụ số phù hợp để giải quyết nhiệm vụ học tập.', 1),
(34, 5, 'Khi gặp sự cố kỹ thuật đơn giản, bạn có thể tự tìm cách khắc phục.', 2),
(35, 5, 'Bạn sẵn sàng học và sử dụng các công nghệ mới khi cần thiết.', 3),
(36, 5, 'Bạn biết cách kết hợp nhiều công cụ số để nâng cao hiệu quả công việc.', 4),
(37, 5, 'Bạn có thể ứng dụng công nghệ để giải quyết các bài toán thực tế trong học tập hoặc nghiên cứu.', 5),
(38, 6, 'Bạn thường xuyên sử dụng công nghệ số để hỗ trợ học tập.', 1),
(39, 6, 'Bạn biết sử dụng các công cụ AI (ChatGPT, Copilot, Gemini...) để hỗ trợ học tập và nghiên cứu một cách phù hợp.', 2),
(40, 6, 'Bạn sử dụng các phần mềm chuyên ngành phục vụ lĩnh vực học tập của mình.', 3),
(41, 6, 'Bạn chủ động cập nhật các công nghệ mới nhằm nâng cao năng lực nghề nghiệp.', 4),
(42, 6, 'Bạn tin rằng năng lực số hiện tại đáp ứng tương đối tốt yêu cầu học tập và công việc trong tương lai.', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ket_qua_tieu_chi`
--

CREATE TABLE `ket_qua_tieu_chi` (
  `id` int(11) NOT NULL,
  `id_nguoi_khao_sat` int(11) NOT NULL,
  `id_tieu_chi` int(11) NOT NULL,
  `diem_tieu_chi` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ket_qua_tieu_chi`
--

INSERT INTO `ket_qua_tieu_chi` (`id`, `id_nguoi_khao_sat`, `id_tieu_chi`, `diem_tieu_chi`, `created_at`) VALUES
(36, 12, 1, 5.00, '2026-08-05 21:00:31'),
(37, 12, 2, 4.20, '2026-08-05 21:00:37'),
(38, 12, 3, 4.20, '2026-08-05 21:00:42'),
(39, 12, 4, 5.00, '2026-08-05 21:00:48'),
(40, 12, 5, 3.60, '2026-08-05 21:00:53'),
(41, 12, 6, 3.40, '2026-08-05 21:00:59'),
(42, 14, 1, 4.40, '2026-08-05 21:56:19'),
(43, 14, 2, 4.80, '2026-08-05 21:56:24'),
(44, 14, 3, 3.60, '2026-08-05 21:56:29'),
(45, 14, 4, 3.40, '2026-08-05 21:56:34'),
(46, 14, 5, 3.80, '2026-08-05 21:56:39'),
(47, 14, 6, 3.80, '2026-08-05 21:56:50'),
(48, 13, 1, 4.60, '2026-08-05 21:58:56'),
(49, 13, 2, 3.40, '2026-08-05 21:59:02'),
(50, 13, 3, 5.00, '2026-08-05 21:59:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoa`
--

CREATE TABLE `khoa` (
  `id` int(11) NOT NULL,
  `ten_khoa` varchar(100) NOT NULL,
  `thu_tu` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khoa`
--

INSERT INTO `khoa` (`id`, `ten_khoa`, `thu_tu`) VALUES
(1, 'K20 Trở về trước', 1),
(2, 'K21', 2),
(3, 'K22', 3),
(4, 'K23', 4),
(5, 'K24', 5),
(6, 'K25', 6);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_khao_sat`
--

CREATE TABLE `nguoi_khao_sat` (
  `id` int(11) NOT NULL,
  `ho_ten` varchar(150) NOT NULL,
  `email_sdt` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `id_khoa` int(11) DEFAULT NULL,
  `lop` varchar(150) NOT NULL,
  `da_hoan_thanh` tinyint(1) NOT NULL DEFAULT 0,
  `diem_tong_dms` decimal(5,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_khao_sat`
--

INSERT INTO `nguoi_khao_sat` (`id`, `ho_ten`, `email_sdt`, `password`, `role`, `id_khoa`, `lop`, `da_hoan_thanh`, `diem_tong_dms`, `created_at`, `completed_at`, `password_hash`) VALUES
(11, 'Administrator', 'admin@ttn.edu.vn', 'admin123', 'admin', 1, 'Khoa CNTT', 0, 0.00, '2026-08-05 20:16:07', NULL, NULL),
(12, 'Phạm Đắc Thanh', '23103115@sv.ttn.edu.vn', '$2y$10$wA8tZepRrRQ0WXHGnyTATeQfeqkwgcsAsklGHpvp4Iv1Y59seQMny', 'user', 4, 'CNTT K23', 1, 4.23, '2026-08-05 20:58:23', '2026-08-05 21:00:59', NULL),
(13, 'Trần Văn A', 'dacthanh0605@gmail.com', '$2y$10$xH7NAiH9i.KwFbytK3kRFuU3/34xw3QpNPbIMPclvEQ1hOFPdeOp6', 'user', 1, 'Thú Y', 0, 0.00, '2026-08-05 21:32:19', NULL, NULL),
(14, 'Trần Văn B', 'tester01@gmail.com', '$2y$10$SunNY8jf5oVTTT/4roWv.uOM8jE2sSZXD.ng44B8LOJjs3.0D88GG', 'user', 1, 'Thú Y', 1, 3.97, '2026-08-05 21:56:10', '2026-08-05 21:56:50', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tieu_chi`
--

CREATE TABLE `tieu_chi` (
  `id` int(11) NOT NULL,
  `ten_tieu_chi` varchar(200) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-circle-exclamation',
  `thu_tu` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tieu_chi`
--

INSERT INTO `tieu_chi` (`id`, `ten_tieu_chi`, `icon`, `thu_tu`) VALUES
(1, 'Trụ cột 1 - THÔNG TIN VÀ DỮ LIỆU SỐ', 'fa-circle-exclamation', 1),
(2, 'Trụ cột 2 - GIAO TIẾP VÀ HỢP TÁC SỐ', 'fa-circle-exclamation', 2),
(3, 'Trụ cột 3 - SÁNG TẠO NỘI DUNG SỐ', 'fa-circle-exclamation', 3),
(4, 'Trụ cột 4 - AN TOÀN VÀ ĐẠO ĐỨC SỐ', 'fa-circle-exclamation', 4),
(5, 'Trụ cột 5 - GIẢI QUYẾT VẤN ĐỀ BẰNG CÔNG NGHỆ', 'fa-circle-exclamation', 5),
(6, 'Trụ cột 6 - CÔNG NGHỆ SỐ TRONG HỌC TẬP, NGHIÊN CỨU VÀ NGHỀ NGHIỆP', 'fa-circle-exclamation', 6);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cau_hoi`
--
ALTER TABLE `cau_hoi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tieu_chi` (`id_tieu_chi`);

--
-- Chỉ mục cho bảng `ket_qua_tieu_chi`
--
ALTER TABLE `ket_qua_tieu_chi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_nguoi_tieuchi` (`id_nguoi_khao_sat`,`id_tieu_chi`),
  ADD KEY `fk_kq_tieuchi` (`id_tieu_chi`);

--
-- Chỉ mục cho bảng `khoa`
--
ALTER TABLE `khoa`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nguoi_khao_sat`
--
ALTER TABLE `nguoi_khao_sat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_sdt` (`email_sdt`),
  ADD KEY `id_khoa` (`id_khoa`);

--
-- Chỉ mục cho bảng `tieu_chi`
--
ALTER TABLE `tieu_chi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cau_hoi`
--
ALTER TABLE `cau_hoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `ket_qua_tieu_chi`
--
ALTER TABLE `ket_qua_tieu_chi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT cho bảng `khoa`
--
ALTER TABLE `khoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `nguoi_khao_sat`
--
ALTER TABLE `nguoi_khao_sat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `tieu_chi`
--
ALTER TABLE `tieu_chi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cau_hoi`
--
ALTER TABLE `cau_hoi`
  ADD CONSTRAINT `cau_hoi_ibfk_1` FOREIGN KEY (`id_tieu_chi`) REFERENCES `tieu_chi` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `ket_qua_tieu_chi`
--
ALTER TABLE `ket_qua_tieu_chi`
  ADD CONSTRAINT `fk_kq_nguoi` FOREIGN KEY (`id_nguoi_khao_sat`) REFERENCES `nguoi_khao_sat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kq_tieuchi` FOREIGN KEY (`id_tieu_chi`) REFERENCES `tieu_chi` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nguoi_khao_sat`
--
ALTER TABLE `nguoi_khao_sat`
  ADD CONSTRAINT `nguoi_khao_sat_ibfk_1` FOREIGN KEY (`id_khoa`) REFERENCES `khoa` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
