<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Services\ReservationItemService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Khách đặt hàng online (không qua đặt bàn)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'table_number'   => ['nullable', 'string', 'max:50'],
            'items'          => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id'     => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        try {
            $order = DB::transaction(function () use ($data) {
                // Giá, tên món LUÔN lấy từ DB — không tin dữ liệu client gửi lên
                $menus = Menu::whereIn('id', collect($data['items'])->pluck('id'))->get()->keyBy('id');

                $total = 0;
                $lines = [];
                foreach ($data['items'] as $item) {
                    $menu = $menus[$item['id']];
                    $qty  = (int) $item['quantity'];
                    $total += $menu->price * $qty;
                    $lines[] = [
                        'menu_id'      => $menu->id,
                        'product_name' => $menu->name,
                        'quantity'     => $qty,
                        'price'        => $menu->price,
                    ];
                }

                $order = Order::create([
                    'user_id'      => Auth::id(),
                    'table_number' => $data['table_number'] ?? 'Mang về',
                    'total_price'  => $total,
                    'status'       => 'pending',
                    'name'         => Auth::user()->name,
                    'phone'        => Auth::user()->phone,
                ]);
                $order->orderItems()->createMany($lines);

                return $order;
            });

            return response()->json(['success' => true, 'message' => 'Đã chuyển đơn hàng vào hệ thống!', 'order_id' => $order->id]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Không thể tạo đơn hàng, vui lòng thử lại.'], 500);
        }
    }

    /**
     * Lịch sử đơn hàng của khách
     */
    public function history()
    {
       // Chỉ trả đơn CỦA CHÍNH người đăng nhập (tránh lộ đơn của khách khác)
       $orders = Order::with(['items.menu'])
                      ->where('user_id', Auth::id())
                      ->latest()
                      ->paginate(20);
    
    return view('orders.history', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng — FIX: Admin có thể xem bất kỳ đơn nào
     */
    public function show($id)
{
    $order = Order::with([
        'items',
        'items.menu',
        'user',
        'table'
    ])->findOrFail($id);

    return view('admin.orders.show', compact('order'));
}

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->input('status')]);
        return redirect()->back()->with('success', 'Đã cập nhật trạng thái!');
    }

    /**
     * Hủy đơn hàng (chỉ khi ở trạng thái pending)
     */
    public function cancel($id)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($id); // chỉ huỷ đơn của chính mình

            // Chỉ cho phép hủy khi ở trạng thái pending
            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể hủy đơn ở trạng thái chờ duyệt!'
                ], 400);
            }

            // Cập nhật trạng thái thành cancelled
            $order->update(['status' => 'cancelled']);

            // Broadcast cập nhật cho admin, nhân viên và khách hàng qua Echo
            event(new \App\Events\OrderStatusUpdated($order));

            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã bị hủy thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // =========================================================
    // MÀN HÌNH BẾP (KDS - Kitchen Display System)
    // Luồng: pending → confirmed → arrived → serving → ready → served
    // =========================================================

    /**
     * Dashboard bếp — Bếp thấy đơn khi status = 'serving' hoặc 'arrived'
     */
    public function chefIndex(ReservationItemService $items)
    {
        // Đơn cũ (đang ở bếp) chưa có dòng trong reservation_items: đưa sang trước khi hiển thị.
        Reservation::whereIn('status', ['arrived', 'serving'])->doesntHave('reservationItems')->get()
            ->each(fn ($r) => $items->ensureImported($r));

        $unfinished = fn ($q) => $q->active()->whereIn('status', ['pending', 'cooking']);

        // Đơn cần nấu: đang ở bếp (arrived/serving) HOẶC đã ready/served nhưng có món gọi thêm chưa nấu xong
        $pendingOrders = Reservation::with(['table', 'reservationItems' => fn ($q) => $q->active()])
            ->whereIn('status', ['arrived', 'serving', 'ready', 'served'])
            ->where(function ($q) use ($unfinished) {
                $q->whereIn('status', ['arrived', 'serving'])
                  ->orWhereHas('reservationItems', $unfinished);
            })
            ->oldest('updated_at')
            ->get();

        // Đơn đã nấu xong (chờ Staff bưng ra): không còn món chờ/đang nấu
        $readyOrders = Reservation::where('status', 'ready')
            ->whereDoesntHave('reservationItems', $unfinished)
            ->latest('updated_at')
            ->get();

        $pendingItems = 0;
        $cookingItems = 0;
        foreach ($pendingOrders as $res) {
            foreach ($res->reservationItems as $line) {
                if ($line->status === ReservationItem::STATUS_PENDING) $pendingItems += $line->active_quantity;
                if ($line->status === ReservationItem::STATUS_COOKING) $cookingItems += $line->active_quantity;
            }
        }

        return view('chef.dashboard', compact('pendingOrders', 'readyOrders', 'pendingItems', 'cookingItems'));
    }

    /** Trạng thái đơn mà bếp còn thao tác được (có thể có món gọi thêm sau khi đã ready/served). */
    private const CHEF_OPEN = ['arrived', 'serving', 'ready', 'served'];

    /**
     * Route CŨ theo menu_id (giữ để tương thích): áp dụng cho các dòng còn hiệu lực của món đó.
     * Chỉ tiến lên, không đụng dòng đã bưng.
     */
    public function updateItemStatus(Request $request, $reservationId, $menuId, ReservationItemService $items)
    {
        $data = $request->validate([
            'chef_status' => ['required', Rule::in(['pending', 'cooking', 'done'])],
        ]);

        $reservation = Reservation::findOrFail($reservationId);
        abort_unless(in_array($reservation->status, self::CHEF_OPEN, true), 422, 'Đơn này không còn ở bếp.');

        try {
            $items->setMenuStatus($reservation, (int) $menuId, $data['chef_status']);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Món đầu tiên được nấu → đơn chuyển sang 'serving'
        if ($data['chef_status'] === 'cooking' && $reservation->status === 'arrived') {
            $reservation->update(['status' => 'serving']);
        }

        return back()->with('success', 'Đã cập nhật trạng thái món!');
    }

    /**
     * Đổi trạng thái đúng MỘT lần gọi món (một dòng). Hai lần gọi cùng món không còn dùng chung trạng thái.
     */
    public function updateLineStatus(Request $request, ReservationItem $item, ReservationItemService $items)
    {
        $data = $request->validate([
            'chef_status' => ['required', Rule::in(['pending', 'cooking', 'done'])],
        ]);

        $reservation = Reservation::findOrFail($item->reservation_id);
        abort_unless(in_array($reservation->status, self::CHEF_OPEN, true), 422, 'Đơn này không còn ở bếp.');

        try {
            $items->setLineStatus($item, $data['chef_status']);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($data['chef_status'] === 'cooking' && $reservation->status === 'arrived') {
            $reservation->update(['status' => 'serving']);
        }

        return back()->with('success', 'Đã cập nhật trạng thái món!');
    }

    /**
     * Bếp bấm "Bắt đầu nấu" toàn bộ đơn: chỉ các dòng đang chờ chuyển sang đang nấu.
     */
    public function startOrder($id, ReservationItemService $items)
    {
        $reservation = Reservation::findOrFail($id);

        if (!in_array($reservation->status, self::CHEF_OPEN, true)) {
            return back()->with('error', 'Đơn này không ở trạng thái chờ nấu.');
        }

        $changed = $items->startAll($reservation);

        if ($reservation->status === 'arrived') {
            $reservation->update(['status' => 'serving']);
        }

        return back()->with(
            $changed > 0 ? 'success' : 'error',
            $changed > 0 ? 'Đã bắt đầu chế biến các món đang chờ!' : 'Không có món nào đang chờ nấu.'
        );
    }

    /**
     * Bếp bấm "Nấu xong": các dòng chờ/đang nấu => xong.
     * Đơn đang 'serving' => 'ready' (như trước). Đơn đã ready/served (chỉ nấu món gọi thêm) giữ nguyên trạng thái.
     */
    public function finishOrder($id, ReservationItemService $items)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status === 'serving') {
            if (!Reservation::canTransition($reservation->status, 'ready')) {
                return back()->with('error', 'Đơn chưa ở trạng thái đang nấu nên không thể báo xong.');
            }
        } elseif (!in_array($reservation->status, ['ready', 'served'], true)) {
            return back()->with('error', 'Đơn chưa ở trạng thái đang nấu nên không thể báo xong.');
        }

        $items->finishAll($reservation);

        if ($reservation->status === 'serving') {
            $reservation->update(['status' => 'ready']);
            if ($order = $reservation->linkedOrder()) {
                $order->update(['status' => 'ready']);
            }
        }

        return back()->with('success', 'Bếp đã nấu xong! Staff hãy bưng món ra.');
    }

    /**
     * Bếp đánh dấu đơn đã hoàn tất (Served)
     */
    public function markAsDone($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'served']);
        return back()->with('success', 'Đã báo hoàn thành!');
    }
    // Danh sách đơn (Admin)
public function adminIndex()
{
    $orders = Order::with(['user', 'items'])->latest()->paginate(20);
    return view('admin.orders.index', compact('orders'));
}

// Form sửa trạng thái
public function edit($id)
{
    $order = Order::with('user')->findOrFail($id);
    return view('admin.orders.edit', compact('order'));
}

// Lưu trạng thái mới
// Lưu trạng thái mới (Hàm cập nhật trạng thái dành cho Admin)
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'pending_payment', 'serving', 'ready', 'paid', 'completed', 'cancelled'])],
        ]);

        $order = Order::with('orderItems')->findOrFail($id);
        $isReservationOrder = $order->reservation_id !== null;
        if (!$isReservationOrder && is_numeric($order->table_number)) {
            $isReservationOrder = Reservation::where('user_id', $order->user_id)
                ->where('table_id', (int) $order->table_number)
                ->whereIn('status', Reservation::ACTIVE_STATUSES)
                ->exists();
        }
        
        // Lưu lại trạng thái cũ của hóa đơn trước khi cập nhật dữ liệu mới
        $oldStatus = $order->status; 
        $newStatus = $request->status;

        // Tiến hành cập nhật trạng thái mới từ form gửi lên
        $order->update(['status' => $newStatus]);

        // 🔥 LOGIC NGHIỆP VỤ: Kích hoạt gửi Gmail khi Admin duyệt thanh toán thành công
        // Nếu đơn hàng cũ ở trạng thái chờ tiền (pending_payment) và được duyệt sang trạng thái hợp lệ (paid hoặc pending)
        if (!$isReservationOrder && $oldStatus === 'pending_payment' && ($newStatus === 'paid' || $newStatus === 'pending')) {
            
            // Tái cấu trúc danh sách món ăn từ mối quan hệ orderItems trong Database để truyền sang Mail
            $cartToEmail = [];
            foreach ($order->orderItems as $item) {
                $cartToEmail[] = [
                    'name'     => $item->product_name,
                    'quantity' => $item->quantity,
                    'price'    => $item->price,
                    'total'    => $item->price * $item->quantity
                ];
            }

            $mailData = [
                'invoice' => $order->code,
                'total'   => $order->total_price,
                'table'   => $order->table_number,
                'cart'    => $cartToEmail,
                'status'  => $newStatus,
            ];

            // Tìm thông tin email của tài khoản khách hàng để thực hiện gửi
            $customerEmail = $order->user->email ?? null;

            if ($customerEmail) {
                try {
                    \Illuminate\Support\Facades\Mail::to($customerEmail)->send(new \App\Mail\OrderConfirmed($mailData));
                } catch (\Throwable $e) {
                    \Log::error('Standalone bank order email failed.', [
                        'order_id' => $order->id,
                        'exception' => $e,
                    ]);
                }
            } else {
                \Log::warning('Standalone bank order email skipped because the customer has no email address.', [
                    'order_id' => $order->id,
                ]);
            }
        }

        return redirect()->route('admin.orders.show', $id)
                         ->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    }

// Xoá đơn
public function destroy($id)
{
    Order::findOrFail($id)->delete();
    return redirect()->route('admin.index')
                     ->with('success', 'Đã xoá đơn hàng!');
}
/**
     * Hiển thị lịch làm việc trong tuần của Đầu bếp đang đăng nhập
     */
    public function chefSchedule()
    {
        $user = auth()->user();

        // 7 ngày trong tuần
        $days = [
            'T2' => 'Thứ Hai',
            'T3' => 'Thứ Ba',
            'T4' => 'Thứ Tư',
            'T5' => 'Thứ Năm',
            'T6' => 'Thứ Sáu',
            'T7' => 'Thứ Bảy',
            'CN' => 'Chủ Nhật',
        ];

        // Lấy lịch ca của Chef
        $myShifts = $user->shifts ?? [];

        // Cấu hình nhãn, giờ và màu sắc
        $shiftConfigs = [
            'morning'   => ['label' => 'Ca Sáng',  'time' => '06:00 – 13:00', 'color' => '#1d4ed8', 'bg' => '#eff6ff', 'badge' => '🌅'],
            'afternoon' => ['label' => 'Ca Chiều', 'time' => '13:00 – 20:00', 'color' => '#854d0e', 'bg' => '#fefce8', 'badge' => '🌇'],
            'full'      => ['label' => 'Cả Ngày',  'time' => '06:00 – 20:00', 'color' => '#7e22ce', 'bg' => '#fdf4ff', 'badge' => '⭐'],
            'off'       => ['label' => 'Nghỉ',     'time' => 'Không có ca',   'color' => '#94a3b8', 'bg' => '#f1f5f9', 'badge' => '☕'],
        ];

        // Highlight thứ hôm nay
        $todayMap = [1 => 'T2', 2 => 'T3', 3 => 'T4', 4 => 'T5', 5 => 'T6', 6 => 'T7', 0 => 'CN'];
        $todayKey = $todayMap[now()->dayOfWeek] ?? 'T2';

        return view('chef.schedule', compact('days', 'myShifts', 'shiftConfigs', 'todayKey'));
    }
}