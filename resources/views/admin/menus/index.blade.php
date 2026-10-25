@extends('layouts.admin')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menus-index.css') }}?v=1">
@endpush

<section class="panel-custom">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="title-category">
            <i class="fa fa-list-ul" style="color: #D4AF37; margin-right: 8px;"></i> Quản Lý Thực Đơn
        </h3>
        <!-- <a href="{{ route('admin.menus.create') }}" class="btn-add-new" style="background-color: #D4AF37; color: #FFF; padding: 10px 20px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 13px;">
            + THÊM MÓN MỚI
        </a> -->
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th class="text-center">STT</th>
                    <th>Danh mục</th>
                    <th>Tên món ăn</th>
                    <th>Giá bán</th>
                    <th class="text-center">Hình ảnh</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center">Tùy chọn</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $index => $menu)
                    <tr>
                        <td class="text-center text-muted font-weight-bold">
                            {{ ($menus->currentPage() - 1) * $menus->perPage() + $loop->iteration }}
                        </td>
                        
                        <td>
                            @if($menu->category_relation)
                                <span class="badge-category">{{ $menu->category_relation->name }}</span>
                            @else
                                <span class="text-muted" style="font-size: 11px;">(ID: {{ $menu->category_id }})</span>
                            @endif
                        </td>

                        <td><strong style="color: #2D3748;">{{ $menu->name }}</strong></td>

                        <td style="color: #C53030; font-weight: 700;">{{ number_format($menu->price) }}đ</td>

                        <td class="text-center">
                            @php
                                $imageUrl = str_contains($menu->image, 'images/') ? asset($menu->image) : asset('images/' . $menu->image);
                            @endphp
                            <img src="{{ $imageUrl }}" class="img-food" onerror="this.src='{{ asset('images/default.jpg') }}'">
                        </td>

                        {{-- ✅ Kiểm tra đúng status --}}
<td class="text-center">
    @if($menu->status == 1)
        <span class="badge-status" style="background:#E6FFFA;color:#2C7A7B;">✅ Đang bán</span>
    @else
        <span class="badge-status" style="background:#F7FAFC;color:#A0AEC0;">⏸ Ngừng bán</span>
    @endif
</td>

                        <td class="text-center">
                            <div class="dropdown-action">
                                <button class="btn-dots" onclick="toggleMenu('dropdown-{{ $menu->id }}')">⋮</button>
                                <div id="dropdown-{{ $menu->id }}" class="dropdown-content-custom">
                                    <a href="{{ route('admin.menus.edit', $menu->id) }}">
                                        <i class="fa fa-edit mr-2"></i> Chỉnh sửa
                                    </a>
                                    <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Xóa món {{ $menu->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">
                                            <i class="fa fa-trash mr-2"></i> Xóa món
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có dữ liệu món ăn.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $menus->links('pagination::bootstrap-4') }}
    </div>
</section>

<script>
    function toggleMenu(id) {
        // Đóng tất cả các menu khác trước khi mở menu mới
        document.querySelectorAll('.dropdown-content-custom').forEach(el => {
            if (el.id !== id) el.classList.remove('show');
        });
        document.getElementById(id).classList.toggle('show');
    }

    // Đóng menu nếu click ra ngoài vùng dropdown
    window.onclick = function(event) {
        if (!event.target.matches('.btn-dots')) {
            document.querySelectorAll('.dropdown-content-custom').forEach(el => {
                el.classList.remove('show');
            });
        }
    }
</script>
@endsection