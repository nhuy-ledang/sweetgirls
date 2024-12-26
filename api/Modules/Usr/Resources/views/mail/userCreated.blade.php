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
    </style>
</head>
<body style="padding: 0;margin-top: 0;margin-right: 0;margin-left: 0;">
<table role="presentation" align="center" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f4f4f4">
    <tbody>
    <tr>
        <td align="center" style="padding: 30px 0;">
            <table role="presentation" align="center" width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
                <tbody>
                <tr>
                    <td align="left">
                        <table role="presentation" width="700" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td align="center" style="border-collapse: collapse;border-spacing: 0;">
                                    <table role="presentation" width="700" border="0" cellspacing="0" cellpadding="5" valign="top" style="box-sizing: border-box;">
                                        <tbody>
                                        <tr>
                                            <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;color: #454545;padding: 30px 0">
                                                <img style="width: 240px;" src="{{ url('/') }}/html/logo.png">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;color: #454545;padding: 15px 0 0;">
                                                <strong>THÔNG TIN ĐĂNG NHẬP</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;">
                                                <hr style="width: 70%; border: none; border-bottom: 1px solid #e5e5e5 !important;">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-collapse: collapse;border-spacing: 0;padding: 10px 30px;">
                                    <table style="width:100%;border-collapse: collapse;border-spacing: 0;margin-bottom: 30px;font-size: 14px;table-layout: fixed;" border="0" valign="top" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td valign="top" style="padding: 0 10px 0 0;">
                                                <table style="width:100%;border-collapse: collapse;border-spacing: 0;font-size: 14px;" border="0" valign="top" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td><strong>Email đăng nhập: </strong>{!! $model->email !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Mật khẩu: </strong>{!! $password !!}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="2" style="border-collapse: collapse;border-spacing: 0;padding: 15px 0;">
                                                <a href="http://admin.{!! parse_url(url('/'), PHP_URL_HOST) ?: url('/') !!}/" target="_blank" style="display: inline-block;text-align: center; white-space: nowrap;vertical-align: middle;padding: .375rem .75rem;border-radius: 5px;cursor: pointer;background-color: #343a40;color: #fff;border: 1px solid #343a40;text-decoration: none;">ĐĂNG NHẬP NGAY</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <p>Vui lòng đổi mật khẩu sau khi đăng nhập thành công để đảm bảo tính bảo mật!</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-collapse: collapse;border-spacing: 0;padding: 30px;" bgcolor="#c5a25d">
                                    <table role="presentation" style="width:100%;border-collapse: collapse;border-spacing: 0;color: #fff;font-size: 14px;vertical-align: middle;" border="0" valign="top" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td style="border-collapse: collapse;border-spacing: 0;padding: 1px 0;"><strong>Trân trọng,<br>{!! $config_owner !!}</strong></td>
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
