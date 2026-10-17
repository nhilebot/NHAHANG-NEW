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

<style>
.sd-wrap{max-width:1250px;margin:auto}
.sd-alert{padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600}
.sd-alert.ok{background:#ecfdf5;color:#065f46}.sd-alert.err{background:#fef2f2;color:#991b1b}

.sd-hello{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;
    background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border-radius:22px;padding:28px 32px;margin-bottom:22px}
.sd-hello h2{margin:0;font-size:26px}
.sd-hello p{margin:6px 0 0;opacity:.8}
.sd-hello b{color:#d4af37}
.sd-hello-btns{display:flex;gap:10px;flex-wrap:wrap}
.sd-btn{padding:11px 18px;border-radius:12px;font-weight:700;text-decoration:none;font-size:14px}
.sd-btn.gold{background:#d4af37;color:#fff}.sd-btn.gold:hover{background:#b89020}
.sd-btn.light{background:rgba(255,255,255,.12);color:#fff}.sd-btn.light:hover{background:rgba(255,255,255,.22)}

.sd-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:22px}
.sd-stat{background:#fff;border-radius:18px;padding:18px 20px;box-shadow:0 10px 30px rgba(0,0,0,.05);position:relative}
.sd-stat p{margin:0;color:#64748b;font-size:13px}
.sd-stat h3{margin:6px 0 0;font-size:30px;color:#1e293b}
.sd-stat small{color:#94a3b8}
.sd-stat .dot{position:absolute;top:18px;right:18px;width:10px;height:10px;border-radius:50%}
.sd-stat.money{background:#fffbeb;border:1px solid #fde68a}
.sd-stat.money h3{font-size:22px;color:#b45309}

.sd-main{display:grid;grid-template-columns:1.2fr 1fr;gap:22px;align-items:start}
.sd-side{display:flex;flex-direction:column;gap:22px}
.sd-card{background:#fff;border-radius:20px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.05)}
.sd-card h3{margin:0 0 16px;font-size:18px;color:#1e293b}

.sd-group{margin-bottom:18px}
.sd-group-title{display:flex;justify-content:space-between;align-items:center;border-left:4px solid;padding:4px 10px;
    margin-bottom:8px;font-weight:700;color:#334155;font-size:14px}
.sd-group-title b{color:#fff;border-radius:999px;padding:1px 10px;font-size:12px}
.sd-task{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:#f8fafc;margin-bottom:6px}
.sd-task-info strong{display:block;color:#1e293b}
.sd-task-info span{color:#64748b;font-size:13px}
.sd-act{border:none;color:#fff;font-weight:700;font-size:13px;padding:8px 14px;border-radius:10px;cursor:pointer;text-decoration:none;white-space:nowrap}
.sd-act:hover{filter:brightness(.92);color:#fff}
.sd-empty{text-align:center;padding:40px 10px;color:#94a3b8}

.sd-legend{display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#64748b;margin-bottom:12px}
.sd-legend i{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:5px}
.sd-tables{display:grid;grid-template-columns:repeat(auto-fill,minmax(88px,1fr));gap:10px}
.sd-table{border-radius:14px;padding:12px 6px;text-align:center;border:2px solid transparent}
.sd-table b{display:block;font-size:14px}.sd-table small{font-size:11px;opacity:.8}
.tb-empty{background:#ecfdf5;color:#065f46;border-color:#a7f3d0}
.tb-occupied{background:#fef2f2;color:#991b1b;border-color:#fecaca}
.tb-reserved{background:#fffbeb;color:#92400e;border-color:#fde68a}
.tb-cleaning{background:#f1f5f9;color:#475569;border-color:#cbd5e1}
.sd-legend .tb-empty{background:#10b981}.sd-legend .tb-occupied{background:#ef4444}
.sd-legend .tb-reserved{background:#f59e0b}.sd-legend .tb-cleaning{background:#94a3b8}

.sd-bill{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f1f5f9;text-decoration:none;color:#1e293b}
.sd-bill small{display:block;color:#94a3b8}
.sd-bill:hover{background:#f8fafc}
.sd-more{display:block;text-align:center;margin-top:12px;color:#b45309;font-weight:700;text-decoration:none;font-size:14px}
.sd-muted{color:#94a3b8;text-align:center;margin:10px 0}

@media(max-width:1100px){.sd-stats{grid-template-columns:repeat(2,1fr)}.sd-main{grid-template-columns:1fr}}
</style>

<script>
    setTimeout(() => location.reload(), 30000);
</script>
@endsection