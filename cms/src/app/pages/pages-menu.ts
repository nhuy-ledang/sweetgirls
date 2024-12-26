// import { NbMenuItem } from '@nebular/theme';
/**
 *
 *
 * Menu Item options example
 * @stacked-example(Menu Link Parameters, menu/menu-link-params.component)
 *
 *
 */
export declare class MenuItem {
  /**
   * Item Id
   * @type {string}
   */
  id?: number;

  /**
   * Item Module
   * @type {string}
   */
  module?: string;

  /**
   * Item Title
   * @type {string}
   */
  title: string;
  /**
   * Item relative link (for routerLink)
   * @type {string}
   */
  link?: string;
  /**
   * Item URL (absolute)
   * @type {string}
   */
  url?: string;
  /**
   * Icon class name
   * @type {string}
   */
  icon?: string;
  /**
   * Expanded by default
   * @type {boolean}
   */
  expanded?: boolean;
  /**
   * Children items
   * @type {List<MenuItem>}
   */
  children?: MenuItem[];
  /**
   * HTML Link target
   * @type {string}
   */
  target?: string;
  /**
   * Hidden Item
   * @type {boolean}
   */
  hidden?: boolean;
  /**
   * Item is selected when partly or fully equal to the current url
   * @type {string}
   */
  pathMatch?: 'full'|'prefix';
  /**
   * Where this is a home item
   * @type {boolean}
   */
  home?: boolean;
  parent?: MenuItem;
  selected?: boolean;
  menuData?: any;

  /**
   * @returns item parents in top-down order
   */
  static getParents(item: MenuItem): MenuItem[];

  static isParent(item: MenuItem, possibleChild: MenuItem): boolean;
}

export const MENU_ITEMS: MenuItem[] = [
  {module: 'dashboard', title: 'Trang chủ', icon: 'ic_tachometer', link: '/pages/dashboard', home: true},
  {
    module: 'products', title: 'Sản phẩm', icon: 'ic_products font-weight-bold', children: [
      {title: 'Thương hiệu', link: '/pages/prd/manufacturers'},
      {title: 'Danh mục', link: '/pages/prd/categories'},
      {title: 'Sản phẩm', link: '/pages/prd/products'},
      {title: 'Tùy chọn', link: '/pages/prd/options'},
    ],
  },
  {
    module: 'exchange', title: 'Giao dịch', icon: 'ic_sales', children: [
      {module: 'orders', title: 'Đơn hàng', link: '/pages/ord/orders'},
      {module: 'exchange_settings', title: 'Cài đặt', link: '/pages/ord/settings'},
      {title: 'Tình trạng thanh toán', link: '/pages/payments'},
    ],
  },
  {
    module: 'stocks', title: 'Kho vận', icon: 'ic_logistics', children: [
      {title: 'Triển khai đơn', link: '/pages/sto/dep/orders'},
      {
        title: 'Quản lý kho', children: [
          {title: 'Nhập kho', link: '/pages/sto/imp/requests'},
          {title: 'Xuất kho', link: '/pages/sto/exp/requests'},
          // {title: 'Tồn/kiểm kho', link: '/pages/sto/inventories'},
        ],
      },
      {title: 'Danh sách kho', link: '/pages/sto/stocks'},
    ],
  },
  {
    module: 'users', title: 'Khách hàng', icon: 'ic_user_group', children: [
      {module: 'user_list', title: 'Danh sách', link: '/pages/users/users'},
    ],
  },
  {module: 'administrator', title: 'Quản trị viên', link: '/pages/usrs'},
];

export const SETUP_ITEMS: MenuItem[] = [
  {module: 'menus', title: 'Menu', icon: 'ic_menus', link: '/pages/pages/menus'},
  {
    module: 'config', title: 'Trang', icon: 'ic_category', children: [
      {title: 'Danh sách trang', link: '/pages/pages/pages'},
      {title: 'Danh sách module', link: '/pages/pages/modules'},
      {title: 'Thiết lập', link: '/pages/pages/settings'},
    ],
  },
  {
    module: 'medias', title: 'Đa phương tiện', icon: 'ic_media', children: [
      {title: 'Quản lý hình ảnh', link: '/pages/media/filemanager'},
      // {title: 'Sitemap', link: '/pages/media/sitemaps'},
    ],
  },
  // {module: 'informations', title: 'Thông tin', icon: 'ic_information', link: '/pages/informations'},
  // {module: 'designs', title: 'Translation', icon: 'ic_information', link: '/pages/design/translates'},
  // {
  //   module: 'localizations', title: 'Địa phương', icon: 'fa fa-location-arrow', children: [
  //     {title: 'Quốc gia', link: '/pages/localization/countries'},
  //     {title: 'Tỉnh/thành phố', link: '/pages/localization/provinces'},
  //     {title: 'Quận/huyện', link: '/pages/localization/districts'},
  //     {title: 'Xã/phường', link: '/pages/localization/wards'},
  //     {title: 'Địa điểm', link: '/pages/localization/locations'},
  //   ],
  // },
  {
    module: 'decentralizations', title: 'Phân quyền', icon: 'ic_permission', children: [
      {module: 'administrator', title: 'Quản trị viên', link: '/pages/usrs'},
      {module: 'admin_group', title: 'Nhóm quản trị', link: '/pages/usrs/groups'},
      {module: 'user_role', title: 'Vai trò người dùng', link: '/pages/usrs/roles'},
    ],
  },
  // {module: 'language', title: 'Ngôn ngữ', icon: 'ic_globe', link: '/pages/setting/languages'},
  {module: 'system', title: 'Hệ thống', icon: 'ic_system', link: '/pages/setting/settings'},
];
