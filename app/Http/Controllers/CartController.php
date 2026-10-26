<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * 1. HIỂN THỊ TRANG GIỎ HÀNG & BILL THANH TOÁN
     */
/**
     * 1. HIỂN THỊ TRANG GIỎ HÀNG & BILL THANH TOÁN
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        app(\App\Services\PendingReservationExpiry::class)->expire();
        
        // ✅ DEBUG
        \Log::info('=== CART INDEX DEBUG ===', [
            'user_id' => $userId,
            'user_name' => auth()->user()->name ?? 'No user',
        ]);
        
        // 1. LẤY GIỎ HÀNG TỪ DATABASE
        $dbCart = \App\Models\Cart::with('menu')
                    ->where('user_id', $userId)
                    ->get();

        $cart = $dbCart->map(function($item) {
            return [
                'id'       => $item->menu_id,
                'name'     => $item->menu->name ?? 'Món đã bị xóa',
                'price'    => $item->menu->price ?? 0,
                'quantity' => $item->quantity,
                'image'    => $item->menu->image ?? '',
            ];
        })->toArray();

        // Khi mở từ lịch sử, chỉ tải đúng đặt bàn thuộc tài khoản hiện tại.
        $latestReservation = $request->filled('reservation_id')
            ? Reservation::where('user_id', $userId)->findOrFail($request->integer('reservation_id'))
            : Reservation::where('user_id', $userId)
                ->whereIn('status', Reservation::ACTIVE_STATUSES)
                ->latest('id')
                ->first();
        if ($latestReservation && is_array($latestReservation->cart_data)) {
            $cart = $latestReservation->cart_data;
        }
        $reservationTable = $latestReservation
            ? \App\Models\Table::find($latestReservation->table_id)
            : null;
        $reservationStatusLabel = $latestReservation
            ? \App\Helpers\StatusHelper::getTextWithEmoji($latestReservation->status)
            : 'Chưa đặt';
        $latestPendingReservation = Reservation::where('user_id', $userId)
            ->where('status', 'pending_deposit')
            ->latest('id')
            ->first(['id', 'payment_status']);
        $editableReservationId = $latestPendingReservation?->payment_status === 'unpaid'
            ? $latestPendingReservation->id
            : null;
        $hasTableConflict = $latestReservation
            && \App\Models\Reservation::blockingTable()
                ->where('table_id', $latestReservation->table_id)
                ->where('id', '!=', $latestReservation->id)
                ->exists();
// Chưa có đặt bàn -> phải đi đặt bàn trước, chưa được cọc
$needReservation = !$latestReservation;

// Chỉ được cọc khi: đã có đặt bàn, đang chờ cọc, chưa trả, bàn còn trống, không bị trùng
$canCheckout = $latestReservation
    && $latestReservation->status === 'pending_deposit'
    && $latestReservation->payment_status === 'unpaid'
    && $reservationTable
    && $reservationTable->status === 'empty'
    && !$hasTableConflict
    && (int) $latestReservation->id === (int) $editableReservationId;
        $canEditReservation = $latestReservation
            && $latestReservation->status === 'pending_deposit'
            && $latestReservation->payment_status === 'unpaid'
            && (int) $latestReservation->id === (int) $editableReservationId;
        $checkoutUnavailableMessage = null;
        if ($latestReservation && !$canCheckout) {
            if ($latestReservation->status !== 'pending_deposit'
                || $latestReservation->payment_status !== 'unpaid') {
                $checkoutUnavailableMessage = "Yêu cầu đặt bàn đang ở trạng thái {$reservationStatusLabel}. Vui lòng liên hệ nhân viên để được hỗ trợ.";
            } elseif (!$reservationTable || $reservationTable->status !== 'empty') {
                $tableName = $reservationTable->name ?? ('Số ' . $latestReservation->table_id);
                $checkoutUnavailableMessage = "Bàn {$tableName} hiện không trống. Vui lòng chờ nhân viên dọn bàn hoặc liên hệ nhân viên để được hỗ trợ.";
            } elseif ($hasTableConflict) {
                $tableName = $reservationTable->name;
                $checkoutUnavailableMessage = "Bàn {$tableName} đã được giữ cho khách khác. Vui lòng chọn bàn trống khác hoặc liên hệ nhân viên.";
            } elseif ((int) $latestReservation->id !== (int) $editableReservationId) {
                $checkoutUnavailableMessage = 'Bạn có yêu cầu đặt bàn chờ cọc mới hơn. Hãy tiếp tục thanh toán hoặc chỉnh sửa yêu cầu mới nhất trong lịch sử giao dịch.';
            }
        }

        // ✅ DEBUG
        \Log::info('=== LATEST RESERVATION ===', [
            'found' => $latestReservation ? 'YES' : 'NO',
            'reservation' => $latestReservation ? [
                'id' => $latestReservation->id,
                'full_name' => $latestReservation->full_name,
                'table_id' => $latestReservation->table_id,
            ] : null
        ]);

        if ($latestReservation) {
            // Lấy tên bàn từ bảng tables (nếu không thấy thì in ra mã ID chữa cháy)
            $tableName = $reservationTable?->name ?? ('Số ' . $latestReservation->table_id);
            
            $reservation = [
    'name'      => $latestReservation->full_name,
    'table'     => $tableName,
    'table_id'  => $latestReservation->table_id, // ✅ THÊM DÒNG NÀY
    'date'      => \Carbon\Carbon::parse($latestReservation->reservation_date)->format('d/m/Y'),
    'time'      => \Carbon\Carbon::parse($latestReservation->reservation_time)->format('H:i'),
    'notes'     => $latestReservation->notes ?? 'Không có yêu cầu đặc biệt',
    'status'    => $reservationStatusLabel
];
        } else {
            // Khách chưa đặt bàn, chỉ rẽ ngang vào xem Giỏ hàng
            $reservation = [
    'name'      => auth()->check() ? auth()->user()->name : 'Khách hàng',
    'table'     => 'Chưa chọn',
    'table_id'  => null, // ✅ THÊM DÒNG NÀY
    'date'      => '--/--/----',
    'time'      => '--:--',
    'notes'     => 'Chưa có ghi chú',
    'status'    => $reservationStatusLabel
];
        }

        // 3. TÍNH TỔNG TIỀN
        $total = 0;
        foreach($cart as $item) {
            $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        }

        // Mã khuyến mãi đang gắn với đặt bàn (chỉ để hiển thị; tiền giảm được chốt lúc thanh toán tại quầy)
        $promoInfo = $latestReservation
            ? app(\App\Services\PromotionService::class)->summary($latestReservation, (int) $total)
            : ['promotion' => null, 'discount' => 0, 'error' => null];
        $appliedPromotion  = $promoInfo['promotion'];
        $promotionDiscount = $promoInfo['discount'];
        $promotionError    = $promoInfo['error'];
        // Danh sách mã để khách chọn (dropdown); trang tự đánh dấu mã nào đủ điều kiện theo tổng tiền món
        $promoOptions = ($latestReservation && !$latestReservation->promotion_id)
            ? app(\App\Services\PromotionService::class)->listActive()
            : [];

        // Trả dữ liệu ra view
        return view('cart.index', compact(
            'cart',
            'total',
            'reservation',
            'latestReservation',
            'canCheckout',
            'checkoutUnavailableMessage',
            'needReservation',
            'canEditReservation',
            'editableReservationId',
            'appliedPromotion',
            'promotionDiscount',
            'promotionError',
            'promoOptions'
        ));
    }

    /**
     * 2. THÊM MÓN VÀO GIỎ HÀNG
     */
    /**
     * 2. THÊM MÓN VÀO GIỎ HÀNG
     */
public function addToCart(Request $request)
{
    // 1. Kiểm tra đăng nhập
    if (!auth()->check()) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn cần đăng nhập!'
        ], 401);
    }

    // 2. Validate dữ liệu đầu vào cơ bản
    $data = $request->validate([
        'menu_id' => ['required', 'integer', 'exists:menus,id'],
        'quantity' => 'required|integer|min:1|max:10',
    ], [
        'quantity.min' => 'Số lượng ít nhất phải là 1.',
        'quantity.max' => 'Bạn không thể đặt quá 10 phần cho mỗi món ăn.',
    ]);

    $userId = auth()->id();
    try {
        DB::transaction(function () use ($data, $userId) {
            \App\Models\User::whereKey($userId)->lockForUpdate()->firstOrFail();
            $this->assertCartEditable($userId);
            $menu = Menu::whereKey($data['menu_id'])->lockForUpdate()->firstOrFail();
            $cartItem = Cart::where('user_id', $userId)
                ->where('menu_id', $menu->id)
                ->lockForUpdate()
                ->first();
            $newQuantity = (int) ($cartItem->quantity ?? 0) + (int) $data['quantity'];

            if ($newQuantity > $menu->stock) {
                throw new \DomainException("Món '{$menu->name}' hiện không đủ số lượng trong kho.");
            }

            if ($cartItem) {
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                Cart::create(['user_id' => $userId, 'menu_id' => $menu->id, 'quantity' => $newQuantity]);
            }
            $this->syncCustomerCartToStaff($userId);
        });
    } catch (\DomainException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }

    // 5. Lấy tổng số lượng món trong giỏ để cập nhật badge trên giao diện
    $count = \App\Models\Cart::where('user_id', $userId)->sum('quantity');

    // ✅ PHẢN HỒI THÀNH CÔNG
    return response()->json([
        'success' => true,
        'message' => 'Đã thêm vào thực đơn!',
        'count' => $count
    ]);
}
public function getCart()
{
    $userId = auth()->id();

    $cart = Cart::with('menu')
        ->where('user_id', $userId)
        ->get();

    return response()->json([
        'success' => true,
        'cart' => $cart->map(function ($item) {
            return [
                'id' => $item->menu->id,
                'name' => $item->menu->name,
                'price' => $item->menu->price,
                'image' => $item->menu->image,
                'quantity' => $item->quantity
            ];
        })
    ]);
}
    /**
     * 3. XÓA SẠCH GIỎ HÀNG
     */
    public function clear()
    {
        $userId = Auth::id();
        try {
            DB::transaction(function () use ($userId) {
                \App\Models\User::whereKey($userId)->lockForUpdate()->firstOrFail();
                $this->assertCartEditable($userId);
                Cart::where('user_id', $userId)->delete();
                $this->syncCustomerCartToStaff($userId);
            });
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        
        session()->forget(['cart']);
        return redirect()->route('reservation.index')->with('success', 'Đã xóa sạch giỏ hàng!');
    }

    /**
     * 4. THANH TOÁN (Xử lý chốt đơn cuối cùng)
     */
/**
     * 4. THANH TOÁN (Xử lý chốt đơn cuối cùng)
     */
/**
     * 4. THANH TOÁN (Xử lý chốt đơn cuối cùng)
     */
    public function checkout(Request $request)
    {
        $userId = auth()->id();
        $data = $request->validate([
            'reservation_id' => ['nullable', 'integer'],
            'payment_method' => ['nullable', 'in:BANK'],
        ]);

        try {
            $reservation = DB::transaction(function () use ($data, $userId, $request) {
                \App\Models\User::whereKey($userId)->lockForUpdate()->firstOrFail();

                $reservationQuery = Reservation::where('user_id', $userId)->lockForUpdate();
                $reservation = !empty($data['reservation_id'])
                    ? $reservationQuery->findOrFail($data['reservation_id'])
                    : $reservationQuery
                        ->whereIn('status', Reservation::ACTIVE_STATUSES)
                        ->latest('id')
                        ->first();

                if (!$reservation || !$reservation->table_id) {
                    throw new \DomainException('Vui lòng nhập thông tin đặt bàn trước khi thanh toán cọc.');
                }
                $latestPending = Reservation::where('user_id', $userId)
                    ->where('status', 'pending_deposit')
                    ->latest('id')
                    ->lockForUpdate()
                    ->first(['id', 'payment_status']);
                if (
                    !$latestPending
                    || $latestPending->payment_status !== 'unpaid'
                    || (int) $reservation->id !== (int) $latestPending->id
                ) {
                    throw new \DomainException('Chỉ yêu cầu đặt bàn đang chờ cọc mới nhất có thể tiếp tục thanh toán.');
                }
                if ($reservation->status !== 'pending_deposit' || $reservation->payment_status !== 'unpaid') {
                    throw new \DomainException('Yêu cầu này không còn chờ thanh toán cọc hoặc đã được gửi xác nhận.');
                }

                $table = \App\Models\Table::whereKey($reservation->table_id)->lockForUpdate()->first();
                $conflict = !$table || $table->status !== 'empty'
                    || Reservation::blockingTable()
                        ->where('table_id', $reservation->table_id)
                        ->where('id', '!=', $reservation->id)
                        ->exists();
                if ($conflict) {
                    throw new \DomainException('Bàn này không còn trống. Vui lòng chọn bàn khác hoặc liên hệ nhân viên.');
                }

                $cartItems = Cart::where('user_id', $userId)->lockForUpdate()->get();
                $menus = Menu::whereIn('id', $cartItems->pluck('menu_id')->unique())
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                $wanted = [];
                foreach ($cartItems as $item) {
                    $menu = $menus->get($item->menu_id);
                    if (!$menu) {
                        throw new \DomainException('Một món trong giỏ không còn tồn tại. Vui lòng cập nhật giỏ hàng.');
                    }
                    if ((int) $item->quantity > (int) $menu->stock) {
                        throw new \DomainException("Món '{$menu->name}' hiện không đủ số lượng trong kho.");
                    }
                    if ((int) $item->quantity > 0) {
                        $wanted[(int) $menu->id] = (int) $item->quantity;
                    }
                }

                $items = app(\App\Services\ReservationItemService::class);
                $items->syncCustomerCart($reservation, $wanted, $userId);
                $reservation->refresh();
                $reservation->deposit_amount = Reservation::calcDeposit($items->total($reservation));
                $reservation->save();

                $order = Order::where('reservation_id', $reservation->id)
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();
                if ($cartItems->isNotEmpty() && !$order) {
                    $order = Order::create([
                        'user_id' => $userId,
                        'reservation_id' => $reservation->id,
                        'code' => (string) random_int(100000, 999999),
                        'total_price' => $reservation->total_price,
                        'status' => 'pending_payment',
                        'payment_method' => 'BANK',
                        'table_number' => $reservation->table_id,
                        'name' => $reservation->full_name,
                        'phone' => $reservation->phone,
                        'notes' => $request->input('order_notes'),
                    ]);

                    foreach ($cartItems as $item) {
                        $menu = $menus->get($item->menu_id);
                        $menu->stock = (int) $menu->stock - (int) $item->quantity;
                        $menu->save();
                    }
                } elseif ($order && !in_array($order->status, ['cancelled', 'paid', 'completed'], true)) {
                    $order->update([
                        'total_price' => $reservation->total_price,
                        'status' => 'pending_payment',
                        'table_number' => $reservation->table_id,
                    ]);
                } elseif ($order && in_array($order->status, ['cancelled', 'paid', 'completed'], true)) {
                    throw new \DomainException('Đơn hàng liên kết đã được xử lý. Vui lòng liên hệ nhân viên.');
                }

                $reservation->payment_status = 'deposit_submitted';
                $reservation->save();
                $table->update(['status' => 'reserved']);

                if ($order) {
                    $items->syncCaches($reservation);
                }

                Cart::where('user_id', $userId)->delete();

                return $reservation;
            });
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        session()->forget('cart');

        $successMessage = $reservation->total_price > 0
            ? 'Đã ghi nhận thông tin chuyển khoản cọc. Nhân viên sẽ xác nhận sau khi đối soát.'
            : 'Đã ghi nhận đặt cọc giữ bàn. Nhân viên sẽ xác nhận sau khi nhận được tiền.';

        return redirect()->route('orders.history')->with('success', $successMessage);
    }
public function removeItem($id)
{
    $userId = auth()->id();

    // Tìm món đó trong giỏ của đúng User này và xóa
    \App\Models\Cart::where('user_id', $userId)
                    ->where('menu_id', $id)
                    ->delete();

    // THÊM DÒNG NÀY: Xóa thêm giỏ hàng trong Session (nếu Nhi có dùng session để hiện badge số lượng)
    session()->forget('cart');
    return redirect()->route('order-history') 
        ->with('success', 'Đơn hàng của bạn đã được gửi đi thành công!');
}


public function history()
{
    // Lấy danh sách đơn hàng của người dùng đang đăng nhập
    // Sắp xếp theo thời gian mới nhất
    $orders = \App\Models\Order::where('user_id', auth()->id())->get();
    return view('cart.history', compact('orders')); // Dấu chấm đại diện cho thư mục cart/
}
/**
     * CẬP NHẬT SỐ LƯỢNG (KHI KHÁCH BẤM +/-)
     */
   public function update(Request $request)
{
    $data = $request->validate([
        'id' => ['required', 'integer', 'exists:menus,id'],
        'quantity' => ['required', 'integer', 'min:0', 'max:50'],
    ]);
    $userId = auth()->id();
    try {
        DB::transaction(function () use ($data, $userId) {
            \App\Models\User::whereKey($userId)->lockForUpdate()->firstOrFail();
            $this->assertCartEditable($userId);
            $menu = Menu::whereKey($data['id'])->lockForUpdate()->firstOrFail();
            $quantity = (int) $data['quantity'];
            if ($quantity > (int) $menu->stock) {
                throw new \DomainException("Món '{$menu->name}' hiện chỉ còn {$menu->stock} phần.");
            }

            $item = Cart::where('user_id', $userId)->where('menu_id', $menu->id)->lockForUpdate()->first();
            if ($quantity === 0) {
                $item?->delete();
            } elseif ($item) {
                $item->update(['quantity' => $quantity]);
            }
            $this->syncCustomerCartToStaff($userId);
        });
    } catch (\DomainException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }

    return response()->json(['success' => true]);
}

    /**
     * XÓA MÓN ĂN (KHI KHÁCH BẤM NÚT THÙNG RÁC)
     */
    public function remove($id)
    {
        $userId = auth()->id();
        try {
            DB::transaction(function () use ($id, $userId) {
                \App\Models\User::whereKey($userId)->lockForUpdate()->firstOrFail();
                $this->assertCartEditable($userId);
                Cart::where('user_id', $userId)->where('menu_id', $id)->delete();
                $this->syncCustomerCartToStaff($userId);
            });
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Xóa luôn session phụ trợ (nếu có dùng để hiển thị icon giỏ hàng)
        $cart = session()->get('cart', []);
        if(isset($cart[$id])) { unset($cart[$id]); }
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Đã xóa món ăn khỏi giỏ hàng!');
    }

    private function assertCartEditable(int $userId): void
    {
        $reservation = Reservation::where('user_id', $userId)
            ->where('status', 'pending_deposit')
            ->latest('id')
            ->lockForUpdate()
            ->first();

        if ($reservation && $reservation->payment_status !== 'unpaid') {
            throw new \DomainException('Bạn đã gửi xác nhận chuyển khoản. Không thể chỉnh sửa món trong yêu cầu này.');
        }
    }

    /**
     * HÀM TIỆN ÍCH: ĐỒNG BỘ TỪ GIỎ HÀNG KHÁCH -> MÀN HÌNH NHÂN VIÊN
     */
    /**
     * HÀM TIỆN ÍCH: ĐỒNG BỘ TỪ GIỎ HÀNG KHÁCH -> MÀN HÌNH NHÂN VIÊN & DATABASE CHUẨN
     */
    private function syncCustomerCartToStaff($userId)
    {
        $cartItems = Cart::where('user_id', $userId)->lockForUpdate()->get();
        $menus = Menu::whereIn('id', $cartItems->pluck('menu_id')->unique())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
        $reservation = Reservation::where('user_id', $userId)
            ->where('status', 'pending_deposit')
            ->latest('id')
            ->lockForUpdate()
            ->first();

        if ($reservation) {
            if ($reservation->payment_status !== 'unpaid') {
                throw new \DomainException('Bạn đã gửi xác nhận chuyển khoản. Không thể chỉnh sửa món trong yêu cầu này.');
            }

            $wanted = [];
            foreach ($cartItems as $item) {
                if (!$menus->has($item->menu_id)) {
                    throw new \DomainException('Một món trong giỏ không còn tồn tại. Vui lòng cập nhật giỏ hàng.');
                }
                $wanted[(int) $item->menu_id] = (int) $item->quantity;
            }

            $itemService = app(\App\Services\ReservationItemService::class);
            $itemService->syncCustomerCart($reservation, $wanted, $userId);
            $reservation->refresh();
            $reservation->deposit_amount = Reservation::calcDeposit($itemService->total($reservation));
            $reservation->save();
        }
    }
}