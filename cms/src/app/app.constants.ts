export const CAPP = {
  ASSETS_PLACEHOLDER: 'assets/images/default-profile-square.png',
  TOKEN_NAME: 'sAuthorization',
};

export const CPATTERN = {
  // EMAIL: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
  EMAIL: /.+@.+\..+/,
  NUMBER: /^\d+$/,
  DATE: /[0-9]{2}/,
  MONTH: /[0-9]{2}/,
  YEAR: /[0-9]{4}/,
  AGE: /^[1-9]+[0-9]*$/,
};

export const CERROR_CODES = {
  AUTH_FAILED: {errorCode: 9006, errorMessage: 'Please make sure your request has an Authorization header!'},
};

export const CONSTANTS = {
  // PAGING
  PAGE_SIZE: 20,
  PAGE_SIZE_MAX: 100,

  // IMAGE
  WIDTH_MAX: 640,
  HEIGHT_MAX: 360,

  // POSITION
  LATITUDE: 10.77838053,
  LONGITUDE: 106.69682016,

  // === USER STATUS
  USER_STATUS_STARTER: 'starter',
  USER_STATUS_ACTIVATED: 'activated',
  USER_STATUS_BANNED: 'banned',

  // === USER GENDER
  USER_GENDER_UNKNOWN: 0,
  USER_GENDER_MALE: 1,
  USER_GENDER_FEMALE: 2,

  // === USER ROLE
  USER_ROLE_SUPER_ADMIN: 1,
  USER_ROLE_ADMIN: 2,
  USER_ROLE_USER: 3,
  USER_ROLE_POSTER: 4,
  USER_ROLE_CONTENT_CREATOR: 5,
  USER_ROLE_SEO: 6,

  // === PAYMENT METHODS
  PAYMENT_MT_CASH: 'cash',                    // Payment in cash
  PAYMENT_MT_BANK_TRANSFER: 'bank_transfer',  // Bank transfer
  PAYMENT_MT_DOMESTIC: 'domestic',            // Domestic ATM / Internet Banking card
  PAYMENT_MT_FOREIGN: 'international',        // Visa, Master, JCB international card
  PAYMENT_MT_COD: 'cod',
  PAYMENT_MT_DIRECT: 'direct',

  // === ORDER STATUS
  ORDER_SS_PENDING: 'pending',
  ORDER_SS_PROCESSING: 'processing',
  ORDER_SS_SHIPPING: 'shipping',
  ORDER_SS_COMPLETED: 'completed',
  ORDER_SS_CANCELED: 'canceled',
  ORDER_SS_RETURNING: 'returning',
  ORDER_SS_RETURNED: 'returned',
  // 'pending', 'processing', 'shipping', 'completed', 'canceled', 'returning', 'returned'

  // === PAYMENT STATUS
  PAYMENT_SS_PENDING: 'pending',
  PAYMENT_SS_INPROGRESS: 'in_process',
  PAYMENT_SS_PAID: 'paid',
  PAYMENT_SS_FAILED: 'failed',
  PAYMENT_SS_UNKNOWN: 'unknown',
  PAYMENT_SS_REFUNDED: 'refunded',
  PAYMENT_SS_CANCELED: 'canceled',
  // 'pending', 'in_process', 'paid', 'failed', 'unknown', 'refunded', 'canceled'

  // === SHIPPING STATUS
  SHIPPING_SS_CREATE_ORDER: 'create_order',
  SHIPPING_SS_DELIVERING: 'delivering',
  SHIPPING_SS_DELIVERED: 'delivered',
  SHIPPING_SS_RETURN: 'return',
  // 'create_order', 'delivering', 'delivered', 'return'

  // === SALARY
  SALARY_TYPE_NO_REPEAT: 0,
  SALARY_TYPE_REPEAT: 1,
  SALARY_TYPE_ALLOWANCE: 2,

  SALARY_STATUS_UNAPPROVED: 0,
  SALARY_STATUS_APPROVED: 1,
  SALARY_STATUS_FINAL: 2,

  // === DISCOUNT
  DISCOUNT_AMOUNT: 0,
  DISCOUNT_PERCENT: 1,

  // === INVOICE
  INVOICE_DRAFT: 0,     // Nháp
  INVOICE_PARTIAL: 1,   // Thanh toán 1 phần
  INVOICE_LATE: 2,      // Trễ hạn
  INVOICE_CANCEL: 3,    // Hủy biên lai
  INVOICE_PAID: 4,      // Đã thanh toán

  // === OTHER
  NOTIFY_TYPE_CHAT: 100,

  // === STOCK
  STO_IN_TYPE_PURCHASE: 0,   // Mua vào
  STO_IN_TYPE_PRODUCE: 1,    // Tự sản xuất
  STO_IN_TYPE_RETURN: 2,     // Chuyển nội bộ
  STO_IN_TYPE_REFUND: 3,     // Hàng hoàn

  STO_OUT_TYPE_SALE: 0,      // Bán ra
  STO_OUT_TYPE_GIFT: 1,      // Xuất tặng
  STO_OUT_TYPE_TRANSFER: 2,  // Chuyển kho
  STO_OUT_TYPE_REFUND: 3,    // Hoàn đổi
  STO_OUT_TYPE_USE: 4,       // Xuất sử dụng
  STO_OUT_TYPE_DESTROY: 5,   // Xuất hủy

  STO_SHIPPING_SS_WAIT_OUT: 0,    // Chờ xuất
  STO_SHIPPING_SS_WAIT_IN: 1,     // Chờ nhập
  STO_SHIPPING_SS_ADJUST: 2,      // Cần điều chỉnh
  STO_SHIPPING_SS_DELIVERING: 3,  // Đang vận đơn
  STO_SHIPPING_SS_COMPLETED: 4,   // Hoàn tất

  STO_REQUEST_SS_PENDING: 0,      // Chờ xử lý
  STO_REQUEST_SS_ADJUST: 1,       // Cần điều chỉnh
  STO_REQUEST_SS_PROCESSING: 2,   // Đang xử lý
  STO_REQUEST_SS_COMPLETED: 3,    // Duyệt triển khai
  STO_REQUEST_SS_STOP: 4,         // Ngưng xử lý
  STO_REQUEST_SS_REJECTED: 5,     // Từ chối
  STO_REQUEST_SS_REVOKE: 6,       // Thu lệnh

  STO_TICKET_SS_PENDING: 0,       // Phiếu tạm
  STO_TICKET_SS_COMPLETED: 1,     // Hoàn thành
  STO_TICKET_SS_REJECTED: 2,      // Từ chối

};

// === OPERATE
export const PRODUCT_TYPES = [
  {id: 0, name: 'Sản phẩm sản xuất'},
  {id: 2, name: 'Sản phẩm nhập'},
];

export const STO_IN_TYPES = [
  {id: 0, name: 'Mua vào', colour: '#3986ff'},
  {id: 1, name: 'Tự sản xuất', colour: '#ff4a65'},
  {id: 2, name: 'Chuyển nội bộ', colour: '#d991a4'},
  {id: 3, name: 'Hàng hoàn', colour: '#ff802c'},
];

export const STO_OUT_TYPES = [
  {id: 0, name: 'Bán ra', colour: '#3986ff'},
  {id: 1, name: 'Xuất tặng', colour: '#ff4a65'},
  {id: 2, name: 'Chuyển kho', colour: '#ff802c'},
  {id: 3, name: 'Hoàn đổi', colour: '#d991a4'},
  {id: 4, name: 'Xuất sử dụng', colour: '#29c6e3'},
  {id: 5, name: 'Xuất hủy', colour: '#172228'},
];

export const STO_SHIPPING_STATUSES = [
  {id: 0, name: 'Chờ xuất', colour: '#8b9095'},
  {id: 1, name: 'Chờ nhập', colour: '#677788'},
  {id: 2, name: 'Cần điều chỉnh', colour: '#ff802c'},
  {id: 3, name: 'Đang vận đơn', colour: '#3986ff'},
  {id: 4, name: 'Hoàn tất', colour: '#d991a4'},
];

export const STO_REQUEST_STATUSES = [
  {id: 0, name: 'Chờ xử lý', colour: '#677788'},
  {id: 1, name: 'Cần điều chỉnh', colour: '#ff802c'},
  {id: 2, name: 'Đang xử lý', colour: '#3986ff'},
  {id: 3, name: 'Duyệt triển khai', colour: '#d991a4'},
  {id: 4, name: 'Ngưng xử lý', colour: '#ff4a65'},
  {id: 5, name: 'Từ chối', colour: '#172228'},
  {id: 6, name: 'Thu lệnh', colour: '#6906a2'},
];

export const STO_TICKET_STATUSES = [
  {id: 0, name: 'Phiếu tạm', colour: '#677788'},
  {id: 1, name: 'Hoàn thành', colour: '#d991a4'},
  {id: 2, name: 'Từ chối', colour: '#ff4a65'},
];
