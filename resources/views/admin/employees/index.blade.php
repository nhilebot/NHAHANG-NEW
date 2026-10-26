@extends('layouts.admin')
@section('page_title', 'Nhân sự & lịch ca')
@section('topbar_title', 'Nhân sự & lịch ca')

@section('admin_content')
@php
    // Cấu hình ca lấy từ config/shifts.php (nhãn ngắn, giờ, màu). Đổi giờ ở đó là trang này tự cập nhật.
    $shiftCfg = \App\Support\ShiftSchedule::viewConfigs();
@endphp
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/employees-index.css') }}?v=2">
@endpush

<div class="ui-page emp-page">

    {{-- TIÊU ĐỀ + NÚT THÊM (thêm / sửa / xóa tài khoản làm tập trung ở trang Tài khoản) --}}
    <div class="ui-head">
        <div>
            <h2 class="ui-title">🗓️ Nhân sự &amp; lịch ca</h2>
            <p class="ui-subtitle">Xếp ca làm việc trong tuần. Thêm, sửa, xóa tài khoản ở mục <a href="{{ route('admin.users.index') }}">Tất cả tài khoản</a>.</p>
        </div>
        <a href="{{ route('admin.users.create', ['role' => $role]) }}" class="btn btn-primary">＋ Thêm nhân viên</a>
    </div>

    {{-- TABS --}}
    <div class="ui-tabs">
        <a href="{{ route('admin.employees.index', ['role'=>'staff']) }}"
           class="ui-tab {{ $role === 'staff' ? 'active' : '' }}">🧑‍💼 Nhân viên phục vụ</a>
        <a href="{{ route('admin.employees.index', ['role'=>'chef']) }}"
           class="ui-tab {{ $role === 'chef' ? 'active' : '' }}">👨‍🍳 Đầu bếp</a>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">
                {{ $role === 'staff' ? '🧑‍💼 Nhân viên phục vụ' : '👨‍🍳 Đầu bếp' }}
                <span class="ui-count">({{ $employees->count() }} người)</span>
            </span>
        </div>

        @if($employees->isEmpty())
        <div class="empty-box">
            <div class="empty-icon">👤</div>
            Chưa có nhân viên nào. Bấm “＋ Thêm nhân viên” để tạo mới.
        </div>
        @else
        <div class="table-wrap">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Liên hệ</th>
                        <th>Lịch ca làm việc</th>
                        <th class="col-actions">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                    @php
                        $days = ['T2','T3','T4','T5','T6','T7','CN'];
                        $userShifts = $emp->shifts ?? [];
                    @endphp
                    <tr>
                        {{-- Avatar + Tên --}}
                        <td class="cell-user" data-label="Nhân viên">
                            <div class="ui-user">
                                <div class="ui-avatar">
                                    {{ mb_strtoupper(mb_substr($emp->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="ui-user-name">{{ $emp->name }}</div>

{{-- SỬ DỤNG TRỰC TIẾP BIẾN $role CỦA TAB, KHÔNG CẦN GỌI DATABASE --}}
@php
    $roleLabel = ($role === 'staff') ? 'Nhân viên phục vụ' : 'Đầu bếp';
@endphp

                                    <span class="role-badge role-{{ $role }}">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Liên hệ --}}
                        <td class="cell-text" data-label="Liên hệ">
                            <div>
                                <div>{{ $emp->email }}</div>
                                <span class="cell-sub">{{ $emp->phone ?? '—' }}</span>
                            </div>
                        </td>

                        {{-- Lịch ca --}}
                        <td class="cell-shifts" data-label="Lịch ca làm việc">
                            <div class="shift-grid">
                                @foreach($days as $i => $day)
                                @php
                                    $current = $userShifts[$day] ?? 'off';
                                    $labels = collect($shiftCfg)->map(fn($c) => $c['short'])->all(); // nhãn ngắn trên nút ca
                                @endphp
                                <div class="shift-cell">
                                    <div class="shift-day">{{ $day }}</div>
                                    <button class="shift-pill shift-{{ $current }}"
                                            data-uid="{{ $emp->id }}"
                                            data-day="{{ $day }}"
                                            data-current="{{ $current }}"
                                            onclick="cycleShift(this)">
                                        {{ $labels[$current] }}
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </td>

                        {{-- Sửa tài khoản (xóa / đổi vai trò / đặt lại mật khẩu làm ở trang Tài khoản) --}}
                        <td class="cell-actions" data-label="Thao tác">
                            <div class="row-actions">
                                <a href="{{ route('admin.users.edit', $emp->id) }}" class="btn btn-ghost btn-sm">✏️ Sửa tài khoản</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Chú thích ca --}}
        <div class="shift-legend">
            <span class="shift-legend-title">Ghi chú lịch ca:</span>
            {{-- Chú thích 3 ca lấy từ config/shifts.php (màu theo từng ca nên giữ inline) --}}
            @foreach(collect($shiftCfg)->except('off') as $key => $cfg)
            <span class="shift-legend-item"><span class="shift-chip" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">{{ $cfg['short'] }}</span> {{ $cfg['time'] }}</span>
            @endforeach
            <span class="shift-legend-item"><span class="shift-chip shift-chip--off">Nghỉ</span> Không làm</span>
            <span class="shift-legend-hint">💡 Nhấn vào ô ca để chuyển: Sáng → Chiều → Full → Nghỉ</span>
        </div>
        @endif
    </div>

</div>

<script>
const shiftCycle = ['morning', 'afternoon', 'full', 'off'];
// Nhãn ngắn của từng ca lấy từ config/shifts.php (gồm cả 'off')
const shiftLabel = @json(collect($shiftCfg)->map(fn($c) => $c['short']));

function cycleShift(btn) {
    const cur   = btn.dataset.current;
    const next  = shiftCycle[(shiftCycle.indexOf(cur) + 1) % shiftCycle.length];
    const uid   = btn.dataset.uid;
    const day   = btn.dataset.day; // Bây giờ day là T2, T3...

    btn.textContent       = '...';
    btn.className         = 'shift-pill shift-off';
    btn.dataset.current   = next;

    fetch('{{ route("admin.employees.shift") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        // Gửi day (ví dụ: "T2") thay vì index 0-6
        body: JSON.stringify({ user_id: uid, day_of_week: day, shift: next })
    })
    .then(r => r.json())
    .then(() => {
        btn.textContent = shiftLabel[next];
        btn.className   = 'shift-pill shift-' + next;
    })
    .catch(() => {
        btn.textContent = shiftLabel[cur];
        btn.className   = 'shift-pill shift-' + cur;
        btn.dataset.current = cur;
    });
}
</script>
@endsection
