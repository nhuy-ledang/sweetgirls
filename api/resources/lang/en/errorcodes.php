<?php
/*** 400-Validation ErrorCode definition: must be from 2000 to 2999, and need to define here ***/
return [
    // Error System
    'system.wrong_arguments'       => [9000, 'Dữ liệu không đúng'],
    'system.forbidden'             => [9001, 'Bạn không có quyền truy cập!'],
    'system.not_found'             => [9002, '​Không tìm thấy tài nguyên'],
    'system.internal_error'        => [9003, '​Lỗi nội bộ vui lòng thử lại sau'],
    'system.not_implemented'       => [9004, 'Không được thực hiện'],
    'system.unauthorized'          => [9005, 'Không được phép! Xin đăng nhập.'],
    'system.token_required'        => [9006, 'Please make sure your request has an Authorization header'],
    'system.permission'            => [9007, 'Bạn không được phép!'],
    'system.suspended'             => [9008, 'Hi, your account has been suspended. Please contact us'],
    'system.user_has_blocked'      => [9009, 'Hi, your account has been denied'],
    'system.change_device'         => [9010, 'Unauthorized, You are signing in on another device.'],
    // Token Exception
    'access_denied.missing'        => [8000, 'Missing "Authorization" header'],
    'access_denied.verify'         => [8001, 'Verify if the key matches with the one that created the signature'],
    'access_denied.invalid'        => [8002, 'Access token is invalid'],
    'access_denied.revoked'        => [8003, 'Access token has been revoked'],
    'access_denied.denied'         => [8004, 'The user denied the request'],
    'access_denied.expired'        => [8005, 'The code has been expired'],
    // Auth Exception
    'auth.invalid'                 => [8020, 'User invalid'],
    'auth.invalid_password'        => [8021, 'Password invalid'],
    'auth.locked'                  => [8022, 'Account has been locked after 5 failed attempts.'],
    'auth.inactivate'              => [8023, 'Your account is inactivate'],
    'auth.banned'                  => [8024, 'Your account have been banned. Please contact admin to fix the problem.'],
    'auth.blocked'                 => [8025, 'Your account is blocked'],
    'auth.activated'               => [8026, 'Your account have been activated'],
    'auth.not_activated'           => [8027, 'Your account not yet validated. please first login'],
    'auth.login_not_activated'     => [8028, 'Your account not yet activated. please activate'],
    'auth.ready_not_activated'     => [8029, 'Your account ready but not yet activated. please activate'],
    'auth.reset_failed'            => [8030, 'Đặt lại thất bại!'],
    'auth.reset_code_invalid'      => [8031, 'Mã không hợp lệ. Đặt lại thất bại!'],
    // Validation code for “Name” field
    'name.required'                => [2001, 'The name field is required.'],
    'name.exists'                  => [2001, 'Tên đã tồn tại.'],
    'name.min'                     => [2002, 'The name length must be greater than 3 characters.'],
    'name.max'                     => [2003, 'The name may not be greater than 50 characters.'],
    'name.unique'                  => [2003, 'The name is already.'],
    // Validation code for “First Name” field
    'first_name.required'          => [2020, 'The first name field is required.'],
    'first_name.min'               => [2021, 'The first name length must be greater than 3 characters.'],
    'first_name.max'               => [2022, 'The first name may not be greater than 50 characters.'],
    // Validation code for “Last Name” field
    'last_name.required'           => [2030, 'The last name field is required.'],
    'last_name.min'                => [2031, 'The last name length must be greater than 3 characters.'],
    'last_name.max'                => [2032, 'The last name may not be greater than 50 characters.'],
    // Validation code for “Username” field
    'username.required'            => [2040, 'The username field is required.'],
    'username.exists'              => [2041, 'The username has already been taken.'],
    'username.unique'              => [2042, 'The supplied username is already registered to another account'],
    'username.min'                 => [2043, 'The username length must be greater than 4 characters.'],
    'username.max'                 => [2044, 'The username length must be at least than 20 characters'],
    // Validation code for “User Id” field
    'user_id.required'             => [2044, 'Thành viên là bắt buộc.'],
    'user_id.invalid'              => [2044, 'Thành viên không tồn tại'],
    'user_id.exists'               => [2044, 'Thành viên đã tồn tại'],
    // Validation code for “Email” field
    'email.required'               => [2051, 'Yêu cầu email.'],
    'email.exists'                 => [2052, 'The email is invalid.'],
    'email.unique'                 => [2053, 'The supplied email is already registered to another account'],
    'email.invalid'                => [2054, 'Email không đúng.'],
    'email.email'                  => [2054, 'Email không đúng.'],
    'email.not_change'             => [2055, 'The email is not change.'],
    'email.change_failed'          => [2056, 'Change email failed.'],
    'email.verified'               => [2056, 'Email đã được xác thực'],
    // Validation code for “Password” field
    'password.required'            => [2060, 'The password field is required.'],
    'password.min'                 => [2061, 'Password must be at least 8 characters.'],
    'password.max'                 => [2062, 'Password must least 255 characters.'],
    'password.regex'               => [2063, 'Password must meet at least 3 of the 4 criteria: upper case, lower case, numbers, special characters.'],
    'password.confirmed'           => [2064, 'New password doesn\'t match.'],
    'password.change_failed'       => [2065, 'Change password failed.'],
    'password.old'                 => [2066, 'Password cannot re-use the last 14 passwords.'],
    'password.invalid'             => [2067, 'Password invalid.'],
    'password.current_failed'      => [2068, 'Current password doesn\'t match.'],
    // Validation code for “phone” field
    'phone_number.required'        => [2080, 'The phone number field is required.'],
    'phone_number.unique'          => [2081, 'Số điện thoại đã sẵn sàng.'],
    'phone_number.exists'          => [2082, 'Số điện thoại đã được sử dụng.'],
    'phone_number.invalid'         => [2086, 'Phone Number invalid.'],
    'phone_number.change_failed'   => [2087, 'Change Phone Number failed.'],
    'phone_number.not_exists'      => [2088, 'The phone number is not exist.'],
    'phone_number.not_change'      => [2089, 'The phone number is not change.'],
    // Validation code for “code” field
    'code.required'                => [2080, 'Yêu cầu nhập Mã giảm giá.'],
    'code.unique'                  => [2080, 'Mã đã tồn tại.'],
    'code.exists'                  => [2080, 'Mã giảm giá đã tồn tại'],
    'code.invalid'                 => [2080, 'Mã giảm giá không đúng'],
    'code.min'                     => [2080, 'Mã giảm giá không đúng'],
    // Validation code for “Title” field
    'title.required'               => [2091, 'The title field is required.'],
    'title.min'                    => [2092, 'The title be at least 3 characters.'],
    'title.max'                    => [2093, 'The title must least 25 characters.'],
    // Validation code for “Description” field
    'description.required'         => [2100, 'The description field is required.'],
    'description.min'              => [2101, 'Mô tả phải có ít nhất 100 ký tự.'],
    'description.max'              => [2102, 'Mô tả phải không quá 2000 ký tự.'],
    // Validation code for “file” field
    'file.required'                => [2130, 'The File field is required.'],
    'file.invalid'                 => [2131, 'The File field is invalid.'],
    'file.max'                     => [2132, 'Đã vượt kích thước tối đa (8M).'],
    'file.mime'                    => [2133, 'Định dạng không đúng.'],
    'file.video_max_size'          => [2134, 'Đã vượt kích thước tối đa (100M).'],
    // Validation code for “folder_id” field
    'folder_id.required'           => [2252, 'Yêu cầu folder!'],
    'folder_id.invalid'            => [2252, 'Folder không hợp lệ!'],
    'folder_id.exists'             => [2252, 'Folder không tồn tại!'],
    'folder_id.empty'              => [2252, 'Folder không không rỗng!'],
    // Validation code for “country_id” field
    'country_id.required'          => [2120, 'The Country field is required.'],
    'country_id.invalid'           => [2121, 'The Country field is invalid.'],
    'country_id.exists'            => [2122, 'The Country field is invalid.'],
    // Validation code for “shipping_country_id” field
    'shipping_country_id.required' => [2120, 'The Shipping Country field is required.'],
    'shipping_country_id.invalid'  => [2122, 'The Shipping Country field is invalid.'],
    'shipping_country_id.exists'   => [2123, 'The Shipping Country field is invalid.'],
    // Validation code for “province_id” field
    'province_id.required'         => [2240, 'The Province field is required.'],
    'province_id.exists'           => [2241, 'The Province has already been taken.'],
    'province_id.invalid'          => [2242, 'The Province field is invalid.'],
    // Validation code for “district_id” field
    'district_id.required'         => [2250, 'The City field is required.'],
    'district_id.exists'           => [2251, 'The City has already been taken.'],
    'district_id.invalid'          => [2252, 'The City field is invalid.'],
    // Validation code for “contact_id” field
    'contact_id.required'          => [2252, 'Yêu cầu chọn danh bạ!'],
    'contact_id.invalid'           => [2252, 'Danh bạ không hợp lệ!'],
    'contact_id.exists'            => [2252, 'Danh bạ đã tồn tại!'],
    'contact_id.noexists'          => [2252, 'Danh bạ không tồn tại'],
    // Validation code for “category_type” field
    'category_type.required'       => [2252, 'Danh mục không đúng!'],
    'category_type.exists'         => [2252, 'Danh mục không đúng!'],
    'category_type.invalid'        => [2252, 'Danh mục không đúng!'],
    'category_type.in'             => [2252, 'Danh mục không đúng!'],
    // Validation code for “payment_method” field
    'payment_method.required'      => [2252, 'Yêu cầu phương thức thanh toán!'],
    'payment_method.invalid'       => [2252, 'Phương thức thanh toán không đúng!'],
    'payment_method.in'            => [2252, 'Phương thức thanh toán không đúng!'],
    // Validation code for “ticket” field
    'ticket.required'              => [2252, 'Bạn chưa chọn vé hoặc vé đã hết!'],
    // Validation code for “ticket_id” field
    'ticket_id.required'           => [2252, 'Yêu cầu chọn vé!'],
    'ticket_id.invalid'            => [2252, 'Vé không hợp lệ!'],
    'ticket_id.exists'             => [2252, 'Vé đã tồn tại!'],
    'ticket_id.sold_out'           => [2252, 'Vé đã bán hết'],
    // Validation code for “product_id” field
    'product_id.required'          => [2252, 'Yêu cầu sản phẩm!'],
    'product_id.invalid'           => [2252, 'Sản phẩm không hợp lệ!'],
    'product_id.exists'            => [2252, 'Sản phẩm đã tồn tại!'],
    // Validation code for “product” field
    'product.required'             => [2252, 'Sản phẩm không còn trong kho!'],
    // Validation code for “property_id” field
    'property_id.exists'           => [2252, 'Thuộc tính đã tồn tại!'],
    // Validation code for “option_id” field
    'option_id.exists'             => [2252, 'Tùy chọn đã tồn tại!'],
    // Validation code for “other” field
    'recaptcha.required'           => [2252, 'Recaptcha không đúng.'],
    // Validation code for “transaction” field
    'transaction.status.failed'    => [2252, 'Giao dịch thất bại!'],
    'transaction.status.completed' => [2252, 'Giao dịch đã thanh toán thành công!'],
    'transaction.status.paid'      => [2252, 'Giao dịch đã thanh toán thành công!'],
    'transaction.status.unknown'   => [2252, 'Giao dịch thất bại không rõ nguyên nhân!'],
    'transaction.status.canceled'  => [2252, 'Giao dịch bị hủy bỏ!'],
    'transaction.status.refunded'  => [2252, 'Giao dịch đã hoàn trả!'],
    // Payment
    'payment.state.created'        => [2900, 'The transaction was successfully created.'],
    'payment.state.approved'       => [2901, 'The buyer approved the transaction.'],
    'payment.state.failed'         => [2902, 'The transaction request failed.'],
    'payment.state.has_failed'     => [2903, 'Có đơn hàng đã hủy thanh toán!'],
    // Shipping
    'shipping.status.create_order' => [2902, 'Đã chuyển qua đơn vị vận chuyển.'],
    // Keyword
    'keyword.exists'               => [2902, 'Chuỗi SEO link này đã được dùng!'],
    // Start Time
    'start_time.required'          => [2902, 'Yêu cầu Khung giờ'],
    'start_time.exists'            => [2902, 'Khung giờ đã tồn tại'],
    // Validation code for “order” field
    'order.required'               => [2902, 'Yêu cầu đơn hàng!'],
    'order.status.completed'       => [2902, 'Đơn hàng đã hoàn thành!'],
    'order.status.canceled'        => [2902, 'Đơn hàng đã bị hủy!'],
    'order.shipping.create_order'  => [2902, 'Có đơn hàng đã tạo đơn vận chuyển!'],
    'order.stock.request.exists'   => [2902, 'Có đơn hàng đã tạo yêu cầu xuất kho!'],
    'order.status.invalid'         => [2902, 'Hủy không thành công! Đơn hàng đã được vận chuyển'],
    'order.payment.cod'            => [2902, 'Đơn hàng chứa sản phẩm không hỗ trợ phương thức thanh toán khi nhận hàng'],
    // Validation code for “affiliate” field
    'auth.affiliate.exists'        => [2252, 'Bạn đã đăng ký affiliate'],
    'auth.affiliate.disapproved'   => [2252, 'Bạn đã đăng ký affiliate. Xin chờ admin duyệt'],
    // Validation code for “coupon” field
    'coupon.required'              => [2252, 'Yêu cầu mã giảm giá!'],
    'coupon.invalid'               => [2252, 'Mã giảm giá không hợp lệ!'],
    'coupon.order_total'           => [2252, 'Giá trị đơn hàng không đủ điệu kiện'],
    'coupon.product.invalid'       => [2252, 'Mã giảm giá chỉ áp dụng cho một số sản phẩm nhất định'],
    'coupon.user.new_member'       => [2252, 'Mã giảm giá chỉ áp dụng cho khách hàng mới'],
    'coupon.category.invalid'      => [2252, 'Danh mục sản phẩm không áp dụng được mã giảm giá'],
    // Validation code for “Voucher Code” field
    'voucher_code.required'        => [2252, 'Yêu cầu mã voucher'],
    'voucher_code.invalid'         => [2252, 'Mã voucher không có gía trị'],
    'voucher_code.order_total'     => [2252, 'Đơn hàng phải >= 800.000 để áp dụng mã voucher'],
    // Validation code for “Referral Code” field
    'referral_code.required'       => [2252, 'Mã giới thiệu là bắt buộc'],
    'referral_code.invalid'        => [2252, 'Mã giới thiệu không có gía trị'],
    'referral_code.exist'          => [2252, 'Mã giới thiệu đã tồn tại'],
    'referral_code.order_total'    => [2252, 'Đơn hàng phải >= 500.000 để áp dụng mã giới thiệu'],
    'referral_code.order_first'    => [2252, 'Mã giới thiệu có giá trị với đơn hàng đầu tiên'],
    // Validation code for “cart” field
    'cart.empty'                   => [2252, 'Giỏ hàng trống!'],
    'cart.coins.invalid'           => [2252, 'Bạn không đủ coin!'],
    'cart.coins.expired'           => [2252, 'Coin của bạn đã hết hạn sử dụng!'],
    'cart.include_product.exists'  => [2252, 'Sản phẩm đã tồn tại!'],
    'cart.coin_product.invalid'    => [2252, 'Không thể đổi quà SweetGirl coin với số lượng lớn hơn 2!'],
    // Validation balance for “Agent Withdrawal” field
    'withdrawal.balance.invalid'   => [2252, 'Cộng tác viên chưa phát sinh hoa hồng'],
];
