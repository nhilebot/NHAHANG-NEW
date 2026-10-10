<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác Nhận Đơn Hàng</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f8f6; color: #1a2228; padding: 20px; }
        .email-container { background: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 8px; border-top: 5px solid #D4AF37; padding: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { color: #1a2228; text-align: center; font-family: 'Playfair Display', serif; }
        .divider { height: 1px; background: #e2e8f0; margin: 20px 0; }
        .info-box { background: #fcfbf8; padding: 15px; border-left: 4px solid #D4AF37; margin-bottom: 20px; }
        .info-box p { margin: 5px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #1a2228; color: #ffffff; text-align: left; padding: 10px; font-size: 13px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px dashed #e2e8f0; font-size: 14px; }
        .total-row { font-weight: bold; background: #fcfbf8; color: #D4AF37; font-size: 16px; }
        .footer { text-align: center; font-size: 12px; color: #718096; margin-top: 30px; font-style: italic; }
    </style>
</head>
<body>

<div class="email-container">
    <h2>NHÀ HÀNG AURORA GARDEN</h2>
    <p style="text-align: center; font-size: 13px; color: #718096; margin-top: -10px;">Cảm ơn quý khách đã đặt bàn và thực đơn tại nhà hàng chúng tôi!</p>
    
    <div class="divider"></div>

    <div class="info-box">
        <p><strong>Mã phiếu hóa đơn:</strong> {{ $mailData['invoice'] }}</p>
        <p><strong>Vị trí phục vụ:</strong> Bàn số {{ $mailData['table'] ?? 'Chưa xếp' }}</p>
        <p><strong>Trạng thái thanh toán:</strong> Đã xác nhận hệ thống thành công</p>
    </div>

    <h3>Chi Tiết Thực Đơn Đã Đặt:</h3>
    <table>
        <thead>
            <tr>
                <th>Tên món</th>
                <th style="text-align: center;">SL</th>
                <th style="text-align: right;">Đơn giá</th>
                <th style="text-align: right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mailData['cart'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td style="text-align: center;">{{ $item['quantity'] }}</td>
                    <td style="text-align: right;">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                    <td style="text-align: right;">{{ number_format($item['total'], 0, ',', '.') }}đ</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding: 15px;">TỔNG HÓA ĐƠN:</td>
                <td style="text-align: right; padding: 15px;">{{ number_format($mailData['total'], 0, ',', '.') }}đ</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Chúc quý khách có một trải nghiệm ẩm thực tuyệt vời !</p>
        <p>Mọi thắc mắc xin liên hệ Hotline: 0899.111.390</p>
    </div>
</div>

</body>
</html>