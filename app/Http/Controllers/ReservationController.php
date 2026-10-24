<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Mail\BookingPendingDeposit;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\Cart;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    private const RESERVATION_EMAILS_ENABLED = false; // true để bật gửi email xác nhận đặt bàn cho khách, false để tắt

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Trang đặt bàn cho khách
     */
    public function index(Request $request)
    {
        Table::releaseExpiredCleaning();

        $menus  = Menu::all();
        $tables = Table::all();

        // Bàn đang được sử dụng/đặt/dọn không thể được khách chọn.
        $occupiedTableIds = Table::where('status', '!=', 'empty')->pluck('id')->toArray();

        // Chỉ khóa bàn theo reservation khi khách đã xác nhận thanh toán (hoặc đã qua bước cọc).
        $busyTableIds = Reservation::blockingTable()->pluck('table_id')->toArray();
        $bookedTableIds = array_unique(array_merge($occupiedTableIds, $busyTableIds));
        $paymentTableIds = Reservation::whereIn('status', Reservation::PAID_STATUSES)
            ->pluck('table_id')
            ->toArray();

        return view('reservation', compact('menus', 'tables', 'bookedTableIds', 'paymentTableIds'));
    }

    /**
     * Lưu đặt bàn từ khách hàng
     */
public function store(Request $request)
{
    $request->validate([
        'full_name'        => 'required|string|max:255',
        'phone'            => 'required|string|max:20',
        'reservation_date' => 'required|date|after_or_equal:today',
        'reservation_time' => 'required',
        'table_id'         => 'required|integer|exists:tables,id',
    ]);
    
    
$time = $request->input('reservation_time');

// Bước 1: Kiểm tra định dạng giờ và khung giờ nhà hàng.
if (
    !is_string($time) ||
    !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) ||
    $time < '06:00' ||
    $time > '22:00'
) {
    return back()
        ->withInput()
        ->withErrors([
            'reservation_time' => 'Nhà hàng chỉ nhận đặt bàn từ 06:00 đến 22:00.',
        ]);
}

// Bước 2: Kiểm tra ngày giờ đặt bàn theo giờ Việt Nam.
try {
    $reservationDateTime = \Carbon\Carbon::createFromFormat(
        '!Y-m-d H:i',
        $request->input('reservation_date') . ' ' . $time,
        'Asia/Ho_Chi_Minh'
    );
} catch (\Throwable $e) {
    return back()
        ->withInput()
        ->withErrors([
            'reservation_time' => 'Ngày hoặc giờ đặt bàn không hợp lệ.',
        ]);
}

// Bước 3: Không cho đặt vào thời điểm đã qua hoặc đúng thời điểm hiện tại.
if (
    !$reservationDateTime ||
    $reservationDateTime->lessThanOrEqualTo(now('Asia/Ho_Chi_Minh'))
) {
    return back()
        ->withInput()
        ->withErrors([
            'reservation_time' => 'Giờ đặt bàn đã qua. Vui lòng chọn thời điểm trong tương lai.',
        ]);
}


    if (
        !is_string($time) ||
        !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) ||
        $time < '06:00' ||
        $time > '22:00'
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'reservation_time' => 'Nhà hàng chỉ nhận đặt bàn từ 06:00 đến 22:00.',
            ]);
    }


    $dbCart     = Cart::with('menu')->where('user_id', auth()->id())->get();
    $cartData   = [];
    $totalAmount = 0;

    foreach ($dbCart as $item) {
        if ($item->menu && $item->quantity > 0) {
            $subTotal     = $item->menu->price * $item->quantity;
            $totalAmount += $subTotal;
            $cartData[]   = [
                'id'       => $item->menu_id,
                'name'     => $item->menu->name,
                'price'    => $item->menu->price,
                'quantity' => $item->quantity,
                'image'    => $item->menu->image,
            ];
        }
    }

    // Có món -> 20%, không món -> 100.000đ cố định
$depositAmount = Reservation::calcDeposit($totalAmount);

    try {
        $reservation = DB::transaction(function () use ($request, $cartData, $totalAmount, $depositAmount) {
            if (!Table::lockIfAvailableForBooking((int) $request->table_id)) {
                throw new \DomainException('Bàn này hiện đang được sử dụng, đã đặt hoặc đang dọn. Vui lòng chọn bàn khác.');
            }

            $reservation                     = new Reservation();
            $reservation->user_id            = auth()->id();
            $reservation->full_name          = $request->full_name;
            $reservation->email              = auth()->user()->email;
            $reservation->phone              = $request->phone;
            $reservation->reservation_date   = $request->reservation_date;
            $reservation->reservation_time   = $request->reservation_time;
            $reservation->table_id           = $request->table_id;
            $reservation->notes              = $request->notes;
            $reservation->status             = 'pending_deposit';
            $reservation->payment_status     = 'unpaid';
            $reservation->payment_method     = 'BANK';
            $reservation->deposit_amount     = $depositAmount;
            $reservation->total_price        = $totalAmount;
            $reservation->cart_data          = $cartData;
            $reservation->save();

            // Mỗi món là một dòng reservation_items (lần gọi đầu tiên của khách).
            // cart_data / order_items được service sinh lại từ các dòng này.
            app(\App\Services\ReservationItemService::class)->addLines(
                $reservation, $cartData, \App\Models\ReservationItem::SOURCE_CUSTOMER_PREORDER, auth()->id()
            );

            return $reservation;
        });
    } catch (\DomainException $e) {
        return back()->withInput()->with('error', $e->getMessage());
    }

    // KHÔNG khóa bàn ở bước này: bàn chỉ được khóa khi khách bấm "xác nhận thanh toán"
    // ở trang giỏ hàng (CartController::checkout).

    // At this point the booking is only a request awaiting payment and staff verification.
    if (self::RESERVATION_EMAILS_ENABLED) {
        try {
            $reservation->refresh()->load('table');
            Mail::to($reservation->email)->send(new BookingPendingDeposit($reservation));
        } catch (\Throwable $e) {
            \Log::error('Booking pending-deposit email failed.', [
                'reservation_id' => $reservation->id,
                'exception' => $e,
            ]);
        }
    }

    return redirect()->route('cart.index')->with('success', 'Ghi nhận yêu cầu đặt bàn! Vui lòng hoàn tất cọc để giữ bàn.');
}

    /**
     * Thêm món vào giỏ qua AJAX
     */
    public function addToCartAjax(Request $request)
    {
        $id       = $request->menu_id ?? $request->food_id;
        $quantity = (int) ($request->quantity ?? 1);
        $isUpdate = $request->is_update ?? false;

        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Món không tồn tại!']);
        }

        $dbCartItem = Cart::where('user_id', auth()->id())->where('menu_id', $id)->first();

        // 🔥 ĐOẠN FIX QUAN TRỌNG:
        // Nếu khách bấm giảm số lượng về <= 0, ta tiến hành XÓA món đó khỏi Database
        if ($isUpdate && $quantity <= 0) {
            if ($dbCartItem) {
                $dbCartItem->delete();
            }
        } 
        // Ngược lại, nếu > 0 thì mới thêm hoặc cập nhật
        else {
            if ($dbCartItem) {
                $dbCartItem->quantity = $isUpdate ? $quantity : $dbCartItem->quantity + 1;
                $dbCartItem->save();
            } else {
                Cart::create(['user_id' => auth()->id(), 'menu_id' => $id, 'quantity' => $quantity]);
            }
        }

        // Lấy lại giỏ hàng mới nhất để trả về giao diện
        $dbCart = Cart::with('menu')->where('user_id', auth()->id())->get();
        
        // Chỉ map những món CÓ số lượng > 0 trả về JS
        $cart   = $dbCart->filter(fn($i) => $i->menu && $i->quantity > 0)->map(fn($i) => [
            'id'       => $i->menu_id,
            'name'     => $i->menu->name,
            'price'    => $i->menu->price,
            'quantity' => $i->quantity,
            'image'    => $i->menu->image,
        ])->values()->toArray();

        return response()->json(['success' => true, 'cartCount' => count($cart), 'cartData' => $cart]);
    }
    public function saveNoteAjax(Request $request)
    {
        $reservation          = session()->get('reservation', []);
        $reservation['notes'] = $request->notes;
        session()->put('reservation', $reservation);
        return response()->json(['success' => true]);
    }

    public function autoSave(Request $request)
    {
        return response()->json(['success' => true]);
    }

    /**
     * Danh sách đặt bàn — dùng chung cho Admin và Staff.
     */
    public function adminIndex()
    {
        $user         = auth()->user();
        $reservations = Reservation::with(['user', 'table'])->latest()->get();

        // Staff, hoặc Admin vào route /staff/*  -> giao diện phục vụ
        if ($user->isStaff() || ($user->isAdmin() && request()->is('staff/*'))) {
            return view('staff.reservations', compact('reservations'));
        }

        if ($user->isAdmin()) {
            return view('admin.reservations.index', compact('reservations'));
        }

        abort(403);
    }

/**
 * Khách gửi yêu cầu hủy đặt bàn đã xác nhận cọc.
 */
public function requestCancellation(Request $request, $id)
{
    $data = $request->validate([
        'cancellation_reason' => ['nullable', 'string', 'max:1000'],
    ]);

    try {
        \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data) {
            $reservation = \App\Models\Reservation::where('user_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            if (
                !in_array($reservation->status, ['pending', 'confirmed'], true)
                || $reservation->payment_status !== 'deposit_paid'
                || !$reservation->deposit_paid_at
            ) {
                throw new \DomainException(
                    'Chỉ được yêu cầu hủy khi đặt bàn đã được nhân viên xác nhận cọc.'
                );
            }

            $reservationDateTime = \Carbon\Carbon::parse(
                $reservation->reservation_date->format('Y-m-d') . ' ' .
                $reservation->reservation_time
            );

            if (now()->greaterThanOrEqualTo($reservationDateTime->copy()->subHours(2))) {
                throw new \DomainException(
                    'Bạn chỉ được yêu cầu hủy khi còn ít nhất 2 tiếng trước giờ đặt bàn.'
                );
            }

            if ($reservation->cancellation_status === 'pending') {
                throw new \DomainException(
                    'Yêu cầu hủy của bạn đang chờ nhân viên xử lý.'
                );
            }

            $reservation->cancellation_status = 'pending';
            $reservation->cancellation_reason = $data['cancellation_reason'] ?? null;
            $reservation->cancellation_requested_at = now();
            $reservation->cancellation_reviewed_at = null;
            $reservation->cancellation_reviewed_by = null;
            $reservation->save();
        });
    } catch (\DomainException $e) {
        return back()->with('error', $e->getMessage());
    }

    return back()->with(
        'success',
        'Đã gửi yêu cầu hủy đặt bàn. Vui lòng chờ nhân viên xử lý. Tiền cọc không tự động hoàn lại.'
    );
}

/**
 * Nhân viên hoặc quản trị viên duyệt/từ chối yêu cầu hủy.
 */
public function reviewCancellation(Request $request, $id)
{
    $data = $request->validate([
        'decision' => ['required', \Illuminate\Validation\Rule::in(['approve', 'reject'])],
    ]);

    try {
        \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data) {
            $reservation = \App\Models\Reservation::lockForUpdate()->findOrFail($id);

            if ($reservation->cancellation_status !== 'pending') {
                throw new \DomainException(
                    'Yêu cầu hủy này không tồn tại hoặc đã được xử lý.'
                );
            }

            if ($data['decision'] === 'reject') {
                $reservation->cancellation_status = 'rejected';
                $reservation->cancellation_reviewed_at = now();
                $reservation->cancellation_reviewed_by = auth()->id();
                $reservation->save();

                return;
            }

            if (
                !in_array($reservation->status, ['pending', 'confirmed'], true)
                || $reservation->payment_status !== 'deposit_paid'
                || !$reservation->deposit_paid_at
            ) {
                throw new \DomainException(
                    'Đặt bàn không còn đủ điều kiện để hủy theo quy tắc hiện tại.'
                );
            }

            $reservationDateTime = \Carbon\Carbon::parse(
                $reservation->reservation_date->format('Y-m-d') . ' ' .
                $reservation->reservation_time
            );

            if (now()->greaterThanOrEqualTo($reservationDateTime->copy()->subHours(2))) {
                throw new \DomainException(
                    'Đã không còn đủ 2 tiếng trước giờ đặt bàn. Không thể duyệt hủy.'
                );
            }

            $reservation->cancellation_status = 'approved';
            $reservation->cancellation_reviewed_at = now();
            $reservation->cancellation_reviewed_by = auth()->id();
            $reservation->status = 'cancelled';
            $reservation->save();

            // Chỉ hủy đơn món ăn liên kết chính xác với lượt đặt bàn này.
            $order = \App\Models\Order::where('reservation_id', $reservation->id)
                ->lockForUpdate()
                ->first();

            if ($order && !in_array($order->status, ['paid', 'completed'], true)) {
                $order->status = 'cancelled';
                $order->save();
            }

            // Chỉ giải phóng bàn nếu không còn lượt đặt bàn nào khác giữ bàn.
            $this->releaseTableIfFree($reservation);
        });
    } catch (\DomainException $e) {
        return back()->with('error', $e->getMessage());
    }

    return back()->with(
        'success',
        $data['decision'] === 'approve'
            ? 'Đã duyệt hủy đặt bàn. Tiền cọc không tự động hoàn lại.'
            : 'Đã từ chối yêu cầu hủy đặt bàn.'
    );
}

    /**
     * Cập nhật trạng thái đặt bàn (Admin & Staff) — CHỈ theo máy trạng thái Reservation::TRANSITIONS.
     * Thanh toán đi qua pay() riêng.
     */
    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Reservation::manualTargets())],
        ]);
        $newStatus = $data['status'];

        $oldStatus = null;
        $sendDepositMail = false;

        try {
            $reservation = DB::transaction(function () use ($id, $newStatus, &$oldStatus, &$sendDepositMail) {
                $reservation = Reservation::lockForUpdate()->findOrFail($id);
                $oldStatus   = $reservation->status;

                if (!Reservation::canTransition($oldStatus, $newStatus)) {
                    throw new \DomainException("Không thể chuyển trạng thái từ '{$oldStatus}' sang '{$newStatus}'.");
                }

                if ($oldStatus === 'pending_deposit'
                    && $newStatus === 'pending'
                    && $reservation->payment_status !== 'deposit_submitted') {
                    throw new \DomainException('Khách chưa xác nhận đã chuyển khoản tiền cọc.');
                }

                $itemService = app(\App\Services\ReservationItemService::class);

                // Báo "đã lên món" khi bếp CHƯA nấu xong các món => chặn (admin được phép xử lý thủ công)
                if ($newStatus === 'served'
                    && !auth()->user()?->isAdmin()
                    && $itemService->hasUnfinished($reservation)) {
                    throw new \DomainException('Còn món chưa nấu xong, chưa thể báo đã lên món. Vui lòng chờ bếp hoàn tất.');
                }

                // Admin/staff duyệt cọc hoặc xác nhận đơn => chắc chắn bàn phải được giữ
                if (in_array($newStatus, ['pending', 'confirmed'], true)) {
                    Table::where('id', $reservation->table_id)->where('status', 'empty')->update(['status' => 'reserved']);
                }

                if ($oldStatus === 'pending_deposit' && $newStatus === 'pending') {
                    $reservation->payment_status  = 'deposit_paid';
                    $reservation->deposit_paid_at = now();
                    $sendDepositMail = true;
                }

                $reservation->status = $newStatus;
                $reservation->save();

                // Trạng thái bếp theo TỪNG DÒNG món (không còn gán chung theo menu_id).
                // Service tự sinh lại cart_data/chef_statuses cho các màn hình cũ.
                if ($newStatus === 'serving') {
                    $itemService->markSentToKitchen($reservation);
                } elseif ($newStatus === 'ready') {
                    $itemService->finishAll($reservation);
                } elseif ($newStatus === 'served') {
                    $itemService->finishAll($reservation);
                    $itemService->serveAll($reservation);
                }

                // Đồng bộ hoá đơn liên kết (nếu có)
                if ($order = $reservation->linkedOrder()) {
                    $order->update(['status' => $newStatus]);
                }

                // Giải phóng / dọn bàn
                if ($newStatus === 'completed') {
                    Table::where('id', $reservation->table_id)->update([
                        'status' => 'cleaning', 'cleanup_started_at' => now(),
                    ]);
                } elseif ($newStatus === 'cancelled') {
                    $this->releaseTableIfFree($reservation);
                } elseif ($newStatus === 'arrived') {
                    Table::where('id', $reservation->table_id)->update(['status' => 'occupied']);
                }

                return $reservation;
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Gửi mail SAU khi đã lưu thành công (không để lỗi SMTP làm hỏng giao dịch)
        if (self::RESERVATION_EMAILS_ENABLED && $sendDepositMail && $reservation->email) {
            try {
                Mail::to($reservation->email)->send(new BookingConfirmation($reservation->load('table')));
            } catch (\Throwable $e) {
                \Log::error('Booking confirmation email failed.', [
                    'reservation_id' => $reservation->id,
                    'exception' => $e,
                ]);
            }
        } elseif (self::RESERVATION_EMAILS_ENABLED && $sendDepositMail) {
            \Log::warning('Booking confirmation email skipped because the reservation has no email address.', [
                'reservation_id' => $reservation->id,
            ]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái đơn đặt bàn.');
    }

    /**
     * Staff/Admin xác nhận thanh toán tại quầy.
     * Số tiền thực thu = tổng bill - tiền cọc đã nhận. Chỉ thực hiện được khi đơn ở trạng thái 'served'.
     */
    public function checkout($id)
{
    $reservation = Reservation::with('table')->findOrFail($id);

    if ($reservation->status !== 'served') {
        return redirect()->route('staff.reservations.index')
            ->with('error', 'Chỉ thanh toán được khi bàn đã lên món xong.');
    }

    if (app(\App\Services\ReservationItemService::class)->hasUnfinished($reservation)) {
        return redirect()->route('staff.reservations.index')
            ->with('error', 'Còn món gọi thêm chưa nấu xong, chưa thể thanh toán.');
    }

    $total = (int) $reservation->total_price;
    $due   = max(0, $total - min((int) ($reservation->deposit_amount ?? 0), $total));

    $qrUrl = 'https://img.vietqr.io/image/'
        . config('services.bank.id') . '-' . config('services.bank.account') . '-compact2.png'
        . '?amount=' . $due
        . '&addInfo=' . urlencode('HD' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT))
        . '&accountName=' . urlencode(config('services.bank.name'));

    return view('staff.reservations.checkout', [
        'res'   => $reservation,
        'due'   => $due,
        'qrUrl' => $qrUrl,
    ]);
}
    public function pay(Request $request, $id)
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['cash', 'transfer'])],
        ]);

        try {
            DB::transaction(function () use ($id, $data) {
                $reservation = Reservation::lockForUpdate()->findOrFail($id);

                if ($reservation->status !== 'served') {
                    throw new \DomainException('Chỉ thanh toán được khi bàn đã lên món xong (trạng thái "Đã lên món").');
                }

                // Có món gọi thêm sau khi đã lên món mà bếp chưa nấu xong => chưa thanh toán
                if (app(\App\Services\ReservationItemService::class)->hasUnfinished($reservation)) {
                    throw new \DomainException('Còn món gọi thêm chưa nấu xong, chưa thể thanh toán.');
                }

                $total   = (int) $reservation->total_price;
                $deposit = min((int) ($reservation->deposit_amount ?? 0), $total);

                $reservation->status         = $data['payment_method'] === 'cash' ? 'paid_cash' : 'paid_transfer';
                $reservation->payment_status = 'paid';
                $reservation->paid_amount    = $total - $deposit;   // phần còn lại thu tại quầy
                $reservation->paid_at        = now();
                $reservation->paid_by        = auth()->id();
                $reservation->save();

                if ($order = $reservation->linkedOrder()) {
                    $order->update(['status' => 'paid', 'payment_method' => $data['payment_method']]);
                }
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        $billRoute = request()->is('admin/*') ? 'admin.bills.show' : 'staff.bills.show';

return redirect()->route($billRoute, $id)
    ->with('success', 'Đã xác nhận thanh toán.')
    ->with('auto_print', true);
    }

    /** Trả bàn về trống nếu không còn đơn nào đang hoạt động trên bàn đó. */
    private function releaseTableIfFree(Reservation $reservation): void
    {
        $stillUsed = Reservation::where('table_id', $reservation->table_id)
            ->where('id', '!=', $reservation->id)
            ->blockingTable()
            ->exists();

        if (!$stillUsed) {
            Table::where('id', $reservation->table_id)->update(['status' => 'empty', 'cleanup_started_at' => null]);
        }
    }

    // -------------------------------------------------------
    // Các hàm phụ (Staff / Admin tạo đặt bàn trực tiếp)
    // -------------------------------------------------------

    public function staffCreate()
    {
        $menus  = Menu::all();
        $tables = Table::all();
        return view('admin.reservations.create', compact('menus', 'tables'));
    }

    public function staffStore(Request $request)
    {
        // 1. Kiểm tra xem có phải khách đến trực tiếp (Walk-in) hay không
        $isWalkIn = $request->has('is_walk_in');

        // 2. Validate dữ liệu linh hoạt
        $rules = [
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'table_id'         => 'required|integer|exists:tables,id',
        ];

        // Chỉ bắt buộc tên và SĐT nếu KHÔNG phải khách Walk-in
        if (!$isWalkIn) {
            $rules['full_name'] = 'required|string|max:255';
            $rules['phone']     = 'required|string|max:20';
            $rules['reservation_date'] .= '|after_or_equal:today';
        }

        $request->validate($rules);
        $selectedDate = $request->input('reservation_date');
$selectedTime = $request->input('reservation_time');

$now = now('Asia/Ho_Chi_Minh');
$today = $now->toDateString();
$currentTime = $now->format('H:i');

if ($selectedDate < $today) {
    return back()->withInput()->withErrors([
        'reservation_date' => 'Ngày đặt bàn đã qua. Vui lòng chọn ngày khác.',
    ]);
}

if ($selectedDate === $today && $selectedTime <= $currentTime) {
    return back()->withInput()->withErrors([
        'reservation_time' => 'Giờ đặt bàn đã qua. Vui lòng chọn giờ khác.',
    ]);
}

        // 3. Xử lý mảng món ăn nhân viên gọi trực tiếp từ form
        $cartData = [];
        $totalAmount = 0;

        if ($request->has('foods')) {
            foreach ($request->foods as $foodId => $details) {
                $menu = \App\Models\Menu::find($foodId);
                if ($menu) {
                    $qty = (int) $details['quantity'];
                    $subTotal = $menu->price * $qty;
                    $totalAmount += $subTotal;
                    
                    $cartData[] = [
                        'id'       => $menu->id,
                        'name'     => $menu->name,
                        'price'    => $menu->price,
                        'quantity' => $qty,
                        'image'    => $menu->image,
                    ];
                }
            }
        }

        try {
            DB::transaction(function () use ($request, $isWalkIn, $cartData, $totalAmount) {
                if (!Table::lockIfAvailableForBooking((int) $request->table_id)) {
                    throw new \DomainException('Bàn này hiện đang được sử dụng, đã đặt hoặc đang dọn. Vui lòng chọn bàn khác.');
                }

                $reservation                   = new Reservation();
                $reservation->user_id          = auth()->id();
                $reservation->full_name        = $request->full_name ?? 'Khách vãng lai';
                $reservation->email            = auth()->user()->email ?? null;
                $reservation->phone            = $request->phone ?? null;
                $reservation->reservation_date = $request->reservation_date;
                $reservation->reservation_time = $request->reservation_time;
                $reservation->table_id         = $request->table_id;
                $reservation->notes            = $request->notes;
                $reservation->status           = $isWalkIn ? 'arrived' : 'confirmed';
                $reservation->payment_status   = 'unpaid';
                $reservation->total_price      = $totalAmount;
                $reservation->cart_data        = $cartData;
                $reservation->save();

                $tableStatus = $isWalkIn ? 'occupied' : 'reserved';
                Table::whereKey($request->table_id)->update(['status' => $tableStatus]);

                // Món do nhân viên nhập: mỗi món một dòng reservation_items (bỏ món số lượng 0)
                $staffLines = array_values(array_filter($cartData, fn ($c) => (int) $c['quantity'] > 0));
                app(\App\Services\ReservationItemService::class)->addLines(
                    $reservation, $staffLines, \App\Models\ReservationItem::SOURCE_STAFF_WALKIN, auth()->id()
                );

                if ($isWalkIn && count($cartData) > 0) {
                    $order = \App\Models\Order::create([
                        'user_id'        => auth()->id(),
                        'reservation_id' => $reservation->id,
                        'table_number'    => $request->table_id,
                        'total_price'    => $totalAmount,
                        'status'         => 'pending',
                    ]);

                    // order_items của hóa đơn được sinh từ reservation_items
                    app(\App\Services\ReservationItemService::class)->syncCaches($reservation);
                }
            });
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $route = (int) auth()->user()->role_id === 2
    ? 'admin.reservations.index'
    : 'staff.reservations.index';

return redirect()->route($route)
    ->with('success', 'Đã mở bàn và chuyển Order xuống bếp thành công!');
    }

    public function editItems($id)
    {
        $reservation = Reservation::findOrFail($id);
        $menus       = Menu::all();
        $cartItems   = $reservation->cart_data ?? [];

        return view('staff.reservations.edit-items', compact('reservation', 'menus', 'cartItems'));
    }

    /**
 * Staff/Admin lưu danh sách món của một đặt bàn (khách đã đến order).
 * - Lưu món vào reservation.cart_data + cập nhật total_price
 * - Món mới được đánh dấu 'pending' để bếp nhận, món cũ giữ nguyên trạng thái bếp
 * - Nếu bấm "Lưu & gửi xuống bếp": chuyển đặt bàn sang 'serving' để bếp thấy
 */
public function updateItems(Request $request, $id)
{
    try {
        DB::transaction(function () use ($request, $id) {
            // Khóa dòng để 2 nhân viên không sửa cùng lúc
            $reservation = Reservation::lockForUpdate()->findOrFail($id);

            // Chỉ cho order khi khách đã đến hoặc đang phục vụ
            if (!in_array($reservation->status, ['arrived', 'serving'], true)) {
                throw new \DomainException('Chỉ order món được khi khách đã đến bàn.');
            }

            // 1. Dựng lại danh sách món từ form (lấy giá từ DB, không tin giá từ form)
            $newCartData = [];
            $totalPrice  = 0;
            foreach ($request->items ?? [] as $item) {
                $menu = Menu::find($item['id']);
                $qty  = (int) ($item['quantity'] ?? 0);
                if (!$menu || $qty <= 0) continue;

                $newCartData[] = [
                    'id'       => $menu->id,
                    'name'     => $menu->name,
                    'price'    => (float) $menu->price,
                    'quantity' => $qty,
                    'image'    => $menu->image,
                ];
                $totalPrice += $menu->price * $qty;
            }

            // 2. Trạng thái bếp: món mới = pending, món cũ giữ nguyên
            $chef    = is_array($reservation->chef_statuses) ? $reservation->chef_statuses : [];
            $newChef = [];
            foreach ($newCartData as $cd) {
                $newChef[$cd['id']] = $chef[$cd['id']] ?? 'pending';
            }

            $reservation->cart_data     = $newCartData;
            $reservation->total_price   = $totalPrice;
            $reservation->chef_statuses = $newChef;

            // 3. Gửi xuống bếp: chỉ khi bấm nút "gửi bếp", đang 'arrived', có món,
            //    và máy trạng thái cho phép arrived -> serving
            if ($request->boolean('send_to_kitchen')
                && $reservation->status === 'arrived'
                && count($newCartData) > 0
                && Reservation::canTransition('arrived', 'serving')) {
                $reservation->status = 'serving';
            }
            $reservation->save();

            // 4. Đồng bộ bảng order_items theo reservation (màn hình staff/bếp đọc từ đây)
            OrderItem::where('reservation_id', $reservation->id)->delete();
            foreach ($newCartData as $cd) {
                OrderItem::create([
                    'reservation_id' => $reservation->id,
                    'order_id'       => null,
                    'menu_id'        => $cd['id'],
                    'product_name'   => $cd['name'],
                    'quantity'       => $cd['quantity'],
                    'price'          => $cd['price'],
                ]);
            }

            // 5. Nếu đã có hóa đơn (Order) liên kết thì cập nhật luôn cho khớp
            if ($order = $reservation->linkedOrder()) {
                $order->update(['total_price' => $totalPrice]);
                $order->items()->delete();
                foreach ($newCartData as $cd) {
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'menu_id'      => $cd['id'],
                        'product_name' => $cd['name'],
                        'quantity'     => $cd['quantity'],
                        'price'        => $cd['price'],
                    ]);
                }
            }
        });
    } catch (\DomainException $e) {
        return back()->with('error', $e->getMessage());
    }

    $route = auth()->user()->isAdmin() ? 'admin.reservations.index' : 'staff.reservations.index';
    return redirect()->route($route)->with('success', 'Đã lưu order.');
}
  public function create()
    {
        Table::releaseExpiredCleaning();

        // 2. Sau khi đã làm mới trạng thái, mới tiến hành lấy danh sách Bàn và Menu
        $tables = \App\Models\Table::all();
        $bookedTableIds = array_unique(array_merge(
            Table::where('status', '!=', 'empty')->pluck('id')->toArray(),
            Reservation::blockingTable()->pluck('table_id')->toArray()
        ));
        $menus = \App\Models\Menu::all(); 

        // 3. Gọi đúng file giao diện của Admin/Staff (file vừa được gắn code đếm ngược)
        return view('admin.reservations.create', compact('tables', 'menus', 'bookedTableIds'));
    }
}