@extends('layouts.admin')

@section('topbar_title', 'Hệ thống bếp – Quản lý đơn hàng')

@section('admin_content')
<style>
    .kds-wrapper {
        background-color: #f1f5f9;
        min-height: calc(100vh - 100px);
        padding: 20px;
        overflow-x: auto;
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }
    .kds-card {
        min-width: 300px;
        max-width: 320px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .kds-header { padding: 15px; color: #fff; position: relative; }
    .bg-new-order     { background-color: #f87171; }
    .bg-cooking-order { background-color: #fbbf24; }
    .kds-header .table-name { font-size: 1.4rem; font-weight: 800; margin: 0; display: block; }
    .kds-header .order-time { font-size: 0.9rem; opacity: 0.9; }
    .kds-body { padding: 0; flex-grow: 1; }
    .kds-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
    }
    .kds-item:last-child { border-bottom: none; }
    .item-info { display: flex; align-items: center; gap: 10px; }
    .item-qty  { font-weight: 800; color: #ef4444; font-size: 1.1rem; }
    .item-name { font-weight: 600; color: #1e293b; }
    .item-done { text-decoration: line-through; opacity: 0.4; }
    .kds-footer { padding: 12px; background: #f8fafc; display: flex; gap: 8px; }
    .btn-kds {
        flex: 1; border: none; padding: 10px; border-radius: 8px;
        font-weight: 700; text-transform: uppercase; font-size: 0.75rem; cursor: pointer;
    }
    .status-bar {
        position: fixed; bottom: 0; left: 0; right: 0;
        background: #fff; padding: 10px 20px;
        border-top: 2px solid #e2e8f0;
        display: flex; gap: 15px; z-index: 999;
    }
    .status-mini {
        padding: 5px 15px; border-radius: 4px;
        font-size: 0.8rem; font-weight: 600;
    }
    .badge-item {
        font-size: .72rem; font-weight: 700; padding: 2px 8px;
        border-radius: 20px; white-space: nowrap;
    }
    .bi-pending  { background:#fff4e5; color:#92400e; }
    .bi-cooking  { background:#fef3c7; color:#b45309; }
    .bi-done     { background:#d1fae5; color:#065f46; }
</style>

<div class="kds-wrapper" style="padding-bottom: 60px;">

    @forelse($pendingOrders as $reservation)
    @php
        $statuses  = $reservation->chef_statuses ?? [];
        $cartItems = $reservation->cart_data ?? [];
        $isCooking = $reservation->status === 'serving';
        $tableName = $reservation->table->name ?? ('Bàn ' . $reservation->table_id);
    @endphp

    <div class="kds-card">
        {{-- HEADER --}}
        <div class="kds-header {{ $isCooking ? 'bg-cooking-order' : 'bg-new-order' }}">
            <span class="table-name">{{ $tableName }}</span>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="order-time">
                    {{ $reservation->updated_at->format('H:i') }} •
                    {{ $reservation->full_name }}
                </span>
                <span style="font-size:.8rem; background:rgba(0,0,0,.2); padding:2px 8px; border-radius:10px;">
                    {{ $isCooking ? '🔥 Đang nấu' : '🆕 Đơn mới' }}
                </span>
            </div>
             @if(!empty($reservation->notes))
        <div style="margin-top: 8px; background: rgba(0,0,0,0.2); border-radius: 6px; padding: 6px 10px; font-size: 0.82rem; line-height: 1.4;">
            <span style="opacity: 0.85;">📝 Ghi chú:</span>
            <span style="font-weight: 700;">{{ $reservation->notes }}</span>
        </div>
    @endif
        </div>

        {{-- DANH SÁCH MÓN --}}
        <div class="kds-body">
            @forelse($cartItems as $item)
            @php
                $menuId    = $item['id'];
                $itemSt    = $statuses[$menuId] ?? 'pending';
            @endphp
            <div class="kds-item">
                <div class="item-info">
                    <span class="item-qty">{{ $item['quantity'] }}</span>
                    <span class="item-name {{ $itemSt === 'done' ? 'item-done' : '' }}">
                        {{ $item['name'] }}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    {{-- Badge trạng thái --}}
                    <span class="badge-item {{ \App\Helpers\StatusHelper::getBgClass($itemSt) }}">
    {{ \App\Helpers\StatusHelper::getTextWithEmoji($itemSt) }}
</span>

                    {{-- Nút đổi trạng thái từng món --}}
                    @if($itemSt !== 'done')
                    <form action="{{ route('chef.item.status', [$reservation->id, $menuId]) }}" method="POST">
                        @csrf
                        @if($itemSt === 'pending')
                            <button name="chef_status" value="cooking"
                                class="btn btn-sm btn-outline-warning p-1" title="Bắt đầu nấu món này">🍳</button>
                        @else
                            <button name="chef_status" value="done"
                                class="btn btn-sm btn-outline-success p-1" title="Món này đã xong">✅</button>
                        @endif
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-3" style="font-size:.85rem;">Chưa có món trong đơn này</div>
            @endforelse
        </div>

        {{-- FOOTER NÚT BẤM --}}
        <div class="kds-footer">
            @if($reservation->status === 'arrived')
            <form action="{{ route('chef.order.start', $reservation->id) }}" method="POST" class="w-100">
                @csrf
                <button class="btn-kds w-100" style="background:#fbbf24; color:#fff;">▶ Bắt đầu nấu tất cả</button>
            </form>
            @endif
            <form action="{{ route('chef.order.finish', $reservation->id) }}" method="POST" class="w-100">
                @csrf
                <button class="btn-kds w-100" style="background:#10b981; color:#fff;">✅ Xong tất cả</button>
            </form>
        </div>
    </div>

    @empty
    <div class="w-100 text-center py-5">
        <div style="font-size: 3rem;">🎉</div>
        <h4 class="text-muted mt-3">Đang đợi đơn hàng mới từ khách...</h4>
        <p class="text-muted" style="font-size:.85rem;">
            Đơn sẽ xuất hiện khi nhân viên xác nhận khách đã nhận bàn
        </p>
    </div>
    @endforelse

</div>

{{-- STATUS BAR --}}
<div class="status-bar">
    <div class="status-mini bg-warning text-dark">⏳ Đang chờ: {{ $pendingItems }} món</div>
    <div class="status-mini bg-danger text-white">🔥 Đang nấu: {{ $cookingItems }} món</div>
    <div class="status-mini bg-success text-white">✅ Đã xong: {{ $readyOrders->count() }} đơn</div>
    <div class="status-mini" style="background:#e2e8f0; color:#475569; margin-left:auto;">
        🕐 {{ now()->format('H:i:s') }}
    </div>
</div>

{{-- Tự động refresh mỗi 30 giây để cập nhật đơn mới --}}
<script>setTimeout(() => location.reload(), 30000);</script>

@endsection
