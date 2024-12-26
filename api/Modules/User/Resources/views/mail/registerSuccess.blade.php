@extends('user::layouts.mail', ['data'=>$data])
@section('content')
<table style="background:#fff;border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;vertical-align:top;width:100%">
  <tr style="padding:0;text-align:center;vertical-align:top">
    <td style="border-collapse:collapse!important;color:#0a0a0a;margin:0;padding:0;padding-top:40px;vertical-align:top;word-wrap:break-word;line-height:0">
      <h1 style="font-size:20px;font-weight:700;line-height:1.3;margin:0;padding:0;text-align:center;word-wrap:normal">Chào mừng bạn đã đăng ký tài khoản thành công</h1>
      {{--<h1 style="font-size:20px;font-weight:700;line-height:1.3;margin:0;margin-bottom:30px;padding:0;text-align:center;word-wrap:normal">Chào mừng bạn đã đăng ký tài khoản thành công</h1>
      <table style="border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:auto;">
        <tr style="padding:0;text-align:left;vertical-align:top">
          <td style="border-collapse:collapse!important;font-size:16px;line-height:1.3;margin:0;padding:10px;text-align:left;vertical-align:top;word-wrap:break-word">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
              <tr style="padding:0;text-align:left;vertical-align:top">
                <td style="background:#007bfc;border-collapse:collapse!important;color:#fefefe;font-size:15px;line-height:1.3;margin:0;padding:8px 15px;text-align:left;vertical-align:top;word-wrap:break-word;border-radius:25px;box-shadow:0 0 10px 0 rgba(0, 160, 220, 0.5);background-image:linear-gradient(to right, #c9302f, #13b5da);"><a href="{{$verify_link}}" style="border:0 solid #007bfc;border-radius:3px;color:#fefefe;display:inline-block;font-size:15px;font-weight:bold;line-height:1.3;margin:0;padding:8px 25px;text-align:left;text-decoration:none" target="_blank">Xác Nhận E-mail</a></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>--}}
    </td>
  </tr>
</table>
<table style="background:#fff;border-collapse:collapse;border-spacing:0;padding:0;vertical-align:top;width:100%">
  <tr style="padding:0;text-align:left;vertical-align:top">
    <th style="font-size:16px;line-height:1.3;margin:0;padding:0 30px;padding-top:30px;padding-bottom:47px;text-align:center">
      <p style="color:#919499;font-size:15px;font-weight:400;line-height:23px;margin:0 auto;padding:0 22px;text-align:center;width:80%;display:block">Nhằm cung cấp cho Quý khách hàng dịch vụ tốt nhất, xin vui lòng <a href="{{ url('/') }}/dang-nhap" target="_blank" style="color:#c9302f;font-size:14px;font-weight:400;line-height:24px;margin:0;padding:0;text-align:left;text-decoration:none"> </br>đăng nhập </a></p>
    </th>
  </tr>
</table>
@stop