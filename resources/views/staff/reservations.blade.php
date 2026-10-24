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

    /* ===== Cột thao tác phục vụ ===== */
    .svc-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
        min-width: 170px;
        max-width: 200px;
        margin: 0 auto;
    }

    .svc-actions .btn,
    .svc-review-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        min-height: 36px;
        margin: 0;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.3;
        white-space: normal;       /* cho nút dài như "Xác nhận cọc 500.000đ" tự xuống dòng */
        text-align: center;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
        transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
    }

    .svc-actions .btn:hover,
    .svc-review-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
        filter: brightness(1.04);
    }

    .svc-actions .btn i { font-size: 12px; flex-shrink: 0; }

    /* Nút phụ: Hủy đơn */
    .svc-actions .btn-cancel-soft {
        min-height: 32px;
        background: #fff;
        color: #dc3545;
        border: 1px solid #fecaca;
        font-size: 12px;
        box-shadow: none;
    }

    .svc-actions .btn-cancel-soft:hover {
        background: #fef2f2;
        border-color: #f87171;
        color: #b91c1c;
        box-shadow: none;
    }

    /* Dòng chữ chú thích (ví dụ: chờ khách chuyển khoản) */
    .svc-note {
        padding: 8px 10px;
        text-align: center;
        font-size: 12px;
        color: #718096;
        background: #f8fafc;
        border-radius: 8px;
    }

    /* Thẻ yêu cầu hủy do khách gửi */
    .svc-review {
        width: 100%;
        max-width: 200px;
        margin: 10px auto 0;
        padding: 10px;
        text-align: left;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-left: 3px solid #f97316;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .svc-review-title {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
        color: #c2410c;
        font-size: 12px;
        font-weight: 700;
    }

    .svc-review-reason {
        margin-bottom: 8px;
        color: #64748b;
        font-size: 11.5px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;     /* lý do dài chỉ hiện 2 dòng */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .svc-review-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
    }

    .svc-review-actions .btn {
        min-height: 30px;
        padding: 4px 6px;
        font-size: 12px;
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
                                        {{-- Ẩn bút sửa khi đã thanh toán hoặc đã hoàn tất --}}
                                        @php
                                            $isPaid = in_array(
                                                $res->status,
                                                \App\Models\Reservation::PAID_STATUSES,
                                                true
                                            );
                                        @endphp

                                        @if(!$isPaid && $res->status !== 'completed')
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

                            {{-- THAO TÁC PHỤC VỤ --}}
                            <td class="text-center">
                                <form action="{{ route('staff.reservations.updateStatus', $res->id) }}" method="POST">
                                    @csrf

                                    <div class="svc-actions">

                                        {{-- 0. Xác nhận cọc (đứng trước "Duyệt đơn") --}}
                                        @if($res->status == 'pending_deposit')
                                            @if($res->payment_status === 'deposit_submitted')
                                                <button type="submit" name="status" value="pending"
                                                        class="btn btn-warning text-dark"
                                                        onclick="return confirm('Xác nhận đã nhận cọc {{ number_format($res->deposit_amount ?? 0, 0, ',', '.') }}đ qua {{ $res->payment_method == 'BANK' ? 'chuyển khoản' : 'tại quầy' }}?')">
                                                    <i class="fas fa-coins"></i>
                                                    Xác nhận cọc {{ number_format($res->deposit_amount ?? 0, 0, ',', '.') }}đ
                                                </button>
                                            @else
                                                <div class="svc-note">
                                                    <i class="fas fa-clock"></i> Chờ khách xác nhận chuyển khoản
                                                </div>
                                            @endif

                                        {{-- 1. Duyệt đơn --}}
                                        @elseif($res->status == 'pending')
                                            <button type="submit" name="status" value="confirmed" class="btn btn-info text-white">
                                                <i class="fas fa-check-circle"></i> Duyệt đơn
                                            </button>

                                        {{-- 2. Khách vào bàn --}}
                                        @elseif($res->status == 'confirmed')
                                            <button type="submit" name="status" value="arrived" class="btn btn-primary">
                                                <i class="fas fa-chair"></i> Khách vào bàn
                                            </button>

                                        {{-- 3. Báo bếp --}}
                                        @elseif($res->status == 'arrived')
                                            <button type="submit" name="status" value="serving" class="btn btn-warning">
                                                <i class="fas fa-fire"></i> Báo bếp nấu
                                            </button>

                                        {{-- 4. Bếp nấu xong (ready) --}}
                                        @elseif($res->status == 'ready')
                                            <button type="submit" name="status" value="served" class="btn btn-primary">
                                                <i class="fas fa-concierge-bell"></i> Đã bưng món
                                            </button>

                                        {{-- 5. Thanh toán --}}
                                        @elseif($res->status == 'served')
                                            <a href="{{ route('staff.reservations.checkout', $res->id) }}" class="btn btn-success">
                                                <i class="fas fa-cash-register"></i> Thanh toán
                                            </a>
                                        @endif

                                        {{-- 6. Dọn bàn khi đã thanh toán xong --}}
                                        @if(in_array($res->status, \App\Models\Reservation::PAID_STATUSES, true))
                                            <button type="submit" name="status" value="completed" class="btn btn-secondary">
                                                <i class="fas fa-broom"></i> Xong & Dọn bàn
                                            </button>
                                        @endif

                                        {{-- 7. Nút phụ: hủy đơn --}}
                                        @if(in_array($res->status, ['pending_deposit', 'pending', 'confirmed'], true))
                                            <button type="submit" name="status" value="cancelled"
                                                    class="btn btn-cancel-soft"
                                                    onclick="return confirm('Hủy đơn này?')">
                                                <i class="fas fa-times-circle"></i> Hủy đơn
                                            </button>
                                        @endif

                                    </div>
                                </form>

                                {{-- YÊU CẦU HỦY DO KHÁCH GỬI --}}
                                @if($res->cancellation_status === 'pending')
                                    <div class="svc-review">
                                        <div class="svc-review-title">
                                            <i class="fas fa-exclamation-circle"></i> Khách xin hủy
                                        </div>

                                        <div class="svc-review-reason" title="{{ $res->cancellation_reason ?: 'Không cung cấp' }}">
                                            Lý do: {{ $res->cancellation_reason ?: 'Không cung cấp' }}
                                        </div>

                                        <form action="{{ route('staff.reservations.reviewCancellation', $res->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Bạn xác nhận xử lý yêu cầu hủy này?')">
                                            @csrf

                                            <div class="svc-review-actions">
                                                <button type="submit" name="decision" value="approve" class="btn btn-danger">
                                                    Duyệt hủy
                                                </button>
                                                <button type="submit" name="decision" value="reject" class="btn btn-secondary">
                                                    Từ chối
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
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