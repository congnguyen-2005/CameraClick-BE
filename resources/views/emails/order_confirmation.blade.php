<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng Alpha</title>
    <style>
        /* Reset & Base Styles */
        body { margin: 0; padding: 0; background-color: #f4f4f4; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        table { border-collapse: collapse; width: 100%; }
        img { border: 0; outline: none; text-decoration: none; }
        
        /* Typography */
        .ls-1 { letter-spacing: 1px; }
        .ls-2 { letter-spacing: 2px; }
        .fw-900 { font-weight: 900; }
        
        /* Layout */
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f4f4; padding-bottom: 60px; }
        .main-container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .column { width: 100% !important; display: block !important; padding-left: 0 !important; border-left: none !important; border-right: none !important; border-bottom: 1px solid #eeeeee; padding-bottom: 20px; }
            .column-last { border-bottom: none !important; padding-top: 20px; }
            .header-padding { padding: 40px 20px !important; }
            .content-padding { padding: 30px 20px !important; }
            .total-text { font-size: 24px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <center class="wrapper">
        <table class="main-container" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-padding" style="background-color: #000000; padding: 60px 40px; text-align: center;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 900; letter-spacing: 6px; text-transform: uppercase;">CamStore</h1>
                    <div style="width: 30px; height: 1px; background-color: #CC6600; margin: 20px auto;"></div>
                    <p style="color: #888888; margin: 0; font-size: 11px; text-transform: uppercase; letter-spacing: 3px;">Hệ thống thiết bị hình ảnh Alpha</p>
                </td>
            </tr>

            <tr>
                <td class="content-padding" style="padding: 50px 50px 20px;">
                    <h2 style="color: #111111; margin: 0 0 15px; font-size: 22px; font-weight: 400;">Kính chào {{ $order->name }},</h2>
                    <p style="color: #666666; line-height: 1.8; margin: 0; font-size: 15px;">
                        Yêu cầu sở hữu thiết bị của bạn đã được ghi nhận vào hệ thống. Đơn hàng <strong style="color: #000000;">#{{ $order->id }}</strong> đang trong giai đoạn kiểm tra tiêu chuẩn trước khi chuyển phát.
                    </p>
                </td>
            </tr>

            <tr>
                <td class="content-padding" style="padding: 20px 50px;">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee; padding: 30px 0;">
                        <tr>
                            <td class="column" width="50%" valign="top" style="border-right: 1px solid #eeeeee; padding-right: 20px;">
                                <p style="margin: 0 0 10px; color: #aaaaaa; font-size: 10px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px;">Thông tin định danh</p>
                                <p style="margin: 0; color: #111111; font-weight: bold; font-size: 14px;">Đơn hàng: #{{ $order->id }}</p>
                                <p style="margin: 5px 0 0; color: #666666; font-size: 13px;">{{ date('d.m.Y - H:i') }}</p>
                                <p style="margin: 15px 0 0; color: #CC6600; font-weight: bold; font-size: 12px; text-transform: uppercase;">COD - Thanh toán tiền mặt</p>
                            </td>
                            <td class="column column-last" width="50%" valign="top" style="padding-left: 30px;">
                                <p style="margin: 0 0 10px; color: #aaaaaa; font-size: 10px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px;">Địa điểm bàn giao</p>
                                <p style="margin: 0; color: #111111; font-weight: bold; font-size: 14px;">{{ $order->name }}</p>
                                <p style="margin: 5px 0 0; color: #666666; font-size: 13px; line-height: 1.5;">{{ $order->address }}</p>
                                <p style="margin: 5px 0 0; color: #000000; font-size: 13px; font-weight: bold;">{{ $order->phone }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td class="content-padding" style="padding: 10px 50px;">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th align="left" style="padding: 20px 0; font-size: 11px; text-transform: uppercase; color: #aaaaaa; letter-spacing: 1px; border-bottom: 1px solid #f5f5f5;">Mô tả thiết bị</th>
                                <th align="center" style="padding: 20px 0; font-size: 11px; text-transform: uppercase; color: #aaaaaa; letter-spacing: 1px; border-bottom: 1px solid #f5f5f5;">SL</th>
                                <th align="right" style="padding: 20px 0; font-size: 11px; text-transform: uppercase; color: #aaaaaa; letter-spacing: 1px; border-bottom: 1px solid #f5f5f5;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderDetails as $detail)
                            <tr>
                                <td style="padding: 20px 0; border-bottom: 1px solid #f5f5f5;">
                                    <p style="margin: 0; color: #111111; font-weight: bold; font-size: 14px;">{{ $detail->product->name }}</p>
                                    <p style="margin: 5px 0 0; color: #999999; font-size: 12px; font-style: italic;">Đơn giá: {{ number_format($detail->price, 0, ',', '.') }}₫</p>
                                </td>
                                <td align="center" style="padding: 20px 0; border-bottom: 1px solid #f5f5f5; color: #666666; font-size: 14px;">
                                    {{ $detail->qty }}
                                </td>
                                <td align="right" style="padding: 20px 0; border-bottom: 1px solid #f5f5f5; color: #111111; font-weight: bold; font-size: 14px;">
                                    {{ number_format($detail->price * $detail->qty, 0, ',', '.') }}₫
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>

            <tr>
                <td class="content-padding" style="padding: 40px 50px;">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center" style="background-color: #000000; padding: 40px; border-radius: 4px;">
                                <p style="margin: 0 0 10px; font-size: 11px; color: #888888; text-transform: uppercase; letter-spacing: 2px;">Giá trị đơn hàng hoàn tất</p>
                                <p class="total-text" style="margin: 0; font-size: 32px; color: #CC6600; font-weight: 900; letter-spacing: 1px;">
                                    {{ number_format($order->total_money, 0, ',', '.') }}₫
                                </p>
                                <div style="width: 20px; height: 1px; background-color: #444444; margin: 20px auto;"></div>
                                <p style="margin: 0; font-size: 11px; color: #666666; font-style: italic;">Phí vận chuyển & VAT đã được áp dụng</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td align="center" style="padding: 0 50px 50px;">
                    <a href="http://localhost:3000/orders/{{ $order->id }}" target="_blank"
                        style="background-color: transparent; color: #111111; border: 1px solid #111111; text-decoration: none; padding: 15px 40px; font-weight: bold; display: inline-block; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; transition: all 0.3s;">
                        Truy xuất đơn hàng
                    </a>
                </td>
            </tr>

            <tr>
                <td style="background-color: #fafafa; padding: 60px 50px; text-align: center; border-top: 1px solid #eeeeee;">
                    <p style="margin: 0 0 15px; font-weight: 900; color: #111111; letter-spacing: 3px; text-transform: uppercase; font-size: 15px;">CamStore</p>
                    <p style="margin: 0; font-size: 12px; color: #999999; line-height: 2; letter-spacing: 0.5px;">
                        Hỗ trợ kỹ thuật: 1900 xxxx<br>
                        Email: concierge@camstore.com<br>
                        Địa chỉ: 123 Alpha Avenue, High-Tech District, HCMC
                    </p>
                    <div style="margin-top: 40px; font-size: 10px; color: #cccccc; text-transform: uppercase; letter-spacing: 2px;">
                        &copy; {{ date('Y') }} CamStore Alpha System.
                    </div>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>