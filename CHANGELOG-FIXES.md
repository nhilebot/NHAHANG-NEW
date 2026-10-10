# Các thay đổi đã áp dụng (đợt 1: bảo mật + phân quyền + thanh toán)

## 1. Gỡ module Thu ngân & vá bảo mật
- Xoá `CashierController`, `CheckCashier`, view `cashier/`, route `/cashier/*`, menu/CSS/nhãn "Thu ngân", migration `add_cashier_role`, tài khoản `cashier@…`.
- Migration mới `2026_10_10_000001_remove_cashier_role`: với CSDL cũ, chuyển user role Thu ngân → Staff rồi xoá role.
- Xoá route debug `/test-models`; bật lại CSRF cho `/chatbot/ask` (+ throttle 15/phút).
- `/order-history`: chỉ trả đơn của chính người đăng nhập (trước đây lộ toàn bộ đơn).
- `OrderController@store`: giá/tên món/tổng tiền lấy từ DB, không tin client.
- `OrderController@cancel`: chỉ huỷ đơn của chính mình. Không trả `$e->getMessage()` cho client.
- `User::$fillable` bỏ `role_id` (chống mass-assignment leo thang quyền); gán role tường minh.
- Login: validate, `session()->regenerate()`, throttle; đăng ký/OTP có throttle; mật khẩu tối thiểu 8.
- Xoá code chết: `backup_views/`, `debug-reservation`, `welcome`, `EmployeeController` (không route), middleware `IsAdmin/CheckChef/CheckStaff`.

## 2. Chuẩn hoá Role
- `App\Models\Role`: hằng số `ADMIN=2, STAFF=3, CHEF=4, CUSTOMER=5` + `homeRoute()`. (Giữ ID cũ để không phải sửa dữ liệu/view; ID 1 bỏ trống.)
- `CheckRole` dùng tên: `role:admin`, `role:staff`, `role:chef`. Chỉ Admin được vào khu Staff; Staff/Chef/Customer không vào được khu Admin.
- `RoleSeeder` seed ID cố định (không phụ thuộc thứ tự insert); `UserSeeder` tạo admin/staff/chef/customer (mật khẩu `Demo@123456`); seeder khác tra user theo email thay vì `user_id = 3`.

## 3. State machine & thanh toán
- `Reservation::TRANSITIONS` + `canTransition()`: chỉ cho phép các bước hợp lệ (pending_deposit → pending → confirmed → arrived → serving → ready → served → paid_* → completed; huỷ trước khi nấu).
- `updateStatus`: validate, chạy trong transaction + `lockForUpdate`, gửi mail SAU khi lưu, giải phóng bàn khi huỷ, ghi `deposit_paid_at`.
- Route mới `POST {staff|admin}/reservations/{id}/pay`: chỉ khi `served`; số thu = tổng − cọc; lưu `paid_amount`, `paid_at`, `paid_by` (migration `2026_10_10_000002`).
- Chef: validate `chef_status ∈ {pending,cooking,done}`, kiểm tra món thuộc đơn, kiểm tra trạng thái đơn; `startOrder` không ghi đè món đã xong.
- Staff gọi thêm món / gửi bếp: chặn khi đơn đã thanh toán/huỷ; validate đầu vào.
- Đồng bộ Order qua `Reservation::linkedOrder()` (khớp chặt hơn trước, không còn cập nhật nhầm đơn bàn/khách khác).

## 4. Khác
- Thêm `doctrine/dbal` vào composer.json (Laravel 10 cần cho `->change()` trong migration). **Chạy `composer update doctrine/dbal`** để cập nhật composer.lock.
- Thêm `.env.example`, `.gitignore`, `README.md`, `phpunit.xml` dùng SQLite in-memory, 18 test (phân quyền, IDOR, state machine, thanh toán trừ cọc).

## Cách áp dụng trên CSDL đang dùng
```bash
composer update doctrine/dbal
php artisan migrate           # chạy 2 migration mới
php artisan db:seed --class=RoleSeeder
php artisan test
```
(Nếu muốn sạch hoàn toàn: `php artisan migrate:fresh --seed`.)

## Chưa làm trong đợt này (đề xuất đợt sau)
TTL 15 phút cho `pending_deposit`, kiểm tra trùng bàn theo ngày/giờ, Floor Plan + chuyển/gộp bàn, KDS theo từng món, Dashboard Chart.js, Inventory + BOM,
và `CartController::syncCustomerCartToStaff` (đang ghi đè giỏ khách vào đơn đặt bàn mới nhất, không tính lại tiền cọc).
