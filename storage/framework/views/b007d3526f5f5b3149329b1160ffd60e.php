<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Đã tiếp nhận yêu cầu đặt bàn - Aurora Garden</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f3ee; font-family:Arial, Helvetica, sans-serif; color:#252525;">

<?php
    $total = (int) ($booking->total_price ?? 0);
    $deposit = (int) ($booking->deposit_amount ?? 0);

    $reservationDate = $booking->reservation_date
        ? \Illuminate\Support\Carbon::parse($booking->reservation_date)->format('d/m/Y')
        : 'Chưa cập nhật';

    $reservationTime = $booking->reservation_time
        ? \Illuminate\Support\Carbon::parse($booking->reservation_time)->format('h:i A')
        : 'Chưa cập nhật';
?>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="width:100%; background-color:#f5f3ee;">
    <tr>
        <td align="center" style="padding:24px 10px;">

            <!-- Khung email -->
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
                   style="width:100%; max-width:600px; background-color:#ffffff; border-collapse:separate; border-spacing:0; border-radius:10px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td align="center"
                        style="padding:30px 20px 26px; background-color:#1d2021; border-bottom:4px solid #d4af37;">

                        <p style="margin:0 0 8px; color:#d4af37; font-size:11px; letter-spacing:3px; font-weight:bold;">
                            WELCOME TO
                        </p>

                        <h1 style="margin:0; color:#ffffff; font-size:25px; line-height:1.4; letter-spacing:1px;">
                            AURORA GARDEN
                        </h1>

                        <p style="margin:8px 0 0; color:#e2dfd5; font-size:12px; line-height:1.6;">
                            Tinh hoa ẩm thực · Trọn vẹn trải nghiệm
                        </p>
                    </td>
                </tr>

                <!-- Nội dung chính -->
                <tr>
                    <td style="padding:28px 24px 12px;">

                        <p style="margin:0 0 10px; color:#777777; font-size:13px;">
                            KÍNH GỬI QUÝ KHÁCH
                        </p>

                        <h2 style="margin:0 0 16px; color:#252525; font-size:23px; line-height:1.4;">
                            Xin chào <?php echo e($booking->full_name); ?>!
                        </h2>

                        <p style="margin:0 0 14px; font-size:14px; line-height:1.8; color:#555555;">
                            Aurora Garden đã tiếp nhận yêu cầu đặt bàn của quý khách. Cảm ơn quý khách đã lựa chọn nhà hàng cho trải nghiệm ẩm thực sắp tới.
                        </p>

                        <!-- Trạng thái -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="width:100%; background-color:#fff9e8; border:1px solid #f0dfab; border-radius:8px;">
                            <tr>
                                <td style="padding:16px;">
                                    <p style="margin:0 0 8px; color:#856404; font-size:14px; font-weight:bold;">
                                        &#9203; YÊU CẦU ĐANG CHỜ XÁC MINH
                                    </p>

                                    <p style="margin:0; color:#695b36; font-size:13px; line-height:1.8;">
                                        Quý khách vui lòng chuyển khoản tiền cọc theo hướng dẫn của nhà hàng. Nhân viên sẽ kiểm tra khoản thanh toán và gửi email xác nhận đặt bàn chính thức.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <h3 style="margin:28px 0 14px; padding-bottom:10px; border-bottom:1px solid #eee8d9; color:#252525; font-size:17px;">
                            THÔNG TIN ĐẶT BÀN
                        </h3>

                        <!-- Thông tin đặt bàn -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="width:100%; border-collapse:collapse;">

                            <tr>
                                <td width="38%" valign="top"
                                    style="padding:12px 8px 12px 0; border-bottom:1px solid #f0eee9; color:#777777; font-size:13px;">
                                    Mã yêu cầu
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; border-bottom:1px solid #f0eee9; color:#252525; font-size:13px; font-weight:bold; overflow-wrap:anywhere;">
                                    #<?php echo e($booking->id); ?>

                                </td>
                            </tr>

                            <tr>
                                <td valign="top"
                                    style="padding:12px 8px 12px 0; border-bottom:1px solid #f0eee9; color:#777777; font-size:13px;">
                                    Bàn đã chọn
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; border-bottom:1px solid #f0eee9; color:#252525; font-size:13px; font-weight:bold; overflow-wrap:anywhere;">
                                    <?php echo e($booking->table->name ?? ('Số ' . $booking->table_id)); ?>

                                </td>
                            </tr>

                            <tr>
                                <td valign="top"
                                    style="padding:12px 8px 12px 0; border-bottom:1px solid #f0eee9; color:#777777; font-size:13px;">
                                    Ngày đặt bàn
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; border-bottom:1px solid #f0eee9; color:#252525; font-size:13px;">
                                    <?php echo e($reservationDate); ?>

                                </td>
                            </tr>

                            <tr>
                                <td valign="top"
                                    style="padding:12px 8px 12px 0; border-bottom:1px solid #f0eee9; color:#777777; font-size:13px;">
                                    Giờ đến
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; border-bottom:1px solid #f0eee9; color:#252525; font-size:13px;">
                                    <?php echo e($reservationTime); ?>

                                </td>
                            </tr>

                            <tr>
                                <td valign="top"
                                    style="padding:12px 8px 12px 0; color:#777777; font-size:13px;">
                                    Trạng thái
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; color:#a07812; font-size:13px; font-weight:bold;">
                                    Chờ thanh toán và xác minh
                                </td>
                            </tr>
                        </table>

                        <!-- Chi phí -->
                        <h3 style="margin:28px 0 14px; padding-bottom:10px; border-bottom:1px solid #eee8d9; color:#252525; font-size:17px;">
                            CHI PHÍ ĐẶT BÀN
                        </h3>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="width:100%; background-color:#faf9f6; border-radius:8px;">

                            <tr>
                                <td style="padding:15px 12px; color:#555555; font-size:13px; line-height:1.6;">
                                    Tổng tiền thực đơn
                                </td>
                                <td align="right"
                                    style="padding:15px 12px; color:#252525; font-size:14px; font-weight:bold; white-space:nowrap;">
                                    <?php echo e(number_format($total, 0, ',', '.')); ?>đ
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="padding:0 12px;">
                                    <div style="border-top:1px dashed #ded8c8;"></div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:16px 12px; color:#555555; font-size:13px; line-height:1.6;">
                                    Tiền cọc cần chuyển
                                    <p style="margin:4px 0 0; color:#888888; font-size:11px;">
                                        Theo mức cọc 20%
                                    </p>
                                </td>
                                <td align="right"
                                    style="padding:16px 12px; color:#a07812; font-size:20px; font-weight:bold; white-space:nowrap;">
                                    <?php echo e(number_format($deposit, 0, ',', '.')); ?>đ
                                </td>
                            </tr>
                        </table>

                        <!-- Thực đơn -->
                        <?php if(!empty($booking->cart_data)): ?>
                            <h3 style="margin:28px 0 14px; padding-bottom:10px; border-bottom:1px solid #eee8d9; color:#252525; font-size:17px;">
                                THỰC ĐƠN ĐÃ CHỌN
                            </h3>

                            <?php $__currentLoopData = $booking->cart_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                       style="width:100%; border-bottom:1px solid #f0eee9;">
                                    <tr>
                                        <td valign="top" style="padding:12px 8px 12px 0;">
                                            <p style="margin:0 0 5px; color:#333333; font-size:13px; line-height:1.6; font-weight:bold; overflow-wrap:anywhere;">
                                                <?php echo e($item['name'] ?? 'Món ăn'); ?>

                                            </p>
                                            <p style="margin:0; color:#888888; font-size:12px;">
                                                Số lượng: <?php echo e($item['quantity'] ?? 0); ?>

                                            </p>
                                        </td>
                                        <td align="right" valign="top"
                                            style="padding:12px 0; color:#333333; font-size:13px; font-weight:bold; white-space:nowrap;">
                                            <?php echo e(number_format((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0), 0, ',', '.')); ?>đ
                                            <p style="margin:5px 0 0; color:#888888; font-size:11px; font-weight:normal;">
                                                <?php echo e(number_format((float) ($item['price'] ?? 0), 0, ',', '.')); ?>đ/món
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                        <p style="margin:24px 0 10px; color:#555555; font-size:13px; line-height:1.8;">
                            Nếu quý khách đã chuyển khoản, vui lòng chờ nhân viên kiểm tra và xác nhận. Vui lòng chưa xem yêu cầu này là một đặt bàn đã được xác nhận chính thức.
                        </p>

                        <p style="margin:0 0 26px; color:#555555; font-size:13px; line-height:1.8;">
                            Trân trọng,<br>
                            <strong style="color:#a07812;">Đội ngũ Aurora Garden</strong>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center"
                        style="padding:22px 20px; background-color:#1d2021; border-top:3px solid #d4af37;">

                        <p style="margin:0 0 8px; color:#d4af37; font-size:14px; font-weight:bold; letter-spacing:1px;">
                            AURORA GARDEN
                        </p>

                        <p style="margin:0; color:#d0d0d0; font-size:11px; line-height:1.8;">
                            Cảm ơn quý khách đã tin tưởng lựa chọn chúng tôi.<br>
                            Email này được gửi tự động từ hệ thống đặt bàn.
                        </p>

                        <p style="margin:14px 0 0; color:#888888; font-size:10px;">
                            © <?php echo e(date('Y')); ?> Aurora Garden. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
<?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/emails/booking_pending_deposit.blade.php ENDPATH**/ ?>