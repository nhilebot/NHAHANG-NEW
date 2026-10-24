<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;

class StaffDashboardController extends Controller
{
    public function index()
    {
        Table::releaseExpiredCleaning();

        // ---- Bàn ----
        $tables = Table::orderBy('name')->get();
        $tableStats = [
            'empty'    => $tables->where('status', 'empty')->count(),
            'reserved' => $tables->where('status', 'reserved')->count(),
            'occupied' => $tables->where('status', 'occupied')->count(),
            'cleaning' => $tables->where('status', 'cleaning')->count(),
        ];

        // ---- Thu tiền hôm nay (phần thu tại quầy) ----
        $paidToday = Reservation::whereIn('status', [...Reservation::PAID_STATUSES, 'completed'])
            ->whereDate('paid_at', today());
        $revenueToday = (int) (clone $paidToday)->sum('paid_amount');
        $billsToday   = (clone $paidToday)->count();

        // ---- Việc cần làm ----
        $with = ['table'];
        $groups = [
            [
                'title'  => 'Chờ xác nhận cọc',
                'icon'   => '🪙',
                'color'  => '#f59e0b',
                'items'  => Reservation::with($with)->where('status', 'pending_deposit')
                                ->where('payment_status', 'deposit_submitted')->latest()->get(),
                'action' => ['type' => 'status', 'value' => 'pending', 'label' => 'Xác nhận cọc',
                             'confirm' => 'Xác nhận đã nhận tiền cọc?'],
            ],
            [
                'title'  => 'Chờ duyệt đơn',
                'icon'   => '📝',
                'color'  => '#0ea5e9',
                'items'  => Reservation::with($with)->where('status', 'pending')->latest()->get(),
                'action' => ['type' => 'status', 'value' => 'confirmed', 'label' => 'Duyệt đơn'],
            ],
            [
                'title'  => 'Khách sắp đến',
                'icon'   => '🚪',
                'color'  => '#3b82f6',
                'items'  => Reservation::with($with)->where('status', 'confirmed')
                                ->orderBy('reservation_date')->orderBy('reservation_time')->get(),
                'action' => ['type' => 'status', 'value' => 'arrived', 'label' => 'Khách vào bàn'],
            ],
            [
                'title'  => 'Khách đã vào, chưa báo bếp',
                'icon'   => '🔔',
                'color'  => '#eab308',
                'items'  => Reservation::with($with)->where('status', 'arrived')->latest()->get(),
                'action' => ['type' => 'status', 'value' => 'serving', 'label' => 'Báo bếp nấu'],
            ],
            [
                'title'  => 'Món đã xong, chờ bưng',
                'icon'   => '🍽',
                'color'  => '#8b5cf6',
                'items'  => Reservation::with($with)->where('status', 'ready')->latest()->get(),
                'action' => ['type' => 'status', 'value' => 'served', 'label' => 'Đã bưng món'],
            ],
            [
                'title'  => 'Chờ thanh toán',
                'icon'   => '💰',
                'color'  => '#10b981',
                'items'  => Reservation::with($with)->where('status', 'served')->latest()->get(),
                'action' => ['type' => 'link', 'label' => 'Thanh toán', 'route' => 'staff.reservations.checkout'],
            ],
            [
                'title'  => 'Đã thanh toán, chờ dọn bàn',
                'icon'   => '🧹',
                'color'  => '#64748b',
                'items'  => Reservation::with($with)->whereIn('status', Reservation::PAID_STATUSES)->latest()->get(),
                'action' => ['type' => 'status', 'value' => 'completed', 'label' => 'Xong & Dọn bàn'],
            ],
        ];

        $cooking = Reservation::where('status', 'serving')->count();
        $totalTasks = collect($groups)->sum(fn ($g) => $g['items']->count());

        $recentBills = Reservation::with('table')
            ->whereIn('status', [...Reservation::PAID_STATUSES, 'completed'])
            ->latest('paid_at')->take(5)->get();

        return view('staff.dashboard', compact(
            'tables', 'tableStats', 'revenueToday', 'billsToday',
            'groups', 'cooking', 'totalTasks', 'recentBills'
        ));
    }
    /**
     * Hiển thị lịch làm việc trong tuần của nhân viên đang đăng nhập
     */
    public function schedule()
    {
        $user = auth()->user();

        // Định nghĩa 7 ngày trong tuần
        $days = [
            'T2' => 'Thứ Hai',
            'T3' => 'Thứ Ba',
            'T4' => 'Thứ Tư',
            'T5' => 'Thứ Năm',
            'T6' => 'Thứ Sáu',
            'T7' => 'Thứ Bảy',
            'CN' => 'Chủ Nhật',
        ];

        // Lấy lịch ca của user (hỗ trợ cả cột JSON shifts hoặc Accessor getShiftsAttribute)
        $myShifts = $user->shifts ?? [];

        // Thông tin chi tiết từng loại ca
        // Nhãn, giờ, màu của từng ca lấy từ config/shifts.php (qua ShiftSchedule)
        $shiftConfigs = \App\Support\ShiftSchedule::viewConfigs();

        // Xác định thứ hôm nay để làm nổi bật (Highlight today)
        $todayMap = [1 => 'T2', 2 => 'T3', 3 => 'T4', 4 => 'T5', 5 => 'T6', 6 => 'T7', 0 => 'CN'];
        $todayKey = $todayMap[now()->dayOfWeek] ?? 'T2';

        return view('staff.schedule', compact('days', 'myShifts', 'shiftConfigs', 'todayKey'));
    }
}