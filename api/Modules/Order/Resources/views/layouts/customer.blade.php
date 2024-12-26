<!DOCTYPE html>
<html dir="ltr" lang="vi">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <style>
    @import url('//fonts.googleapis.com/css?family=Montserrat:400,600');

    body {
      font-family: Montserrat, Arial, DejaVu Sans, sans-serif;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.5;
      color: #272727;
      text-align: left;
      background-color: #fff;
    }
    @media (max-width:690px) {
      .row-content {
        width: 100% !important;
      }
      .row-content-1 {
        width: 450px !important;
      }
    }
  </style>
</head>
<body style="padding: 0;margin-top: 0;margin-right: 0;margin-left: 0;">
<table role="presentation" align="center" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f4f4f4">
  <tbody>
  <tr>
    <td align="center" style="padding: 30px 0;">
      <table class="row-content" role="presentation" align="center" width="800" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
        <tbody>
        <tr>
          <td align="left">
            <table class="row-content-1" role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
              <tr>
                <td align="center" style="border-collapse: collapse;border-spacing: 0;">
                  <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="5" valign="top" style="box-sizing: border-box;">
                    <tbody>
                    <tr>
                      <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;color: #454545;padding: 30px 0">
                        <img style="width: 240px;" src="{{ url('/') }}/html/logo.png">
                      </td>
                    </tr>

                    </tbody>
                  </table>
                </td>
              </tr>

              @yield('content')

              <tr>
                <td style="border-collapse: collapse;border-spacing: 0;padding: 30px;" bgcolor="#c5a25d">
                  <table role="presentation" style="width:100%;border-collapse: collapse;border-spacing: 0;color: #fff;font-size: 14px;vertical-align: middle;" border="0" valign="top" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                      <td style="border-collapse: collapse;border-spacing: 0;padding: 1px 0;"><strong>Trân trọng,<br>{!! $config_owner !!}</strong></td>
                    </tr>
                    <tr>
                      <td style="border-collapse: collapse;border-spacing: 0;padding: 1px 0;">
                        <div style="width: 60%">{!! $setting['address'] !!}</div>
                      </td>
                    </tr>
                    <tr>
                      <td style="border-collapse: collapse;border-spacing: 0;padding: 1px 0;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                          <tr>
                            <td style="border-collapse: collapse;border-spacing: 0;">
                              <a style="color: #fff;text-decoration: none;" href="tel:{!! $setting['hotline'] !!}"><img src="{{ url('/') }}/html/phone.png" style="float: left;margin-top: 3px;">&nbsp;{!! $setting['hotline'] !!}</a>
                            </td>
                            <td style="border-collapse: collapse;border-spacing: 0;text-align: right">
                              <a style="color: #fff;text-decoration: none;" href="{{ url('/') }}"><img src="{{ url('/') }}/html/web_25.png?v=2" style="width: 25px"></a>
                              @if ($setting['facebook_url'])
                                &nbsp;&nbsp;<a style="color: #fff;text-decoration: none;" href="{!! $setting['facebook_url'] !!}"><img src="{{ url('/') }}/html/face_25.png?v=2" style="width: 25px"></a>
                              @endif
                              @if ($setting['instagram_url'])
                                &nbsp;&nbsp;<a style="color: #fff;text-decoration: none;" href="{!! $setting['instagram_url'] !!}"><img src="{{ url('/') }}/html/ins_25.png?v=2" style="width: 25px"></a>
                              @endif
                              @if ($setting['zalo_url'])
                                &nbsp;&nbsp;<a style="color: #fff;text-decoration: none;" href="{!! $setting['zalo_url'] !!}"><img src="{{ url('/') }}/html/zalo_25.png?v=2" style="width: 25px"></a>
                              @endif
                            </td>
                          </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="border-collapse: collapse;border-spacing: 0;padding: 20px 0;text-align: center;">Đây là email tự động. Xin vui lòng không gửi phản hồi vào hộp thư này<br>© {!! $config_owner !!}. All Rights Reserved.</td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  </tbody>
</table>
</body>
</html>
