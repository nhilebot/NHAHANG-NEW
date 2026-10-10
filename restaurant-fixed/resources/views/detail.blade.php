@extends('shared')

@section('title', $menu->name . ' - Chi tiết món ăn')

@section('head')
<style>
    body { background-color: #f9f9f9; padding-top: 100px; }

    /* Navbar đồng bộ */
    .navbar-default {
        background-color: #fff;
        border: none !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05) !important;
    }

    /* Container chính */
    .product-detail-container {
        background: #ffffff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 50px;
    }

    /* Khung ảnh vuông 1:1 */
    .detail-image-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 100%;
        overflow: hidden;
        border-radius: 20px;
        background-color: #fcfcfc;
        border: 1px solid #f0f0f0;
    }

    .detail-image-wrapper img {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .detail-image-wrapper:hover img { transform: scale(1.05); }

    /* Phần thông tin món ăn */
    .info-section h1 {
        font-family: 'Playball', cursive;
        color: #041526e0;
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 38px;
    }

    .price-tag {
        font-size: 27px;
        color: #8a6d3b;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .rating-stars {
        font-size: 16px;
        background: #fff4f1;
        padding: 5px 15px;
        border-radius: 30px;
        color: #e74c3c;
    }

    .description-text {
        font-size: 17px;
        color: #666;
        line-height: 1.8;
        margin-bottom: 30px;
        border-left: 4px solid #e74c3c;
        padding-left: 20px;
    }

    /* Form đặt hàng chính */
    .order-controls {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 25px 0;
        border-top: 1px dashed #eee;
    }

    .qty-input-group {
        display: flex;
        align-items: center;
        border: 2px solid #eee;
        border-radius: 30px;
        overflow: hidden;
    }

    .qty-input-group input {
        width: 60px;
        height: 45px;
        border: none;
        text-align: center;
        font-weight: bold;
        outline: none;
    }

    .btn-add-cart {
        background-color: #D4AF37;
        color: white;
        border: none;
        padding: 12px 35px;
        border-radius: 30px;
        font-weight: bold;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        cursor: pointer;
    }

    .btn-add-cart:hover:not(:disabled) {
       background-color: #B5952F;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    }

    /* ─── PHẦN SẢN PHẨM LIÊN QUAN ─── */
    .related-container {
        margin-top: 40px;
        margin-bottom: 50px;
    }

    .related-title {
        font-family: 'Playball', cursive;
        color: #041526e0;
        font-size: 32px;
        margin-bottom: 25px;
        border-bottom: 2px dashed #eee;
        padding-bottom: 10px;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
    }

    .related-card-wrapper {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }

    .related-card-wrapper:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }

    .related-card { text-decoration: none !important; display: block; }

    .related-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .related-card:hover .related-img { transform: scale(1.08); }

    .related-info { padding: 15px; text-align: center; }

    .related-name {
        font-weight: 700;
        color: #333;
        font-size: 16px;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .related-price {
        color: #8a6d3b;
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 10px;
    }

    .btn-related-add {
        width: 100%;
       background-color: #D4AF37;
        color: #fff;
        border: none;
        padding: 10px;
        border-radius: 30px;
        font-weight: bold;
        font-size: 13px;
        transition: 0.3s;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(231, 76, 60, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-related-add:hover:not(:disabled) {
        background-color: #B5952F;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    }

    /* Phần bình luận */
    .comments-container {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        margin-top: 30px;
    }

    .avatar-circle {
        width: 50px; height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .comment-item {
        padding: 20px 0;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        gap: 15px;
    }

    /* ── CSS CHỌN SAO KIỂU SHOPEE ── */
    .shopee-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        align-items: center;
        gap: 4px;
        margin-bottom: 10px;
    }
    .shopee-rating input[type="radio"] { display: none; }
    .shopee-rating label {
        font-size: 26px;
        color: #e4e4e4;
        cursor: pointer;
        transition: color 0.1s ease;
        margin: 0;
    }
    .shopee-rating label:hover,
    .shopee-rating label:hover ~ label { color: #ff9727; }
    .shopee-rating input[type="radio"]:checked ~ label { color: #ee4d2d; }
    
    .rating-text {
        margin-left: 15px;
        font-weight: 600;
        font-size: 14px;
        color: #ee4d2d;
    }

    /* Nút hành động sửa/xóa bình luận */
    .comment-actions {
        display: flex;
        gap: 10px;
    }
    .btn-action-edit { background: none; border: none; color: #4aa0e6; cursor: pointer; font-size: 14px; padding: 0; }
    .btn-action-delete { background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 14px; padding: 0; }
    .btn-action-edit:hover { text-decoration: underline; }
    .btn-action-delete:hover { text-decoration: underline; }
</style>
@endsection

@section('content')
<div class="container">
    {{-- 1. Chi tiết sản phẩm chính --}}
    <div class="product-detail-container">
        <div class="row">
            <div class="col-md-5">
                <div class="detail-image-wrapper">
                    <img src="{{ asset($menu->image) }}" alt="{{ $menu->name }}">
                </div>
            </div>

            <div class="col-md-7 info-section">
                <h1>{{ $menu->name }}</h1>
                
                <div class="price-tag">
                    {{ number_format($menu->price, 0, ',', '.') }} VNĐ
                    <span class="rating-stars">
                        5.0 <i class="fa fa-star"></i> 
                        <span style="color: #999; font-size: 13px; margin-left: 10px;">
                            ({{ $menu->comments->count() }} đánh giá)
                        </span>
                    </span>
                </div>

                <div class="description-text">
                    {{ $menu->description ?? 'Thưởng thức hương vị ẩm thực đặc sắc được chế biến từ những nguyên liệu tươi ngon nhất.' }}
                </div>

                <div class="order-controls">
                    <div class="qty-box">
                        <label style="display: block; font-size: 12px; color: #999;">Số lượng</label>
                        <div class="qty-input-group">
                            <input type="number" id="quantity" value="1" min="1" class="form-control">
                        </div>
                    </div>
                    
                    <button type="button" class="btn-add-cart" onclick="addToCart({{ $menu->id }})"
                        {{ $menu->stock <= 0 ? 'disabled' : '' }}
                        style="{{ $menu->stock <= 0 ? 'background: #ccc; cursor: not-allowed;' : '' }}">
                        <i class="fa fa-shopping-cart"></i> 
                        {{ $menu->stock <= 0 ? 'TẠM HẾT HÀNG' : 'Thêm vào thực đơn' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Sản phẩm liên quan --}}
    @if(isset($relatedMenus) && $relatedMenus->count() > 0)
    <div class="related-container">
        <h3 class="related-title">Có thể bạn sẽ thích</h3>
        <div class="related-grid">
            @foreach($relatedMenus as $related)
            <div class="related-card-wrapper">
                <a href="{{ url('/chi-tiet-mon-an/' . $related->id) }}" class="related-card">
                    <div style="overflow:hidden;">
                        <img src="{{ asset($related->image) }}" alt="{{ $related->name }}" class="related-img">
                    </div>
                    <div class="related-info">
                        <div class="related-name">{{ $related->name }}</div>
                        <div class="related-price">{{ number_format($related->price, 0, ',', '.') }} VNĐ</div>
                    </div>
                </a>
                
                <div style="padding: 0 15px 20px 15px;">
                    <button type="button" 
                            onclick="addToCart({{ $related->id }}, 1)" 
                            class="btn-related-add"
                            {{ $related->stock <= 0 ? 'disabled' : '' }}>
                        <i class="fa fa-shopping-cart"></i> 
                        {{ $related->stock <= 0 ? 'HẾT HÀNG' : 'Thêm vào thực đơn' }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 3. Bình luận và đánh giá --}}
    <div class="comments-container">
        <h3 style="margin-bottom: 30px; font-weight: bold; color: #333;">
            <i class="fa fa-comments-o" style="color: #e74c3c;"></i> Đánh giá từ khách hàng
        </h3>

        @auth
            <div class="comment-form-box" style="background: #fdfdfd; padding: 20px; border-radius: 15px; border: 1px solid #f0f0f0; margin-bottom: 30px;">
                <form action="{{ route('comment.store', $menu->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                    
                    <div style="margin-bottom: 15px;">
                        <label class="d-block" style="font-weight: 600; margin-bottom: 5px;">Mức độ hài lòng:</label>
                        {{-- HỆ THỐNG SAO SHOPEE BẤM 1 CHẠM --}}
                        <div class="shopee-rating">
                            <input type="radio" id="star5" name="rating" value="5" checked />
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4" />
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3" />
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2" />
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1" />
                            <label for="star1">★</label>
                            <span class="rating-text">Tuyệt vời 😍</span>
                        </div>
                    </div>

                    <textarea name="content" class="form-control" rows="3" style="border-radius: 10px; margin-bottom:10px;" placeholder="Chia sẻ trải nghiệm của bạn..." required></textarea>
                    <button type="submit" class="btn-add-cart" style="padding: 8px 25px; font-size: 14px;">Gửi đánh giá</button>
                </form>
            </div>
      {{-- Đoạn code hiển thị thông báo chỉ bằng chữ màu đỏ/xanh đơn giản --}}
@if(session('success'))
    <div style="color: #af2424; font-weight: 600; margin-bottom: 15px; font-size: 15px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="color: #e74c3c; font-weight: 600; margin-bottom: 15px; font-size: 15px;">
        {{ session('error') }}
    </div>
@endif
            @else
            <div class="alert alert-warning" style="border-radius: 15px;">
                Vui lòng <a href="{{ route('login') }}" class="alert-link">đăng nhập</a> để để lại đánh giá.
            </div>
        @endauth

        <div class="comment-list">
            @forelse($menu->comments as $comment)
                <div class="comment-item" id="comment-box-{{ $comment->id }}">
                    <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=e74c3c&color=fff' }}" class="avatar-circle">
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h5 style="margin: 0; font-weight: bold;">{{ $comment->user->name }}</h5>
                            
                            {{-- Nút sửa/xóa (Chỉ hiện nếu đúng là chủ sở hữu bình luận) --}}
                            @auth
                                @if(auth()->id() === $comment->user_id)
                                    <div class="comment-actions">
                                        <button type="button" class="btn-action-edit" onclick="showEditForm({{ $comment->id }}, '{{ $comment->content }}', {{ $comment->rating }})">
                                            <i class="fa fa-pencil"></i> Sửa
                                        </button>
                                        &nbsp;|&nbsp;
                                        <form action="{{ route('comment.destroy', $comment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-delete">
                                                <i class="fa fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                        
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        
                        <div style="color: #ffc107; font-size: 12px; margin: 5px 0;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa {{ $i <= $comment->rating ? 'fa-star' : 'fa-star-o' }}"></i>
                            @endfor
                        </div>
                        
                        <p style="color: #555;" class="comment-content-text">{{ $comment->content }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center" style="padding: 40px; color: #bbb;">
                    <i class="fa fa-commenting-o" style="font-size: 40px;"></i>
                    <p>Chưa có đánh giá nào.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL CHỈNH SỬA BÌNH LUẬN ẨN --}}
<div class="modal fade" id="editCommentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title" style="font-weight:bold;">✏️ Chỉnh sửa đánh giá</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-comment-form" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div style="margin-bottom: 15px;">
                        <label class="d-block" style="font-weight:600;">Sửa số sao:</label>
                        <div class="shopee-rating modal-shopee-rating">
                            <input type="radio" id="mod-star5" name="rating" value="5" />
                            <label for="mod-star5">★</label>
                            <input type="radio" id="mod-star4" name="rating" value="4" />
                            <label for="mod-star4">★</label>
                            <input type="radio" id="mod-star3" name="rating" value="3" />
                            <label for="mod-star3">★</label>
                            <input type="radio" id="mod-star2" name="rating" value="2" />
                            <label for="mod-star2">★</label>
                            <input type="radio" id="mod-star1" name="rating" value="1" />
                            <label for="mod-star1">★</label>
                            <span class="rating-text modal-rating-text">Tuyệt vời 😍</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:600;">Nội dung đánh giá:</label>
                        <textarea name="content" id="edit-comment-content" class="form-control" rows="4" style="border-radius:10px;" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:20px;">Hủy</button>
                    <button type="submit" class="btn-add-cart" style="padding: 6px 20px; font-size: 14px;">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Đoạn JS điều khiển đổi text theo số sao động chuẩn Shopee
document.addEventListener("DOMContentLoaded", function() {
    const texts = {
        "1": "Tệ 😡",
        "2": "Không ngon 😞",
        "3": "Tạm được 😐",
        "4": "Ngon 😋",
        "5": "Tuyệt vời 😍"
    };

    // Áp dụng cho form thêm mới
    document.querySelectorAll('.shopee-rating:not(.modal-shopee-rating) input').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelector('.shopee-rating:not(.modal-shopee-rating) .rating-text').textContent = texts[this.value];
        });
    });

    // Áp dụng cho form trên Modal chỉnh sửa
    document.querySelectorAll('.modal-shopee-rating input').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelector('.modal-rating-text').textContent = texts[this.value];
        });
    });
});

// Hàm hiển thị modal và nạp dữ liệu cũ của bình luận lên để sửa
// Hàm hiển thị modal và nạp dữ liệu cũ của bình luận lên để sửa
function showEditForm(commentId, currentContent, currentRating) {
    const texts = { "1": "Tệ 😡", "2": "Không ngon 😞", "3": "Tạm được 😐", "4": "Ngon 😋", "5": "Tuyệt vời 😍" };
    
    // Sử dụng cú pháp thay thế chuỗi của Javascript để truyền ID vào URL động
    document.getElementById('edit-comment-form').action = "{{ url('/comments') }}/" + commentId;
    
    // Nạp nội dung cũ
    document.getElementById('edit-comment-content').value = currentContent;
    
    // Kích hoạt tích chọn vào ngôi sao cũ
    const targetRadio = document.getElementById('mod-star' + currentRating);
    if(targetRadio) {
        targetRadio.checked = true;
        document.querySelector('.modal-rating-text').textContent = texts[currentRating];
    }
    
    // Bật modal lên hiển thị
    $('#editCommentModal').modal('show');
}

function addToCart(foodId, forceQty = null) {
    let qtyInput = document.getElementById('quantity');
    let qty = forceQty ? forceQty : (qtyInput ? qtyInput.value : 1);
    
    $.ajax({
        url: "{{ route('cart.add') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            menu_id: foodId,
            quantity: qty
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Đã thêm vào thực đơn!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    iconColor: '#e74c3c'
                });
                if(response.count !== undefined) {
                    $('.badge').text(response.count);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: response.message,
                    confirmButtonColor: '#e74c3c'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Thông báo',
                text: 'Vui lòng đăng nhập để thực hiện đặt món!',
                confirmButtonColor: '#e74c3c'
            });
        }
    });
}
</script>
@endsection