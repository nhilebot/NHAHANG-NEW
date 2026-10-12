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
    public function index()
    {
        $userId = auth()->id();
        
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

        // 2. LẤY THÔNG TIN ĐẶT BÀN MỚI NHẤT TỪ DATABASE (Tuyệt đối không sợ mất Session)
        $latestReservation = \App\Models\Reservation::where('user_id', $userId)
                            ->whereIn('status', Reservation::ACTIVE_STATUSES)
                            ->orderBy('created_at', 'desc')
                            ->first();
        $reservationTable = $latestReservation
            ? \App\Models\Table::find($latestReservation->table_id)
            : null;
        $reservationStatusLabel = $latestReservation
            ? \App\Helpers\StatusHelper::getTextWithEmoji($latestReservation->status)
            : 'Chưa đặt';
        $hasTableConflict = $latestReservation
            && \App\Models\Reservation::blockingTable()
                ->where('table_id', $latestReservation->table_id)
                ->where('id', '!=', $latestReservation->id)
                ->exists();
        $canCheckout = !$latestReservation || (
            $latestReservation->status === 'pending_deposit'
            && $latestReservation->payment_status === 'unpaid'
            && $reservationTable
            && $reservationTable->status === 'empty'
            && !$hasTableConflict
        );
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
            $tableName = \App\Models\Table::find($latestReservation->table_id)->name ?? ('Số ' . $latestReservation->table_id);
            
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

        // Trả dữ liệu ra view
        return view('cart.index', compact(
            'cart',
            'total',
            'reservation',
            'canCheckout',
            'checkoutUnavailableMessage'
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
    $request->validate([
        'quantity' => 'required|integer|min:1|max:10',
    ], [
        'quantity.min' => 'Số lượng ít nhất phải là 1.',
        'quantity.max' => 'Bạn không thể đặt quá 10 phần cho mỗi món ăn.',
    ]);

    $userId = auth()->id();
    $foodId = $request->menu_id;
    $quantity = (int)$request->input('quantity', 1);

    // 3. Tìm món ăn trong Database
    $menu = \App\Models\Menu::find($foodId);

    if (!$menu) {
        return response()->json([
            'success' => false,
            'message' => 'Món không tồn tại'
        ]);
    }

    // 🔥 TRƯỜNG HỢP 1: HẾT HÀNG HOÀN TOÀN
    if ($menu->stock <= 0) {
        return response()->json([
            'success' => false,
            'message' => 'Rất tiếc, món ăn này hiện đã hết hàng!'
        ]);
    }

    // 4. Kiểm tra giỏ hàng hiện tại của User
    $cartItem = \App\Models\Cart::where('user_id', $userId)
        ->where('menu_id', $foodId)
        ->first();

    if ($cartItem) {
        $newQuantity = $cartItem->quantity + $quantity;
        
        // 🔥 TRƯỜNG HỢP 2: TỔNG TRONG GIỎ VƯỢT QUÁ KHO
        if ($newQuantity > $menu->stock) {
            return response()->json([
                'success' => false,
                'message' => "Rất tiếc, bạn đã có {$cartItem->quantity} phần trong giỏ. Kho chỉ còn tổng cộng {$menu->stock} phần."
            ]);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();
    } else {
        // 🔥 TRƯỜNG HỢP 3: THÊM MỚI VƯỢT QUÁ KHO
        if ($quantity > $menu->stock) {
            return response()->json([
                'success' => false,
                'message' => "Món này hiện chỉ còn {$menu->stock} phần."
            ]);
        }

        \App\Models\Cart::create([
            'user_id' => $userId,
            'menu_id' => $foodId,
            'quantity' => $quantity
        ]);
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
        if (Auth::check()) {
            // Xóa dự phòng trong DB nếu có
            $latestRes = Reservation::where('user_id', Auth::id())->latest()->first();
            if ($latestRes) {
                $latestRes->update(['cart_data' => null]);
            }
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

        // 1. LẤY GIỎ HÀNG TỪ DATABASE ĐỂ KIỂM TRA ĐẦU VÀO
        $cartItems = \App\Models\Cart::with('menu')
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // 2. KIỂM TRA TỒN KHO (STOCK) TRƯỚC KHI XỬ LÝ
        foreach ($cartItems as $item) {
            if (!$item->menu || $item->quantity > $item->menu->stock) {
                $stockAvailable = $item->menu->stock ?? 0;
                return redirect()->back()->with('error', "Món '{$item->menu->name}' hiện không đủ số lượng (Chỉ còn {$stockAvailable} phần). Vui lòng điều chỉnh lại giỏ hàng.");
            }
        }

        // 3. Dùng một snapshot món ăn từ giỏ DB cho cả hóa đơn và đơn bếp
        $totalAmount = 0;
        $cartData = [];
        foreach ($cartItems as $item) {
            $price = $item->menu->price;
            $totalAmount += $price * $item->quantity;
            $cartData[] = [
                'id'       => $item->menu_id,
                'name'     => $item->menu->name,
                'price'    => $price,
                'quantity' => $item->quantity,
                'image'    => $item->menu->image,
            ];
        }

        // 4. Lấy phương thức thanh toán & Tạo mã đơn hàng ngẫu nhiên
        $pm = $request->input('payment_method', 'COD');
        $orderCode = (string)rand(100000, 999999);

        // 5. Lấy TABLE_ID từ RESERVATION gần nhất
        $latestReservation = \App\Models\Reservation::where('user_id', $userId)
            ->whereIn('status', Reservation::ACTIVE_STATUSES)
            ->latest()
            ->first();
        $tableId = $latestReservation->table_id ?? null;

        // Checkout chỉ được gắn với yêu cầu đặt bàn chưa xác nhận cọc và bàn còn trống.
        if ($latestReservation) {
            try {
                DB::transaction(function () use ($latestReservation) {
                    $lockedReservation = \App\Models\Reservation::lockForUpdate()->findOrFail($latestReservation->id);
                    $table = \App\Models\Table::whereKey($lockedReservation->table_id)->lockForUpdate()->first();
                    $conflict = !$table || $table->status !== 'empty'
                        || \App\Models\Reservation::blockingTable()
                            ->where('table_id', $lockedReservation->table_id)
                            ->where('id', '!=', $lockedReservation->id)
                            ->exists();

                    if ($lockedReservation->status !== 'pending_deposit'
                        || $lockedReservation->payment_status !== 'unpaid'
                        || $conflict) {
                        throw new \DomainException('Bàn này không còn trống hoặc yêu cầu đặt bàn đã được xử lý. Vui lòng chọn bàn khác hoặc liên hệ nhân viên.');
                    }

                    $lockedReservation->payment_status = 'deposit_submitted';
                    $lockedReservation->save();
                    $table->update(['status' => 'reserved']);
                });
            } catch (\DomainException $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            $latestReservation->refresh();
        }

        // Staff/bếp đọc món từ reservations.cart_data, còn hóa đơn đọc order_items.
        // Cập nhật cả hai từ cùng snapshot để không lệch với giỏ khách vừa chốt.
        if ($latestReservation) {
            $reservationUpdate = [
                'cart_data' => $cartData,
                'total_price' => $totalAmount,
            ];
            $reservationUpdate['deposit_amount'] = (int) round($totalAmount * 20 / 100);
            $latestReservation->update($reservationUpdate);
            $latestReservation->chef_statuses = array_fill_keys(array_column($cartData, 'id'), 'pending');
            $latestReservation->save();

            \App\Models\OrderItem::where('reservation_id', $latestReservation->id)->delete();
            foreach ($cartData as $item) {
                \App\Models\OrderItem::create([
                    'reservation_id' => $latestReservation->id,
                    'order_id'       => null,
                    'menu_id'        => $item['id'],
                    'quantity'       => $item['quantity'],
                    'price'          => $item['price'],
                    'product_name'   => $item['name'],
                ]);
            }
        }

        // 6. XÁC ĐỊNH TRẠNG THÁI ĐƠN HÀNG BAN ĐẦU THEO NGHIỆP VỤ
        // Nếu chọn chuyển khoản BANK -> Chờ thanh toán (pending_payment)
        // Nếu chọn trả tại bàn COD -> Chờ duyệt món (pending)
        $orderStatus = ($pm === 'BANK') ? 'pending_payment' : 'pending';

        // 7. Tạo bản ghi Đơn hàng (Order) vào Database
        $order = \App\Models\Order::create([
            'user_id'         => $userId,
            'reservation_id'  => $latestReservation->id ?? null, // liên kết chắc chắn với đơn đặt bàn
            'code'            => $orderCode,
            'total_price'     => $totalAmount,
            'status'          => $orderStatus,
            'payment_method'  => $pm,
            'table_number'    => $tableId,
            'name'            => auth()->user()->name,
            'phone'           => auth()->user()->phone,
            'notes'           => $request->order_notes ?? null,
        ]);

        // 8. Lưu OrderItem & TRỪ TỒN KHO TRONG DATABASE VẬT LÝ
        $cartToEmail = []; // Chuẩn bị mảng dữ liệu sạch phục vụ gửi Mail
        foreach ($cartItems as $item) {
            $price = $item->menu->price;
            \App\Models\OrderItem::create([
                'order_id'     => $order->id,
                'menu_id'      => $item->menu_id,
                'quantity'     => $item->quantity,
                'price'        => $price,
                'product_name' => $item->menu->name,
            ]);

            // Cập nhật giảm số lượng hàng tồn kho của món ăn
            $item->menu->decrement('stock', $item->quantity);

            // Gom thông tin món phục vụ cấu trúc Mail nhận dữ liệu
            $cartToEmail[] = [
                'name'     => $item->menu->name,
                'quantity' => $item->quantity,
                'price'    => $price,
                'total'    => $price * $item->quantity
            ];
        }

        // 9. GỬI MAIL THEO PHƯƠNG THỨC THANH TOÁN ĐÃ CHỌN
        $mailData = [
            'invoice' => $order->code,
            'total'   => $order->total_price,
            'table'   => $order->table_number,
            'cart'    => $cartToEmail
        ];

        if ($pm === 'COD') {
            // Thanh toán tại bàn -> Gửi mail xác nhận thông tin đặt món ngay lập tức
            try {
                \Illuminate\Support\Facades\Mail::to(auth()->user()->email)->send(new \App\Mail\OrderConfirmed($mailData));
            } catch (\Exception $e) {
                \Log::error('Gửi mail đơn hàng COD thất bại: ' . $e->getMessage());
            }
            $successMessage = 'Thanh toán tại bàn được ghi nhận! Đơn hàng đã được chuyển tới nhà bếp.';
        } else {
            // Chuyển khoản QR ngân hàng -> KHÔNG gửi mail tại đây, chờ duyệt tiền ở trang Admin
            $successMessage = 'Đặt món thành công! Vui lòng hoàn tất chuyển khoản qua mã QR để đơn hàng được nhà bếp xử lý.';
        }

        // 10. Dọn sạch dữ liệu giỏ hàng sau khi đã chuyển thành đơn hàng thành công
       Cart::where('user_id', $userId)->delete();
        session()->forget('cart');

        return redirect('/')->with('success', $successMessage);
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
    $userId = auth()->id();
    $menuId = $request->id;
    $quantity = (int) $request->quantity;

    // 1. Nếu số lượng > 0 thì cập nhật, ngược lại (bằng 0) thì XÓA món
    if ($quantity > 0) {
        \App\Models\Cart::where('user_id', $userId)
            ->where('menu_id', $menuId)
            ->update(['quantity' => $quantity]);
    } else {
        \App\Models\Cart::where('user_id', $userId)
            ->where('menu_id', $menuId)
            ->delete();
    }

    // 2. Gọi hàm đồng bộ để cập nhật bảng order_items (để Admin thấy thay đổi)
    $this->syncCustomerCartToStaff($userId);

    return response()->json(['success' => true]);
}

    /**
     * XÓA MÓN ĂN (KHI KHÁCH BẤM NÚT THÙNG RÁC)
     */
    public function remove($id)
    {
        $userId = auth()->id();

        // 1. Xóa món khỏi Database giỏ hàng
        \App\Models\Cart::where('user_id', $userId)
                        ->where('menu_id', $id)
                        ->delete();

        // Xóa luôn session phụ trợ (nếu có dùng để hiển thị icon giỏ hàng)
        $cart = session()->get('cart', []);
        if(isset($cart[$id])) { unset($cart[$id]); }
        session()->put('cart', $cart);

        // 2. Đồng bộ ngay lập tức cho Nhân viên
        $this->syncCustomerCartToStaff($userId);

        return redirect()->back()->with('success', 'Đã xóa món ăn khỏi giỏ hàng!');
    }

    /**
     * HÀM TIỆN ÍCH: ĐỒNG BỘ TỪ GIỎ HÀNG KHÁCH -> MÀN HÌNH NHÂN VIÊN
     */
    /**
     * HÀM TIỆN ÍCH: ĐỒNG BỘ TỪ GIỎ HÀNG KHÁCH -> MÀN HÌNH NHÂN VIÊN & DATABASE CHUẨN
     */
    private function syncCustomerCartToStaff($userId)
    {
        // 1. Lấy giỏ hàng mới nhất
        $cartItems = \App\Models\Cart::with('menu')->where('user_id', $userId)->get();
        $cartData = [];
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            if (!$item->menu) continue;
            $cartData[] = [
                'id'       => $item->menu_id,
                'name'     => $item->menu->name,
                'price'    => $item->menu->price,
                'quantity' => $item->quantity,
                'image'    => $item->menu->image
            ];
            $totalPrice += $item->quantity * $item->menu->price;
        }

        // 2. Cập nhật Bảng Đặt Bàn (Reservations)
        $reservation = \App\Models\Reservation::where('user_id', $userId)->latest()->first();
        
        if ($reservation) {
            // Cập nhật lại mảng JSON và tổng tiền
            $payload = [
    'cart_data'   => $cartData,
    'total_price' => $totalPrice,
];
// Đơn chưa cọc: tiền cọc luôn = 20% tổng mới
if ($reservation->status === 'pending_deposit') {
    $payload['deposit_amount'] = (int) round($totalPrice * 20 / 100);
}
$reservation->update($payload);

            // =======================================================
            // 🔥 ĐOẠN FIX QUAN TRỌNG: ĐỒNG BỘ VÀO BẢNG ORDER_ITEMS 
            // =======================================================
            // Bước A: Xóa sạch toàn bộ chi tiết món cũ của cái hóa đơn này đi
            \App\Models\OrderItem::where('reservation_id', $reservation->id)->delete();

            // Bước B: Dựa vào giỏ hàng mới nhất, tạo lại các món ăn (nếu giỏ hàng đã bị xóa trống thì vòng lặp này bỏ qua)
            foreach ($cartData as $cd) {
                \App\Models\OrderItem::create([
                    'reservation_id' => $reservation->id,
                    'order_id'       => null,
                    'menu_id'        => $cd['id'],
                    'quantity'       => $cd['quantity'],
                    'price'          => $cd['price'],
                    'product_name'   => $cd['name']
                ]);
            }
            // =======================================================

            // 3. Cập nhật Hóa đơn (Orders) — chỉ khi đơn còn ở bước đầu và chưa có món gọi thêm tại bàn
            $order = $reservation->linkedOrder();

            if ($order
                && in_array($order->status, ['pending_payment', 'pending'], true)
                && !$order->items()->where('is_extra', true)->exists()) {
                $order->update(['total_price' => $totalPrice]);
                \App\Models\OrderItem::where('order_id', $order->id)->delete();
                foreach ($cartData as $cd) {
                    \App\Models\OrderItem::create([
                        'order_id'       => $order->id,
                        'reservation_id' => null,
                        'menu_id'        => $cd['id'],
                        'quantity'       => $cd['quantity'],
                        'price'          => $cd['price'],
                        'product_name'   => $cd['name']
                    ]);
                }
            }
        }
    }
}
