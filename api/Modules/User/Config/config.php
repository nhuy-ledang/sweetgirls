<?php

return [
    'name'                      => 'User',
    'transaction_export_fields' => [
        'id'                  => [
            'header' => '#ID',
            'type'   => 'string'
        ],
        'invoice_no'          => [
            'header' => 'Mã hóa đơn',
            'type'   => 'string'
        ],
        'fullname'            => [
            'header' => 'Họ tên',
            'type'   => 'string',
        ],
        'phone_number'        => [
            'header' => 'Số điện thoại',
            'type'   => 'string'
        ],
        'email'               => [
            'header' => 'Email',
            'type'   => 'string',
            'rules'  => 'required|email',
        ],
        'address'             => [
            'header' => 'Địa chỉ',
            'type'   => 'string',
        ],
        'payment_method_name' => [
            'header' => 'Phương thức thanh toán',
            'type'   => 'string'
        ],
        'price'               => [
            'header' => 'Giá',
            'type'   => 'string',
        ],
        'quantity'            => [
            'header' => 'Số lượng',
            'type'   => 'string',
        ],
        'total'               => [
            'header' => 'Tổng tiền',
            'type'   => 'string',
        ],
        'status_name'         => [
            'header' => 'Tình trạng',
            'type'   => 'string',
        ],
        'created_at'          => [
            'header' => 'Ngày tạo',
            'type'   => 'string',
        ],
        'payment_at'          => [
            'header' => 'Ngày thanh toán',
            'type'   => 'string',
        ],
        'expired_at_old'      => [
            'header' => 'Ngày hết hạn',
            'type'   => 'string',
        ],
        'expired_at_new'      => [
            'header' => 'Gia hạn đến',
            'type'   => 'string',
        ],
        'uc__name'            => [
            'header' => 'Tên',
            'type'   => 'string',
        ],
        'uc__name_en'         => [
            'header' => 'Tên tiếng anh',
            'type'   => 'string',
        ],
        'uc__mst'             => [
            'header' => 'MST',
            'type'   => 'string',
        ],
        'uc__president'       => [
            'header' => 'Giám đốc',
            'type'   => 'string',
        ],
        'uc__group_name'      => [
            'header' => 'Nhóm DN',
            'type'   => 'string',
        ],
        'uc__type_name'       => [
            'header' => 'Loại DN',
            'type'   => 'string',
        ],
        'uc__employee_label'  => [
            'header' => 'SL nhân viên',
            'type'   => 'string',
        ],
        'uc__revenue_label'   => [
            'header' => 'Doanh thu',
            'type'   => 'string',
        ],
        'uc__address'   => [
            'header' => 'Địa chỉ',
            'type'   => 'string',
        ],
        'uc__country'   => [
            'header' => 'Quốc gia',
            'type'   => 'string',
        ],
        'uc__province'   => [
            'header' => 'Tỉnh/thành phố',
            'type'   => 'string',
        ],
        'uc__zip_code'   => [
            'header' => 'Zip Code',
            'type'   => 'string',
        ],
    ]
];
