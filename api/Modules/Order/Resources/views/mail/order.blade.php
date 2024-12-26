<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @import url('//fonts.googleapis.com/css?family=Montserrat:400,600');

    body {
      font-family: Montserrat, Arial, sans-serif;
      font-size: 15px;
      font-weight: 400;
      line-height: 1.5;
      color: #000;
      text-align: left;
      background-color: #fff;
    }
  </style>
</head>
<body style="padding: 0;margin: 0;">
<table border="0" cellpadding="0" cellspacing="0" style="width:600px;border-collapse: collapse;border-spacing: 0;" valign="top">
  <tbody>
  <tr>
    <td>
      <div style="background: url('https://motila.vn/assets/ord/email/header-bg.png') 0 0 no-repeat;background-size: cover;padding: 30px 0 30px 50px;border-bottom: 1px solid #eee;"><img src="https://motila.vn/assets/ord/email/logo.png"></div>
    </td>
  </tr>
  <tr>
    <td style="border-collapse: collapse;border-spacing: 0;padding: 30px 50px;">
      <h1 style="font-size: 24px; margin-top: 0; margin-bottom: 30px;"><strong>Thư thông báo Khách hàng</strong></h1>
      <div style="margin: 0;">
        <p>Chào bạn {!! $contact_name !!}</p>
        <p>Vui lòng xem phụ lục trong file đinh kèm #{!! $order_number !!}</p>
        {{--<p>Tình trạng Báo giá: {order_status}</p>
        <p>Bạn có thể xem báo giá online tại đây: {order_number}</p>--}}
        <p>Trân trọng,{{--<br>{email_signature}--}}</p>
      </div>
    </td>
  </tr>
  <tr>
    <td style="border-collapse: collapse;border-spacing: 0;background: #272727;color: #fff;padding: 30px 50px;">
      <table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse: collapse;border-spacing: 0;line-height: 1.5;" valign="top">
        <tbody>
        <tr>
          <td style="border-collapse: collapse;border-spacing: 0;border-left: 3px solid #E73C56;padding-left: 10px;">Sincerely,
            <p style="margin: 6px 0 0 0;font-size: 18px;font-weight: 600;">Namthi corporation</p></td>
          <td style="border-collapse: collapse;border-spacing: 0;vertical-align: top;width: 150px;font-size: 13px;"><img src="https://motila.vn/assets/ord/email/phone.png"> +84 28 2220 2053<br>
            <img src="https://motila.vn/assets/ord/email/email.png"> <a style="color: #fff;text-decoration: none;" href="mailto:info@sweetgirlbeauty.com" target="_blank">info@sweetgirlbeauty.com</a><br>
            <img src="https://motila.vn/assets/ord/email/website.png"> <a style="color: #fff;text-decoration: none;" href="https://sweetgirlbeauty.com" target="_blank">sweetgirlbeauty.com</a>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  <tr>
    <td style="border-collapse: collapse;border-spacing: 0;text-align: center;padding: 20px 0;">Đây là email tự động. Xin vui lòng không gửi phản hồi vào hộp thư này<br>
      © 2020 Namthi Corp. All Rights Reserved.
    </td>
  </tr>
  </tbody>
</table>
</body>
</html>

