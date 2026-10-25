@extends('layouts.admin')
@section('title', 'Dashboard Staff')
@section('page_title', 'Dashboard')
@section('topbar_title', 'Dashboard phục vụ')

@section('admin_content')
@php
    $tableLabel = ['empty' => 'Trống', 'reserved' => 'Đã đặt', 'occupied' => 'Có khách', 'cleaning' => 'Đang dọn'];
    $statusText = [
        'paid_cash' => 'Tiền mặt', 'paid_transfer' => 'Chuyển khoản', 'completed' => 'Hoàn tất',
    ];
@endphp

<div class="sd-wrap">

    @if(session('success'))<div class="sd-alert ok">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="sd-alert err">{{ session('error') }}</div>@endif

    {{-- LỜI CHÀO --}}
    <div class="sd-hello">
        <div>
            <h2>Xin chào, {{ auth()->user()->name }} 👋</h2>
            <p>{{ now()->format('d/m/Y') }} ·
                @if($totalTasks > 0) Có <b>{{ $totalTasks }}</b> việc đang chờ bạn xử lý
                @else Hiện không có việc nào đang chờ ✅ @endif
            </p>
        </div>
        <div class="sd-hello-btns">
            <a href="{{ route('staff.reservations.create') }}" class="sd-btn gold">+ Mở bàn / Tạo đặt bàn</a>
            <a href="{{ route('staff.reservations.index') }}" class="sd-btn light">Danh sách phục vụ</a>
        </div>
    </div>

    {{-- THỐNG KÊ --}}
    <div class="sd-stats">
        <div class="sd-stat"><span class="dot" style="background:#10b981"></span><p>Bàn trống</p><h3>{{ $tableStats['empty'] }}</h3></div>
        <div class="sd-stat"><span class="dot" style="background:#ef4444"></span><p>Bàn có khách</p><h3>{{ $tableStats['occupied'] }}</h3></div>
        <div class="sd-stat"><span class="dot" style="background:#f59e0b"></span><p>Bàn đã đặt</p><h3>{{ $tableStats['reserved'] }}</h3></div>
        <div class="sd-stat"><span class="dot" style="background:#8b5cf6"></span><p>Bếp đang nấu</p><h3>{{ $cooking }}</h3></div>
        <div class="sd-stat money"><p>Thu tại quầy hôm nay</p><h3>{{ number_format($revenueToday, 0, ',', '.') }}đ</h3><small>{{ $billsToday }} hóa đơn</small></div>
    </div>

    <div class="sd-main">

        {{-- ========== CỘT TRÁI ========== --}}
        <div class="sd-side">

            {{-- VIỆC CẦN LÀM --}}
            <div class="sd-card">
                <h3>⚡ Việc cần làm</h3>

                @php $hasTask = false; @endphp
                @foreach($groups as $g)
                    @continue($g['items']->isEmpty())
                    @php $hasTask = true; @endphp

                    <div class="sd-group">
                        <div class="sd-group-title" style="border-color:{{ $g['color'] }}">
                            <span>{{ $g['icon'] }} {{ $g['title'] }}</span>
                            <b style="background:{{ $g['color'] }}">{{ $g['items']->count() }}</b>
                        </div>

                        @foreach($g['items'] as $r)
                            <div class="sd-task">
                                <div class="sd-task-info">
                                    <strong>{{ $r->table->name ?? '---' }}</strong>
                                    <span>{{ $r->full_name }}
                                        @if(in_array($r->status, ['pending', 'confirmed']))
                                            · {{ optional($r->reservation_date)->format('d/m') }} {{ substr($r->reservation_time, 0, 5) }}
                                        @endif
                                        @if($r->status === 'pending_deposit')
                                            · cọc {{ number_format($r->deposit_amount ?? 0, 0, ',', '.') }}đ
                                        @endif
                                        @if($r->status === 'served')
                                            · cần thu {{ number_format(max(0, (int)$r->total_price - (int)($r->deposit_amount ?? 0)), 0, ',', '.') }}đ
                                        @endif
                                    </span>
                                </div>

                                @if($g['action']['type'] === 'link')
                                    <a href="{{ route($g['action']['route'], $r->id) }}" class="sd-act" style="background:{{ $g['color'] }}">
                                        {{ $g['action']['label'] }}
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('staff.reservations.updateStatus', $r->id) }}">
                                        @csrf
                                        <button type="submit" name="status" value="{{ $g['action']['value'] }}"
                                                class="sd-act" style="background:{{ $g['color'] }}"
                                                @if(!empty($g['action']['confirm'])) onclick="return confirm('{{ $g['action']['confirm'] }}')" @endif>
                                            {{ $g['action']['label'] }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach

                @unless($hasTask)
                    <div class="sd-empty">🎉 Không có việc nào đang chờ.</div>
                @endunless
            </div>

            {{-- HÓA ĐƠN GẦN ĐÂY --}}
            <div class="sd-card">
                <h3>🧾 Hóa đơn gần đây</h3>
                @forelse($recentBills as $b)
                    <a href="{{ route('staff.bills.show', $b->id) }}" class="sd-bill">
                        <div>
                            <strong>#{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }} · {{ $b->table->name ?? '---' }}</strong>
                            <small>{{ $statusText[$b->status] ?? $b->status }} · {{ optional($b->paid_at)->format('H:i d/m') }}</small>
                        </div>
                        <b>{{ number_format($b->paid_amount ?? 0, 0, ',', '.') }}đ</b>
                    </a>
                @empty
                    <p class="sd-muted">Chưa có hóa đơn nào.</p>
                @endforelse
                <a href="{{ route('staff.bills.index') }}" class="sd-more">Xem tất cả hóa đơn →</a>
            </div>
        </div>

        {{-- ========== CỘT PHẢI ========== --}}
        <div class="sd-side">

            {{-- SƠ ĐỒ BÀN --}}
            <div class="sd-card">
                <h3>🪑 Sơ đồ bàn</h3>
                <div class="sd-legend">
                    @foreach($tableLabel as $k => $v)
                        <span><i class="tb-{{ $k }}"></i>{{ $v }}</span>
                    @endforeach
                </div>
                <div class="sd-tables">
                    @forelse($tables as $t)
                        <div class="sd-table tb-{{ $t->status }}" title="{{ $tableLabel[$t->status] ?? $t->status }}">
                            <b>{{ $t->name }}</b>
                            <small>{{ $t->capacity }} ghế</small>
                        </div>
                    @empty
                        <p class="sd-muted">Chưa có bàn nào.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/staff/dashboard.css') }}?v=1">
@endpush

<script>
    setTimeout(() => location.reload(), 30000);
</script>
@endsection