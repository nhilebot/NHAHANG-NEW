<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\ReservationItem;

class AdminController extends Controller
{
    public function __construct()
    {
        // Chỉ cần auth — việc kiểm tra role:2 đã được xử lý ở routes/web.php
        $this->middleware('auth');
    }

    public function index()
    {
        $reservations = Reservation::with(['user', 'table'])->latest()->get();
        $orders       = Order::with('user')->latest()->get();
        $menus        = Menu::all();

        $totalRevenue = Order::sum('total_price');
        $totalToday   = Order::whereDate('created_at', today())->sum('total_price');
        $totalMonth   = Order::whereMonth('created_at', now()->month)->sum('total_price');

        // ── MÓN BÁN CHẠY NHẤT ──────────────────────────────────────────────
        // Chỉ tính món của đơn mà khách ĐÃ THANH TOÁN PHẦN CÒN LẠI:
        //  (1) Đơn đặt bàn: Reservation::pay() chuyển status -> paid_cash / paid_transfer (sau đó completed).
        //      Món lấy từ reservation_items (nguồn chính), số lượng = quantity - cancelled_quantity.
        //  (2) Đơn lẻ không gắn đặt bàn (OrderController::store, reservation_id = NULL): tính khi status = paid / completed.
        //      Đơn gắn đặt bàn KHÔNG lấy từ order_items vì bảng đó chỉ là bản sao của reservation_items (tránh cộng 2 lần).
        $paidReservationStatuses = array_merge(Reservation::PAID_STATUSES, ['completed']);

        $soldFromReservations = ReservationItem::query()
            ->join('reservations', 'reservations.id', '=', 'reservation_items.reservation_id')
            ->whereIn('reservations.status', $paidReservationStatuses)
            ->groupBy('reservation_items.menu_id')
            ->selectRaw('reservation_items.menu_id as menu_id, MAX(reservation_items.menu_name) as name,
                         SUM(reservation_items.quantity - reservation_items.cancelled_quantity) as qty');

        $soldFromOrders = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNull('orders.reservation_id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->groupBy('order_items.menu_id')
            ->selectRaw('order_items.menu_id as menu_id, MAX(order_items.product_name) as name,
                         SUM(order_items.quantity) as qty');

        $topRows = DB::query()
            ->fromSub($soldFromReservations->unionAll($soldFromOrders), 'sold')
            ->selectRaw('menu_id, MAX(name) as name, SUM(qty) as qty')
            ->groupBy('menu_id')
            ->havingRaw('SUM(qty) > 0')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // Lấy tên hiện tại + ảnh từ bảng menus (món đã xóa thì dùng tên lưu trong đơn)
        $topMenus  = Menu::whereIn('id', $topRows->pluck('menu_id')->filter())->get()->keyBy('id');
        $topDishes = $topRows->map(fn ($r) => [
            'name' => $topMenus[$r->menu_id]->name ?? $r->name,
            'qty'  => (int) $r->qty,
            'menu' => $topMenus[$r->menu_id] ?? null,
        ])->values();

        // Thống kê nhanh trạng thái đặt bàn
        $stats = [
            'pending'   => $reservations->where('status', 'pending')->count(),
            'confirmed' => $reservations->where('status', 'confirmed')->count(),
            'serving'   => $reservations->where('status', 'serving')->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
        ];

        // 🟢 1. Xác định thứ hôm nay (T2 -> CN)
        $dayMap = [
            Carbon::MONDAY    => 'T2',
            Carbon::TUESDAY   => 'T3',
            Carbon::WEDNESDAY => 'T4',
            Carbon::THURSDAY  => 'T5',
            Carbon::FRIDAY    => 'T6',
            Carbon::SATURDAY  => 'T7',
            Carbon::SUNDAY    => 'CN',
        ];
        $todayKey = $dayMap[Carbon::now()->dayOfWeek] ?? 'T2';

        // 🟢 2. Lấy toàn bộ danh sách nhân sự (Staff và Chef)
        $employees = User::whereIn('role_id', [Role::STAFF, Role::CHEF])
            ->orWhereIn('role', ['staff', 'chef'])
            ->get();

        $todayStaffOnDuty = 0;
        $todayChefOnDuty  = 0;
        $shiftDetails = [
            'staff' => ['morning' => 0, 'afternoon' => 0, 'full' => 0],
            'chef'  => ['morning' => 0, 'afternoon' => 0, 'full' => 0],
        ];

        // Mảng chứa thông tin chi tiết phục vụ hiển thị Modal
        $staffListToday = [];
        $chefListToday  = [];

        // Tên ca kèm giờ (hiển thị trong modal danh sách nhân viên trực hôm nay), lấy từ config/shifts.php
        $shiftNames = \App\Support\ShiftSchedule::names();

        // 🟢 3. Lọc người có lịch trực hôm nay (loại bỏ 'off')
        foreach ($employees as $emp) {
            $userShifts = $emp->shifts ?? [];
            $shiftToday = $userShifts[$todayKey] ?? 'off';

            if ($shiftToday !== 'off') {
                $isChef = ($emp->role_id == Role::CHEF || $emp->role === 'chef');

                $empData = [
                    'name'       => $emp->name,
                    'email'      => $emp->email,
                    'phone'      => $emp->phone ?? '—',
                    'shift_code' => $shiftToday,
                    'shift_name' => $shiftNames[$shiftToday] ?? $shiftToday,
                ];

                if ($isChef) {
                    $todayChefOnDuty++;
                    if (isset($shiftDetails['chef'][$shiftToday])) {
                        $shiftDetails['chef'][$shiftToday]++;
                    }
                    $chefListToday[] = $empData;
                } else {
                    $todayStaffOnDuty++;
                    if (isset($shiftDetails['staff'][$shiftToday])) {
                        $shiftDetails['staff'][$shiftToday]++;
                    }
                    $staffListToday[] = $empData;
                }
            }
        }

        // Lưu ý: View hiển thị là admin.index
        return view('admin.index', compact(
            'reservations',
            'orders',
            'menus',
            'topDishes',
            'totalRevenue',
            'totalToday',
            'totalMonth',
            'stats',
            'todayKey',
            'todayStaffOnDuty',
            'todayChefOnDuty',
            'shiftDetails',
            'staffListToday',
            'chefListToday'
        ));
    }

    public function updateStock(Request $request, Menu $menu)
    {
        $request->validate(['stock' => 'required|integer|min:0']);
        $menu->update(['stock' => $request->stock]);
        return redirect()->back()->with('success', 'Cập nhật stock thành công.');
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,serving,paid']);
        $order->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}