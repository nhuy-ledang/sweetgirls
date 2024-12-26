<meta charset="utf-8">
<style>
  .hover-button {
    color:white;
    background-color:dodgerblue;
    border:0;
    cursor:pointer;
    text-transform:uppercase;
    transition:box-shadow 150ms linear, background-color 150ms linear, transform 150ms linear;
  }

  .hover-button:hover {
    border-radius:20px;
    box-shadow:0 4px 4px 0 rgba(0, 0, 0, 0.30) !important;
  }

  .hover-button:active {
    border-radius:20px;
    box-shadow:0 6px 4px 0 rgba(0, 0, 0, 0.30) inset !important;
  }
</style>
<div style="margin:0;padding:0;background-color:transparent;width:100%;height:100%">
  <table style="border-collapse:collapse;width:100%">
    <tr>
      <td style="width:200px;padding:25px 0 32px 0;background-color:#f7f7f7"></td>
      <td style="width:440px;padding:25px 0 32px 0;background-color:#f7f7f7;vertical-align:middle;font-size:24px;font-weight:bold;color:#ffffff;text-align:left">
        <img src="{{ url('/') }}/assets/mail/logo.png" alt="Vietnammanufacturers" style="height:40px">
      </td>
      <td style="width:140px;padding:25px 0 32px 0;background-color:#f7f7f7;vertical-align:middle;font-size:16px;font-weight:bold;color:#ffffff;text-align:right">
        <a href="{{ url('/') }}/dang-nhap" style="text-decoration:none;position:relative;font-weight:normal;color:#c9302f;" target="_blank">
          <img style="position:absolute;left:-25px;bottom:0;" src="{{ url('/') }}/assets/mail/login.png" alt="Log In" style="width:16px"> Đăng nhập
        </a>
      </td>
      <td style="width:200px;padding:25px 0 32px 0;background-color:#f7f7f7"></td>
    </tr>
    <tr>
      <td style="width:200px;padding:0 0 10px 0;background-color:#c9302f;vertical-align:top">
        <div style="background-color:#f7f7f7;height:235px"></div>
      </td>
      <td style="width:500px;padding:0;background-color:#ffffff;vertical-align:middle;font-size:18px;font-weight:400;color:#616366" colspan="2">
        <table style="border-collapse:collapse;border-spacing:0;color:#0a0a0a;font-size:16px;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;margin:0 auto;width:100%">
          <tr style="padding:0;text-align:left;vertical-align:top">
            <td align="center" valign="top" style="background:#c9302f;border-collapse:collapse!important;color:#0a0a0a;font-size:16px;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
              <table style="background:#fff;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;padding-top:30px;text-align:center;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:center;vertical-align:top">
                  <td align="center" valign="top" style="background:#c9302f;border-collapse:collapse!important;color:#0a0a0a;line-height:0;margin:0;padding:0;text-align:center;padding-top:0;padding-bottom:0;vertical-align:top;word-wrap:break-word">
                    @yield('content')
                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                      <tr style="padding:0;text-align:left;vertical-align:top">
                        <td height="20" style="border-collapse:collapse!important;font-size:22px;line-height:22px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word"></td>
                      </tr>
                    </table>
                    <!-- Footer -->
                    <table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                      <tr style="background:#fff;padding:0;text-align:left;vertical-align:top">
                        <td style="border-collapse:collapse!important;font-size:16px;line-height:1.3;margin:0;padding:0;padding-top:45px;text-align:left;vertical-align:top;word-wrap:break-word">
                          <h3 style="font-size:22px;font-weight:700;line-height:1.3;margin:0;margin-bottom:40px;padding:0;text-align:center;word-wrap:normal">Hãy Bắt Đầu Cùng Chúng Tôi</h3>
                          <table style="border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%">
                            <tr style="padding:0;text-align:left;vertical-align:top">
                              <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;padding-left:32px;padding-right:8px;text-align:left;width:129px;position:relative">
                                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                  <tr style="padding:0;text-align:left;vertical-align:top">
                                    <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:left">
                                      <table style="border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                          <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;text-align:left;width:83.33333%">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                              <tr style="padding:0;text-align:left;vertical-align:top">
                                                <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:center;position:relative;height:120px;">
                                                  <a style="color:#c9302f" href="{{ url('/') }}" target="_blank">
                                                    <img src="{{ url('/') }}/assets/mail/mail-login.png" alt="" style="clear:both;display:block;float:none;margin:0 auto;max-width:100%;outline:none;text-align:center;text-decoration:none;width:60px;position:absolute;max-height:70%;top:0;left:0;right:0;">
                                                    <p style="font-size:15px;font-weight:700;line-height:19px;margin:0;margin-bottom:10px;padding:10px;text-align:center;position:absolute;top:70px;left:0;right:0;">Đăng nhập</p>
                                                  </a>
                                                </th>
                                              </tr>
                                            </table>
                                          </th>
                                          <th valign="middle" style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;text-align:left;width:16.66667%;position:absolute">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                              <tr style="padding:0;text-align:center;vertical-align:top">
                                                <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:center">
                                                  <img src="{{ url('/') }}/assets/mail/next.png" alt="" style="clear:both;display:block;float:right;max-width:20px;outline:none;text-align:right;text-decoration:none;width:auto;position:absolute;top:40px;">
                                                </th>
                                              </tr>
                                            </table>
                                          </th>
                                        </tr>
                                      </table>
                                    </th>
                                  </tr>
                                </table>
                              </th>
                              <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;padding-left:8px;padding-right:8px;text-align:left;width:129px;position:relative">
                                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                  <tr style="padding:0;text-align:left;vertical-align:top">
                                    <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:left">
                                      <table style="border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                          <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;text-align:left;width:83.33333%">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                              <tr style="padding:0;text-align:left;vertical-align:top">
                                                <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:center;position:relative;height:120px;">
                                                  <a style="color:#c9302f" href="{{ url('/') }}" target="_blank">
                                                    <img src="{{ url('/') }}/assets/mail/mail-update.png" alt="" style="clear:both;display:block;float:none;margin:0 auto;max-width:100%;outline:none;text-align:center;text-decoration:none;width:70px;position:absolute;max-height:70%;top:0;left:0;right:0;">
                                                    <p style="font-size:15px;font-weight:700;line-height:19px;margin:0;margin-bottom:10px;padding:10px;text-align:center;position:absolute;top:70px;left:0;right:0;">Cập nhật thông tin của bạn</p>
                                                  </a>
                                                </th>
                                              </tr>
                                            </table>
                                          </th>
                                          <th valign="middle" style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;text-align:left;width:16.66667%;position:absolute">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                              <tr style="padding:0;text-align:left;vertical-align:top">
                                                <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:left">
                                                  <img src="{{ url('/') }}/assets/mail/next.png" alt="" style="clear:both;display:block;float:right;max-width:20px;outline:none;text-align:right;text-decoration:none;width:auto;position:absolute;top:40px;">
                                                </th>
                                              </tr>
                                            </table>
                                          </th>
                                        </tr>
                                      </table>
                                    </th>
                                  </tr>
                                </table>
                              </th>
                              <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;padding-left:8px;padding-right:8px;text-align:left;width:129px;position:relative">
                                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                  <tr style="padding:0;text-align:left;vertical-align:top">
                                    <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:left">
                                      <table style="border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                          <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;text-align:left;width:83.33333%">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                              <tr style="padding:0;text-align:left;vertical-align:top">
                                                <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:center;position:relative;height:120px;">
                                                  <a style="color:#c9302f" href="{{ url('/') }}" target="_blank">
                                                    <img src="{{ url('/') }}/assets/mail/mail-buy.png" alt="" style="clear:both;display:block;float:none;margin:0 auto;max-width:100%;outline:none;text-align:center;text-decoration:none;width:60px;position:absolute;max-height:70%;top:0;left:0;right:0;">
                                                    <p style="font-size:15px;font-weight:700;line-height:19px;margin:0;margin-bottom:10px;padding:10px;text-align:center;position:absolute;top:70px;left:0;right:0;">Mua dịch vụ</p>
                                                  </a>
                                                </th>
                                              </tr>
                                            </table>
                                          </th>
                                          <th valign="middle" style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;text-align:left;width:16.66667%;position:absolute">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                              <tr style="padding:0;text-align:left;vertical-align:top">
                                                <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:left">
                                                  <img src="{{ url('/') }}/assets/mail/next.png" alt="" style="clear:both;display:block;float:right;max-width:20px;outline:none;text-align:right;text-decoration:none;width:auto;position:absolute;top:40px;">
                                                </th>
                                              </tr>
                                            </table>
                                          </th>
                                        </tr>
                                      </table>
                                    </th>
                                  </tr>
                                </table>
                              </th>
                              <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;padding-left:8px;padding-right:16px;text-align:left;width:129px">
                                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                  <tr style="padding:0;text-align:left;vertical-align:top">
                                    <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:left">
                                      <table style="border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                          <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;text-align:left;width:83.33333%">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                              <tr style="padding:0;text-align:left;vertical-align:top">
                                                <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:center;position:relative;height:120px;">
                                                  <a style="color:#c9302f" href="{{ url('/') }}" target="_blank">
                                                    <img src="{{ url('/') }}/assets/mail/mail-trail.png" alt="" style="clear:both;display:block;float:none;margin:0 auto;max-width:100%;outline:none;text-align:center;text-decoration:none;width:60px;position:absolute;max-height:70%;top:0;left:0;right:0;">
                                                    <p style="font-size:15px;font-weight:700;line-height:19px;margin:0;margin-bottom:10px;padding:10px;text-align:center;position:absolute;top:70px;left:0;right:0;">Bạn muốn dùng thử trước</p>
                                                  </a>
                                                </th>
                                              </tr>
                                            </table>
                                          </th>
                                          <th valign="middle" style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;text-align:left;width:8.33333%">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                              <tr style="padding:0;text-align:left;vertical-align:top">
                                                <th style="font-size:16px;line-height:1.3;margin:0;padding:0;text-align:left"></th>
                                              </tr>
                                            </table>
                                          </th>
                                        </tr>
                                      </table>
                                    </th>
                                  </tr>
                                </table>
                              </th>
                            </tr>
                          </table>
                          <div style="text-align:center">
                            <a class="hover-button" href="{{ url('/') }}" target="_blank" style="display:inline-block;text-decoration:none;color:#fefefe;font-size:15px;font-weight:bold;line-height:1.3;margin:30px auto 45px auto;padding:16px 50px;vertical-align:top;border-radius:25px;box-shadow:0 0 10px 0 rgba(0, 160, 220, 0.5);background-image:linear-gradient(to right, #c9302f, #13b5da);">Mua dịch vụ</a>
                          </div>
                          <table style="border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%">
                            <tr style="padding:0;text-align:left;vertical-align:top">
                              <th style="font-size:16px;line-height:1.3;margin:0 auto;padding:0;padding-left:16px;padding-right:16px;text-align:left;width:564px">
                                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">

                                  <tr style="padding:0;text-align:left;vertical-align:top">
                                    <th style="font-size:16px;line-height:1.3;margin:0;padding:0 30px;text-align:left">
                                      <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                          <td height="1" style="background:#e3e3e3;border-collapse:collapse!important;display:block;font-size:1px;line-height:1px;margin:0 auto;max-width:100%;padding:0;text-align:left;vertical-align:top;width:100%;word-wrap:break-word;"></td>
                                        </tr>
                                      </table>
                                      <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                          <td height="32" style="border-collapse:collapse!important;font-size:32px;line-height:32px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word"></td>
                                        </tr>
                                      </table>
                                      <h5 style="font-size:16px;font-weight:700;line-height:1.3;margin:0;margin-bottom:10px;padding:0;text-align:left;word-wrap:normal">Bạn không thể đăng nhập ?</h5>
                                      @if(isset($data['email']))
                                        <p style="color:#919499;font-size:14px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding:0;text-align:left">Tài khoản của bạn: <b style="color:#616366!important;font-size:14px;font-weight:700!important"><a style="color:#c9302f !important;text-decoration:none" href="mailto:{{$data['email']}}" target="_blank">{{$data['email']}}</a></b></p>
                                      @endif
                                      <p style="color:#919499;font-size:14px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding:0;text-align:left">Quên mật khẩu ? <span style="color:#616366!important;font-size:14px;font-weight:400!important">Vui lòng chọn </span>
                                        <a href="{{ url('/') }}/quen-mat-khau" target="_blank" style="color:#c9302f;font-size:14px;font-weight:400;line-height:24px;margin:0;padding:0;text-align:left;text-decoration:none">{{ url('/') }}/quen-mat-khau</a>
                                      </p>
                                      <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                          <td height="15" style="border-collapse:collapse!important;font-size:32px;line-height:15px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word"></td>
                                        </tr>
                                      </table>
                                    </th>
                                  </tr>
                                </table>
                              </th>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
      <td style="width:200px;padding:0 0 10px 0;background-color:#c9302f;vertical-align:top"><div style="background-color:#f7f7f7;height:235px"></div></td>
    </tr>
    <tr>
      <td style="width:200px;padding:0 0 10px 0;background-color:#c9302f"></td>
      <td style="width:500px;padding:0 0 10px 0;background-color:#ffffff;border-bottom:1px solid #e3e3e3" colspan="2"></td>
      <td style="width:200px;padding:0 0 10px 0;background-color:#c9302f"></td>
    </tr>
    <tr>
      <td style="width:200px;padding:40px 0 50px 0;background-color:#c9302f"></td>
      <td style="width:500px;padding:40px 40px 50px 40px;background-color:#ffffff;vertical-align:middle;font-size:20px;font-weight:700;text-align:center" colspan="2">
        Cảm ơn bạn đã chọn <a href="{{ url('/') }}" style="color:#c9302f;font-size:20px;font-weight:700;text-decoration:none" target="_blank">Vietnammanufacturers!</a>
      </td>
      <td style="width:200px;padding:40px 0 50px 0;background-color:#c9302f"></td>
    </tr>
    <tr>
      <td style="width:200px;padding:28px 0 40px 0;background-color:#c9302f"></td>
      <td style="width:428px;padding:28px 0 40px 12px;background-color:#c9302f;vertical-align:middle;font-size:14px;color:#ffffff;text-align:left">Quyền sở hữu thuộc Công Ty TNHH Truyền Thông YES</td>
      <td style="width:140px;padding:28px 0 40px 0;background-color:#c9302f;vertical-align:middle;font-size:14px;color:#ffffff;text-align:right">
        <!--<a href="https://twitter.com/cloudbase/" style="color:#c9302f;text-decoration:none" target="_blank"><img src="{{ url('/') }}/assets/mail/fb.png" alt="Facebook" style="width:36px"></a>-->
      </td>
      <td style="width:200px;padding:28px 0 40px 0;background-color:#c9302f"></td>
    </tr>
  </table>
</div>
