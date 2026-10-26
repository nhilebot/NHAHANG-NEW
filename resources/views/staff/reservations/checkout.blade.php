@extends('layouts.admin')
@section('title', 'Thanh toán')
@section('page_title', 'Thanh toán')
@section('topbar_title', 'Thanh toán')

@section('admin_content')
@php
    $deposit   = min((int) ($res->deposit_amount ?? 0), (int) $res->total_price);
    $bankId    = config('services.bank.id');
    $bankAcc   = config('services.bank.account');
    $bankName  = config('services.bank.name');
    $transfer  = 'HD' . str_pad($res->id, 5, '0', STR_PAD_LEFT);
    $qrReady   = !empty($bankAcc) && !empty($bankId);
@endphp

<div class="co-wrap">

    {{-- TIÊU ĐỀ --}}
    <div class="co-head">
        <div>
            <h2>💳 Thanh toán</h2>
            <p>{{ $res->table->name ?? '---' }} · {{ $res->full_name }} · #{{ str_pad($res->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        <a href="{{ route('staff.reservations.index') }}" class="co-back">← Quay lại</a>
    </div>

    <div class="co-grid">

        {{-- CỘT TRÁI: TÓM TẮT --}}
        <div class="co-card">
            <h3>🧾 Tóm tắt hóa đơn</h3>

            @php $items = is_array($res->cart_data) ? $res->cart_data : []; @endphp
            @if(count($items))
                <div class="co-items">
                    @foreach($items as $it)
                        <div class="co-item">
                            <span>{{ $it['name'] ?? 'Món' }} <small>× {{ $it['quantity'] }}</small></span>
                            <b>{{ number_format($it['price'] * $it['quantity'], 0, ',', '.') }}đ</b>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="co-row"><span>Tổng hóa đơn</span><b>{{ number_format($res->total_price, 0, ',', '.') }}đ</b></div>
            @if(($discount ?? 0) > 0)
                <div class="co-row green"><span>Khuyến mãi {{ $promo['promotion']->code }}</span><b>− {{ number_format($discount, 0, ',', '.') }}đ</b></div>
            @endif
            <div class="co-row green"><span>Đã cọc</span><b>− {{ number_format($deposit, 0, ',', '.') }}đ</b></div>

            {{-- MÃ KHUYẾN MÃI --}}
            @php
                $pfx = request()->is('admin/*') ? 'admin' : 'staff';
                $appliedPromo = $promo['promotion'] ?? null;
            @endphp
            <div style="margin:14px 0;padding:12px;border:1px dashed #c9a45c;border-radius:10px;background:#FFFDF8;">
                <div style="font-weight:700;font-size:14px;margin-bottom:8px;">🎟️ Mã khuyến mãi</div>
                @if($appliedPromo)
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span><b>{{ $appliedPromo->code }}</b> — {{ $appliedPromo->name }} ({{ $appliedPromo->discount_text }})</span>
                        <form method="POST" action="{{ route($pfx . '.reservations.promotion.remove', $res->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" style="border:1px solid #FED7D7;background:#fff;color:#C53030;border-radius:6px;padding:5px 10px;cursor:pointer;">Gỡ mã</button>
                        </form>
                    </div>
                    @if(!empty($promo['error']))
                        <div style="margin-top:8px;color:#C53030;font-size:13px;">⚠️ {{ $promo['error'] }} Hãy gỡ mã để tiếp tục thanh toán.</div>
                    @endif
                @else
                    <form method="POST" action="{{ route($pfx . '.reservations.promotion.apply', $res->id) }}" style="display:flex;gap:8px;">
                        @csrf
                        <input type="text" name="code" placeholder="Nhập mã" maxlength="30" required
                               style="flex:1;padding:8px 10px;border:1px solid #E2E8F0;border-radius:8px;text-transform:uppercase;">
                        <button type="submit" style="border:none;background:#D4AF37;color:#fff;font-weight:700;border-radius:8px;padding:8px 14px;cursor:pointer;">Áp dụng</button>
                    </form>
                @endif
            </div>

            <div class="co-due">
                <p>Khách cần thanh toán</p>
                <h1>{{ number_format($due, 0, ',', '.') }}đ</h1>
            </div>
        </div>

        {{-- CỘT PHẢI: CHỌN PHƯƠNG THỨC --}}
        <div class="co-card">
            <h3>Chọn phương thức thanh toán</h3>

            <form method="POST" action="{{ route('staff.reservations.pay', $res->id) }}" id="payForm">
                @csrf

                <div class="co-methods">
                    <label class="co-method" data-m="cash">
                        <input type="radio" name="payment_method" value="cash" required>
                        <span class="ico">💰</span>
                        <span><b>Tiền mặt</b><small>Thu trực tiếp tại quầy</small></span>
                    </label>

                    <label class="co-method" data-m="transfer">
                        <input type="radio" name="payment_method" value="transfer">
                        <span class="ico">📱</span>
                        <span><b>Chuyển khoản</b><small>Khách quét mã QR</small></span>
                    </label>
                </div>

                {{-- TIỀN MẶT --}}
                <div class="co-panel" id="panel-cash">
                    <p>Nhận đủ số tiền <b>{{ number_format($due, 0, ',', '.') }}đ</b> từ khách rồi bấm xác nhận.</p>
                </div>

                {{-- CHUYỂN KHOẢN --}}
                <div class="co-panel" id="panel-transfer">
                    @if($qrReady)
                        <div class="co-qr">
                            <img src="{{ $qrUrl }}" alt="QR chuyển khoản"
                                 onerror="this.style.display='none';document.getElementById('qrErr').style.display='block'">
                            <div id="qrErr" class="co-qr-err">Không tải được ảnh QR. Vui lòng cho khách chuyển khoản theo thông tin bên dưới.</div>
                        </div>
                        <div class="co-bank">
                            <div><span>Ngân hàng</span><b>{{ $bankId }}</b></div>
                            <div><span>Số tài khoản</span><b>{{ $bankAcc }}</b></div>
                            <div><span>Chủ tài khoản</span><b>{{ $bankName }}</b></div>
                            <div><span>Số tiền</span><b>{{ number_format($due, 0, ',', '.') }}đ</b></div>
                            <div><span>Nội dung</span><b>{{ $transfer }}</b></div>
                        </div>
                        <p class="co-note">Chỉ bấm xác nhận khi đã thấy tiền về tài khoản.</p>
                    @else
                        <div class="co-qr-err" style="display:block">
                            ⚠️ Chưa cấu hình tài khoản ngân hàng. Thêm <code>BANK_ID</code>, <code>BANK_ACCOUNT</code>,
                            <code>BANK_ACCOUNT_NAME</code> vào file .env rồi chạy <code>php artisan config:clear</code>.
                        </div>
                    @endif
                </div>

                <button type="submit" id="confirmBtn" class="co-confirm" disabled>Chọn phương thức thanh toán</button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/staff/reservations-checkout.css') }}?v=1">
@endpush

<script>
const money = '{{ number_format($due, 0, ',', '.') }}đ';
const btn   = document.getElementById('confirmBtn');
const promoBlocked = {{ !empty($promo['error']) ? 'true' : 'false' }};

document.querySelectorAll('.co-method').forEach(label => {
    label.querySelector('input').addEventListener('change', () => {
        const m = label.dataset.m;

        document.querySelectorAll('.co-method').forEach(l => l.classList.toggle('active', l === label));
        document.getElementById('panel-cash').classList.toggle('show', m === 'cash');
        document.getElementById('panel-transfer').classList.toggle('show', m === 'transfer');

        btn.disabled = promoBlocked;   // mã khuyến mãi không còn hợp lệ => phải gỡ mã trước
        btn.textContent = m === 'transfer'
            ? '✅ Đã nhận chuyển khoản ' + money
            : '✅ Đã thu tiền mặt ' + money;
    });
});

document.getElementById('payForm').addEventListener('submit', e => {
    if (!confirm('Xác nhận đã nhận đủ ' + money + '? Thao tác này không hoàn tác.')) e.preventDefault();
});
</script>
@endsection