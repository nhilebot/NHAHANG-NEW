@extends('layouts.admin')

@section('page_title', 'Lịch làm việc của Đầu bếp')
@section('topbar_title', 'Lịch ca làm việc tuần này')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/chef/schedule.css') }}?v=1">
@endpush

<div class="schedule-wrapper">

    {{-- 1. Banner trạng thái ca làm việc hôm nay --}}
    @php
        $todayShiftType = $myShifts[$todayKey] ?? 'off';
        $todayCfg = $shiftConfigs[$todayShiftType] ?? $shiftConfigs['off'];
    @endphp

    <div class="today-banner">
        <div class="today-left">
            <h2>👨‍🍳 Chào Đầu bếp, {{ auth()->user()->name }}!</h2>
            <p>Hôm nay là <strong>{{ $days[$todayKey] }}</strong> ({{ now()->format('d/m/Y') }}). Hãy kiểm tra ca trực để chuẩn bị nguyên liệu và khu vực bếp.</p>
        </div>
        <div class="today-badge-box">
            <span class="today-badge" style="background: {{ $todayCfg['bg'] }}; color: {{ $todayCfg['color'] }};">
                {{ $todayCfg['badge'] }} {{ $todayCfg['label'] }}: {{ $todayCfg['time'] }}
            </span>
        </div>
    </div>

    {{-- 2. Lưới 7 ngày trong tuần --}}
    <div class="week-grid">
        @foreach($days as $code => $label)
            @php
                $shiftType = $myShifts[$code] ?? 'off';
                $cfg = $shiftConfigs[$shiftType] ?? $shiftConfigs['off'];
                $isToday = ($code === $todayKey);
            @endphp
            <div class="day-card {{ $isToday ? 'is-today' : '' }}">
                <div class="day-name">{{ $label }}</div>
                <div class="day-code">{{ $code }}</div>

                <div class="shift-status-pill" style="background: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }};">
                    <span>{{ $cfg['badge'] }}</span>
                    <span>{{ $cfg['label'] }}</span>
                </div>

                <div class="shift-time-text">
                    {{ $cfg['time'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- 3. Quy định giờ ca của Bếp --}}
    <div class="legend-card">
        <div class="legend-title">ℹ️ Khung giờ hoạt động khu vực Bếp</div>
        <div class="legend-grid">
            {{-- Các ca lấy từ config/shifts.php, đổi giờ ở đó là bảng này tự cập nhật --}}
            @php
                // Mô tả công việc của bếp theo từng ca (chỉ là chữ hiển thị, không ảnh hưởng logic)
                $chefNotes = [
                    'morning'   => 'Sơ chế & Phục vụ trưa',
                    'afternoon' => 'Phục vụ tối & Vệ sinh bếp',
                    'full'      => 'Toàn bộ hoạt động bếp',
                ];
            @endphp
            @foreach(collect($shiftConfigs)->except('off') as $key => $cfg)
            <div class="legend-item">
                <span style="font-size:1.4rem;">{{ $cfg['badge'] }}</span>
                <div>
                    <div style="font-weight:700;font-size:0.85rem;color:{{ $cfg['color'] }};">{{ $cfg['label'] }}{{ $key === 'full' ? ' (Full)' : '' }}</div>
                    <div style="font-size:0.75rem;color:#64748b;">{{ $cfg['time'] }}@if(isset($chefNotes[$key])) ({{ $chefNotes[$key] }})@endif</div>
                </div>
            </div>
            @endforeach
            <div class="legend-item">
                <span style="font-size:1.4rem;">☕</span>
                <div>
                    <div style="font-weight:700;font-size:0.85rem;color:#64748b;">Ngày Nghỉ</div>
                    <div style="font-size:0.75rem;color:#94a3b8;">Không phát sinh ca trực</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection