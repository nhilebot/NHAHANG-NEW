@extends('layouts.admin')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/reservations-index.css') }}?v=1">
@endpush

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