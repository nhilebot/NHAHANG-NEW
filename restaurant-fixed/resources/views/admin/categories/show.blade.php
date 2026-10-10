@extends('layouts.admin')

@section('admin_content')
<section class="panel">
    <div class="panel-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 class="panel-title">📁 Danh mục: <span style="color:#5b3cff;">{{ $category->name }}</span></h3>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.menus.create', ['category_id' => $category->id]) }}"
               class="btn btn-primary btn-sm">＋ Thêm món vào danh mục</a>
            <a href="{{ route('admin.categories.index') }}"
               class="btn btn-ghost btn-sm">← Quay lại</a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:90px;text-align:center;">Hình ảnh</th>
                    <th>Tên món</th>
                    <th>Mô tả</th>
                    <th style="width:120px;">Giá</th>
                    <th style="width:80px;text-align:center;">Kho</th>
                    <th style="width:80px;text-align:center;">Trạng thái</th>
                    <th style="width:160px;text-align:center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $menu)
                <tr>
                    <td style="text-align:center;">
                        @if($menu->image)
                            <img src="{{ asset($menu->image) }}" alt="{{ $menu->name }}"
                                 style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:1px solid #f1f5f9;">
                        @else
                            <div style="width:72px;height:72px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:auto;">🍽️</div>
                        @endif
                    </td>
                    <td><strong>{{ $menu->name }}</strong></td>
                    <td style="color:#64748b;font-size:.85rem;">{{ Str::limit($menu->description, 60) }}</td>
                    <td style="font-weight:700;color:#16a34a;">{{ number_format($menu->price) }}đ</td>
                    <td style="text-align:center;">{{ $menu->stock ?? 0 }}</td>
                    <td style="text-align:center;">
                        @if($menu->status == 1)
                            <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:700;">Đang bán</span>
                        @else
                            <span style="background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:700;">Ngừng bán</span>
                        @endif
                    </td>
                    <td style="text-align:center;white-space:nowrap;">

                        {{-- ✅ Nút Sửa — trỏ đúng route admin.menus.edit --}}
                        <a href="{{ route('admin.menus.edit', $menu->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;border-radius:8px;background:#fffbeb;color:#d97706;font-size:.78rem;font-weight:700;text-decoration:none;border:1px solid #fde68a;transition:background .15s;"
                           onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">
                            ✏️ Sửa
                        </a>

                        {{-- ✅ Nút Xóa — dùng form POST + @method('DELETE') --}}
                        <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST"
                              style="display:inline-block;margin-left:6px;"
                              onsubmit="return confirm('Xóa món {{ $menu->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;border-radius:8px;background:#fff5f5;color:#c53030;font-size:.78rem;font-weight:700;border:1px solid #fed7d7;cursor:pointer;transition:background .15s;"
                                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fff5f5'">
                                🗑️ Xóa
                            </button>
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:#94a3b8;">
                        <div style="font-size:2.5rem;margin-bottom:8px;">🍽️</div>
                        Chưa có món nào trong danh mục này.<br>
                        <a href="{{ route('admin.menus.create', ['category_id' => $category->id]) }}"
                           style="color:#5b3cff;font-weight:600;font-size:.85rem;">＋ Thêm món ngay</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection