@extends('shared')

@section('title', 'Thanh toán bàn của bạn')

@section('head')
<link rel="stylesheet" href="{{ asset('css/cart.css') }}?v=1">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('content')

@php
    // Dùng chung công thức với controller: có món 20%, không món 100.000đ
$depositAmount = \App\Models\Reservation::calcDeposit((int) $total);
    $remainingAmount = $total - $depositAmount;

    $bankID      = config('services.bank.id');
    $accountNo   = config('services.bank.account');
    $accountName = config('services.bank.name'); 
    $info = "Thanh toan " . ($reservation['table'] ?? '0'); 
    $infoEncoded = urlencode($info);
    $nameEncoded = urlencode($accountName);
    $qrUrl = "https://img.vietqr.io/image/{$bankID}-{$accountNo}-compact.png?amount={$depositAmount}&addInfo={$infoEncoded}&accountName={$nameEncoded}";
    $invoiceNumber = 'INV-' . strtoupper(substr(md5(now()->timestamp . auth()->id()), 0, 8));
    $currentDateTime = now()->format('H:i d/m/Y');
@endphp

<div class="cart-page">
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 8px; border-left: 4px solid #e74c3c;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <div class="order-container">
        <h3 class="page-title">Hóa đơn của quý khách</h3>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if ($checkoutUnavailableMessage)
            <div class="alert alert-warning shadow-sm" role="alert" aria-live="assertive">
                <strong><i class="fas fa-exclamation-triangle mr-2" aria-hidden="true"></i>Không thể đặt món lúc này.</strong>
                {{ $checkoutUnavailableMessage }}
            </div>
        @endif

        <div class="bill-info">
    <div class="row">

        {{-- CỘT TRÁI --}}
        <div class="col-md-6">
            <p><strong>Mã phiếu:</strong> {{ $invoiceNumber }}</p>

            <p><strong>Thời gian:</strong> {{ $currentDateTime }}</p>

            <p>
                <strong>Lịch đặt:</strong>
                {{ $reservation['time'] }} - {{ $reservation['date'] }}
            </p>

            <p><strong>Khách hàng:</strong> {{ $reservation['name'] }}</p>
        </div>

        {{-- CỘT PHẢI --}}
        <div class="col-md-6 text-md-right">

            <p>
                <strong>Vị trí:</strong>

                <span style="
                    background: var(--ft-bg, #111010);
                    color: var(--ft-gold, #c9a45c);
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-weight: 600;
                    font-size: 13px;
                ">
                    {{ $reservation['table'] }}
                </span>
            </p>

            <p>
                <strong>Trạng thái:</strong>

                <span class="font-weight-bold status-value">
                    {{ $reservation['status'] }}
                </span>
            </p>

            {{-- GHI CHÚ --}}
            @php
                $notesValue = $reservation['notes'] ?? '';

                $notesEmpty =
                    empty($notesValue)
                    || $notesValue === 'Không có yêu cầu đặc biệt'
                    || $notesValue === 'Chưa có ghi chú';
            @endphp

            @if(!$notesEmpty)
                <p>
                    <strong>Ghi chú:</strong>

                    <span style="
                        color: var(--ct-gold-text, #8a6a28);
                        font-style: italic;
                    ">
                        {{ $notesValue }}
                    </span>
                </p>
            @endif

        </div>

    </div>
</div>

        <div class="table-responsive mb-5">
            <table class="table table-order">
                <thead>
                    <tr>
                        <th width="40%">Chi tiết món ăn</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-right">Thành tiền</th>
                        <th class="text-center">Tùy chỉnh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cart as $item)
                        @if($item['quantity'] > 0)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset($item['image'] ?? 'images/default.jpg') }}" class="item-img-mini">
                                    <strong style="color: var(--ft-bg, #111010);">{{ $item['name'] ?? 'Món không tồn tại' }}</strong>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="qty-box">
                                    <button type="button" class="qty-btn" onclick="changeQty(this, -1)">-</button>
                                    <input type="text" class="qty-input" data-id="{{ $item['id'] }}" value="{{ $item['quantity'] }}" readonly data-price="{{ $item['price'] ?? 0 }}" data-stock="{{ \App\Models\Menu::find($item['id'])->stock ?? 0 }}">
                                    <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                                </div>
                            </td>
                            <td class="text-right" style="color: #718096;">{{ number_format($item['price'] ?? 0, 0, ',', '.') }}đ</td>
                            <td class="text-right font-weight-bold" style="color: var(--ft-bg, #111010); font-size: 15px;">
                                {{ number_format(($item['price'] ?? 0) * $item['quantity'], 0, ',', '.') }}đ
                            </td>
                            <td class="text-center">
                                <form action="{{ route('cart.remove', $item['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm" style="background: #FEE2E2; color: #EF4444; border: none; padding: 6px 10px; border-radius: 6px;" onclick="return confirm('Bạn có chắc chắn muốn bỏ món này?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr><td colspan="5" class="text-center py-4" style="color: #718096;">Danh sách thực đơn trống.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
    <tr>
        <th colspan="3" class="text-right" style="font-size: 15px; vertical-align: middle;">Tổng thanh toán:</th>
        <th class="text-right total-amount-text" id="grand-total-text">
            {{ number_format($total, 0, ',', '.') }} VNĐ
        </th>
        <th></th>
    </tr>
    <tr>
        <th colspan="3" class="text-right" style="font-size: 14px; color: var(--ct-gold-text, #8a6a28);">Tiền cọc cần thanh toán:</th>
        <th class="text-right" style="color: var(--ct-gold-text, #8a6a28); font-size: 1.2rem; font-weight:700;" id="deposit-total-text">
            {{ number_format($depositAmount, 0, ',', '.') }} VNĐ
        </th>
        <th></th>
    </tr>
    <tr>
        <th colspan="3" class="text-right" style="font-size: 13px; color:#718096;">Còn lại thanh toán tại quầy:</th>
        <th class="text-right" style="color:#718096; font-size: 13px;" id="remaining-total-text">
            {{ number_format($remainingAmount, 0, ',', '.') }} VNĐ
        </th>
        <th></th>
    </tr>
</tfoot>
            </table>
        </div>

{{-- ===== KHỐI NÚT / FORM CỌC ===== --}}
@if($canCheckout)
    {{-- Đã có đặt bàn, đang chờ cọc: hiện form + QR (giỏ trống thì QR là 100.000đ) --}}
    <form id="checkoutForm" action="{{ route('cart.checkout') }}" method="POST">
        @csrf
        <input type="hidden" name="total_price" value="{{ $total }}">
        <input type="hidden" name="deposit_amount" id="deposit_amount_input" value="{{ $depositAmount }}">
        <input type="hidden" name="table_number" value="{{ $reservation['table_id'] }}">
        <input type="hidden" name="order_notes" value="{{ $reservation['notes'] ?? '' }}">

        <div class="payment-methods-box mt-2">
            <h5>
                <i class="fas fa-gem mr-2" style="color: var(--ct-gold-text, #8a6a28);"></i> Phương thức đặt cọc
            </h5>

            {{-- Cọc bắt buộc qua chuyển khoản, không cho chọn phương thức khác --}}
            <input type="hidden" name="payment_method" value="BANK">

            <div class="bank-transfer-card">
                <div class="bank-transfer-header">
                    <i class="fas fa-qrcode"></i>
                    <div>
                        <h6>Chuyển khoản ngân hàng</h6>
                        <small>Quét mã QR bên dưới để đặt cọc giữ bàn</small>
                    </div>
                </div>

                <div id="qr-inline-area" style="display:block; text-align:center;">
                    <h6 class="font-weight-bold mb-3">Mã QR thanh toán tự động</h6>
                    <img id="qr-payment-img" src="{{ $qrUrl }}" alt="QR Code Payment" class="img-fluid mb-3">
                    <div class="bank-info-box">
                        <p class="small mb-1">Chủ tài khoản: <strong style="color:var(--ft-bg, #111010);">{{ $accountName }}</strong></p>
                        <p class="small mb-0">Nội dung CK: <strong style="color: var(--ct-gold-text, #8a6a28);">{{ $info }}</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <button type="button" class="btn-checkout" data-toggle="modal" data-target="#billModal">
                Xác nhận đặt cọc <span id="checkout-deposit-text">{{ number_format($depositAmount, 0, ',', '.') }}</span>đ
            </button>
        </div>
    </form>

@elseif($needReservation)
    {{-- Chưa có đặt bàn: chưa cho cọc, dẫn khách sang trang đặt bàn --}}
    <div style="background:#FCFBF8; border:2px solid #E2E8F0; border-radius:12px; padding:35px 30px; text-align:center; margin:30px 0;">
        <p style="font-size:17px; color:var(--ft-bg, #111010); font-weight:600; margin-bottom:8px;">
            <i class="fas fa-calendar-check mr-2" style="color: var(--ct-gold-text, #8a6a28);"></i>Bạn chưa chọn bàn
        </p>
        <p style="color:#718096; margin-bottom:20px;">
            Vui lòng nhập thông tin đặt bàn (bàn, ngày, giờ) trước khi đặt cọc.
        </p>
        <a href="{{ route('reservation.index') }}" class="btn-checkout"
           style="width:auto; max-width:320px; text-decoration:none; display:inline-block;">
            Đặt bàn ngay
        </a>
        @unless(count($cart))
            <a href="{{ route('menu.index') }}" class="btn-checkout btn-checkout--dark"
               style="width:auto; max-width:320px; text-decoration:none; display:inline-block; margin-left:10px;">
                Thêm món ăn
            </a>
        @endunless
    </div>
@endif
{{-- ===== HẾT KHỐI ===== --}}
    </div>
</div>
</div>{{-- /.cart-page --}}

<div class="modal fade" id="billModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content premium-bill">
            <div class="modal-body p-4">
                <div class="bill-header-gold">
                    <img src="{{ asset('images/logo.png') }}" alt="Aurora Garden" style="height: 115px; width: auto;">
                    <!-- <h5 class="bill-title-luxury">Aurora Garden</h5> -->
                    <p class="small text-muted mb-0" style="font-size: 12px;">Đỉnh cao ẩm thực truyền thống</p>
                </div>
                
                <div class="luxury-divider"></div>
                
                <div class="mb-3" style="font-size: 13px; line-height: 1.6;">
                    <p class="mb-1"><span style="color: #718096; display: inline-block; width: 75px;">Số phiếu:</span> <strong>{{ $invoiceNumber }}</strong></p>
                    <p class="mb-1"><span style="color: #718096; display: inline-block; width: 75px;">Thời gian:</span> <strong>{{ $currentDateTime }}</strong></p>
                    <p class="mb-1"><span style="color: #718096; display: inline-block; width: 75px;">Phục vụ:</span> <strong style="color: var(--ct-gold-text, #8a6a28);">{{ $reservation['table'] ?? '1' }}</strong></p>
                    <p class="mb-1"><span style="color: #718096; display: inline-block; width: 75px;">Thanh toán:</span> <strong id="bill-payment-method-text">Chuyển khoản QR (đặt cọc)</strong></p>
                    {{-- ✅ THÊM MỚI: GHI CHÚ TRONG MODAL BILL --}}
                    @if(!$notesEmpty)
                        <p class="mb-1">
                            <span style="color: #718096; display: inline-block; width: 75px;">Ghi chú:</span> 
                            <strong style="color: var(--ct-gold-text, #8a6a28); font-style: italic;">{{ $notesValue }}</strong>
                        </p>
                    @endif
                </div>

                <table class="table-premium mt-3">
                    <thead>
                        <tr>
                            <th class="text-left">Tên món</th>
                            <th class="text-center">SL</th>
                            <th class="text-right">Tổng</th>
                        </tr>
                    </thead>
                    <tbody id="bill-items">
                        @foreach($cart as $item)
                            @if($item['quantity'] > 0)
                            <tr>
                                <td style="font-weight: 500;">{{ $item['name'] }}</td>
                                <td class="text-center">{{ $item['quantity'] }}</td>
                                <td class="text-right">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                
               <div class="total-highlight">
    <div class="d-flex justify-content-between align-items-center">
        <span class="font-weight-bold" style="font-size: 14px; letter-spacing: .3px;">Tổng hóa đơn</span>
        <span class="h5 mb-0 font-weight-bold" id="bill-total">{{ number_format($total, 0, ',', '.') }}đ</span>
    </div>
</div>
<div class="total-highlight" style="margin-top: 8px; background:#FFFDF8; color:var(--ft-bg, #111010); border:1px dashed var(--ft-gold, #c9a45c);">
    <div class="d-flex justify-content-between" style="font-size:13px;">
        <span>Đặt cọc</span><strong style="color: var(--ct-gold-text, #8a6a28);" id="bill-deposit">{{ number_format($depositAmount, 0, ',', '.') }}đ</strong>
    </div>
    <div class="d-flex justify-content-between" style="font-size:13px; color:#718096;">
        <span>Còn lại tại quầy</span><span id="bill-remaining">{{ number_format($remainingAmount, 0, ',', '.') }}đ</span>
    </div>
</div>
                {{-- (đã bỏ một thẻ </div> thừa ở đây: nó đóng .modal-body quá sớm) --}}

                <div class="text-center mt-4">
                    <p style="font-size: 12px; font-style: italic; color: #718096;" class="mb-0">Trân trọng cảm ơn quý khách!</p>
                </div>
                
                <button type="button" class="btn-luxury-pay" onclick="submitOrder()">Xác nhận thanh toán</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Hằng số & hàm tạo QR: đặt ở phạm vi toàn cục để updateGrandTotal() và các hàm khác dùng được
        const QR_BANK_ID = "{{ $bankID }}";
const QR_ACCOUNT_NO = "{{ $accountNo }}";
const QR_ACCOUNT_NAME = "{{ $nameEncoded }}";
const QR_INFO = "{{ $infoEncoded }}";

function buildQrUrl(amount) {
    return `https://img.vietqr.io/image/${QR_BANK_ID}-${QR_ACCOUNT_NO}-compact.png?amount=${amount}&addInfo=${QR_INFO}&accountName=${QR_ACCOUNT_NAME}`;
}

    function changeQty(btn, value) {
        const row = btn.closest('tr');
        const input = row.querySelector('.qty-input');
        const stockAvailable = parseInt(input.dataset.stock);
        const menuId = input.dataset.id;
        
        let qty = parseInt(input.value);
        let newQty = qty + value;

        if (newQty < 0) return; 

        if (newQty === 0) {
            if (confirm('Bạn có chắc chắn muốn bỏ món này khỏi thực đơn?')) {
                row.remove();
                try { updateGrandTotal(); } catch (e) { console.error(e); }
            } else {
                return;
            }
        } else {
            if (value > 0 && newQty > stockAvailable) {
                alert('Hết hàng! Còn ' + stockAvailable + ' phần.');
                return;
            }
            input.value = newQty;
            try { updateRowTotal(row); updateGrandTotal(); } catch (e) { console.error(e); }
        }

        fetch('{{ route("cart.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                id: menuId, 
                quantity: newQty
            })
        })
        .then(response => response.json())
        .then(data => {
            if(!data.success) {
                alert("Có lỗi xảy ra, vui lòng thử lại!");
                location.reload();
            }
        })
        .catch(error => console.error('Lỗi đồng bộ:', error));
    }

    function updateRowTotal(row) {
        const qty = parseInt(row.querySelector('.qty-input').value);
        const price = parseInt(row.querySelector('.qty-input').dataset.price);
        row.querySelector('.font-weight-bold').innerText = new Intl.NumberFormat('vi-VN').format(qty * price) + 'đ';
    }

   // Vẽ lại hoá đơn (modal) theo đúng các món đang có trong bảng, để luôn khớp khi bấm +/-
   function refreshBill(total, depositAmount, remainingAmount) {
    const fmt = n => new Intl.NumberFormat('vi-VN').format(n) + 'đ';
    const tbody = document.getElementById('bill-items');
    if (tbody) {
        tbody.innerHTML = '';
        document.querySelectorAll('.table-order tbody tr').forEach(row => {
            const input = row.querySelector('.qty-input');
            if (!input) return;
            const qty = parseInt(input.value);
            if (!(qty > 0)) return;
            const name = row.querySelector('strong') ? row.querySelector('strong').textContent : '';
            const tr = document.createElement('tr');
            const tdName = document.createElement('td');
            tdName.style.fontWeight = '500';
            tdName.textContent = name;                 // textContent: không chèn HTML (chống XSS)
            const tdQty = document.createElement('td');
            tdQty.className = 'text-center';
            tdQty.textContent = qty;
            const tdSum = document.createElement('td');
            tdSum.className = 'text-right';
            tdSum.textContent = fmt(qty * parseInt(input.dataset.price));
            tr.append(tdName, tdQty, tdSum);
            tbody.appendChild(tr);
        });
    }
    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = fmt(v); };
    set('bill-total', total);
    set('bill-deposit', depositAmount);
    set('bill-remaining', remainingAmount);
   }

   function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        total += parseInt(input.value) * parseInt(input.dataset.price);
    });

    // Phải khớp Reservation::calcDeposit() ở PHP: có món 20%, không món 100.000đ
const TABLE_ONLY_DEPOSIT = {{ \App\Models\Reservation::TABLE_ONLY_DEPOSIT }};
const depositAmount = total > 0 ? Math.round(total * 20 / 100) : TABLE_ONLY_DEPOSIT;
    const remainingAmount = total - depositAmount;

    document.getElementById('grand-total-text').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
    document.getElementById('deposit-total-text').innerText = new Intl.NumberFormat('vi-VN').format(depositAmount) + ' VNĐ';
    const btnDeposit = document.getElementById('checkout-deposit-text');
    if (btnDeposit) btnDeposit.innerText = new Intl.NumberFormat('vi-VN').format(depositAmount);
    document.getElementById('remaining-total-text').innerText = new Intl.NumberFormat('vi-VN').format(remainingAmount) + ' VNĐ';

    refreshBill(total, depositAmount, remainingAmount);

        const totalInput = document.querySelector('input[name="total_price"]');
    if (totalInput) totalInput.value = total;
    const depositInput = document.getElementById('deposit_amount_input');
    if (depositInput) depositInput.value = depositAmount;

    const qrImg = document.getElementById('qr-payment-img');
    if (qrImg) {
        qrImg.src = buildQrUrl(depositAmount);
    }
}

    function submitOrder() {
        document.getElementById('checkoutForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const qrArea = document.getElementById('qr-inline-area');
        const labelCod = document.getElementById('label-cod');
        const labelBank = document.getElementById('label-bank');
        const radios = document.querySelectorAll('input[name="payment_method"]');

       function updateUI(value) {
    const methodText = document.getElementById('bill-payment-method-text');
    if (value === 'BANK') {
        qrArea.style.display = 'block';
        labelBank.classList.add('active');
        labelCod.classList.remove('active');
        if (methodText) methodText.innerText = 'Chuyển khoản QR';

        const currentDeposit = document.getElementById('deposit_amount_input').value;
        document.getElementById('qr-payment-img').src = buildQrUrl(currentDeposit);
    } else {
        qrArea.style.display = 'none';
        labelCod.classList.add('active');
        labelBank.classList.remove('active');
        if (methodText) methodText.innerText = 'Tại bàn (tiền mặt / POS)';
    }
}
        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                updateUI(this.value);
            });
        });
    });
</script>
@endsection