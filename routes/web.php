<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    MenuchitietController, ContactController, ReservationController,
    BeerController, FeaturedController, BreadController, PricingController,
    OrderController, TableController, AdminController, AuthController,
    CartController, OtpPasswordController, ProfileController, CommentController,
    AdminCategoryController, AdminMenuController, AdminTableController,
    AdminUserController, BookingController, StaffOrderController, ChatbotController
};

// =========================================================
// 1. TRANG CHỦ & CÔNG KHAI
// =========================================================
Route::get('/', function () { return view('layout'); });
Route::get('/contact', [ContactController::class, 'index']);
Route::get('/beer', [BeerController::class, 'index']);
Route::get('/featured', [FeaturedController::class, 'index']);
Route::get('/bread', [BreadController::class, 'index']);
Route::get('/pricing', [PricingController::class, 'index']);
Route::get('/tables', fn () => redirect('/reservation'));

// Đặt bàn qua form trang chủ (không cần đăng nhập)
Route::post('/book-table', [BookingController::class, 'store'])->name('booking.store');

// QUẢN LÝ THỰC ĐƠN (Khách xem)
Route::controller(MenuchitietController::class)->name('menu.')->group(function () {
    Route::get('/menu', 'index')->name('index');
    Route::get('/seafood', 'seafood')->name('seafood');
    Route::get('/special', 'special')->name('special');
    Route::get('/salad', 'salad')->name('salad');
    Route::get('/desserts', 'desserts')->name('desserts');
    Route::get('/drinks', 'drinks')->name('drinks');
    Route::get('/vietnamese', 'vietnamese')->name('vietnamese');
    Route::get('/chi-tiet-mon-an/{id}', 'showDetail')->name('detail');
});

// =========================================================
// 2. AUTHENTICATION (Guest only)
// =========================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.post');

    Route::get('/forgot-password', [OtpPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [OtpPasswordController::class, 'sendOtp'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/verify-otp', [OtpPasswordController::class, 'showVerifyForm'])->name('password.verify');
    Route::post('/verify-otp', [OtpPasswordController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// =========================================================
// 3. ROUTE YÊU CẦU ĐĂNG NHẬP (dùng chung cho mọi role)
// =========================================================
Route::middleware('auth')->group(function () {

    // Hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    // Route xem lịch sử đơn hàng của khách
Route::get('/order-history', [App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');
    // Giỏ hàng & Đặt hàng (Khách thường)
    Route::controller(CartController::class)->name('cart.')->group(function () {
        Route::get('/cart', 'index')->name('index');
        Route::post('/cart/add', 'addToCart')->name('add');
        Route::get('/cart/get', 'getCart')->name('get');
        Route::get('/clear-cart', 'clear')->name('clear');
        Route::post('/cart/checkout', 'checkout')->name('checkout');
        Route::get('/history', 'history')->name('history');
        Route::post('/cart/remove/{id}', 'remove')->name('remove'); // Đã chuyển vào trong auth
        Route::post('/cart/update', 'update')->name('update');
    });

    // Đặt bàn (Khách đã đăng nhập)
    Route::controller(ReservationController::class)->name('reservation.')->group(function () {
        Route::get('/reservation', 'index')->name('index');
        Route::post('/reservation/store', 'store')->name('store');
        Route::post('/reservation/add-to-cart', 'addToCartAjax')->name('addToCart');
        Route::post('/reservation/save-note-ajax', 'saveNoteAjax')->name('saveNoteAjax');
        Route::post('/reservation/auto-save', 'autoSave')->name('autoSave');
    });

    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    Route::put('/comment/{id}', [CommentController::class, 'update'])
    ->name('comment.update');
    Route::post('/menu/{menuId}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('comment.destroy');

    // =========================================================
    // A. HỆ THỐNG ADMIN — role_id = 2
    //    Admin được phép vào tất cả các trang của Staff (role:3)
    // =========================================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Quản lý hóa đơn
Route::get('/bills', [App\Http\Controllers\AdminBillController::class, 'index'])
    ->name('bills.index');

Route::get('/bills/{id}', [App\Http\Controllers\AdminBillController::class, 'show'])
    ->name('bills.show');
        Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/create', [AdminController::class, 'create'])->name('create');
    // Route::get('/employees', [AdminUserController::class, 'index'])->name('employees.index');
    // Quản lý Nhân viên (Employees) - Đổi tên cho khớp với Blade
    Route::prefix('employees')->name('employees.')->group(function () {
    // Route chính để hiện giao diện Quản lý nhân viên
    Route::get('/', [AdminUserController::class, 'employeeIndex'])->name('index'); 
    
    Route::post('/store', [AdminUserController::class, 'employeeStore'])->name('store');
    Route::delete('/{id}', [AdminUserController::class, 'employeeDestroy'])->name('destroy');
    Route::post('/shift', [AdminUserController::class, 'updateShift'])->name('shift');
});
        // Quản lý danh mục, người dùng, bàn
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('users', AdminUserController::class);
        Route::resource('tables', AdminTableController::class);
        // Hiển thị form tạo đơn đặt bàn mới
Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');

// Xử lý lưu dữ liệu khi submit form
Route::post('/reservations/store', [ReservationController::class, 'staffStore'])->name('reservations.store');
        // Quản lý thực đơn
        Route::prefix('menus')->name('menus.')->group(function () {
            Route::get('/', [AdminMenuController::class, 'index'])->name('index');
            Route::get('/create', [AdminMenuController::class, 'create'])->name('create');
            Route::post('/store', [AdminMenuController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminMenuController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [AdminMenuController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminMenuController::class, 'destroy'])->name('destroy');
        });
// Quản lý đơn hàng (Admin)
Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');      // đã có
Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
        // Quản lý đặt bàn & đơn hàng
        Route::get('/reservations', [ReservationController::class, 'adminIndex'])->name('reservations.index');
        Route::post('/reservations/{id}/update-status', [ReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
        Route::post('/reservations/{id}/pay', [ReservationController::class, 'pay'])->name('reservations.pay');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    });

    // =========================================================
    // B. HỆ THỐNG STAFF — role_id = 3
    //    Admin cũng được vào nhờ logic trong CheckRole
    // =========================================================
    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        // Danh sách đặt bàn
        Route::get('/reservations', [ReservationController::class, 'adminIndex'])->name('reservations.index');
        Route::post('/reservations/{id}/update-status', [ReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
        Route::post('/reservations/{id}/pay', [ReservationController::class, 'pay'])->name('reservations.pay');

        // Quản lý bàn (Staff xem & cập nhật trạng thái bàn)
        Route::get('/tables', [TableController::class, 'index'])->name('tables.index');     
        Route::get('/tables/create', [TableController::class, 'create'])->name('tables.create');
        // 🔥 BỔ SUNG 2 DÒNG NÀY ĐỂ STAFF ĐƯỢC PHÉP SỬA BÀN:
Route::get('/tables/{table}/edit', [TableController::class, 'edit'])->name('tables.edit');
Route::put('/tables/{table}', [TableController::class, 'update'])->name('tables.update');
        Route::get('/reservations/{id}/edit-items', [StaffOrderController::class, 'editItems'])->name('reservations.edit_items');
        Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
       Route::post('/reservations/store', [ReservationController::class, 'staffStore'])->name('reservations.store');
        Route::post('/reservations/{id}/update-items', [StaffOrderController::class, 'update'])->name('reservations.update_items');
        // Tạo & gửi đơn cho bếp
        Route::controller(StaffOrderController::class)->name('order.')->group(function () {
            Route::get('/reservations/{id}/order/create', 'create')->name('create');
            Route::post('/reservations/{id}/order/store', 'store')->name('store');
            Route::post('/reservations/{id}/order/send-kitchen', 'sendToKitchen')->name('sendToKitchen');
            Route::post('/reservations/{id}/order/update', 'update')->name('update');
        });
    });

    // =========================================================
    // C. HỆ THỐNG CHEF (Đầu bếp) — role_id = 4
    //    Chỉ xem đơn bếp, cập nhật trạng thái từng món
    // =========================================================
    Route::middleware('role:chef')->prefix('chef')->name('chef.')->group(function () {
        Route::get('/dashboard', [OrderController::class, 'chefIndex'])->name('index');

        // Bắt đầu / hoàn thành toàn bộ đơn
        Route::post('/order/{id}/start', [OrderController::class, 'startOrder'])->name('order.start');
        Route::post('/order/{id}/finish', [OrderController::class, 'finishOrder'])->name('order.finish');

        // Cập nhật trạng thái từng món
        Route::post('/item/{reservationId}/{menuId}/status', [OrderController::class, 'updateItemStatus'])->name('item.status');
    });

});

// Chatbot: công khai nhưng giới hạn tần suất (CSRF vẫn được bật, JS đã gửi X-CSRF-TOKEN)
Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])
    ->middleware('throttle:15,1')
    ->name('chatbot.ask');
