@extends('layouts.admin')

@section('admin_content')
<style>
    /* Grid hiển thị linh hoạt */
    .chef-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;}
    
    /* Card cho từng đơn hàng */
    .chef-card{background:#fff;border-radius:15px;border:none;box-shadow: 0 4px 15px rgba(0,0,0,0.05);overflow:hidden;transition: transform 0.2s;}
    .chef-card:hover { transform: translateY(-5px); }
    
    /* Màu sắc theo trạng thái */
    .chef-card.arrived { border-top: 5px solid #5b3cff; } /* Đơn mới */
    .chef-card.serving { border-top: 5px solid #f59e0b; background-color: #fffdf5; } /* Đang nấu */

    .chef-card-header{padding:15px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f1f5f9;}
    .chef-card-title{font-weight:800;font-size:16px;color: #1e293b;}
    
    /* Danh sách món ăn */
    .chef-items-list { padding: 10px 0; }
    .chef-item{display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px dashed #e2e8f0;gap:10px;}
    .chef-item:last-child{border-bottom:0;}
    .chef-item-name{font-size:14px;font-weight:600;color: #334155;flex:1;}
    .chef-item-qty{font-size:13px;background:#e2e8f0;color: #475569;padding:3px 10px;border-radius:20px;font-weight:700;}
    
    /* Nút bấm thao tác */
    .btn-chef { padding: 12px; border-radius: 10px; font-weight: 700; font-size: 14px; transition: all 0.3s; border: none; }
    .btn-cooking { background: #f59e0b; color: white; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); }
    .btn-done { background: #10b981; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); }
    .btn-chef:hover { opacity: 0.9; transform: scale(0.98); }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-1">👨‍🍳 Điều hành Bếp</h3>
            <p class="text-muted small mb-0">Quản lý quy trình chế biến món ăn</p>
        </div>
        <div class="text-right">
            <span class="badge badge-pill badge-light shadow-sm p-2 text-primary">
                🔄 Tự động cập nhật: <span id="countdown">30</span>s
            </span>
        </div>
    </div>

    @if($pendingOrders->count() > 0)
    <div class="chef-grid">
        @foreach($pendingOrders as $order)
        <div class="chef-card {{ $order->status }}">
            <div class="chef-card-header">
                <div>
                    <div class="chef-card-title">📍 Bàn số: {{ $order->table_id }}</div>
                    <div style="font-size:12px;color:#64748b;">🕒 {{ $order->created_at?->format('d/m/Y') }} • {{ $order->full_name }}</div>
                </div>
                <span class="badge {{ $order->status == 'serving' ? 'badge-warning' : 'badge-primary' }} badge-pill p-2">
                    {{ $order->status == 'serving' ? '🔥 Đang nấu' : '🆕 Đơn mới' }}
                </span>
            </div>

           <div class="chef-items-list">
    @if(!empty($order->cart_data))
        @php
            $statuses = $order->chef_statuses ?? [];
            // Tách món cần nấu (pending) vs đã xử lý
            $pendingItems = collect($order->cart_data)->filter(
                fn($i) => ($statuses[$i['id']] ?? 'pending') === 'pending'
            );
            $doneItems = collect($order->cart_data)->filter(
                fn($i) => in_array($statuses[$i['id']] ?? 'pending', ['cooking', 'done', 'ready'])
            );
        @endphp

        {{-- Món cần nấu / gọi thêm mới --}}
        @if($pendingItems->count())
        <div style="padding:8px 20px 4px; font-size:11px; color:#f59e0b;
                    font-weight:800; text-transform:uppercase; letter-spacing:.5px;">
            🆕 Cần chế biến
        </div>
        @foreach($pendingItems as $item)
        <div class="chef-item" style="background:#fffbeb; border-left:3px solid #f59e0b;">
            <span class="chef-item-name" style="color:#92400e; font-weight:700;">
                🍲 {{ $item['name'] }}
            </span>
            <span class="chef-item-qty" style="background:#fef3c7; color:#d97706;">
                x{{ $item['quantity'] }}
            </span>
        </div>
        @endforeach
        @endif

        {{-- Món đã/đang nấu (mờ, chỉ tham khảo) --}}
        @if($doneItems->count())
        <div style="padding:8px 20px 4px; font-size:11px; color:#94a3b8;
                    font-weight:700; text-transform:uppercase; letter-spacing:.5px;">
            ✅ Đang nấu / Đã xong
        </div>
        @foreach($doneItems as $item)
        <div class="chef-item" style="opacity:.5;">
            <span class="chef-item-name">{{ $item['name'] }}</span>
            <span class="chef-item-qty">x{{ $item['quantity'] }}</span>
        </div>
        @endforeach
        @endif

    @else
        <div class="p-4 text-center text-muted small">Chưa có thông tin món ăn</div>
    @endif
</div>

          <div class="p-3">
    <form action="{{ route('admin.reservations.updateStatus', $order->id) }}" method="POST">
        @csrf
        {{-- BƯỚC 1: BẾP BẤM ĐỂ XÁC NHẬN BẮT ĐẦU NẤU --}}
        @if($order->status == 'arrived')
            <button name="status" value="serving" class="btn-chef btn-cooking w-100">👨‍🍳 TIẾP NHẬN & BẮT ĐẦU NẤU</button>

        {{-- BƯỚC 2: BẾP BẤM ĐỂ BÁO XONG --}}
        @elseif($order->status == 'serving')
            <button name="status" value="served" class="btn-chef btn-done w-100">✅ ĐÃ XONG & CHỜ LÊN MÓN</button>
        @endif
    </form>
</div>
        </div>
        @endforeach
    </div>
    @else
        <div class="card border-0 shadow-sm text-center py-5">
            <div style="font-size: 50px;">😴</div>
            <h5 class="text-muted mt-3">Hiện tại chưa có đơn hàng nào cần xử lý.</h5>
        </div>
    @endif
</div>

<script>
    // Đếm ngược thời gian reload
    let timeLeft = 30;
    setInterval(() => {
        timeLeft--;
        if(document.getElementById('countdown')) document.getElementById('countdown').innerText = timeLeft;
        if (timeLeft <= 0) location.reload();
    }, 1000);
</script>
@endsection