@extends('layouts.admin')
@section('topbar_title', 'Quản lý nhân viên')

@section('admin_content')
@php
    // Cấu hình ca lấy từ config/shifts.php (nhãn ngắn, giờ, màu). Đổi giờ ở đó là trang này tự cập nhật.
    $shiftCfg = \App\Support\ShiftSchedule::viewConfigs();
@endphp
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/employees-index.css') }}?v=1">
@endpush

<div class="container-fluid" style="max-width:1200px;margin:0 auto;">

    {{-- TABS --}}
    <div class="emp-tabs">
        <a href="{{ route('admin.employees.index', ['role'=>'staff']) }}"
           class="emp-tab {{ $role === 'staff' ? 'active' : '' }}">🧑‍💼 Nhân viên phục vụ</a>
        <a href="{{ route('admin.employees.index', ['role'=>'chef']) }}"
           class="emp-tab {{ $role === 'chef' ? 'active' : '' }}">👨‍🍳 Đầu bếp</a>
    </div>

    <div class="emp-layout">

        {{-- ══ BẢNG TRÁI ══ --}}
        <div>
            <div class="emp-card">
                <div class="emp-card-header">
                    <span class="emp-card-title">
                        {{ $role === 'staff' ? '🧑‍💼 Nhân viên phục vụ' : '👨‍🍳 Đầu bếp' }}
                        <span style="font-size:.8rem;color:#94a3b8;font-weight:500;margin-left:6px;">
                            ({{ $employees->count() }} người)
                        </span>
                    </span>
                </div>

                @if($employees->isEmpty())
                <div style="text-align:center;padding:48px 20px;color:#94a3b8;">
                    <div style="font-size:2.5rem;margin-bottom:10px;">👤</div>
                    Chưa có nhân viên nào. Thêm mới ở bên phải.
                </div>
                @else
                <div style="overflow-x:auto;">
                    <table class="emp-table">
                        <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Liên hệ</th>
                                <th>Lịch ca làm việc</th>
                                <th class="text-center">Xóa</th>
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
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div class="avatar-circle">
                                            {{ mb_strtoupper(mb_substr($emp->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;color:#0f172a;font-size:.88rem;">{{ $emp->name }}</div>

{{-- SỬ DỤNG TRỰC TIẾP BIẾN $role CỦA TAB, KHÔNG CẦN GỌI DATABASE --}}
@php
    $roleLabel = ($role === 'staff') ? 'Nhân viên phục vụ' : 'Đầu bếp';
@endphp

<span class="rbadge rbadge-{{ $role }}">
    {{ $roleLabel }}
</span>
                                    </div>
                                </td>

                                {{-- Liên hệ --}}
                                <td>
                                    <div style="font-size:.82rem;color:#374151;">{{ $emp->email }}</div>
                                    <div style="font-size:.78rem;color:#94a3b8;">{{ $emp->phone ?? '—' }}</div>
                                </td>

                                {{-- Lịch ca --}}
                                <td style="min-width:260px;">
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

                                {{-- Xóa --}}
                                <td class="text-center">
                                    <form action="{{ route('admin.employees.destroy', $emp->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Xóa nhân viên {{ $emp->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-del" title="Xóa">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- ══ FORM PHẢI ══ --}}
        <div class="add-card">
            <div class="add-title">➕ Thêm nhân viên mới</div>

            <form action="{{ route('admin.employees.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Họ và tên *</label>
                    <input type="text" name="name" class="form-control"
                           placeholder="Nguyễn Văn A" value="{{ old('name') }}" required>
                    @error('name') <div style="color:#ef4444;font-size:.78rem;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="nhanvien@nhahang.vn" value="{{ old('email') }}" required>
                    @error('email') <div style="color:#ef4444;font-size:.78rem;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control"
                           placeholder="09xx xxx xxx" value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu *</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Tối thiểu 6 ký tự" required>
                    @error('password') <div style="color:#ef4444;font-size:.78rem;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Vai trò *</label>
                    <select name="role_id" class="form-control form-select" required>
                        <option value="">-- Chọn vai trò --</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->id }}"
                            {{ old('role_id') == $r->id || $role === $r->name ? 'selected' : '' }}>
                            {{ $r->name === 'staff' ? '🧑‍💼 Nhân viên phục vụ' : '👨‍🍳 Đầu bếp' }}
                        </option>
                        @endforeach
                    </select>
                    @error('role_id') <div style="color:#ef4444;font-size:.78rem;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn-add">✅ Thêm nhân viên</button>
            </form>

            {{-- Chú thích ca --}}
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;font-weight:700;color:#64748b;margin-bottom:8px;">
                    Ghi chú lịch ca:
                </div>
                <div style="display:flex;flex-direction:column;gap:5px;">
                    {{-- Chú thích 3 ca lấy từ config/shifts.php --}}
                    @foreach(collect($shiftCfg)->except('off') as $key => $cfg)
                    <span style="font-size:.78rem;"><span style="display:inline-block;min-width:40px;text-align:center;background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};border-radius:4px;padding:1px 4px;font-weight:600;">{{ $cfg['short'] }}</span> {{ $cfg['time'] }}</span>
                    @endforeach
                    <span style="font-size:.78rem;"><span style="display:inline-block;width:40px;text-align:center;background:#f1f5f9;color:#94a3b8;border-radius:4px;padding:1px 4px;font-weight:600;">Nghỉ</span> Không làm</span>
                </div>
                <div style="margin-top:10px;font-size:.75rem;color:#94a3b8;">
                    💡 Nhấn vào ô ca để chuyển: Sáng → Chiều → Full → Nghỉ
                </div>
            </div>
        </div>

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