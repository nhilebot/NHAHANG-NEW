@extends('layouts.admin')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/reservations-create.css') }}?v=1">
@endpush

<div class="admin-booking-container">
    <div class="form-container-custom">
        <h2 class="form-title">Phục Vụ Tại Quầy (Walk-in)</h2>

        @if($errors->any())
            <div role="alert" style="margin-bottom: 20px; padding: 12px 16px; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ auth()->user()->isAdmin()
            ? route('admin.reservations.store')
            : route('staff.reservations.store') }}" method="POST">
            @csrf
            
            <!-- Flag ẩn để Backend biết đây là khách đến trực tiếp -->
            <input type="hidden" name="is_walk_in" value="1">

            <div class="input-grid-system">
                <div class="input-group-custom">
                    <span class="section-label">Họ và Tên (Không bắt buộc)</span>
                    <input type="text" name="full_name" class="input-custom" placeholder="Khách vãng lai">
                </div>
                <div class="input-group-custom">
                    <span class="section-label">Số điện thoại (Không bắt buộc)</span>
                    <input type="tel" name="phone" class="input-custom" placeholder="Để trống nếu không có">
                </div>
                <div class="input-group-custom">
                    <span class="section-label">Ngày đến</span>
                    <input type="date" id="auto_date" name="reservation_date" class="input-custom" required readonly style="background-color: #f1f5f9;">
                </div>
                <div class="input-group-custom">
                    <span class="section-label">Giờ đến</span>
                    <input type="time" id="auto_time" name="reservation_time" class="input-custom" required readonly style="background-color: #f1f5f9;">
                </div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <span class="section-label">Lựa Chọn Vị Trí Bàn (Bắt buộc)</span>
                <div class="table-selection-grid">
                    @foreach($tables as $table)
                    @php $isUnavailable = in_array($table->id, $bookedTableIds ?? []); @endphp
                    <div class="table-item">
                        <input type="radio" name="table_id" value="{{ $table->id }}" id="table{{ $table->id }}" 
                               {{ $isUnavailable ? 'disabled' : '' }} required>
                        <label for="table{{ $table->id }}" id="label-{{ $table->id }}">
    <strong>{{ $table->name }}</strong>
    <div id="status-text-{{ $table->id }}">
        @if($table->status === 'empty' && !$isUnavailable)
            <small style="color: #D4AF37;">(Sẵn sàng)</small>
        @elseif($table->status === 'cleaning')
            @php
                // Ép kiểu chắc chắn để tránh lỗi null
                $cleanupTime = $table->cleanup_started_at ? \Carbon\Carbon::parse($table->cleanup_started_at) : now();
                $secondsPassed = $cleanupTime->diffInSeconds(now());
                $secondsLeft = 60 - $secondsPassed;
                $secondsLeft = $secondsLeft > 0 ? $secondsLeft : 0;
            @endphp
            <small style="color: #e74c3c;" class="cleaning-wrapper" data-id="{{ $table->id }}" data-left="{{ $secondsLeft }}">
                🧹 Dọn dẹp (<span id="timer-{{ $table->id }}">{{ $secondsLeft }}</span>s)
            </small>
        @elseif($isUnavailable)
            <small style="color: #999;">(Đã đặt)</small>
        @else
            <small style="color: #999;">(Đã đặt)</small>
        @endif
    </div>
</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <button type="button" class="btn-food-trigger" onclick="document.getElementById('foodMenuModal').style.display='block'">
                🍴 GỌI MÓN CHO KHÁCH (TÙY CHỌN)
            </button>

            <div id="admin-cart-display" style="display:none; margin-top: 20px; padding: 25px; background: #FDFBF7; border-radius: 12px; border: 1px solid #EAEAEA;">
                <h6 style="font-weight: 700; color: #1A2228; margin-bottom: 15px;">Thực đơn đã chọn:</h6>
                <div id="cart-items-list" style="font-size: 14px; color: #4A5568;"></div>
                <div id="hidden-food-inputs"></div>
            </div>

            <button type="submit" class="btn-reserve-final">Mở Bàn Ngay</button>
        </form>
    </div>
</div>

<div class="modal" id="foodMenuModal" style="display: none; background: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.3);">
            <div class="modal-luxury-header" style="background: #1A2228; color: #D4AF37; padding: 18px 25px; display: flex; justify-content: space-between; align-items: center;">
                <h5 style="font-family: 'Playfair Display', serif; font-size: 22px; margin: 0;">Gọi Món Nhanh</h5>
                <button type="button" style="background:none; border:none; color:#fff; font-size:25px; cursor:pointer;" onclick="document.getElementById('foodMenuModal').style.display='none'">&times;</button>
            </div>
            <div class="modal-body" style="background: #FAFAFA; max-height: 60vh; overflow-y: auto; padding: 20px;">
                <div class="menu-grid-admin" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    @foreach($menus as $menu)
                    
                    <div class="food-card-admin" style="background: #fff; border-radius: 12px; border: 1px solid #edf2f7; overflow: hidden; text-align: center; padding-bottom: 15px;">
                        <img src="{{ asset($menu->image) }}" style="width: 100%; height: 130px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/200x130'">
                        <div style="padding: 10px;">
                            <div style="font-weight: 700; font-size: 14px; color: #1A2228; margin-bottom: 5px;">{{ $menu->name }}</div>
                            <div style="color: #D4AF37; font-weight: 700; margin-bottom: 10px;">{{ number_format($menu->price) }}đ</div>
                            <button type="button" onclick="adminAddFood({{ $menu->id }}, '{{ $menu->name }}')" class="btn-add-item" style="background: #1A2228; color: #fff; border: none; padding: 7px 20px; border-radius: 20px; cursor: pointer;">+ Thêm món</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer" style="background: #FFF; border-top: 1px solid #eee; padding: 15px 25px; text-align: right;">
                <button type="button" style="background: #D4AF37; color: #fff; border: none; padding: 10px 40px; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="document.getElementById('foodMenuModal').style.display='none'">XONG</button>
            </div>
        </div>
    </div>
</div>

<div id="toast-msg"></div>

<script>
    // --- Tự động điền Ngày & Giờ hiện tại cho khách tại quầy ---
    document.addEventListener("DOMContentLoaded", function() {
        const now = new Date();
        
        // Format YYYY-MM-DD
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        document.getElementById('auto_date').value = `${year}-${month}-${day}`;
        
        // Format HH:MM
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('auto_time').value = `${hours}:${minutes}`;
    });

    // --- Quản lý giỏ hàng ---
    var adminCart = [];

    window.adminAddFood = function(id, name) {
        var exist = adminCart.find(function(f) { return f.id === id; });
        if(exist) {
            exist.qty++;
        } else {
            adminCart.push({id: id, name: name, qty: 1});
        }

        renderAdminCart();

        var toast = document.getElementById('toast-msg');
        if(toast) {
            toast.innerText = "✔️ Đã thêm: " + name;
            toast.style.display = 'block';
            setTimeout(function() {
                toast.style.display = 'none';
            }, 1000);
        }
    };

    window.removeAdminFood = function(id) {
        adminCart = adminCart.filter(function(f) { return f.id !== id; });
        renderAdminCart();
    };

    function renderAdminCart() {
        var display = document.getElementById('admin-cart-display');
        var list = document.getElementById('cart-items-list');
        var hidden = document.getElementById('hidden-food-inputs');
        
        if(!display || !list || !hidden) return;

        if(adminCart.length > 0) {
            display.style.display = 'block';
            list.innerHTML = adminCart.map(function(f) {
                return '<div style="display:flex; justify-content:space-between; margin-bottom:8px; border-bottom:1px solid #F0F0F0; padding-bottom:8px;">' +
                       '<span>• ' + f.name + ' <strong>(x' + f.qty + ')</strong></span>' +
                       '<span style="color: #e74c3c; cursor: pointer; font-weight: bold;" onclick="removeAdminFood(' + f.id + ')">✕</span>' +
                       '</div>';
            }).join('');
            
            hidden.innerHTML = adminCart.map(function(f) {
                return '<input type="hidden" name="foods[' + f.id + '][quantity]" value="' + f.qty + '">';
            }).join('');
        } else {
            display.style.display = 'none';
        }
    }
</script>
@endsection