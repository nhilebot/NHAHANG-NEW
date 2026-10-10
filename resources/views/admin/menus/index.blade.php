@extends('layouts.admin')

@section('admin_content')
<style>
    /* ===== CSS LUXURY THEME ===== */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap');

    .panel-custom {
        background: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
        border-top: 4px solid #D4AF37;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .title-category {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: #1A2228;
        font-size: 26px;
    }

    /* Table Styling */
    .table-custom { margin-top: 25px; width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .table-custom th {
        background-color: #FCFBF8;
        color: #4A5568;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        padding: 15px;
        border-bottom: 2px solid #EAEAEA;
    }
    .table-custom td {
        padding: 15px;
        vertical-align: middle;
        background: #fff;
        border-bottom: 1px solid #F1F1F1;
    }

    /* Badge & Image */
    .img-food { width: 55px; height: 55px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
    .badge-category { background: #FDFAF0; color: #B7941F; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid #F9EBC8; display: inline-block; min-width: 90px; text-align: center; }
    .badge-status { background: #E6FFFA; color: #2C7A7B; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; }

    /* Nút 3 chấm (Dropdown) */
    .dropdown-action { position: relative; display: inline-block; }
    .btn-dots { background: none; border: none; font-size: 20px; color: #A0AEC0; cursor: pointer; padding: 5px 10px; border-radius: 50%; transition: all 0.3s; }
    .btn-dots:hover { background: #F7FAFC; color: #1A2228; }

    .dropdown-content-custom {
        display: none;
        position: absolute;
        right: 0;
        background-color: #fff;
        min-width: 140px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        z-index: 100;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #EDF2F7;
    }
    .dropdown-content-custom a, .dropdown-content-custom button {
        color: #4A5568;
        padding: 12px 15px;
        text-decoration: none;
        display: block;
        font-size: 14px;
        font-weight: 500;
        width: 100%;
        text-align: left;
        border: none;
        background: none;
        transition: 0.2s;
    }
    .dropdown-content-custom a:hover { background-color: #F7FAFC; color: #D4AF37; }
    .dropdown-content-custom button:hover { background-color: #FFF5F5; color: #C53030; }
    
    .show { display: block; }
    /* ===== FIX CSS PHÂN TRANG CHO TẤT CẢ MÓN ĂN ===== */
.pagination-wrapper {
    margin-top: 30px;
    display: flex;
    justify-content: flex-end; /* Đẩy sang bên phải */
    padding-bottom: 10px;
}

.pagination-wrapper .pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 8px; /* Khoảng cách giữa các số trang */
}

.pagination-wrapper .page-item .page-link,
.pagination-wrapper .page-item span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 15px;
    color: #4A5568;
    background-color: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
}

/* Khi đang ở trang hiện tại (Màu vàng đồng) */
.pagination-wrapper .page-item.active .page-link,
.pagination-wrapper .page-item.active span {
    background-color: #D4AF37 !important;
    border-color: #D4AF37 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
}

/* Khi rê chuột qua (Màu đen Luxury) */
.pagination-wrapper .page-item .page-link:hover:not(.active) {
    background-color: #1A2228;
    color: #D4AF37;
    border-color: #1A2228;
    transform: translateY(-2px);
}

/* Khi nút bị vô hiệu hóa (Ví dụ nút 'Trướcc' khi ở trang 1) */
.pagination-wrapper .page-item.disabled .page-link,
.pagination-wrapper .page-item.disabled span {
    color: #CBD5E0;
    background-color: #F7FAFC;
    border-color: #E2E8F0;
    cursor: not-allowed;
}
</style>

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