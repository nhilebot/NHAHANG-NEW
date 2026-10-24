@extends('layouts.admin')

@section('admin_content')
<style>
    .reservation-page {
        font-family: 'Be Vietnam Pro', sans-serif;
        color: #1a2228;
    }

    .reservation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .reservation-header h4 {
        font-weight: 700;
        margin: 0;
        color: #1a2228;
    }

    .reservation-subtitle {
        color: #718096;
        font-size: 13px;
        margin-top: 5px;
    }

    .reservation-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .04);
        overflow: hidden;
    }

    .reservation-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .reservation-card-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }

    .reservation-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .reservation-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 15px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .reservation-table tbody td {
        padding: 18px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #edf2f7;
        font-size: 13px;
    }

    .reservation-table tbody tr:last-child td {
        border-bottom: none;
    }

    .reservation-table tbody tr:hover {
        background: #fafcff;
    }

    .customer-name {
        font-weight: 700;
        font-size: 14px;
        color: #1a2228;
        margin-bottom: 5px;
    }

    .reservation-muted {
        color: #718096;
        font-size: 12px;
        margin-top: 5px;
    }

    .table-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 7px 10px;
        border-radius: 8px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 11px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .st-pending   { background: #fff4e5; color: #92400e; }
    .st-confirmed { background: #e7f6f8; color: #087990; }
    .st-arrived   { background: #e7f0ff; color: #1d4ed8; }
    .st-serving   { background: #fff9db; color: #856404; }
    .st-served    { background: #e0f7fa; color: #006064; }
    .st-success   { background: #e9f7ef; color: #198754; }
    .st-danger    { background: #fdf2f2; color: #dc3545; }
    .st-neutral   { background: #edf2f7; color: #475569; }

    /* ===== Nhóm nút thao tác ===== */
    .reservation-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
        min-width: 180px;
        max-width: 200px;
        margin: 0 auto;
    }

    .reservation-actions .btn,
    .cancellation-review-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        height: 36px;
        margin: 0;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
        transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
    }

    .reservation-actions .btn:hover,
    .cancellation-review-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
        filter: brightness(1.04);
    }

    .reservation-actions .btn i {
        font-size: 12px;
        flex-shrink: 0;
    }

    /* 2 nút thanh toán cạnh nhau */
    .reservation-pay-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .reservation-pay-group .btn {
        padding: 0 6px;
        font-size: 12px;
    }

    /* Nút phụ: Hủy đặt bàn */
    .reservation-actions .btn-cancel-soft {
        height: 32px;
        background: #fff;
        color: #dc3545;
        border: 1px solid #fecaca;
        font-size: 12px;
        font-weight: 600;
        box-shadow: none;
    }

    .reservation-actions .btn-cancel-soft:hover {
        background: #fef2f2;
        border-color: #f87171;
        color: #b91c1c;
        box-shadow: none;
    }

    .reservation-actions .reservation-muted {
        margin: 0;
        padding: 8px 10px;
        text-align: center;
        background: #f8fafc;
        border-radius: 8px;
    }

    /* Chỉnh sửa đơn */
    .btn-reservation-edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        margin-top: 9px;
        padding: 6px 9px;
        font-size: 11px;
        border: 1px solid #cbd5e1;
        color: #2563eb;
        border-radius: 7px;
        text-decoration: none;
        background: #fff;
    }

    .btn-reservation-edit:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    /* ===== Thẻ yêu cầu hủy của khách ===== */
    .cancellation-review-box {
        width: 100%;
        max-width: 200px;
        margin: 10px auto 0;
        padding: 10px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-left: 3px solid #f97316;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .cancellation-review-title {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #c2410c;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .cancellation-review-reason {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 8px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cancellation-review-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
    }

    .cancellation-review-actions .btn {
        height: 30px;
        padding: 0 6px;
        font-size: 12px;
    }

    .cancellation-result {
        max-width: 200px;
        margin: 10px auto 0;
        padding: 7px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
    }

    .cancellation-result.approved {
        color: #166534;
        background: #f0fdf4;
    }

    .cancellation-result.rejected {
        color: #475569;
        background: #f1f5f9;
    }

    .empty-reservations {
        text-align: center;
        padding: 45px 20px !important;
        color: #718096;
    }

    .empty-reservations .empty-icon {
        display: block;
        font-size: 34px;
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        .reservation-header {
            align-items: flex-start;
        }

        .reservation-header .btn {
            width: 100%;
        }

        .reservation-card-header {
            padding: 15px;
        }

        .reservation-table {
            min-width: 1050px;
        }
    }
</style>

@php
    $role = auth()->user()->isAdmin() ? 'admin' : 'staff';

    $statusClasses = [
        'pending_deposit' => 'st-pending',
        'pending'         => 'st-pending',
        'confirmed'       => 'st-confirmed',
        'arrived'         => 'st-arrived',
        'preparing'       => 'st-serving',
        'serving'         => 'st-serving',
        'ready'           => 'st-served',
        'served'          => 'st-served',
        'completed'       => 'st-success',
        'cancelled'       => 'st-danger',
    ];
@endphp

<div class="reservation-page">

    <div class="reservation-header">
        <div>
            <h4>🗓️ Quản lý Đặt bàn & Phục vụ</h4>
            <div class="reservation-subtitle">
                Theo dõi đặt bàn, gọi món, thanh toán và tình trạng phục vụ khách hàng.
            </div>
        </div>

        <a href="{{ route('admin.reservations.create') }}"
           class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Đặt bàn tại chỗ
        </a>
    </div>

    <div class="reservation-card">

        <div class="reservation-card-header">
            <h5>
                <i class="fas fa-clipboard-list text-primary"></i>
                Danh sách đặt bàn & phục vụ
            </h5>

            <span class="reservation-muted">
                Tổng số: {{ $reservations->count() }} đơn
            </span>
        </div>

        <div class="table-responsive">
            <table class="reservation-table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Thông tin bàn</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác nghiệp vụ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($reservations as $res)

                        @php
                            $status = $res->status;

                            $isPaid = in_array(
                                $status,
                                \App\Models\Reservation::PAID_STATUSES,
                                true
                            );

                            $statusClass = $statusClasses[$status] ?? 'st-neutral';
                        @endphp

                        <tr>
                            {{-- THÔNG TIN KHÁCH HÀNG --}}
                            <td>
                                <div class="customer-name">
                                    {{ $res->full_name }}
                                </div>

                                <div>
                                    <i class="fas fa-phone text-muted"></i>
                                    {{ $res->phone }}
                                </div>

                                @if($res->email)
                                    <div class="reservation-muted">
                                        <i class="fas fa-envelope"></i>
                                        {{ $res->email }}
                                    </div>
                                @endif

                                <div class="reservation-muted">
                                    Mã đặt bàn: #{{ $res->id }}
                                </div>
                            </td>

                            {{-- THÔNG TIN BÀN --}}
                            <td>
                                <div class="table-label">
                                    <i class="fas fa-chair"></i>
                                    {{ $res->table->name ?? 'Chưa xếp bàn' }}
                                </div>

                                @if($res->table)
                                    <div class="reservation-muted">
                                        {{ $res->table->capacity }} ghế
                                    </div>
                                @endif

                                @if(
                                    !in_array($status, ['completed', 'cancelled'], true)
                                    && !$isPaid
                                )
                                    <a href="{{ route('staff.reservations.edit_items', $res->id) }}"
                                       class="btn-reservation-edit">
                                        <i class="fas fa-pen-to-square"></i>
                                        Chỉnh sửa đơn
                                    </a>
                                @endif
                            </td>

                            {{-- THỜI GIAN --}}
                            <td>
                                <div>
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    {{ $res->reservation_date ?? '—' }}
                                </div>

                                <div class="reservation-muted">
                                    <i class="far fa-clock"></i>
                                    {{ $res->reservation_time ?? '—' }}
                                </div>
                            </td>

                            {{-- TRẠNG THÁI --}}
                            <td>
                                <span class="badge-status {{ $statusClass }}">
                                    {{ \App\Helpers\StatusHelper::getTextWithEmoji($status) }}
                                </span>

                                @if($isPaid)
                                    <div class="reservation-muted">
                                        <i class="fas fa-check-circle text-success"></i>
                                        Đã thanh toán
                                    </div>
                                @endif

                                @if(($res->payment_status ?? null) === 'deposit_submitted')
                                    <div class="reservation-muted">
                                        <i class="fas fa-clock text-warning"></i>
                                        Khách đã gửi thông tin cọc
                                    </div>
                                @endif
                            </td>

                            {{-- THAO TÁC NGHIỆP VỤ --}}
                            <td>
                                <form action="{{ route($role . '.reservations.updateStatus', $res->id) }}"
                                      method="POST">
                                    @csrf

                                    <div class="reservation-actions">

                                        {{-- 1. CHỜ XÁC NHẬN CỌC --}}
                                        @if($status === 'pending_deposit')

                                            @if($res->payment_status === 'deposit_submitted')
                                                <button type="submit"
                                                        name="status"
                                                        value="pending"
                                                        class="btn btn-warning"
                                                        onclick="return confirm('Xác nhận đã nhận tiền cọc {{ number_format($res->deposit_amount ?? 0, 0, ',', '.') }}đ?')">
                                                    <i class="fas fa-check"></i>
                                                    Xác nhận cọc
                                                </button>
                                            @else
                                                <div class="reservation-muted">
                                                    <i class="fas fa-clock"></i>
                                                    Chờ khách cọc
                                                </div>
                                            @endif

                                        {{-- 2. DUYỆT ĐẶT BÀN --}}
                                        @elseif($status === 'pending')

                                            <button type="submit"
                                                    name="status"
                                                    value="confirmed"
                                                    class="btn btn-info text-white">
                                                <i class="fas fa-check-circle"></i>
                                                Duyệt đặt bàn
                                            </button>

                                        {{-- 3. KHÁCH NHẬN BÀN --}}
                                        @elseif($status === 'confirmed')

                                            <button type="submit"
                                                    name="status"
                                                    value="arrived"
                                                    class="btn btn-primary">
                                                <i class="fas fa-chair"></i>
                                                Khách nhận bàn
                                            </button>

                                        {{-- 4. BÁO BẾP NẤU --}}
                                        @elseif($status === 'arrived')

                                            <button type="submit"
                                                    name="status"
                                                    value="serving"
                                                    class="btn btn-warning">
                                                <i class="fas fa-fire"></i>
                                                Báo bếp nấu
                                            </button>

                                        {{-- 5. XÁC NHẬN LÊN MÓN --}}
                                        @elseif(in_array($status, ['serving', 'ready'], true))

                                            <button type="submit"
                                                    name="status"
                                                    value="served"
                                                    class="btn btn-info text-white">
                                                <i class="fas fa-concierge-bell"></i>
                                                Xác nhận lên món
                                            </button>

                                        {{-- 6. THANH TOÁN --}}
                                        @elseif($status === 'served')

                                            @php
                                                $payUrl = route($role . '.reservations.pay', $res->id);
                                            @endphp

                                            <div class="reservation-pay-group">
                                                <button type="submit"
                                                        name="payment_method"
                                                        value="cash"
                                                        formaction="{{ $payUrl }}"
                                                        class="btn btn-success"
                                                        onclick="return confirm('Xác nhận thanh toán bằng tiền mặt?')">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                    Tiền mặt
                                                </button>

                                                <button type="submit"
                                                        name="payment_method"
                                                        value="transfer"
                                                        formaction="{{ $payUrl }}"
                                                        class="btn btn-dark"
                                                        onclick="return confirm('Xác nhận thanh toán chuyển khoản?')">
                                                    <i class="fas fa-credit-card"></i>
                                                    CK
                                                </button>
                                            </div>

                                        @endif

                                        {{-- 7. HOÀN TẤT VÀ DỌN BÀN --}}
                                        @if($isPaid && !in_array($status, ['completed', 'cancelled'], true))
                                            <button type="submit"
                                                    name="status"
                                                    value="completed"
                                                    class="btn btn-secondary"
                                                    onclick="return confirm('Xác nhận hoàn tất và chuyển bàn sang trạng thái dọn dẹp?')">
                                                <i class="fas fa-broom"></i>
                                                Dọn bàn & hoàn tất
                                            </button>
                                        @endif

                                        {{-- 8. NÚT PHỤ: HỦY ĐẶT BÀN BỞI NHÂN VIÊN --}}
                                        @if(in_array($status, [
                                            'pending_deposit',
                                            'pending',
                                            'confirmed',
                                            'arrived'
                                        ], true))
                                            <button type="submit"
                                                    name="status"
                                                    value="cancelled"
                                                    class="btn btn-cancel-soft"
                                                    onclick="return confirm('Bạn có chắc muốn hủy đơn đặt bàn này?')">
                                                <i class="fas fa-times-circle"></i>
                                                Hủy đặt bàn
                                            </button>
                                        @endif

                                    </div>
                                </form>

                                {{-- YÊU CẦU HỦY DO KHÁCH GỬI --}}
                                @if(($res->cancellation_status ?? null) === 'pending')

                                    <div class="cancellation-review-box">
                                        <div class="cancellation-review-title">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Khách xin hủy
                                        </div>

                                        <div class="cancellation-review-reason"
                                             title="{{ $res->cancellation_reason ?: 'Không cung cấp' }}">
                                            <strong>Lý do:</strong>
                                            {{ $res->cancellation_reason ?: 'Không cung cấp' }}
                                        </div>

                                        <form action="{{ route($role . '.reservations.reviewCancellation', $res->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Bạn có chắc muốn xử lý yêu cầu hủy này?')">
                                            @csrf

                                            <div class="cancellation-review-actions">
                                                <button type="submit"
                                                        name="decision"
                                                        value="approve"
                                                        class="btn btn-success">
                                                    <i class="fas fa-check"></i>
                                                    Duyệt hủy
                                                </button>

                                                <button type="submit"
                                                        name="decision"
                                                        value="reject"
                                                        class="btn btn-danger">
                                                    <i class="fas fa-times"></i>
                                                    Từ chối
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                @elseif(($res->cancellation_status ?? null) === 'approved')

                                    <div class="cancellation-result approved">
                                        <i class="fas fa-check-circle"></i>
                                        Đã duyệt yêu cầu hủy
                                    </div>

                                @elseif(($res->cancellation_status ?? null) === 'rejected')

                                    <div class="cancellation-result rejected">
                                        <i class="fas fa-times-circle"></i>
                                        Đã từ chối yêu cầu hủy
                                    </div>

                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="empty-reservations">
                                <span class="empty-icon">📅</span>
                                <strong>Chưa có đơn đặt bàn nào</strong>

                                <div class="reservation-muted">
                                    Các yêu cầu đặt bàn sẽ hiển thị tại đây.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($reservations, 'links'))
            <div class="p-3">
                {{ $reservations->links() }}
            </div>
        @endif

    </div>
</div>
@endsection