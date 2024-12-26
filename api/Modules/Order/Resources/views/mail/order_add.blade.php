@extends('order::layouts.customer', ['config_owner' => $config_owner, 'setting' => $setting])

@section('content')
  <tr>
    <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;color: #454545;padding: 15px 0 0;">
      <strong>THÔNG TIN ĐƠN HÀNG</strong>
    </td>
  </tr>
  <tr>
    <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;">
      <hr style="width: 70%; border: none; border-bottom: 1px solid #e5e5e5 !important;">
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
                <td><strong>Mã đơn hàng: </strong>{!! $model->no !!}</td>
              </tr>
              <tr>
                <td><strong>Ngày đặt hàng: </strong>{!! date('d/m/Y', strtotime($model->created_at)) !!} {!! date('H:i', strtotime($model->created_at)) !!}</td>
              </tr>
              <tr>
                <td><strong>Phương thức thanh toán: </strong>{!! $model->payment_method !!}</td>
              </tr>
              <tr>
                <td><strong>Thời gian xử lý đơn hàng: </strong>Sau 1 - 2 ngày làm việc</td>
              </tr>
              </tbody>
            </table>
          </td>
          <td valign="top" style="padding: 0 0 0 10px;">
            <table style="width:100%;border-collapse: collapse;border-spacing: 0;font-size: 14px;" border="0" valign="top" cellspacing="0" cellpadding="0">
              <tbody>
              <tr>
                <td><strong>Họ tên: </strong>{!! $model->shipping_first_name ? $model->shipping_first_name : $model->first_name !!}</td>
              </tr>
              <tr>
                <td><strong>Số điện thoại nhận hàng: </strong>{!! $model->shipping_phone_number !!}</td>
              </tr>
              <tr>
                <td><strong>Địa chỉ: </strong>{!! $model->shipping_address_1 !!}, {!! $model->shipping_ward !!}, {!! $model->shipping_district !!}, {!! $model->shipping_province !!}</td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <table style="width:100%;border-collapse: collapse;border-spacing: 0;font-size: 14px;" border="0" valign="top" cellspacing="0" cellpadding="10">
              <thead style="border-bottom: 2px solid #e5e5e5; font-weight: bold;">
              <tr>
                <th></th>
                <th style="text-align: left;">Tên sản phầm</th>
                <th style="text-align: right;">Giá</th>
                <th style="text-align: right;">SL</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($products as $item)
                <tr>
                  <td style="border-collapse: collapse;border-spacing: 0;width: 80px;border-top: 1px solid #dee2e6;">@if ($item['thumb_url'])<img width="80" src="{!! $item['thumb_url'] !!}">@endif</td>
                  <td style="border-collapse: collapse;border-spacing: 0;text-align: left;border-top: 1px solid #dee2e6;">{!! $item['name'] !!}</td>
                  <td style="border-collapse: collapse;border-spacing: 0;text-align: right;border-top: 1px solid #dee2e6;">
                    @if ($item['type'] !== 'G')<span>{!! number_format($item['price'], 0, ',', '.') !!}đ</span>@endif
                    @if ($item['type'] === 'G')<span>{!! number_format($item['coins'], 0, ',', '.') !!}&nbsp;coin</span>@endif
                  </td>
                  <td style="border-collapse: collapse;border-spacing: 0;text-align: right;border-top: 1px solid #dee2e6;">{!! $item['quantity'] !!}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="border-collapse: collapse;border-spacing: 0;padding: 25px 0;">
            <hr style="border: 1px solid #e5e5e5;">
          </td>
        </tr>
        <tr>
          <td align="right" colspan="2" style="border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">
            <table style="width:100%;border-collapse: collapse;border-spacing: 0;font-size: 14px;" border="0" valign="top" cellspacing="0" cellpadding="0">
              <tbody>
              <tr>
                <td style="width: 65%;border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">Tổng tạm: </td>
                <td style="width: 35%;border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">{!! number_format($model->sub_total, 0, ',', '.') !!}đ</td>
              </tr>
              @if ($model->discount_total)
                <tr>
                  <td style="width: 65%;border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">Giảm giá: </td>
                  <td style="border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">-{!! number_format($model->discount_total, 0, ',', '.') !!}đ</td>
                </tr>
              @endif
              @if ($model->voucher_total)
                <tr>
                  <td style="width: 65%;border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">Giảm giá: </td>
                  <td style="border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">-{!! number_format($model->voucher_total, 0, ',', '.') !!}đ</td>
                </tr>
              @endif
              <tr>
                <td style="width: 65%;border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">Phí vận chuyển: </td>
                <td style="border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">{!! number_format($model->shipping_fee, 0, ',', '.') !!}đ</td>
              </tr>
              @if ($model->shipping_discount)
                <tr>
                  <td style="width: 65%;border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">Giảm phí giao hàng: </td>
                  <td style="border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">-{!! number_format($model->shipping_discount, 0, ',', '.') !!}đ</td>
                </tr>
              @endif
              <tr>
                <td style="width: 65%;border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;"><strong>Tổng tiền: </strong></td>
                <td style="border-collapse: collapse;border-spacing: 0;text-align: right;padding: 1px 0;">{!! number_format($model->total, 0, ',', '.') !!}đ</td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td align="center" colspan="2" style="border-collapse: collapse;border-spacing: 0;padding: 15px 0;">
            <a href="{{ url('/') }}/account/orders/details?order_id={!! $model->id !!}" style="display: inline-block;text-align: center; white-space: nowrap;vertical-align: middle;padding: .375rem .75rem;border-radius: 5px;cursor: pointer;background-color: #343a40;color: #fff;border: 1px solid #343a40;text-decoration: none;">XEM CHI TIẾT ĐƠN HÀNG</a>
          </td>
        </tr>
        </tbody>
      </table>
      @if (!empty($bank_transfer))
        {!! $bank_transfer !!}
      @endif
      {{--<a href="{{ url('/') }}/account/orders/details?order_id={!! $model->id !!}" style="display: inline-block;text-align: center; white-space: nowrap;vertical-align: middle;padding: .375rem .75rem;border-radius: 5px;cursor: pointer;background-color: #343a40;color: #fff;border: 1px solid #343a40;text-decoration: none;">XEM CHI TIẾT ĐƠN HÀNG</a>--}}
      {{--<p>Mọi thắc mắc xin vui lòng liên hệ với chúng tôi qua Email <a style="color: #c5a25d;text-decoration: none;" href="mailto:{!! $setting['email_support'] !!}">{!! $setting['email_support'] !!}</a>--}}
      {{--hoặc gọi điện trực tiếp đến số Hotline <a href="tel:{!! $setting['hotline'] !!}" style="color: #c5a25d;text-decoration: none;">{!! $setting['hotline'] !!}</a></p>--}}
      {{--<p style="margin-bottom: 0;">Một lần nữa, xin cảm ơn Quý khách đã mua sắm tại <strong>SweetGirl</strong>.<br>Mến chúc Quý khách một ngày tốt lành!</p>--}}
      <p>Chúc bạn có những phút giây mua sắm thú vị tại {!! $config_owner !!}!</p>
    </td>
  </tr>
@endsection
