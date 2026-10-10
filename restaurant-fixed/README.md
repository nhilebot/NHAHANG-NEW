# 🍽️ Hệ thống Quản lý Nhà hàng & Đặt bàn Online

Đồ án tốt nghiệp — website quản lý nhà hàng xây dựng bằng **Laravel 10 (MVC)**.

## 1. Chức năng theo vai trò

| Vai trò | Chức năng chính |
|---|---|
| **Khách hàng (Customer)** | Xem thực đơn, giỏ hàng, đặt bàn online + đặt món trước (cọc 20%), xem đơn của mình, đánh giá món |
| **Quản trị (Admin)** | Quản lý nhân sự, danh mục, thực đơn, bàn, kho nguyên liệu, hóa đơn; Dashboard doanh thu / lợi nhuận / món bán chạy |
| **Nhân viên (Staff)** | Sơ đồ bàn, xác nhận cọc & đơn đặt bàn, gọi món tại bàn, chuyển/gộp bàn, **xác nhận thanh toán** |
| **Bếp (Chef)** | Màn hình KDS: nhận đơn, cập nhật từng món *Chờ → Đang nấu → Đã xong* |

## 2. Yêu cầu môi trường

- [Laragon](https://laragon.org/download/) (Full) — kèm Apache/Nginx, MySQL 8, PHP ≥ 8.1
- Composer ≥ 2, Node.js ≥ 18 (Laragon đã có sẵn)
- Git

## 3. Cài đặt trên Laragon

```bash
# 1. Clone vào thư mục www của Laragon (mặc định C:\laragon\www)
cd C:\laragon\www
git clone <URL_REPO> restaurant
cd restaurant

# 2. Cài thư viện
composer install
composer require doctrine/dbal:^3.8   # cần cho migration có ->change() (Laravel 10); bỏ qua nếu composer.json đã có
npm install

# 3. Tạo file môi trường & khóa ứng dụng
copy .env.example .env
php artisan key:generate
```

### Cấu hình `.env`

Tạo database `restaurant` trong HeidiSQL/phpMyAdmin (Laragon → *Database*), rồi sửa:

```env
APP_NAME="Restaurant"
APP_URL=http://restaurant.test
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurant
DB_USERNAME=root
DB_PASSWORD=

# Gửi mail (ví dụ Gmail — dùng "Mật khẩu ứng dụng", không dùng mật khẩu thật)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

# Chatbot (tùy chọn)
GEMINI_API_KEY=
```

```bash
# 4. Tạo bảng + dữ liệu mẫu
php artisan migrate:fresh --seed

# 5. Liên kết thư mục ảnh upload
php artisan storage:link

# 6. Chạy front-end
npm run dev          # (hoặc: npm run build)
```

Laragon tự tạo virtual host: mở **http://restaurant.test**
(hoặc chạy `php artisan serve` → http://127.0.0.1:8000).

## 4. Tài khoản demo (sau khi seed)

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | admin@gmail.com | Demo@123456 |
| Staff | staff@gmail.com | Demo@123456 |
| Chef | chef@gmail.com | Demo@123456 |
| Customer | customer@gmail.com | Demo@123456 |

> ⚠️ Chỉ dùng cho demo. Đổi mật khẩu khi triển khai thật.

## 5. Phân quyền & đường dẫn

| Khu vực | Prefix | Role |
|---|---|---|
| Công khai | `/` | Tất cả |
| Khách | `/cart`, `/reservation`, `/order-history` | customer |
| Thanh toán | `POST /staff/reservations/{id}/pay` | staff (admin cũng dùng được) |
| Nhân viên | `/staff/*` | staff (admin được vào) |
| Bếp (KDS) | `/chef/*` | chef |
| Quản trị | `/admin/*` | admin |

## 6. Cấu trúc thư mục chính

```
app/Http/Controllers   # Điều khiển request
app/Http/Middleware    # CheckRole (phân quyền)
app/Models             # Eloquent models
database/migrations    # Cấu trúc CSDL
database/seeders       # Dữ liệu mẫu
resources/views        # Blade templates
routes/web.php         # Định tuyến
```

## 7. Lỗi thường gặp

| Lỗi | Cách xử lý |
|---|---|
| `SQLSTATE[HY000] [1049] Unknown database` | Chưa tạo database `restaurant` |
| `No application encryption key` | Chạy `php artisan key:generate` |
| Ảnh không hiển thị | Chạy `php artisan storage:link` |
| Giao diện mất CSS/JS | Chạy `npm run dev` hoặc `npm run build` |
| Sửa `.env` không có tác dụng | `php artisan config:clear` |
| Không gửi được mail | Kiểm tra mật khẩu ứng dụng Gmail & `MAIL_*` |

## 8. Kiểm thử

Test chạy trên SQLite in-memory (không đụng vào database MySQL của bạn):

```bash
php artisan test
```

Bao gồm: phân quyền theo vai trò, chống đọc đơn của người khác, state machine đặt bàn, thanh toán trừ cọc.

## 9. Tác giả

Lê Yến Nhi — Đồ án tốt nghiệp.
