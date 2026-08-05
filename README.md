# HỆ THỐNG KHẢO SÁT (PHP + MySQL/phpMyAdmin)

## 1. Cấu trúc thư mục
```
khaosat_php/
├── index.html                 # Trang chính (form xác minh + giao diện khảo sát, HTML tĩnh)
├── config/
│   └── db.php                 # Cấu hình kết nối MySQL (PDO)
├── api/
│   ├── get_khoa_lop.php       # Lấy danh sách Khoá (Lớp nhập tự do, không lấy từ DB)
│   ├── xac_minh.php           # Xác minh danh tính người khảo sát
│   ├── get_cauhoi.php         # Lấy câu hỏi theo từng Tiêu chí
│   ├── nop_khaosat.php        # Nộp kết quả khảo sát (điểm 1-5)
│   └── ket_qua.php            # Tính điểm trụ cột (Pi) + điểm trưởng thành DMS
├── assets/
│   ├── css/style.css
│   └── js/app.js
└── sql/
    ├── nvdhd.sql              # Schema + dữ liệu mẫu để import vào phpMyAdmin (bản đầy đủ, mới nhất)
    └── migrate_trong_so.sql   # Chỉ chạy nếu DB đã import từ trước, cần thêm cột trọng số
```

## 2. Cài đặt

### Bước 1 — Import database
1. Mở **phpMyAdmin**.
2. Vào tab **SQL** (hoặc **Import**), chạy toàn bộ nội dung file `sql/nvdhd.sql`.
   → File này sẽ tạo database `nvdhd` và 5 bảng: `khoa`, `tieu_chi`, `cau_hoi`, `nguoi_khao_sat`, `ket_qua_khao_sat`, kèm dữ liệu mẫu.
   → Lưu ý: `nguoi_khao_sat.lop` là cột **text tự do** (không còn bảng `lop` riêng) — người dùng tự gõ tên lớp khi khảo sát, không chọn từ danh sách có sẵn.

### Bước 2 — Cấu hình kết nối
Mở `config/db.php`, sửa lại cho đúng với MySQL của bạn:
```php
$host = 'localhost';
$db   = 'nvdhd';
$user = 'root';   // user MySQL
$pass = '';       // mật khẩu MySQL
```

### Bước 3 — Đặt thư mục vào server PHP
- Nếu dùng **XAMPP**: copy toàn bộ thư mục `khaosat_php` vào `htdocs/`, sau đó mở `http://localhost/khaosat_php/`.
- Nếu dùng PHP built-in server (để test nhanh):
  ```bash
  cd khaosat_php
  php -S localhost:8000
  ```
  rồi mở `http://localhost:8000/`.

## 3. Cách hoạt động

1. **Mở web** → hiển thị form xác minh: Họ tên, Email/SĐT, chọn Khoá (dropdown từ DB), nhập Lớp (ô text tự do).
2. **Xác minh** (`api/xac_minh.php`):
   - Kiểm tra Email/SĐT đã làm khảo sát (`da_hoan_thanh = 1`) chưa. Nếu rồi → chặn, báo đã khảo sát.
   - Nếu chưa → lưu/ cập nhật vào bảng `nguoi_khao_sat`, lưu định danh vào session PHP.
3. **Lấy câu hỏi** (`api/get_cauhoi.php`): trả về danh sách Tiêu chí kèm bộ câu hỏi riêng của từng tiêu chí (từ bảng `cau_hoi`, liên kết `id_tieu_chi`).
4. **Làm khảo sát**: mỗi câu hỏi đánh giá bằng thang điểm **1–5**, hiển thị dạng nút tròn (giống Google Forms). Thanh tiến trình + badge trên sidebar hiển thị số câu đã trả lời theo từng Tiêu chí.
5. **Nộp khảo sát** (`api/nop_khaosat.php`):
   - Yêu cầu trả lời đủ tất cả câu hỏi.
   - Lưu từng câu trả lời vào bảng `ket_qua_khao_sat` (điểm 1-5).
   - Đánh dấu `nguoi_khao_sat.da_hoan_thanh = 1` → email/SĐT đó không thể khảo sát lại lần 2.
6. **Tính điểm trưởng thành** (`api/ket_qua.php`): sau khi nộp xong, front-end gọi API này để lấy điểm từng trụ cột (tiêu chí) và điểm DMS tổng, hiển thị ngay ở màn hình "Cảm ơn".

## 4.1. Công thức tính điểm trưởng thành (DMS)

**Điểm từng trụ cột (Pi):**
```
Pi = Σ (wij . Sij)      với j = 1..n câu hỏi thuộc trụ cột i
```
- `Sij`: điểm người khảo sát chấm cho câu hỏi j (1-5) → cột `ket_qua_khao_sat.diem`
- `wij`: trọng số của câu hỏi j trong trụ cột i, với điều kiện Σwij = 1 trong cùng 1 trụ cột → cột `cau_hoi.trong_so`

**Điểm Trưởng thành Số tổng hợp (DMS):**
```
DMS = Σ (Wi . Pi)       với i = 1..m trụ cột
```
- `Wi`: trọng số của trụ cột i trong tổng điểm, với điều kiện ΣWi = 1 → cột `tieu_chi.trong_so`

Mặc định hệ thống chia đều trọng số (mỗi câu hỏi trong 1 tiêu chí = 1/số câu; mỗi tiêu chí = 1/tổng số tiêu chí). Bạn có thể vào phpMyAdmin, sửa trực tiếp cột `trong_so` ở bảng `cau_hoi` và `tieu_chi` để tinh chỉnh theo định hướng ngành học — chỉ cần đảm bảo tổng `wij` trong mỗi tiêu chí và tổng `Wi` của tất cả tiêu chí đều bằng 1 (nếu lệch, hệ thống vẫn tự chuẩn hoá theo tổng trọng số thực tế nên không bị lỗi thang điểm).

## 4.2. Bảng dữ liệu

| Bảng | Cột thêm mới | Ý nghĩa |
|---|---|---|
| `cau_hoi` | `trong_so` | wij — trọng số câu hỏi trong trụ cột |
| `tieu_chi` | `trong_so` | Wi — trọng số trụ cột trong tổng DMS |

Nếu bạn **đã import** `nvdhd.sql` từ trước (chưa có 2 cột này), chạy thêm `sql/migrate_trong_so.sql` để thêm cột mà không mất dữ liệu cũ. Nếu import `nvdhd.sql` mới hoàn toàn thì không cần chạy file migrate.

## 5. Quản lý dữ liệu (qua phpMyAdmin)

- **Thêm/sửa Tiêu chí**: chỉnh bảng `tieu_chi` (`ten_tieu_chi`, `icon` là tên icon Font Awesome ví dụ `fa-shield`, `thu_tu` để sắp xếp thứ tự hiển thị).
- **Thêm câu hỏi cho từng Tiêu chí**: thêm dòng mới vào bảng `cau_hoi`, chọn đúng `id_tieu_chi`.
- **Thêm/sửa Khoá**: chỉnh bảng `khoa` trực tiếp trong phpMyAdmin.
- **Lớp**: không có bảng riêng — người khảo sát tự gõ tên lớp, được lưu thẳng vào cột `nguoi_khao_sat.lop`.
- **Xem kết quả khảo sát**: join bảng `ket_qua_khao_sat` với `nguoi_khao_sat` và `cau_hoi`, ví dụ:
  ```sql
  SELECT n.ho_ten, n.email_sdt, k.ten_khoa, n.lop,
         t.ten_tieu_chi, c.noi_dung, r.diem
  FROM ket_qua_khao_sat r
  JOIN nguoi_khao_sat n ON n.id = r.id_nguoi_khao_sat
  JOIN cau_hoi c ON c.id = r.id_cau_hoi
  JOIN tieu_chi t ON t.id = c.id_tieu_chi
  LEFT JOIN khoa k ON k.id = n.id_khoa
  ORDER BY n.id, t.thu_tu, c.thu_tu;
  ```

## 6. Ghi chú bảo mật
- Việc chặn khảo sát lại dựa trên cột `email_sdt` (UNIQUE) + `da_hoan_thanh`, nên mỗi email/SĐT chỉ nộp được 1 lần dù có mở lại trình duyệt hay xoá session.
- Toàn bộ câu truy vấn dùng **PDO prepared statements** để chống SQL Injection.
- Nên bật HTTPS khi triển khai thực tế để bảo vệ dữ liệu cá nhân (họ tên, email/SĐT) khi truyền qua mạng.
