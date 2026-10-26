@extends('layouts.admin')

@section('page_title', 'Quản lý khuyến mãi')
@section('topbar_title', 'Quản lý khuyến mãi')

@section('admin_content')
<style>
    .pm-card { background:#fff; border-radius:12px; padding:28px; box-shadow:0 10px 30px rgba(0,0,0,.05); }
    .pm-head { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px; }
    .pm-filter { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:18px; }
    .pm-filter input, .pm-filter select { padding:9px 12px; border:1px solid #E2E8F0; border-radius:8px; }
    .pm-btn { padding:9px 16px; border-radius:8px; border:none; font-weight:700; cursor:pointer; text-decoration:none; font-size:13px; display:inline-block; }
    .pm-btn-gold { background:#D4AF37; color:#fff; }
    .pm-btn-gray { background:#F1F5F9; color:#334155; }
    .pm-table { width:100%; border-collapse:collapse; }
    .pm-table th { text-align:left; font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:#64748b; padding:10px 8px; border-bottom:2px solid #E2E8F0; }
    .pm-table td { padding:12px 8px; border-bottom:1px solid #F1F5F9; vertical-align:middle; font-size:14px; }
    .pm-code { font-family:monospace; font-weight:800; background:#FFFBEB; color:#92400E; padding:3px 10px; border-radius:6px; }
    .pm-badge { padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap; }
    .pm-active{background:#DCFCE7;color:#166534} .pm-inactive{background:#F1F5F9;color:#64748b}
    .pm-scheduled{background:#DBEAFE;color:#1D4ED8} .pm-expired{background:#FEE2E2;color:#B91C1C} .pm-exhausted{background:#FEF3C7;color:#B45309}
    .pm-actions { display:flex; gap:6px; flex-wrap:wrap; }
    .pm-act { padding:6px 10px; border-radius:6px; font-size:12px; font-weight:700; border:1px solid; cursor:pointer; text-decoration:none; background:#fff; }
    .pm-edit{color:#D97706;border-color:#FDE68A} .pm-toggle{color:#2B6CB0;border-color:#BEE3F8} .pm-del{color:#C53030;border-color:#FED7D7}
    .pm-sub { color:#94a3b8; font-size:12px; }
    .table-scroll { overflow-x:auto; }
</style>

<div class="pm-card">
    <div class="pm-head">
        <h2 style="margin:0;">🎟️ Quản lý khuyến mãi</h2>
        <a href="{{ route('admin.promotions.create') }}" class="pm-btn pm-btn-gold">+ Thêm mã khuyến mãi</a>
    </div>

    <form method="GET" class="pm-filter">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo mã hoặc tên...">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="active"   @selected(request('status') === 'active')>Đang áp dụng</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Đã tắt</option>
            <option value="expired"  @selected(request('status') === 'expired')>Hết hạn</option>
        </select>
        <button class="pm-btn pm-btn-gray" type="submit">Lọc</button>
    </form>

    <div class="table-scroll">
    <table class="pm-table">
        <thead>
            <tr>
                <th>Mã</th><th>Chương trình</th><th>Giảm</th><th>Tối thiểu</th>
                <th>Lượt dùng</th><th>Thời gian</th><th>Trạng thái</th><th></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($promotions as $p)
            <tr>
                <td><span class="pm-code">{{ $p->code }}</span></td>
                <td>{{ $p->name }}</td>
                <td>
                    <strong>{{ $p->discount_text }}</strong>
                </td>
                <td>{{ $p->min_order_amount ? number_format($p->min_order_amount, 0, ',', '.') . 'đ' : '—' }}</td>
                <td>{{ $p->used_count }}{{ $p->usage_limit ? ' / ' . $p->usage_limit : ' / ∞' }}</td>
                <td class="pm-sub">
                    {{ $p->starts_at ? $p->starts_at->format('d/m/Y H:i') : 'Ngay' }}<br>
                    → {{ $p->ends_at ? $p->ends_at->format('d/m/Y H:i') : 'Không giới hạn' }}
                </td>
                <td><span class="pm-badge pm-{{ $p->status }}">{{ $p->status_label }}</span></td>
                <td>
                    <div class="pm-actions">
                        <form action="{{ route('admin.promotions.toggle', $p->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="pm-act pm-toggle" type="submit">{{ $p->is_active ? 'Tắt' : 'Bật' }}</button>
                        </form>
                        <a class="pm-act pm-edit" href="{{ route('admin.promotions.edit', $p->id) }}">Sửa</a>
                        <form action="{{ route('admin.promotions.destroy', $p->id) }}" method="POST"
                              onsubmit="return confirm('Xóa mã {{ $p->code }}?')">
                            @csrf @method('DELETE')
                            <button class="pm-act pm-del" type="submit">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:40px;">Chưa có mã khuyến mãi nào.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>

    <div style="margin-top:18px;">{{ $promotions->links() }}</div>
</div>
@endsection
