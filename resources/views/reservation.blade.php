@extends('shared')

@section('title', 'Restaurant - Reservation')

@section('head')
<link rel="stylesheet" href="{{ asset('css/pages/reservation.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/pages/reservation-mobile.css') }}?v=1">
@endsection

@section('content')
<section id="reservation" class="description_content">
        <div class="featured background_content">
            <h1 class="text-center">Reserve Your Experience</h1>
        </div>

        <div class="reservation-section">
            <div class="container">
                <div class="form-container-custom">
                    <h2 class="form-title">{{ $editingReservation ? 'Chỉnh sửa yêu cầu đặt bàn' : 'Yêu cầu đặt bàn' }}</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px; border-left: 4px solid #e74c3c;">
                            <strong>❌ Lỗi xác thực:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px; border-left: 4px solid #e74c3c;">
                            <strong>❌ Lỗi:</strong> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                
                    <form id="reservation-form" method="post" action="{{ route('reservation.store') }}">
                        @csrf
                        @if($editingReservation)
                            <input type="hidden" name="reservation_id" value="{{ $editingReservation->id }}">
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <label class="label-custom">Ngày đặt bàn</label>
                                <input 
                                    type="date" 
                                    name="reservation_date" 
                                    class="form-control input-custom"
                                    min="{{ now('Asia/Ho_Chi_Minh')->toDateString() }}"
                                    value="{{ old('reservation_date', $editingReservation?->reservation_date?->format('Y-m-d')) }}"
                                    required
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="label-custom">Giờ đến</label>
                                <input type="time" name="reservation_time" class="form-control input-custom" value="{{ old('reservation_time', $editingReservation?->reservation_time) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="label-custom">Họ và Tên</label>
                                <input type="text" name="full_name" class="form-control input-custom" value="{{ old('full_name', $editingReservation?->full_name) }}" placeholder="Nhập tên của bạn" required>
                            </div>
                            <div class="col-md-6">
                                <label class="label-custom">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-control input-custom" value="{{ old('phone', $editingReservation?->phone) }}" placeholder="Nhập số điện thoại liên lạc" required>
                            </div>
                        </div>

                        <label class="label-custom">Lựa Chọn Vị Trí Bàn</label>

                        <div class="table-selection-grid">
                            @foreach($tables as $table)
                                @php $isUnavailable = in_array($table->id, $bookedTableIds); @endphp
                                <div class="table-item" id="table-container-{{ $table->id }}">

                                    <input 
                                        type="radio"
                                        name="table_id"
                                        value="{{ $table->id }}"
                                        id="table{{ $table->id }}"
                                        {{ $isUnavailable && (int) $editingReservation?->table_id !== (int) $table->id ? 'disabled' : '' }}
                                        {{ (int) old('table_id', $editingReservation?->table_id) === (int) $table->id ? 'checked' : '' }}
                                    >

                                    <label for="table{{ $table->id }}" id="label-{{ $table->id }}">

                                        <div class="table-name">
                                            {{ $table->name }} - {{ $table->capacity }} Ghế
                                        </div>

                                        <div id="status-text-{{ $table->id }}">
                                            @if($table->status === 'empty' && !$isUnavailable)
                                                 <small style="color:#D4AF37;">(Sẵn sàng)</small>

                                            @elseif(in_array($table->id, $paymentTableIds))
                                                <small class="status-reserved">
                                                    <i class="fa fa-lock"></i>
                                                    Đã đặt
                                                </small>

                                            @elseif($table->status === 'reserved')
                                                <small class="status-reserved">
                                                    <i class="fa fa-lock"></i>
                                                    Đã đặt
                                                </small>

                                            @elseif($table->status === 'cleaning')
                                                <small class="status-cleaning">
                                                    🧹 Dọn dẹp (
                                                    <span class="cleaning-timer"
                                                          data-start="{{ $table->cleanup_started_at }}"
                                                          data-id="{{ $table->id }}">
                                                          60
                                                    </span>s)
                                                </small>

                                            @elseif($table->status === 'serving' || $table->status === 'occupied')
                                                <small class="status-serving">
                                                    <i class="fa fa-cutlery"></i>
                                                    Đang phục vụ
                                                </small>

                                            @elseif($isUnavailable)
                                                <small class="status-reserved">
                                                    <i class="fa fa-lock"></i>
                                                    Đã đặt
                                                </small>

                                            @else
                                                <small class="status-serving">
                                                    <i class="fa fa-cutlery"></i>
                                                    Đang phục vụ
                                                </small>
                                            @endif
                                        </div>

                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-3 p-3 rounded" style="background: #FCFBF8; border-left: 4px solid #D4AF37;">
                            <small class="text-muted" style="font-size: 13px;">
                                <i class="fa fa-info-circle" style="color: #D4AF37;"></i> 
                                Bàn trạng thái <strong>(Đã đặt)</strong> bao gồm bàn đang phục vụ hoặc đang trong thời gian <strong>chuẩn bị không gian (15 phút)</strong> để mang lại trải nghiệm hoàn hảo nhất cho quý khách.
                            </small>
                        </div>

                        <label class="label-custom">Thực Đơn Đặt Trước (Tùy Chọn)</label>
                        <button type="button" class="btn-food-select" id="openFoodModalBtn">
                            <i class="fa fa-cutlery" style="margin-right: 8px;"></i> KHÁM PHÁ & CHỌN MÓN ĂN
                        </button>

                        <div id="cart-container" class="cart-wrapper" style="display: none;">
                            <h5 style="margin-top: 0; color: #1A2228; font-family: 'Playfair Display', serif; font-weight: bold; border-bottom: 1px solid #EAEAEA; padding-bottom: 10px;">Thực Đơn Của Bạn:</h5>
                            <div id="cart-list"></div>
                            <div class="cart-total text-right" style="margin-top: 20px; border-top: 1px solid #EAEAEA; padding-top: 15px;">
                                <strong style="font-size: 18px; color: #1A2228;">Tổng hóa đơn: <span id="total-price" style="color: #D4AF37; font-size: 22px;">0</span> VNĐ</strong>
                            </div>
                        </div>

                        <div id="hidden-inputs-container"></div>

                        <label class="label-custom">Ghi Chú Yêu Cầu Đặc Biệt</label>
                        <textarea name="notes" class="form-control input-custom" rows="3" placeholder="Ví dụ: Dị ứng đậu phộng, cần chuẩn bị hoa hồng, kỷ niệm sinh nhật..." style="height: auto !important; padding: 15px;">{{ old('notes', $editingReservation?->notes) }}</textarea>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-reserve">
                                {{ $editingReservation ? 'CẬP NHẬT ĐẶT BÀN' : 'HOÀN TẤT ĐẶT BÀN' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div id="foodMenuModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Thực Đơn Tinh Hoa</h4>
                </div>
                <div class="modal-body" style="max-height: 65vh; overflow-y: auto; background: #FAFAFA;">
                    <div class="search-container" style="position: sticky; top: -15px; background: #FAFAFA; padding: 15px 0; z-index: 10;">
                        <input type="text" id="menuSearch" class="search-input" placeholder="Tìm kiếm món ăn yêu thích..." onkeyup="filterMenu()">
                    </div>
                    <div id="menu-list"></div>
                </div>
                <div class="modal-footer" style="background: #FAFAFA; border-top: 1px solid #EAEAEA;">
                    <button type="button" class="btn btn-finish-modal" data-dismiss="modal">Hoàn Tất Lựa Chọn</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Không thể đặt bàn',
            text: @json($errors->first()),
            confirmButtonText: 'Tôi đã hiểu',
            confirmButtonColor: '#1A2228'
        });
    });
</script>
@endif

@if (session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Không thể đặt bàn',
            text: @json(session('error')),
            confirmButtonText: 'Tôi đã hiểu',
            confirmButtonColor: '#1A2228'
        });
    });
</script>
@endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const menuItems = @json($menus ?? []);
        let cart = @json($cart ?? []);
        window.cart = cart;
        const formatCurrency = (num) => new Intl.NumberFormat('vi-VN').format(num);
        const getImageUrl = (image) => {
            if (!image) return 'https://via.placeholder.com/200x160?text=No+Image';
            if (typeof image === 'string' && (image.startsWith('http://') || image.startsWith('https://') || image.startsWith('//'))) {
                return image;
            }
            return `{{ asset('') }}${image}`;
        };

        console.log('=== RESERVATION PAGE LOADED ===');

        $(document).ready(function () {
            renderMenu();
            updateCartUI();
            
            $('#foodMenuModal').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });
            
            $('#openFoodModalBtn').on('click', function(e) {
                e.preventDefault();
                console.log('Opening food modal...');
                $('#foodMenuModal').modal('show');
            });
        });

        function renderMenu(filter = '') {
            const listElement = document.getElementById('menu-list');
            if (!listElement) return;
            
            if (!menuItems || menuItems.length === 0) {
                listElement.innerHTML = '<div style="color: #e74c3c; padding: 20px; text-align: center;">Thực đơn đang được cập nhật. Vui lòng quay lại sau!</div>';
                return;
            }

            const categoryNames = {
                'seafood': 'Hải Sản Cao Cấp',
                'special': 'Món Đặc Biệt Bếp Trưởng',
                'salad': 'Salad & Khai Vị',
                'Món khác': 'Các Tuyệt Phẩm Khác'
            };

            const groups = menuItems.reduce((acc, item) => {
                const rawCat = item.category || 'Món khác';
                const cat = categoryNames[rawCat] || rawCat; 
                
                if (!acc[cat]) acc[cat] = [];
                acc[cat].push(item);
                return acc;
            }, {});

            let html = '';
            for (const category in groups) {
                const filteredItems = groups[category].filter(item => 
                    item.name.toLowerCase().includes(filter.toLowerCase())
                );

                if (filteredItems.length > 0) {
                    html += `<div class="category-divider">${category}</div><div class="menu-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">`;
                    
                    filteredItems.forEach(item => {
                        const isOutOfStock = item.stock <= 0;
                        const imagePath = getImageUrl(item.image);
                        
                        html += `
                            <div class="menu-card" style="border: none; border-radius: 12px; background: #FFF; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; transition: transform 0.3s;">
                                <img src="${imagePath}" 
                                     alt="${item.name}"
                                     style="width:100%; height:160px; object-fit:cover;"
                                     onerror="this.src='https://via.placeholder.com/200x160?text=Error+Image'">
                                <div class="card-body" style="padding: 15px;">
                                    <h5 style="font-size:15px; font-weight: 600; margin: 0 0 8px 0; height: 38px; overflow: hidden; color: #1A2228;">${item.name}</h5>
                                    <p style="color:#D4AF37; font-weight:bold; margin-bottom: 12px; font-size: 16px;">${formatCurrency(item.price)}đ</p>
                                    <button type="button" onclick="addToCart(${item.id})" ${isOutOfStock ? 'disabled' : ''} 
                                            style="width:100%; background:${isOutOfStock ? '#E2E8F0' : '#1A2228'}; color:${isOutOfStock ? '#A0AEC0' : '#FFF'}; border:none; padding:10px; border-radius:6px; font-weight: 600; transition: background 0.3s;">
                                        ${isOutOfStock ? 'Tạm Hết' : 'Thêm vào bàn'}
                                    </button>
                                </div>
                            </div>`;
                    });
                    html += `</div>`;
                }
            }
            listElement.innerHTML = html || '<p style="text-align: center; padding: 20px; color: #718096;">Không tìm thấy món ăn phù hợp.</p>';
        }

        function addToCart(id) {
            const item = menuItems.find(i => i.id === id);
            if (!item) return;

            fetch("{{ route('reservation.addToCart') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ 
                food_id: item.id,   
                quantity: 1         
            })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if (data.cartData) cart = data.cartData;
                    updateCartUI();
                    
                    if(data.cartCount !== "Đã thêm vào thực đơn") {
                        $('.badge').text(data.cartCount);
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã thêm món!',
                        text: `${item.name} đã được chọn`,
                        showConfirmButton: false,
                        timer: 1500,
                        position: 'top-end',
                        toast: true,
                        iconColor: '#D4AF37'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message || 'Không thể thêm vào giỏ hàng',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            })
            .catch(err => {
                console.error('Lỗi AJAX:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối!',
                    text: 'Không thể kết nối đến máy chủ',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        }

        function updateQuantity(id, change) {
            const index = cart.findIndex(i => i.id === id);
            const item = menuItems.find(i => i.id === id);
            
            if (index === -1 || !item) return;

            const newQty = cart[index].quantity + change;

            if (newQty <= 0) {
                cart.splice(index, 1);
            } 
            else if (newQty > item.stock) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hết hàng!',
                    text: `Rất tiếc, nhà hàng hiện chỉ còn ${item.stock} phần cho món ${item.name}.`,
                    confirmButtonColor: '#1A2228',
                    iconColor: '#e74c3c'
                });
                return; 
            } 
            else if (newQty > 50) { 
                Swal.fire({
                    icon: 'warning',
                    title: 'Số lượng quá lớn',
                    text: 'Với đơn hàng trên 50 phần, vui lòng liên hệ hotline!',
                    confirmButtonColor: '#D4AF37'
                });
                return;
            }
            else {
                cart[index].quantity = newQty;
            }

            updateCartUI();

            fetch("{{ route('reservation.addToCart') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ 
                    food_id: id, 
                    quantity: newQty,
                    is_update: true
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log("Đã đồng bộ số lượng mới vào DB:", data);
            })
            .catch(err => {
                console.error("Lỗi đồng bộ:", err);
            });    
            updateCartUI();
        }

        function updateCartUI() {
            const container = document.getElementById('cart-container');
            const list = document.getElementById('cart-list');
            const hidden = document.getElementById('hidden-inputs-container');
            const totalPriceElement = document.getElementById('total-price');
            
            if (!container || !list || !hidden) return; 

            let total = 0, cartHtml = '', inputsHtml = '';

            cart.forEach(item => {
                total += item.price * item.quantity;
                
                cartHtml += `
                    <div class="cart-item" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #EAEAEA;">
                        <div style="display:flex; align-items:center; gap:15px;">
                            <img src="${getImageUrl(item.image)}" style="width:50px; height:50px; border-radius:6px; object-fit: cover;" alt="${item.name}">
                            <div>
                                <span style="font-weight: 600; color: #1A2228; display: block;">${item.name}</span>
                                <span style="font-size: 13px; color: #D4AF37;">${formatCurrency(item.price)}đ</span>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; background: #F7FAFC; border-radius: 20px; padding: 2px 5px; border: 1px solid #E2E8F0;">
                            <button type="button" onclick="updateQuantity(${item.id}, -1)" style="border:none; background:transparent; font-size:18px; color:#4A5568; cursor:pointer; width: 25px; outline:none;">-</button>
                            <span style="padding:0 10px; font-weight: 600; min-width: 20px; text-align: center;">${item.quantity}</span>
                            <button type="button" onclick="updateQuantity(${item.id}, 1)" style="border:none; background:transparent; font-size:18px; color:#4A5568; cursor:pointer; width: 25px; outline:none;">+</button>
                        </div>
                    </div>`;

                inputsHtml += `
                    <input type="hidden" name="cart[${item.id}][id]" value="${item.id}">
                    <input type="hidden" name="cart[${item.id}][name]" value="${item.name}">
                    <input type="hidden" name="cart[${item.id}][quantity]" value="${item.quantity}">
                    <input type="hidden" name="cart[${item.id}][price]" value="${item.price}">
                    <input type="hidden" name="cart[${item.id}][image]" value="${item.image}">`;
            });

            container.style.display = cart.length > 0 ? 'block' : 'none';
            list.innerHTML = cartHtml;
            hidden.innerHTML = inputsHtml;

            if (totalPriceElement) {
                totalPriceElement.innerText = formatCurrency(total);
            }
        }

        function filterMenu() { 
            renderMenu(document.getElementById('menuSearch').value); 
        }

        $(document).ready(function () {
            renderMenu();
            updateCartUI();

            $('#foodMenuModal').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });

            $('#openFoodModalBtn').on('click', function(e) {
                e.preventDefault();
                $('#foodMenuModal').modal('show');
            });

            $('#reservation-form').on('submit', function(e) {
                e.preventDefault();

                const selectedDate = $('input[name="reservation_date"]').val();
                const today = new Date().toISOString().split('T')[0];

                if (selectedDate < today) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ngày không hợp lệ',
                        text: 'Không thể đặt bàn ở ngày đã qua!',
                        confirmButtonColor: '#1A2228',
                        iconColor: '#e74c3c'
                    });
                    return;
                }

                if (!$('input[name="table_id"]:checked').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa Chọn Bàn',
                        text: 'Quý khách vui lòng chọn vị trí bàn mong muốn trước khi hoàn tất.',
                        confirmButtonColor: '#1A2228',
                        iconColor: '#D4AF37'
                    });
                    return;
                }

                console.log('=== Form Submission Debug ===');
                console.log('Form name:', $('input[name="full_name"]').val());
                console.log('Form table_id:', $('input[name="table_id"]:checked').val());
                console.log('Form date:', $('input[name="reservation_date"]').val());
                console.log('Form time:', $('input[name="reservation_time"]').val());
                console.log('Hidden inputs:', $('#hidden-inputs-container').html());

                this.submit();
            });
        });
    </script>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timers = document.querySelectorAll('.cleaning-timer');

        timers.forEach(timer => {
            const startTimeStr = timer.getAttribute('data-start');
            if (!startTimeStr) return;

            const startTime = new Date(startTimeStr).getTime();
            const duration = 60 * 1000;

            const updateCountdown = setInterval(function() {
                const now = new Date().getTime();
                const distance = now - startTime;
                const secondsLeft = Math.ceil((duration - distance) / 1000);

                if (secondsLeft <= 0) {
                    clearInterval(updateCountdown);
                    window.location.reload();
                } else {
                    timer.innerText = secondsLeft;
                }
            }, 1000);
        });
    });
</script>