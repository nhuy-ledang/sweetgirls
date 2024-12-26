@extends('order::layouts.customer', ['config_owner' => $config_owner, 'setting' => $setting])

@section('content')
  <tr>
    <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;color: #454545;padding: 15px 0 0;">
      <strong>ĐỔI MẬT KHẨU</strong>
    </td>
  </tr>
  <tr>
    <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;">
      <hr style="width: 70%; border: none; border-bottom: 1px solid #e5e5e5 !important;">
    </td>
  </tr>
  <tr>
    <td style="border-collapse: collapse;border-spacing: 0;padding: 30px 50px; text-align: center">
      <table style="background:#fff;border-collapse:collapse;border-spacing:0;padding:0;vertical-align:top;width:100%">
        <tr style="padding:0;text-align:left;vertical-align:top">
          <th style="font-size:16px;line-height:1.3;margin:0;padding-top:30px;padding-bottom:30px;text-align:center">
            <p style="color:#000000;font-size:15px;font-weight:400;line-height:23px;margin:0 auto;text-align:center;display:block">Quý khách vui lòng bấm vào "Đổi mật khẩu" để tạo mật khẩu mới</p>
          </th>
        </tr>
      </table>
      <a href="{!! $reset_link !!}" target="_blank" style="display: inline-block;text-align: center; white-space: nowrap;vertical-align: middle;padding: .375rem .75rem;border-radius: 5px;cursor: pointer;background-color: #343a40;color: #fff;border: 1px solid #343a40;text-decoration: none;">ĐỔI MẬT KHẨU</a>
      <table style="background:#fff;border-collapse:collapse;border-spacing:0;padding:0;vertical-align:top;width:100%">
        <tr style="padding:0;text-align:left;vertical-align:top">
          <th style="font-size:16px;line-height:1.3;margin:0;padding-top:30px;padding-bottom:30px;text-align:center">
            <p style="color:#919499;font-size:15px;font-weight:400;line-height:23px;margin:0 auto;text-align:center;display:block">Nếu không truy cập được, xin vui lòng copy đường dẫn dưới đây: <a href="{!! $reset_link !!}" target="_blank" style="color:#c5a25d;font-size:14px;font-weight:400;line-height:24px;margin:0;padding:0;text-align:left;text-decoration:none"> </br>{!! $reset_link !!}</a></p>
          </th>
        </tr>
      </table>
    </td>
  </tr>
@endsection
