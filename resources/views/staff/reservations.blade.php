@extends('layouts.admin')
@section('title', 'Quản lý Đặt bàn - Staff')

@section('admin_content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .badge-status {
        padding: 6px 14px; font-weight: 600; font-size: 0.85rem;
        border-radius: 50px; display: inline-flex; align-items: center; gap: 6px;
    }
    .st-pending { background-color: #fff4e5; color: #664d03; }
    .st-confirmed { background-color: #e7f6f8; color: #087990; }
    .st-arrived { background-color: #e7f0ff; color: #0d6efd; }
    .st-serving { background-color: #fff9db; color: #856404; }
    .st-served { background-color: #e0f7fa; color: #006064; }
    .st-success { background-color: #e9f7ef; color: #198754; }

    /* Cải thiện hiển thị cột khách hàng */
    .customer-cell {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .customer-info {
        flex: 1;
    }
    .customer-actions {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-left: 10px;
    }
    .btn-action-mini {
        padding: 2px 8px;
        font-size: 11px;
        border-radius: 4px;
        text-decoration: none;
        white-space: nowrap;
    }
</style>

<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 font-weight-bold text-primary">📋 Danh sách Đặt bàn - Quản lý Phục vụ</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Bàn</th>
                            <th>Khách hàng & Hóa đơn</th>
                            <th>Trạng thái hiện tại</th>
                            <th class="text-center">Thao tác phục vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $res)
                        <tr>
                            <td>
                                <span class="badge bg-info text-white fs-6">
                                    <div class="table-name"> {{ $res->table->name }} - {{ $res->table->capacity }} Ghế </div>
                                </span>
                            </td>

                            <td>
                                <div class="customer-cell">
                                    <div class="customer-info">
                                        <strong>{{ $res->full_name }}</strong>
                                        <div class="small text-muted"><i class="fas fa-phone fa-xs"></i> {{ $res->phone }}</div>
                                    </div>

                                    <div class="customer-actions">
                                        {{-- NÚT CHỈNH SỬA MÓN: Chỉ hiện khi chưa hoàn tất để tránh sai lệch kế toán --}}
                                        @if($res->status != 'completed')
                                            <a href="{{ route('staff.reservations.edit_items', $res->id) }}" 
                                               class="btn btn-sm btn-light border text-primary" 
                                               title="Chỉnh sửa món">
                                                <i class="fas fa-pen-to-square"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge {{ \App\Helpers\StatusHelper::getBadgeClass($res->status) }}">
                                    {{ \App\Helpers\StatusHelper::getTextWithEmoji($res->status) }}
                                </span>
                            </td>

                            <td class="text-center">
                                <form action="{{ route('staff.reservations.updateStatus', $res->id) }}" method="POST">
                                    @csrf
                                   <div class="btn-group btn-group-sm shadow-sm">
    {{-- 0. Xác nhận cọc (bước mới, đứng trước "Duyệt đơn") --}}
    @if($res->status == 'pending_deposit')
        @if($res->payment_status === 'deposit_submitted')
            <button
                type="submit"
                name="status"
                value="pending"
                class="btn btn-warning text-dark"
                onclick="return confirm('Xác nhận đã nhận cọc {{ number_format($res->deposit_amount ?? 0, 0, ',', '.') }}đ qua {{ $res->payment_method == 'BANK' ? 'chuyển khoản' : 'tại quầy' }}?')"
            >
                <i class="fas fa-coins"></i> Xác nhận cọc {{ number_format($res->deposit_amount ?? 0, 0, ',', '.') }}đ
            </button>
        @else
            <span class="text-muted small">Chờ khách xác nhận chuyển khoản</span>
        @endif

    {{-- 1. Duyệt đơn --}}
    @elseif($res->status == 'pending')
        <button type="submit" name="status" value="confirmed" class="btn btn-info text-white">Duyệt đơn</button>
    
    {{-- 2. Khách vào --}}
    @elseif($res->status == 'confirmed')
        <button type="submit" name="status" value="arrived" class="btn btn-primary">Khách vào bàn</button>
    
    {{-- 3. Báo bếp --}}
    @elseif($res->status == 'arrived')
        <button type="submit" name="status" value="serving" class="btn btn-warning">Báo bếp nấu</button>
    
    {{-- 4. Bếp nấu xong (ready) --}}
    @elseif($res->status == 'ready')
        <button type="submit" name="status" value="served" class="btn btn-primary">
            <i class="fas fa-check"></i> Đã bưng món
        </button>

    {{-- 5. Thanh toán --}}
@elseif($res->status == 'served')
    <a href="{{ route('staff.reservations.checkout', $res->id) }}" class="btn btn-success">💰 Thanh toán</a>
@endif

    {{-- 6. Dọn bàn khi đã thanh toán xong --}}
    @if(in_array($res->status, \App\Models\Reservation::PAID_STATUSES, true))
        <button type="submit" name="status" value="completed" class="btn btn-secondary">🏁 Xong & Dọn bàn</button>
    @endif
</div>
@if(in_array($res->status, ['pending_deposit', 'pending', 'confirmed'], true))
    <div class="mt-2">
        <button type="submit" name="status" value="cancelled" class="btn btn-sm btn-link text-danger"
                onclick="return confirm('Hủy đơn này?')">Hủy đơn</button>
    </div>
@endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-5 text-muted">📭 Hiện không có yêu cầu đặt bàn nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@if(session('auto_print'))
<script>window.addEventListener('load', () => window.print());</script>
@endif
@endsection