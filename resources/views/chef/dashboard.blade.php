@extends('layouts.admin')

@section('topbar_title', 'Hệ thống bếp – Quản lý đơn hàng')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/chef/dashboard.css') }}?v=1">
@endpush

<div class="kds-wrapper" style="padding-bottom: 60px;">

    @forelse($pendingOrders as $reservation)
    @php
        // Mỗi LẦN GỌI MÓN là một dòng riêng (reservation_items), không gộp theo tên món
        $lines      = $reservation->reservationItems->where('status', '!=', 'served');
        $hasPending = $lines->where('status', 'pending')->isNotEmpty();
        $isCooking  = $reservation->status === 'serving';
        $isExtraOnly = in_array($reservation->status, ['ready', 'served'], true);
        $tableName  = $reservation->table->name ?? ('Bàn ' . $reservation->table_id);
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
                    {{ $isExtraOnly ? '➕ Gọi thêm' : ($isCooking ? '🔥 Đang nấu' : '🆕 Đơn mới') }}
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
            @forelse($lines as $line)
            @php $itemSt = $line->status; @endphp
            <div class="kds-item">
                <div class="item-info">
                    <span class="item-qty">{{ $line->active_quantity }}</span>
                    <span class="item-name {{ $itemSt === 'done' ? 'item-done' : '' }}">
                        {{ $line->menu_name }}
                        @if($line->source === 'staff_extra')
                            <span class="badge-item" style="background:#e0e7ff; color:#3730a3;">➕ Gọi thêm (lần {{ $line->batch }})</span>
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    {{-- Badge trạng thái --}}
                    <span class="badge-item {{ \App\Helpers\StatusHelper::getBgClass($itemSt) }}">
    {{ \App\Helpers\StatusHelper::getTextWithEmoji($itemSt) }}
</span>

                    {{-- Nút đổi trạng thái đúng lần gọi món này --}}
                    @if($itemSt !== 'done')
                    <form action="{{ route('chef.item.line_status', $line->id) }}" method="POST">
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
            @if($hasPending)
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