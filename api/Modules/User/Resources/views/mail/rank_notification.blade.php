@extends('order::layouts.customer', ['config_owner' => $config_owner, 'setting' => $setting])

@section('content')
  <tr>
    <td align="center" style="border-collapse: collapse;border-spacing: 0;vertical-align: top;font-size: 20px;text-align: center;">
      <hr style="width: 70%; border: none; border-bottom: 1px solid #e5e5e5 !important; margin-top: 0;">
    </td>
  </tr>
  <tr>
    <td style="border-collapse: collapse;border-spacing: 0;padding: 10px 15px;">
      <table style="width:100%;border-collapse: collapse;border-spacing: 0;margin-bottom: 30px;font-size: 14px;table-layout: fixed;" border="0" valign="top" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
          @if (!empty($html))
            {!! $html !!}
          @endif
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
@endsection
