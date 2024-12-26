@extends('order::layouts.customer', ['config_owner' => $config_owner, 'setting' => $setting])

@section('content')
  <tr>
    <td style="border-collapse: collapse;border-spacing: 0;padding: 30px 50px;">
      <table style="width:100%;border-collapse: collapse;border-spacing: 0;margin-bottom: 30px;font-size: 14px;" border="0" valign="top" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
          <td colspan="2" style="border-collapse: collapse;border-spacing: 0;text-align: left;padding: 0 0 10px;">Cảm ơn Quý khách đã mua sắm tại {!! $config_owner !!}</td>
        </tr>
        <tr>
          <td colspan="2" style="border-collapse: collapse;border-spacing: 0;text-align: left;padding: 10px 0;line-height: 1.5;">Đơn hàng <a style="color: #c5a25d;">#{!! $model->id !!}</a> của Quý khách đã được thanh toán vào ngày <span style="color: #c5a25d;">{!! $model->payment_at ? date('d-m-Y', strtotime($model->payment_at)) : '' !!}</span>. Hi vọng Quý khách hài lòng với những trải nghiệm mua sắm tại {!! $config_owner !!}.</td>
        </tr>
        <tr>
          <td colspan="2" style="border-collapse: collapse;border-spacing: 0;text-align: left;padding: 10px 0;line-height: 1.5;">Chúc mừng Quý khách đã nhận được <b>1 lượt quay</b> Vòng quay may mắn.</td>
        </tr>
        <tr>
          <td align="center" colspan="2" style="border-collapse: collapse;border-spacing: 0;padding: 15px 0;">
            <a href="{!! $link !!}" target="_blank" style="display: inline-block;text-align: center; white-space: nowrap;vertical-align: middle;padding: .375rem .75rem;border-radius: 5px;cursor: pointer;background-color: #343a40;color: #fff;border: 1px solid #343a40;text-decoration: none;">
              QUAY THƯỞNG
            </a>
          </td>
        </tr>
        </tbody>
      </table>
      <p>Mọi thắc mắc xin vui lòng liên hệ với chúng tôi qua Email <a style="color: #c5a25d;text-decoration: none;" href="mailto:{!! $setting['email_support'] !!}" target="_blank">{!! $setting['email_support'] !!}</a>
        hoặc gọi điện trực tiếp đến số Hotline <a href="tel:{!! $setting['hotline'] !!}" style="color: #c5a25d;text-decoration: none;" target="_blank">{!! $setting['hotline'] !!}</a></p>
      <p style="margin-bottom: 0;">Một lần nữa, xin cảm ơn Quý khách đã mua sắm tại <strong>{!! $config_owner !!}</strong>.<br>Mến chúc Quý khách một ngày tốt lành!</p>
    </td>
  </tr>
@endsection
