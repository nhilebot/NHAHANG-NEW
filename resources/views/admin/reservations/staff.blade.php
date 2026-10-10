@extends('layouts.admin')
@section('title', 'Phục vụ')

@section('admin_content')

{{-- STATS --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:15px; margin-bottom:20px;">
    <div style="background:#fff; padding:15px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        <div style="font-size:12px; color:#64748b;">Bàn trống</div>
        <div style="font-size:24px; font-weight:700; color:#22c55e;">{{ $tables->where('status','empty')->count() }}</div>
    </div>
    <div style="background:#fff; padding:15px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        <div style="font-size:12px; color:#64748b;">Chờ vào bàn</div>
        <div style="font-size:24px; font-weight:700; color:#3b82f6;">{{ $reservations->where('status','confirmed')->count() }}</div>
    </div>
    <div style="background:#fff; padding:15px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        <div style="font-size:12px; color:#64748b;">Bếp đang nấu</div>
        <div style="font-size:24px; font-weight:700; color:#f59e0b;">{{ $reservations->whereIn('status',['arrived','serving'])->count() }}</div>
    </div>
    <div style="background:#fff; padding:15px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        <div style="font-size:12px; color:#64748b;">Chờ bưng món</div>
        <div style="font-size:24px; font-weight:700; color:#10b981;">{{ $reservations->where('status','served')->count() }}</div>
    </div>
</div>

{{-- BẢNG PHỤC VỤ --}}
{{-- ═══ STAFF ═══ --}}
@if(in_array($role, ['staff', 'nhân viên', 'nhan vien'])) 
        <a class="nav-item {{ Request::routeIs('staff.tables.*') ? 'active' : '' }}" href="{{ route('staff.tables.index') }}">
        <div class="nav-item-left"><div class="nav-icon">🪑</div>Quản lý bàn</div>
    </a>
    <a class="nav-item {{ Request::routeIs('staff.reservations.*') ? 'active' : '' }}" href="{{ route('staff.reservations.index') }}">
        <div class="nav-item-left"><div class="nav-icon">📋</div>Danh sách phục vụ</div>
    </a>
@endif
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Bàn</th>
                        <th>Khách hàng</th>
                        <th>Món đã đặt</th>
                        <th>Trạng thái</th>
                        <th class="text-center" style="min-width:200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                    @php
                        $cartItems  = $res->cart_data ?? [];
                        $statuses   = $res->chef_statuses ?? [];
                        $doneCount  = collect($cartItems)->filter(fn($i) => ($statuses[$i['id']] ?? 'pending') === 'done')->count();
                        $totalCount = count($cartItems);
                        $allDone    = $totalCount > 0 && $doneCount === $totalCount;
                    @endphp
                    <tr class="{{ $res->status === 'served' ? 'table-success' : '' }}">

                        {{-- BÀN --}}
                        <td><span class="badge border text-dark fs-6">{{ $res->table->name ?? 'Bàn '.$res->table_id }}</span></td>

                        {{-- KHÁCH --}}
                        <td>
                            <strong>{{ $res->full_name }}</strong>
                            <div class="small text-muted">{{ $res->phone }}</div>
                            <div class="small text-muted">{{ $res->reservation_time }}</div>
                            @if($res->notes)
                            <div class="small text-warning mt-1">📝 {{ $res->notes }}</div>
                            @endif
                        </td>

                        {{-- MÓN ĂN --}}
                        <td style="min-width:200px;">
                            @if($totalCount > 0)
                                @foreach($cartItems as $item)
                                @php $st = $statuses[$item['id']] ?? 'pending'; @endphp
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span style="font-size:.8rem;">
                                        @if($st==='done') ✅ @elseif($st==='cooking') 🔥 @else ⏳ @endif
                                    </span>
                                    <span style="font-size:.85rem; {{ $st==='done' ? 'text-decoration:line-through;opacity:.5;':'' }}">
                                        {{ $item['quantity'] }}x {{ $item['name'] }}
                                    </span>
                                </div>
                                @endforeach
                                @if(in_array($res->status, ['arrived','serving','served']))
                                <div class="progress mt-1" style="height:5px;">
                                    <div class="progress-bar bg-success" style="width:{{ $totalCount>0 ? ($doneCount/$totalCount)*100 : 0 }}%"></div>
                                </div>
                                <div class="small text-muted mt-1">Bếp: {{ $doneCount }}/{{ $totalCount }} món xong</div>
                                @endif
                            @else
                                <span class="text-muted small">Chưa có món</span>
                            @endif
                        </td>

                        {{-- TRẠNG THÁI --}}
                        <td>
                            <span class="badge-status {{ \App\Helpers\StatusHelper::getBadgeClass($res->status) }}">
    {{ \App\Helpers\StatusHelper::getTextWithEmoji($res->status) }}
</span>
                        </td>

                        {{-- THAO TÁC --}}
                        <td>
                            <div class="d-flex flex-column gap-1">

                                {{-- 1. Khách đến → Xác nhận vào bàn --}}
                                @if($res->status === 'confirmed')
                                <form action="{{ route('staff.reservations.updateStatus', $res->id) }}" method="POST">
                                    @csrf
                                    <button name="status" value="arrived" class="btn btn-primary btn-sm w-100">👤 Khách vào bàn</button>
                                </form>
                                @endif

                                {{-- 2. Thêm/sửa món + Gửi bếp --}}
                                @if(in_array($res->status, ['confirmed','arrived','serving']))
                                <a href="{{ route('staff.reservations.order', $res->id) }}" class="btn btn-warning btn-sm w-100 text-dark">
                                    🍴 Quản lý món
                                </a>
                                @if($totalCount > 0 && in_array($res->status, ['confirmed','arrived']))
                                <form action="{{ route('staff.reservations.sendToKitchen', $res->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-orange btn-sm w-100" style="background:#f97316;color:#fff;">
                                        📤 Gửi xuống bếp
                                    </button>
                                </form>
                                @endif
                                @endif

                                {{-- 3. Bếp đang nấu --}}
                                @if($res->status === 'serving')
                                <div class="text-warning small text-center">🔥 Bếp đang nấu ({{ $doneCount }}/{{ $totalCount }})</div>
                                @endif

                                {{-- 4. Bếp xong → Bưng món ra --}}
                                @if($res->status === 'served')
                                <form action="{{ route('staff.reservations.updateStatus', $res->id) }}" method="POST">
                                    @csrf
                                    <button name="status" value="completed" class="btn btn-success btn-sm w-100 fw-bold">🍽️ Đã bưng món ra</button>
                                </form>
                                @endif

                                {{-- 5. Thanh toán --}}
                                @if(in_array($res->status, ['arrived','serving','served','completed']))
                                <button class="btn btn-sm btn-outline-success w-100"
                                    onclick="showPayModal({{ $res->id }}, '{{ $res->table->name ?? $res->table_id }}', {{ $res->total_price }})">
                                    💰 Thanh toán
                                </button>
                                @endif

                                {{-- 6. Đã thanh toán → Dọn bàn --}}
                                @if(str_contains($res->status, 'paid'))
                                <form action="{{ route('staff.reservations.updateStatus', $res->id) }}" method="POST">
                                    @csrf
                                    <button name="status" value="completed" class="btn btn-dark btn-sm w-100">🧹 Dọn bàn & Xong</button>
                                </form>
                                @endif

                                {{-- Ghi chú nhanh --}}
                                <button class="btn btn-sm btn-outline-secondary w-100"
                                    onclick="showNoteModal({{ $res->id }}, `{{ addslashes($res->notes ?? '') }}`)">
                                    📝 Ghi chú
                                </button>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted"><div style="font-size:2rem;">🎉</div>Không có bàn nào cần phục vụ.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL THANH TOÁN --}}
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">💰 Thanh toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="payForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-1">Bàn: <strong id="payTableName"></strong></p>
                    <p class="mb-3">Tổng tiền: <strong id="payAmount" class="text-success fs-5"></strong></p>
                    <div class="d-grid gap-2">
                        <label class="border rounded p-3 d-flex align-items-center gap-3" style="cursor:pointer;">
                            <input type="radio" name="payment_method" value="paid_cash" required> 💵 Tiền mặt
                        </label>
                        <label class="border rounded p-3 d-flex align-items-center gap-3" style="cursor:pointer;">
                            <input type="radio" name="payment_method" value="paid_transfer"> 💳 Chuyển khoản
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">✅ Xác nhận thanh toán</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL GHI CHÚ --}}
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📝 Ghi chú đặc biệt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="noteForm" method="POST">
                @csrf
                <div class="modal-body">
                    <textarea name="notes" id="noteText" class="form-control" rows="4"
                        placeholder="VD: Ít cay, không hành, dị ứng hải sản..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">💾 Lưu ghi chú</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showPayModal(resId, tableName, total) {
    document.getElementById('payTableName').textContent = tableName;
    document.getElementById('payAmount').textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
    document.getElementById('payForm').action = '/staff/reservations/' + resId + '/payment';
    new bootstrap.Modal(document.getElementById('payModal')).show();
}
function showNoteModal(resId, currentNote) {
    document.getElementById('noteText').value = currentNote;
    document.getElementById('noteForm').action = '/staff/reservations/' + resId + '/note';
    new bootstrap.Modal(document.getElementById('noteModal')).show();
}
</script>
@endsection
