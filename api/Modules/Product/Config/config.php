<?php

return [
    'name'                => 'Product',
    'order_export_fields' => [
        'id'                     => [
            'header' => '#ID',
            'type'   => 'string'
        ],
        'invoice_no'             => [
            'header' => 'Mã hóa đơn',
            'type'   => 'string'
        ],
        'product_name'           => [
            'header' => 'Sản phẩm',
            'type'   => 'string',
        ],
        'fullname'               => [
            'header' => 'Họ tên',
            'type'   => 'string',
        ],
        'phone_number'           => [
            'header' => 'Số điện thoại',
            'type'   => 'string'
        ],
        'email'                  => [
            'header' => 'Email',
            'type'   => 'string',
            'rules'  => 'required|email',
        ],
        'address_1'              => [
            'header' => 'Địa chỉ',
            'type'   => 'string',
        ],
        'province_name'          => [
            'header' => 'Tỉnh thành phố',
            'type'   => 'string',
        ],
        'country_name'           => [
            'header' => 'Quốc gia',
            'type'   => 'string',
        ],
        'postcode'               => [
            'header' => 'Postcode',
            'type'   => 'string'
        ],
        'payment_method_name'    => [
            'header' => 'Phương thức thanh toán',
            'type'   => 'string'
        ],
        'shipping_address_1'     => [
            'header' => 'Địa chỉ nhận hàng',
            'type'   => 'string',
        ],
        'shipping_province_name' => [
            'header' => 'Tỉnh thành phố',
            'type'   => 'string',
        ],
        'shipping_country_name'  => [
            'header' => 'Quốc gia',
            'type'   => 'string',
        ],
        'shipping_postcode'      => [
            'header' => 'Postcode',
            'type'   => 'string'
        ],
        'is_inv'                 => [
            'header' => 'In hóa đơn',
            'type'   => 'string'
        ],
        'inv_fullname'           => [
            'header' => 'Họ tên',
            'type'   => 'string',
        ],
        'inv_company'            => [
            'header' => 'Tên công ty',
            'type'   => 'string',
        ],
        'inv_tax_number'         => [
            'header' => 'Mã só thuế',
            'type'   => 'string',
        ],
        'inv_address'            => [
            'header' => 'Địa chỉ',
            'type'   => 'string',
        ],
        'inv_province_name'      => [
            'header' => 'Tỉnh thành phố',
            'type'   => 'string',
        ],
        'inv_country_name'       => [
            'header' => 'Quốc gia',
            'type'   => 'string',
        ],
        'inv_postcode'           => [
            'header' => 'Postcode',
            'type'   => 'string'
        ],
        'price'                  => [
            'header' => 'Giá',
            'type'   => 'string',
        ],
        'quantity'               => [
            'header' => 'Số lượng',
            'type'   => 'string',
        ],
        'shipping_fee'           => [
            'header' => 'Phí vận chuyển',
            'type'   => 'string',
        ],
        'total'                  => [
            'header' => 'Tổng tiền',
            'type'   => 'string',
        ],
        'status_name'            => [
            'header' => 'Tình trạng',
            'type'   => 'string',
        ],
        'created_at'             => [
            'header' => 'Ngày tạo',
            'type'   => 'string',
        ],
        'payment_at'             => [
            'header' => 'Ngày thanh toán',
            'type'   => 'string',
        ],
    ],
];
