<?php $__env->startSection('title', 'Thanh Toán Bàn Của Bạn'); ?>

<?php $__env->startSection('head'); ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    body { 
        background-color: #F9F8F6;
        padding: 50px 0; 
        font-family: 'Plus Jakarta Sans', sans-serif; 
    }
    
    .order-container { 
        background: #ffffff; 
        padding: 45px 50px; 
        border-radius: 12px; 
        box-shadow: 0 15px 40px rgba(0,0,0,0.08); 
        border-top: 5px solid #D4AF37;
    }

    .page-title {
        font-family: 'Playfair Display', serif; 
        color: #1A2228; 
        font-size: 32px; 
        text-align: center; 
        margin-bottom: 30px; 
        font-weight: 700;
        letter-spacing: 1px;
    }
    .page-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background: #D4AF37;
        margin: 15px auto 0;
    }

    .bill-info { 
        background: #FCFBF8; 
        padding: 25px; 
        border-radius: 8px; 
        border-left: 4px solid #D4AF37; 
        margin-bottom: 30px; 
        color: #4A5568;
    }
    .bill-info p { margin-bottom: 8px; }
    .bill-info strong { color: #1A2228; font-weight: 600; }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #E2E8F0;
    }
    .table-order { margin-bottom: 0; }
    .table-order thead { background-color: #1A2228; color: #FFF; }
    .table-order th { 
        font-weight: 600; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
        font-size: 13px; 
        border-bottom: none !important;
        padding: 15px;
    }
    .table-order td { vertical-align: middle; padding: 15px; border-color: #E2E8F0; }
    
    .item-img-mini { 
        width: 60px; 
        height: 60px; 
        object-fit: cover; 
        border-radius: 8px; 
        margin-right: 15px; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .qty-box {
        display: flex;
        justify-content: center;
        align-items: center;
        background: #F7FAFC;
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        padding: 3px 5px;
        width: fit-content;
        margin: 0 auto;
    }
    .qty-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: #4A5568;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }
    .qty-btn:hover { color: #D4AF37; }
    .qty-input {
        width: 35px;
        text-align: center;
        border: none;
        background: transparent;
        font-weight: 700;
        color: #1A2228;
    }

    .table-order tfoot th { background: #FCFBF8; font-family: 'Playfair Display', serif; }
    .total-amount-text { color: #D4AF37 !important; font-size: 1.6rem !important; font-weight: 700; }

    .btn-checkout { 
        background-color: #D4AF37; 
        color: #FFF; 
        padding: 16px; 
        border: none; 
        border-radius: 8px; 
        width: 100%; 
        max-width: 400px;
        font-size: 16px; 
        font-weight: 700; 
        letter-spacing: 2px;
        text-transform: uppercase; 
        margin-top: 20px; 
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    }
    .btn-checkout:hover { 
        background-color: #B5952F;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    }

    .payment-methods-box { 
        border: none; 
        padding: 30px; 
        border-radius: 12px; 
        background: #FAFAFA; 
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.02);
    }
    .payment-methods-box h5 {
        font-family: 'Playfair Display', serif;
        color: #1A2228;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .bank-transfer-card {
        border: 2px solid #D4AF37;
        border-radius: 10px;
        background: #FFF;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.08);
    }
    .bank-transfer-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    .bank-transfer-header i {
        font-size: 26px;
        color: #D4AF37;
        width: 40px;
        text-align: center;
    }
    .bank-transfer-header h6 {
        margin-bottom: 4px;
        font-weight: 700;
        color: #1A2228;
        font-size: 16px;
    }
    .bank-transfer-header small { color: #718096; font-size: 13px; }

    .bank-info-box {
        background: #F7FAFC;
        padding: 12px 16px;
        border-radius: 8px;
        display: inline-block;
        text-align: left;
        margin-top: 5px;
    }
    .bank-info-box p { color: #4A5568; margin-bottom: 4px; }
    .bank-info-box p:last-child { margin-bottom: 0; }.payment-item.active i { color: #D4AF37; }

    #qr-inline-area { 
        display: none; 
        margin-top: 15px; 
        padding: 25px; 
        background: #FFF; 
        border: 2px dashed #E2E8F0; 
        border-radius: 10px; 
        text-align: center; 
        animation: slideDown 0.4s ease-out;
    }
    #qr-inline-area h6 { color: #1A2228; letter-spacing: 1px; }
    #qr-inline-area img { max-width: 220px; border: 1px solid #E2E8F0; padding: 10px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);}
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .premium-bill {
        background: #FFFDF8;
        border-radius: 4px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1A2228;
    }
    .bill-header-gold { padding: 25px 20px 10px; text-align: center; }
    .restaurant-logo-center { width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin-bottom: 10px; border: 2px solid #D4AF37; padding: 2px;}
    .bill-title-luxury {
        font-family: 'Playfair Display', serif;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-weight: 700;
        color: #1A2228;
        font-size: 1.3rem;
        margin-bottom: 2px;
    }
    .luxury-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #D4AF37, transparent);
        margin: 20px 0;
    }
    .table-premium { width: 100%; font-size: 13px; line-height: 1.4; }
    .table-premium th { font-weight: 600; text-transform: uppercase; color: #718096; border-bottom: 1px solid #E2E8F0; padding: 8px 0; }
    .table-premium td { padding: 8px 0; border-bottom: 1px dashed #E2E8F0; }
    
    .total-highlight {
        background: #1A2228;
        color: #FFF;
        padding: 15px;
        border-radius: 6px;
        margin-top: 15px;
        text-align: right;
    }
    .total-highlight .h5 { color: #D4AF37; font-family: 'Playfair Display', serif; font-size: 1.4rem;}

    .btn-luxury-pay {
        background-color: #D4AF37; 
        color: white;
        border: none;
        padding: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        width: 100%;
        border-radius: 6px;
        margin-top: 20px;
        transition: 0.3s;
    }
    .btn-luxury-pay:hover { background-color: #1A2228; color: #D4AF37; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php
    $depositPercent = 20;
    $depositAmount = (int) round($total * $depositPercent / 100);
    $remainingAmount = $total - $depositAmount;

    $bankID      = config('services.bank.id');
    $accountNo   = config('services.bank.account');
    $accountName = config('services.bank.name'); 
    $info = "Thanh toan " . ($reservation['table'] ?? '0'); 
    $infoEncoded = urlencode($info);
    $nameEncoded = urlencode($accountName);
    $qrUrl = "https://img.vietqr.io/image/{$bankID}-{$accountNo}-compact.png?amount={$depositAmount}&addInfo={$infoEncoded}&accountName={$nameEncoded}";
    $invoiceNumber = 'INV-' . strtoupper(substr(md5(now()->timestamp . auth()->id()), 0, 8));
    $currentDateTime = now()->format('H:i d/m/Y');
?>

<div class="container">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 8px; border-left: 4px solid #e74c3c;">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="order-container">
        <h3 class="page-title">Hóa đơn của quý khách</h3>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <?php if($checkoutUnavailableMessage): ?>
            <div class="alert alert-warning shadow-sm" role="alert" aria-live="assertive">
                <strong><i class="fas fa-exclamation-triangle mr-2" aria-hidden="true"></i>Không thể đặt món lúc này.</strong>
                <?php echo e($checkoutUnavailableMessage); ?>

            </div>
        <?php endif; ?>

        <div class="bill-info">
    <div class="row">

        
        <div class="col-md-6">
            <p><strong>Mã phiếu:</strong> <?php echo e($invoiceNumber); ?></p>

            <p><strong>Thời gian:</strong> <?php echo e($currentDateTime); ?></p>

            <p>
                <strong>Lịch đặt:</strong>
                <?php echo e($reservation['time']); ?> - <?php echo e($reservation['date']); ?>

            </p>

            <p><strong>Khách hàng:</strong> <?php echo e($reservation['name']); ?></p>
        </div>

        
        <div class="col-md-6 text-md-right">

            <p>
                <strong>Vị trí:</strong>

                <span style="
                    background: #1A2228;
                    color: #D4AF37;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-weight: 600;
                    font-size: 13px;
                ">
                    <?php echo e($reservation['table']); ?>

                </span>
            </p>

            <p>
                <strong>Trạng thái:</strong>

                <span class="font-weight-bold" style="color: #D4AF37;">
                    <?php echo e($reservation['status']); ?>

                </span>
            </p>

            
            <?php
                $notesValue = $reservation['notes'] ?? '';

                $notesEmpty =
                    empty($notesValue)
                    || $notesValue === 'Không có yêu cầu đặc biệt'
                    || $notesValue === 'Chưa có ghi chú';
            ?>

            <?php if(!$notesEmpty): ?>
                <p>
                    <strong>Ghi chú:</strong>

                    <span style="
                        color: #D4AF37;
                        font-style: italic;
                    ">
                        <?php echo e($notesValue); ?>

                    </span>
                </p>
            <?php endif; ?>

        </div>

    </div>
</div>

        <div class="table-responsive mb-5">
            <table class="table table-order">
                <thead>
                    <tr>
                        <th width="40%">Chi tiết món ăn</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-right">Thành tiền</th>
                        <th class="text-center">Tùy chỉnh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if($item['quantity'] > 0): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo e(asset($item['image'] ?? 'images/default.jpg')); ?>" class="item-img-mini">
                                    <strong style="color: #1A2228;"><?php echo e($item['name'] ?? 'Món không tồn tại'); ?></strong>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="qty-box">
                                    <button type="button" class="qty-btn" onclick="changeQty(this, -1)">-</button>
                                    <input type="text" class="qty-input" data-id="<?php echo e($item['id']); ?>" value="<?php echo e($item['quantity']); ?>" readonly data-price="<?php echo e($item['price'] ?? 0); ?>" data-stock="<?php echo e(\App\Models\Menu::find($item['id'])->stock ?? 0); ?>">
                                    <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                                </div>
                            </td>
                            <td class="text-right" style="color: #718096;"><?php echo e(number_format($item['price'] ?? 0, 0, ',', '.')); ?>đ</td>
                            <td class="text-right font-weight-bold" style="color: #1A2228; font-size: 15px;">
                                <?php echo e(number_format(($item['price'] ?? 0) * $item['quantity'], 0, ',', '.')); ?>đ
                            </td>
                            <td class="text-center">
                                <form action="<?php echo e(route('cart.remove', $item['id'])); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm" style="background: #FEE2E2; color: #EF4444; border: none; padding: 6px 10px; border-radius: 6px;" onclick="return confirm('Bạn có chắc chắn muốn bỏ món này?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center py-4" style="color: #718096;">Danh sách thực đơn trống.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
    <tr>
        <th colspan="3" class="text-right" style="font-size: 15px; vertical-align: middle;">Tổng thanh toán:</th>
        <th class="text-right total-amount-text" id="grand-total-text">
            <?php echo e(number_format($total, 0, ',', '.')); ?> VNĐ
        </th>
        <th></th>
    </tr>
    <tr>
        <th colspan="3" class="text-right" style="font-size: 14px; color:#D4AF37;">Tiền cọc cần thanh toán (20%):</th>
        <th class="text-right" style="color:#D4AF37; font-size: 1.2rem; font-weight:700;" id="deposit-total-text">
            <?php echo e(number_format($depositAmount, 0, ',', '.')); ?> VNĐ
        </th>
        <th></th>
    </tr>
    <tr>
        <th colspan="3" class="text-right" style="font-size: 13px; color:#718096;">Còn lại thanh toán tại quầy:</th>
        <th class="text-right" style="color:#718096; font-size: 13px;" id="remaining-total-text">
            <?php echo e(number_format($remainingAmount, 0, ',', '.')); ?> VNĐ
        </th>
        <th></th>
    </tr>
</tfoot>
            </table>
        </div>

<?php if(count($cart)): ?>
<?php if($canCheckout): ?>
<form id="checkoutForm" action="<?php echo e(route('cart.checkout')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="total_price" value="<?php echo e($total); ?>">
    <input type="hidden" name="deposit_amount" id="deposit_amount_input" value="<?php echo e($depositAmount); ?>">
    <input type="hidden" name="table_number" value="<?php echo e($reservation['table_id']); ?>">
    <input type="hidden" name="order_notes" value="<?php echo e($reservation['notes'] ?? ''); ?>">

    <div class="payment-methods-box mt-2">
        <h5>
            <i class="fas fa-gem mr-2" style="color: #D4AF37;"></i> Phương Thức Đặt Cọc
        </h5>

        
        <input type="hidden" name="payment_method" value="BANK">

        <div class="bank-transfer-card">
            <div class="bank-transfer-header">
                <i class="fas fa-qrcode"></i>
                <div>
                    <h6>Chuyển Khoản Ngân Hàng</h6>
                    <small>Quét mã QR bên dưới để đặt cọc giữ bàn</small>
                </div>
            </div>

            <div id="qr-inline-area" style="display:block; text-align:center;">
                <h6 class="font-weight-bold mb-3">MÃ QR THANH TOÁN TỰ ĐỘNG</h6>
                <img id="qr-payment-img" src="<?php echo e($qrUrl); ?>" alt="QR Code Payment" class="img-fluid mb-3">
                <div class="bank-info-box">
                    <p class="small mb-1">Chủ tài khoản: <strong style="color:#1A2228;"><?php echo e($accountName); ?></strong></p>
                    <p class="small mb-0">Nội dung CK: <strong style="color:#D4AF37;"><?php echo e($info); ?></strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
       <button type="button" class="btn-checkout" data-toggle="modal" data-target="#billModal">
    Xác nhận đặt cọc <span id="checkout-deposit-text"><?php echo e(number_format($depositAmount, 0, ',', '.')); ?></span>đ
</button>
    </div>
</form> 
<?php else: ?>
<?php endif; ?>
<?php else: ?>
<div style="background: #FCFBF8; border: 2px solid #E2E8F0; border-radius: 12px; padding: 40px 30px; text-align: center; margin: 40px 0;">
    <p style="font-size: 18px; color: #1A2228; margin-bottom: 10px; font-weight: 600;">✨ Chỉ Đặt Bàn (Không Có Thực Đơn)</p>
    <p style="color: #718096; margin-bottom: 25px;">Quý khách có thể thêm món ăn hoặc tiếp tục chỉ với đặt bàn.</p>
    <div class="row" style="gap: 15px; justify-content: center; flex-wrap: wrap;">
        <a href="<?php echo e(route('menu.index')); ?>" class="btn-checkout" style="width: auto; max-width: 300px; text-decoration: none; display: inline-block;">
            <i class="fas fa-plus-circle mr-2"></i> THÊM MÓN ĂN
        </a>
    </div>
</div>
<?php endif; ?>
    </div>
</div>

<div class="modal fade" id="billModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content premium-bill">
            <div class="modal-body p-4">
                <div class="bill-header-gold">
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Aurora Garden" style="height: 115px; width: auto;">
                    <!-- <h5 class="bill-title-luxury">Aurora Garden</h5> -->
                    <p class="small text-muted mb-0" style="font-size: 12px;">Đỉnh Cao Ẩm Thực Truyền Thống</p>
                </div>
                
                <div class="luxury-divider"></div>
                
                <div class="mb-3" style="font-size: 13px; line-height: 1.6;">
                    <p class="mb-1"><span style="color: #718096; display: inline-block; width: 75px;">Số phiếu:</span> <strong><?php echo e($invoiceNumber); ?></strong></p>
                    <p class="mb-1"><span style="color: #718096; display: inline-block; width: 75px;">Thời gian:</span> <strong><?php echo e($currentDateTime); ?></strong></p>
                    <p class="mb-1"><span style="color: #718096; display: inline-block; width: 75px;">Phục vụ:</span> <strong style="color: #D4AF37;"><?php echo e($reservation['table'] ?? '1'); ?></strong></p>
                    <p class="mb-1"><span style="color: #718096; display: inline-block; width: 75px;">Thanh toán:</span> <strong id="bill-payment-method-text">Tại Bàn (Tiền mặt / POS)</strong></p>
                    
                    <?php if(!$notesEmpty): ?>
                        <p class="mb-1">
                            <span style="color: #718096; display: inline-block; width: 75px;">Ghi chú:</span> 
                            <strong style="color: #D4AF37; font-style: italic;"><?php echo e($notesValue); ?></strong>
                        </p>
                    <?php endif; ?>
                </div>

                <table class="table-premium mt-3">
                    <thead>
                        <tr>
                            <th class="text-left">Tên món</th>
                            <th class="text-center">SL</th>
                            <th class="text-right">Tổng</th>
                        </tr>
                    </thead>
                    <tbody id="bill-items">
                        <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item['quantity'] > 0): ?>
                            <tr>
                                <td style="font-weight: 500;"><?php echo e($item['name']); ?></td>
                                <td class="text-center"><?php echo e($item['quantity']); ?></td>
                                <td class="text-right"><?php echo e(number_format($item['price'] * $item['quantity'], 0, ',', '.')); ?>đ</td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                
               <div class="total-highlight">
    <div class="d-flex justify-content-between align-items-center">
        <span class="font-weight-bold" style="font-size: 14px; letter-spacing: 1px;">TỔNG HÓA ĐƠN</span>
        <span class="h5 mb-0 font-weight-bold" id="bill-total"><?php echo e(number_format($total, 0, ',', '.')); ?>đ</span>
    </div>
</div>
<div class="total-highlight" style="margin-top: 8px; background:#FFFDF8; color:#1A2228; border:1px dashed #D4AF37;">
    <div class="d-flex justify-content-between" style="font-size:13px;">
        <span>Đặt cọc (20%)</span><strong style="color:#D4AF37;" id="bill-deposit"><?php echo e(number_format($depositAmount, 0, ',', '.')); ?>đ</strong>
    </div>
    <div class="d-flex justify-content-between" style="font-size:13px; color:#718096;">
        <span>Còn lại tại quầy</span><span id="bill-remaining"><?php echo e(number_format($remainingAmount, 0, ',', '.')); ?>đ</span>
    </div>
</div>
                </div>
                
                <div class="text-center mt-4">
                    <p style="font-size: 12px; font-style: italic; color: #718096;" class="mb-0">Trân trọng cảm ơn Quý khách!</p>
                </div>
                
                <button type="button" class="btn-luxury-pay" onclick="submitOrder()">XÁC NHẬN THANH TOÁN</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Hằng số & hàm tạo QR: đặt ở phạm vi toàn cục để updateGrandTotal() và các hàm khác dùng được
        const QR_BANK_ID = "<?php echo e($bankID); ?>";
const QR_ACCOUNT_NO = "<?php echo e($accountNo); ?>";
const QR_ACCOUNT_NAME = "<?php echo e($nameEncoded); ?>";
const QR_INFO = "<?php echo e($infoEncoded); ?>";

function buildQrUrl(amount) {
    return `https://img.vietqr.io/image/${QR_BANK_ID}-${QR_ACCOUNT_NO}-compact.png?amount=${amount}&addInfo=${QR_INFO}&accountName=${QR_ACCOUNT_NAME}`;
}

    function changeQty(btn, value) {
        const row = btn.closest('tr');
        const input = row.querySelector('.qty-input');
        const stockAvailable = parseInt(input.dataset.stock);
        const menuId = input.dataset.id;
        
        let qty = parseInt(input.value);
        let newQty = qty + value;

        if (newQty < 0) return; 

        if (newQty === 0) {
            if (confirm('Bạn có chắc chắn muốn bỏ món này khỏi thực đơn?')) {
                row.remove();
                try { updateGrandTotal(); } catch (e) { console.error(e); }
            } else {
                return;
            }
        } else {
            if (value > 0 && newQty > stockAvailable) {
                alert('Hết hàng! Còn ' + stockAvailable + ' phần.');
                return;
            }
            input.value = newQty;
            try { updateRowTotal(row); updateGrandTotal(); } catch (e) { console.error(e); }
        }

        fetch('<?php echo e(route("cart.update")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ 
                id: menuId, 
                quantity: newQty
            })
        })
        .then(response => response.json())
        .then(data => {
            if(!data.success) {
                alert("Có lỗi xảy ra, vui lòng thử lại!");
                location.reload();
            }
        })
        .catch(error => console.error('Lỗi đồng bộ:', error));
    }

    function updateRowTotal(row) {
        const qty = parseInt(row.querySelector('.qty-input').value);
        const price = parseInt(row.querySelector('.qty-input').dataset.price);
        row.querySelector('.font-weight-bold').innerText = new Intl.NumberFormat('vi-VN').format(qty * price) + 'đ';
    }

   // Vẽ lại hoá đơn (modal) theo đúng các món đang có trong bảng, để luôn khớp khi bấm +/-
   function refreshBill(total, depositAmount, remainingAmount) {
    const fmt = n => new Intl.NumberFormat('vi-VN').format(n) + 'đ';
    const tbody = document.getElementById('bill-items');
    if (tbody) {
        tbody.innerHTML = '';
        document.querySelectorAll('.table-order tbody tr').forEach(row => {
            const input = row.querySelector('.qty-input');
            if (!input) return;
            const qty = parseInt(input.value);
            if (!(qty > 0)) return;
            const name = row.querySelector('strong') ? row.querySelector('strong').textContent : '';
            const tr = document.createElement('tr');
            const tdName = document.createElement('td');
            tdName.style.fontWeight = '500';
            tdName.textContent = name;                 // textContent: không chèn HTML (chống XSS)
            const tdQty = document.createElement('td');
            tdQty.className = 'text-center';
            tdQty.textContent = qty;
            const tdSum = document.createElement('td');
            tdSum.className = 'text-right';
            tdSum.textContent = fmt(qty * parseInt(input.dataset.price));
            tr.append(tdName, tdQty, tdSum);
            tbody.appendChild(tr);
        });
    }
    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = fmt(v); };
    set('bill-total', total);
    set('bill-deposit', depositAmount);
    set('bill-remaining', remainingAmount);
   }

   function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        total += parseInt(input.value) * parseInt(input.dataset.price);
    });

    const depositPercent = 20;
    const depositAmount = Math.round(total * depositPercent / 100);
    const remainingAmount = total - depositAmount;

    document.getElementById('grand-total-text').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
    document.getElementById('deposit-total-text').innerText = new Intl.NumberFormat('vi-VN').format(depositAmount) + ' VNĐ';
    const btnDeposit = document.getElementById('checkout-deposit-text');
    if (btnDeposit) btnDeposit.innerText = new Intl.NumberFormat('vi-VN').format(depositAmount);
    document.getElementById('remaining-total-text').innerText = new Intl.NumberFormat('vi-VN').format(remainingAmount) + ' VNĐ';

    refreshBill(total, depositAmount, remainingAmount);

    document.querySelector('input[name="total_price"]').value = total;
    document.getElementById('deposit_amount_input').value = depositAmount;

    const qrImg = document.getElementById('qr-payment-img');
    if (qrImg) {
        qrImg.src = buildQrUrl(depositAmount);
    }
}

    function submitOrder() {
        document.getElementById('checkoutForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const qrArea = document.getElementById('qr-inline-area');
        const labelCod = document.getElementById('label-cod');
        const labelBank = document.getElementById('label-bank');
        const radios = document.querySelectorAll('input[name="payment_method"]');

       function updateUI(value) {
    const methodText = document.getElementById('bill-payment-method-text');
    if (value === 'BANK') {
        qrArea.style.display = 'block';
        labelBank.classList.add('active');
        labelCod.classList.remove('active');
        if (methodText) methodText.innerText = 'Chuyển Khoản QR';

        const currentDeposit = document.getElementById('deposit_amount_input').value;
        document.getElementById('qr-payment-img').src = buildQrUrl(currentDeposit);
    } else {
        qrArea.style.display = 'none';
        labelCod.classList.add('active');
        labelBank.classList.remove('active');
        if (methodText) methodText.innerText = 'Tại Bàn (Tiền mặt / POS)';
    }
}
        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                updateUI(this.value);
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shared', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/cart/index.blade.php ENDPATH**/ ?>