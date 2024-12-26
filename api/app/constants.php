<?php
//=== PHONE_NUMBER
define('PHONE_NUMBER_COUNTRY_CODE', '84');

//=== MEDIA
define('MEDIA_SUB_AVATAR', 'av');       // Avatar
define('MEDIA_SUB_SQUARE', 'sq');       // Square
define('MEDIA_SUB_COVER', 'cv');        // Cover
define('MEDIA_SUB_CONTACT', 'ct');      // Contact
//define('MEDIA_SUB_PLACE', 'lo');        // Place
define('MEDIA_SUB_BANNER', 'ba');       // Place Banner
//define('MEDIA_SUB_PICTURE', 'pi');      // Place Picture
//define('MEDIA_SUB_REVIEW', 're');       // Place Review
//define('MEDIA_SUB_COMMENT', 'co');      // Place Review Comment
//define('MEDIA_SUB_ARTICLE', 'ar');      // Article
//define('MEDIA_SUB_APICTURE', 'ap');     // Article Picture
//define('MEDIA_SUB_ACOMMENT', 'ac');     // Article Comment
define('MEDIA_SUB_PRODUCT', 'pd');      // Product

// Square 100x100
//define('MEDIA_SUB_SUBJECT', 'su');      // Subject
//define('MEDIA_SUB_MANUFACTURER', 'ma'); // Manufacturer

//=== HOLIDAYS
define('HOLIDAYS', ['*-01-01', '*-04-30', '*-05-01', '*-09-02']);
define('LUNAR_HOLIDAYS', ['*-12-30', '*-01-01', '*-01-02', '*-01-03', '*-01-04', '*-03-10']);

//=== USER STATUS
define('USER_STATUS_STARTER', 'starter');
define('USER_STATUS_ACTIVATED', 'activated');
define('USER_STATUS_BANNED', 'banned');

//=== USER GENDER
define('USER_GENDER_UNKNOWN', 0);
define('USER_GENDER_MALE', 1);
define('USER_GENDER_FEMALE', 2);

//=== USER ROLE
define('USER_ROLE_SUPER_ADMIN', 1);
define('USER_ROLE_ADMIN', 2);
define('USER_ROLE_USER', 3);
define('USER_ROLE_POSTER', 4);
define('USER_ROLE_CONTENT_CREATOR', 5);
define('USER_ROLE_SEO', 6);

//=== USR ROLE
define('USR_ROLE_SUPER_ADMIN', 1);
define('USR_ROLE_ADMIN', 2);
define('USR_ROLE_MANAGER', 3);
define('USR_ROLE_ACCOUNTANT', 4);
define('USR_ROLE_SALES', 5);
define('USR_ROLE_USER', 6);

//=== USER LOGIN
define('USER_PASSWORD_FAILED_MAX', 5);
define('USER_PASSWORD_MIN_LENGTH', 6);
define('USER_PASSWORD_REGEX', '/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])|(?=.*[a-z])(?=.*[A-Z])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~])|(?=.*[a-z])(?=.*[0-9])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~])|(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~]).*$/');
define('USER_USERNAME_REGEX', '/^[A-Za-z][A-Za-z0-9]{5,31}$/');

//=== NOTIFY
define('NOTIFY_TYPE_USER_REGISTERED', 1);
define('NOTIFY_TYPE_ORDER', 4);
define('NOTIFY_TYPE_NEWSLETTER', 5);

//=== PAYMENT METHODS
//define('PAYMENT_MT_CASH', 'cash');                // Payment in cash - Tiền mặt
define('PAYMENT_MT_BANK_TRANSFER', 'bank_transfer');// Bank transfer
define('PAYMENT_MT_DOMESTIC', 'domestic');          // Domestic ATM / Internet Banking card
define('PAYMENT_MT_FOREIGN', 'international');      // Visa, Master, JCB international card
define('PAYMENT_MT_MOMO', 'momo');                  // QR - Momo
define('PAYMENT_MT_COD', 'cod');                    // COD - Thu hộ
//define('PAYMENT_MT_DIRECT', 'direct');

//=== ORDER STATUS
define('ORDER_SS_PENDING', 'pending');
define('ORDER_SS_PROCESSING', 'processing');
define('ORDER_SS_SHIPPING', 'shipping');
define('ORDER_SS_COMPLETED', 'completed');
define('ORDER_SS_CANCELED', 'canceled');
define('ORDER_SS_RETURNING', 'returning');
define('ORDER_SS_RETURNED', 'returned');
// 'pending', 'processing', 'shipping', 'completed', 'canceled', 'returning', 'returned'

//=== PAYMENT STATUS
define('PAYMENT_SS_PENDING', 'pending');
define('PAYMENT_SS_INPROGRESS', 'in_process');
define('PAYMENT_SS_PAID', 'paid');
define('PAYMENT_SS_FAILED', 'failed');
define('PAYMENT_SS_UNKNOWN', 'unknown');
define('PAYMENT_SS_REFUNDED', 'refunded');
define('PAYMENT_SS_CANCELED', 'canceled');
// 'pending', 'in_process', 'paid', 'failed', 'unknown', 'refunded', 'canceled'

//=== SHIPPING STATUS
define('SHIPPING_SS_CREATE_ORDER', 'create_order');
define('SHIPPING_SS_DELIVERING', 'delivering');
define('SHIPPING_SS_DELIVERED', 'delivered');
define('SHIPPING_SS_RETURN', 'return');
// 'create_order', 'delivering', 'delivered', 'return'

// status:
// 'pending','in_process','completed','failed','unknown','paid','refunded','canceled'
// order_status:
// 'pending','processing','shipping','completed','canceled','returning','returned'

//=== referral
define('REFERRAL_ORDER_TOTAL', 500000);

//=== COUPON & VOUCHER RULE
define('COUPON_RULE_INCLUDE', 'include');
define('COUPON_RULE_EXCLUDE', 'exclude');

//=== STOCK
define('STO_IN_TYPE_PURCHASE', 0);   // Mua vào
define('STO_IN_TYPE_PRODUCE', 1);    // Tự sản xuất
define('STO_IN_TYPE_RETURN', 2);     // Chuyển nội bộ
define('STO_IN_TYPE_REFUND', 3);     // Hàng hoàn

define('STO_OUT_TYPE_SALE', 0);      // Bán ra
define('STO_OUT_TYPE_GIFT', 1);      // Xuất tặng
define('STO_OUT_TYPE_TRANSFER', 2);  // Chuyển kho
define('STO_OUT_TYPE_REFUND', 3);    // Hoàn đổi
define('STO_OUT_TYPE_USE', 4);       // Xuất sử dụng
define('STO_OUT_TYPE_DESTROY', 5);   // Xuất hủy

define('STO_SHIPPING_SS_WAIT_OUT', 0);    // Chờ xuất
define('STO_SHIPPING_SS_WAIT_IN', 1);     // Chờ nhập
define('STO_SHIPPING_SS_ADJUST', 2);      // Cần điều chỉnh
define('STO_SHIPPING_SS_DELIVERING', 3);  // Đang vận đơn
define('STO_SHIPPING_SS_COMPLETED', 4);   // Hoàn tất

define('STO_REQUEST_SS_PENDING', 0);      // Chờ xử lý
define('STO_REQUEST_SS_ADJUST', 1);       // Cần điều chỉnh
define('STO_REQUEST_SS_PROCESSING', 2);   // Đang xử lý
define('STO_REQUEST_SS_COMPLETED', 3);    // Duyệt triển khai
define('STO_REQUEST_SS_STOP', 4);         // Ngưng xử lý
define('STO_REQUEST_SS_REJECTED', 5);     // Từ chối
define('STO_REQUEST_SS_REVOKE', 6);       // Thu lệnh

define('STO_TICKET_SS_PENDING', 0);       // Phiếu tạm
define('STO_TICKET_SS_COMPLETED', 1);     // Hoàn thàh
define('STO_TICKET_SS_REJECTED', 2);      // Từ chối
