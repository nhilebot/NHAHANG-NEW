@extends('layouts.admin')

@section('admin_content')
<style>
    /* CSS Badge Tiếng Việt */
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
    .st-danger { background-color: #fdf2f2; color: #dc3545; }    
</style>

<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">🗓️ Quản lý Đặt bàn & Phục vụ</h5>
<a href="{{ route('admin.reservations.create') }}" class="btn btn-primary btn-sm">+ Đặt bàn tại chỗ</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Khách hàng</th>
                            <th>Thông tin bàn</th>
                            <th>Trạng thái hiện tại</th>
                            <th class="text-center">Thao tác nghiệp vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $res)
                        <tr>
                            <td>
                                <strong>{{ $res->full_name }}</strong>
                                <div class="small text-muted">{{ $res->phone }}</div>
                            </td>
                            <td>
                                 <span class="badge bg-info text-white fs-6">
                                    <div class="table-name"> {{ $res->table->name }} - {{ $res->table->capacity }} Ghế </div>
                                </span>
                                <div class="small mt-1 text-primary fw-bold">{{ $res->reservation_time }}</div>
                                <a href="{{ route('staff.reservations.edit_items', $res->id) }}" class="btn btn-outline-primary btn-sm mt-2">
                                    <i class="fas fa-file-invoice"></i> Chỉnh sửa đơn
                                </a>
                            </td>
                            <td>
                                <span class="badge">{{ \App\Helpers\StatusHelper::getTextWithEmoji($res->status) }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $roleId = auth()->user()->role_id ?? 0;
                                    $prefix = auth()->user()->isAdmin() ? 'admin' : 'staff';
                                @endphp

                                <form action="{{ route($prefix . '.reservations.updateStatus', $res->id) }}" method="POST">
                                    @csrf
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        @if($res->status == 'pending_deposit')
                                            @if($res->payment_status === 'deposit_submitted')
                                                <button type="submit" name="status" value="pending" class="btn btn-warning text-dark"
                                                    onclick="return confirm('Xác nhận đã nhận cọc {{ number_format($res->deposit_amount ?? 0, 0, ',', '.') }}đ?')">
                                                    Xác nhận cọc {{ number_format($res->deposit_amount ?? 0, 0, ',', '.') }}đ
                                                </button>
                                            @else
                                                <span class="text-muted small">Chờ khách xác nhận chuyển khoản</span>
                                            @endif
                                        @elseif($res->status == 'pending')
                                            <button type="submit" name="status" value="confirmed" class="btn btn-info text-white">Duyệt đơn</button>
                                        @elseif($res->status == 'confirmed')
                                            <button type="submit" name="status" value="arrived" class="btn btn-primary">Khách nhận bàn</button>
                                        @elseif($res->status == 'arrived')
                                            <button type="submit" name="status" value="serving" class="btn btn-warning">Báo bếp nấu</button>
                                        @elseif($res->status == 'serving' || $res->status == 'ready')
                                            <button type="submit" name="status" value="served" class="btn btn-info text-white">Đã lên món</button>
                                        @elseif($res->status == 'served')
                                            @php $payUrl = route($prefix . '.reservations.pay', $res->id); @endphp
                                            <button type="submit" name="payment_method" value="cash" formaction="{{ $payUrl }}" class="btn btn-success">💰 Tiền mặt</button>
                                            <button type="submit" name="payment_method" value="transfer" formaction="{{ $payUrl }}" class="btn btn-dark">💳 CK</button>
                                        @endif

                                        @if(in_array($res->status, \App\Models\Reservation::PAID_STATUSES, true))
                                            <button type="submit" name="status" value="completed" class="btn btn-secondary">🏁 Dọn bàn & Xong</button>
                                        @endif
                                    </div>

                                    @if(in_array($res->status, ['pending_deposit', 'pending', 'confirmed', 'arrived'], true))
                                        <div class="mt-2">
                                            <button type="submit" name="status" value="cancelled" class="btn btn-sm btn-link text-danger" onclick="return confirm('Hủy đơn này?')">Hủy đơn</button>
                                        </div>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Hiện không có yêu cầu nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection