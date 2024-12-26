<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Tên - Đơn hàng {!! $model->id !!}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<div style="width: 680px;">
  <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
      <tr>
        <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;" colspan="2">Chi tiết đơn hàng</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
          <b>#ID:</b> {!! $model->id !!}
          <br>
          <b>Mã đơn hàng:</b> {!! $model->no !!}
          <br>
          <b>Ngày tạo:</b>  {!! date('d/m/Y', strtotime($model->created_at)) !!}
          <br>
          <b>Phương thức thanh toán:</b> {!! $model->payment_method !!}
        </td>
        <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
          <b>Tên khách hàng:</b> {!! $model->first_name !!}
          <br>
          <b>Địa chỉ E-mail:</b> {!! $model->email !!}
          <br>
          <b>Điện thoại:</b> {!! $model->shipping_phone_number !!}
          <br>
          <b>Địa chỉ:</b> {!! $model->shipping_address_1 !!}, {!! $model->shipping_ward !!}, {!! $model->shipping_district !!}, {!! $model->shipping_province !!}
          <br>
          <b>Địa chỉ IP:</b> {!! $model->ip !!}
          <br>
          <b>Tình trạng đặt hàng:</b> {!! $model->order_status_name !!}
          <br>
        </td>
      </tr>
    </tbody>
  </table>
  <br>
  <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
    <tr>
      <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><div style="min-width: 100px"><b>Sản phẩm</b></div></td>
      <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><div style="min-width: 100px"><b>Số lượng</b></div></td>
      <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><div style="min-width: 100px"><b>Giá</b></div></td>
      <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><div><b>Tổng</b></div></td>
    </tr>
    </thead>
    <tbody>
    @foreach ($products as $item)
      <tr>
        <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">{!! $item['name'] !!}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">{!! $item['quantity'] !!}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
          @if($item['type'] !== 'G')<span>{!! number_format($item['price'], 0, ',', '.') !!}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>@endif
          @if($item['type'] === 'G')<span>{!! number_format($item['coins'], 0, ',', '.') !!}&nbsp;coin&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>@endif
        </td>
        <td style="border-collapse: collapse;border-spacing: 0;font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">
          @if($item['type'] !== 'G')<span>{!! number_format($item['total'], 0, ',', '.') !!}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>@endif
          @if($item['type'] === 'G')<span>{!! number_format($item['coins'] * $item['quantity'], 0, ',', '.') !!}&nbsp;coin&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>@endif
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
</body>
</html>
