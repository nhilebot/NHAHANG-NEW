@extends('layouts.admin')
@section('title', 'Quản lý Đặt bàn - Staff')

@section('admin_content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@push('styles')
<link rel="stylesheet" href="{{ asset('css/staff/reservations.css') }}?v=1">
@endpush

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