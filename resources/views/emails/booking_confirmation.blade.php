<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Xác nhận đặt bàn chính thức - Aurora Garden</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f3ee; font-family:Arial, Helvetica, sans-serif; color:#252525;">

@php
    $total = (int) ($booking->total_price ?? 0);
    $deposit = min((int) ($booking->deposit_amount ?? 0), $total);
    $remaining = max(0, $total - $deposit);

    $reservationDate = $booking->reservation_date
        ? \Illuminate\Support\Carbon::parse($booking->reservation_date)->format('d/m/Y')
        : 'Chưa cập nhật';

    $reservationTime = $booking->reservation_time
        ? \Illuminate\Support\Carbon::parse($booking->reservation_time)->format('h:i A')
        : 'Chưa cập nhật';
@endphp

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
                            YOUR TABLE IS CONFIRMED
                        </p>

                        <h1 style="margin:0; color:#ffffff; font-size:25px; line-height:1.4; letter-spacing:1px;">
                            AURORA GARDEN
                        </h1>

                        <p style="margin:8px 0 0; color:#e2dfd5; font-size:12px; line-height:1.6;">
                            Tinh hoa ẩm thực · Trọn vẹn trải nghiệm
                        </p>
                    </td>
                </tr>

                <!-- Nội dung -->
                <tr>
                    <td style="padding:28px 24px 12px;">

                        <p style="margin:0 0 10px; color:#777777; font-size:13px;">
                            KÍNH GỬI QUÝ KHÁCH
                        </p>

                        <h2 style="margin:0 0 16px; color:#252525; font-size:23px; line-height:1.4;">
                            Xin chào {{ $booking->full_name }}!
                        </h2>

                        <p style="margin:0 0 18px; color:#555555; font-size:14px; line-height:1.8;">
                            Tin vui dành cho quý khách! Nhân viên Aurora Garden đã xác minh khoản tiền cọc thành công.
                            <strong style="color:#277447;">
                                Yêu cầu đặt bàn của quý khách đã được xác nhận chính thức.
                            </strong>
                        </p>

                        <!-- Trạng thái xác nhận -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="width:100%; background-color:#f0f8f1; border:1px solid #cce7d0; border-radius:8px;">
                            <tr>
                                <td align="center" style="padding:18px 14px;">
                                    <p style="margin:0 0 8px; color:#277447; font-size:15px; font-weight:bold; line-height:1.5;">
                                        &#10003; ĐẶT BÀN ĐÃ ĐƯỢC XÁC NHẬN
                                    </p>
                                    <p style="margin:0; color:#47654d; font-size:13px; line-height:1.7;">
                                        Nhà hàng đã ghi nhận thông tin đặt bàn của quý khách.
                                        Hẹn gặp quý khách tại Aurora Garden!
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Thông tin đặt bàn -->
                        <h3 style="margin:28px 0 14px; padding-bottom:10px; border-bottom:1px solid #eee8d9; color:#252525; font-size:17px;">
                            THÔNG TIN ĐẶT BÀN
                        </h3>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="width:100%; border-collapse:collapse;">

                            <tr>
                                <td width="38%" valign="top"
                                    style="padding:12px 8px 12px 0; border-bottom:1px solid #f0eee9; color:#777777; font-size:13px;">
                                    Mã đặt bàn
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; border-bottom:1px solid #f0eee9; color:#a07812; font-size:13px; font-weight:bold; overflow-wrap:anywhere;">
                                    #{{ $booking->id }}
                                </td>
                            </tr>

                            <tr>
                                <td valign="top"
                                    style="padding:12px 8px 12px 0; border-bottom:1px solid #f0eee9; color:#777777; font-size:13px;">
                                    Bàn đã đặt
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; border-bottom:1px solid #f0eee9; color:#252525; font-size:13px; font-weight:bold; overflow-wrap:anywhere;">
                                    {{ $booking->table->name ?? ('Số ' . $booking->table_id) }}
                                </td>
                            </tr>

                            <tr>
                                <td valign="top"
                                    style="padding:12px 8px 12px 0; border-bottom:1px solid #f0eee9; color:#777777; font-size:13px;">
                                    Ngày đến
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; border-bottom:1px solid #f0eee9; color:#252525; font-size:13px;">
                                    {{ $reservationDate }}
                                </td>
                            </tr>

                            <tr>
                                <td valign="top"
                                    style="padding:12px 8px 12px 0; border-bottom:1px solid #f0eee9; color:#777777; font-size:13px;">
                                    Giờ đến
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; border-bottom:1px solid #f0eee9; color:#252525; font-size:13px;">
                                    {{ $reservationTime }}
                                </td>
                            </tr>

                            <tr>
                                <td valign="top"
                                    style="padding:12px 8px 12px 0; color:#777777; font-size:13px;">
                                    Trạng thái
                                </td>
                                <td valign="top"
                                    style="padding:12px 0; color:#277447; font-size:13px; font-weight:bold;">
                                    Đã xác nhận chính thức
                                </td>
                            </tr>
                        </table>

                        <!-- Chi phí -->
                        <h3 style="margin:28px 0 14px; padding-bottom:10px; border-bottom:1px solid #eee8d9; color:#252525; font-size:17px;">
                            CHI TIẾT THANH TOÁN
                        </h3>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="width:100%; background-color:#faf9f6; border-radius:8px;">

                            <tr>
                                <td style="padding:14px 12px; color:#555555; font-size:13px; line-height:1.6;">
                                    Tổng tiền thực đơn
                                </td>
                                <td align="right"
                                    style="padding:14px 12px; color:#252525; font-size:14px; font-weight:bold; white-space:nowrap;">
                                    {{ number_format($total, 0, ',', '.') }}đ
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="padding:0 12px;">
                                    <div style="border-top:1px dashed #ded8c8;"></div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 12px; color:#555555; font-size:13px; line-height:1.6;">
                                    Tiền cọc đã xác minh
                                </td>
                                <td align="right"
                                    style="padding:14px 12px; color:#277447; font-size:14px; font-weight:bold; white-space:nowrap;">
                                    {{ number_format($deposit, 0, ',', '.') }}đ
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="padding:0 12px;">
                                    <div style="border-top:1px dashed #ded8c8;"></div>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:16px 12px; color:#252525; font-size:13px; line-height:1.6; font-weight:bold;">
                                    Số tiền còn lại
                                </td>
                                <td align="right"
                                    style="padding:16px 12px; color:#a07812; font-size:20px; font-weight:bold; white-space:nowrap;">
                                    {{ number_format($remaining, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        </table>

                        <p style="margin:12px 0 0; color:#888888; font-size:11px; line-height:1.7;">
                            Số tiền còn lại được tính bằng tổng tiền thực đơn trừ tiền cọc đã xác minh.
                            Quý khách vui lòng kiểm tra thông tin thanh toán khi đến nhà hàng.
                        </p>

                        <!-- Thực đơn -->
                        <h3 style="margin:28px 0 14px; padding-bottom:10px; border-bottom:1px solid #eee8d9; color:#252525; font-size:17px;">
                            THỰC ĐƠN ĐÃ ĐẶT
                        </h3>

                        @if (!empty($booking->cart_data))
                            @foreach ($booking->cart_data as $item)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                       style="width:100%; border-bottom:1px solid #f0eee9;">
                                    <tr>
                                        <td valign="top" style="padding:12px 8px 12px 0;">
                                            <p style="margin:0 0 5px; color:#333333; font-size:13px; line-height:1.6; font-weight:bold; overflow-wrap:anywhere;">
                                                {{ $item['name'] ?? 'Món ăn' }}
                                            </p>
                                            <p style="margin:0; color:#888888; font-size:12px;">
                                                Số lượng: {{ $item['quantity'] ?? 0 }}
                                            </p>
                                        </td>
                                        <td align="right" valign="top"
                                            style="padding:12px 0; color:#333333; font-size:13px; font-weight:bold; white-space:nowrap;">
                                            {{ number_format((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0), 0, ',', '.') }}đ
                                            <p style="margin:5px 0 0; color:#888888; font-size:11px; font-weight:normal;">
                                                {{ number_format((float) ($item['price'] ?? 0), 0, ',', '.') }}đ/món
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endforeach

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                   style="width:100%; background-color:#faf9f6;">
                                <tr>
                                    <td style="padding:15px 10px; color:#252525; font-size:13px; font-weight:bold;">
                                        TỔNG TIỀN THỰC ĐƠN
                                    </td>
                                    <td align="right"
                                        style="padding:15px 10px; color:#a07812; font-size:15px; font-weight:bold; white-space:nowrap;">
                                        {{ number_format($total, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            </table>
                        @else
                            <p style="margin:0; color:#777777; font-size:13px; line-height:1.8;">
                                Quý khách chưa chọn món trước. Nhà hàng sẽ hỗ trợ quý khách khi đến dùng bữa.
                            </p>
                        @endif

                        <!-- Lời kết -->
                        <p style="margin:26px 0 10px; color:#555555; font-size:13px; line-height:1.8;">
                            Quý khách vui lòng đến đúng thời gian đã đặt để nhà hàng phục vụ chu đáo nhất.
                            Nếu cần hỗ trợ hoặc thay đổi thông tin đặt bàn, vui lòng liên hệ nhà hàng qua kênh hỗ trợ chính thức.
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
                            © {{ date('Y') }} Aurora Garden. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
