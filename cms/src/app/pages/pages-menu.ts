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
  {module: 'orders', title: 'Đơn hàng', link: '/pages/ord/orders', icon: 'ic_sales'},
  {module: 'user_list', title: 'Khách hàng', link: '/pages/users/users', icon: 'ic_user_group'},
  {module: 'administrator', title: 'Quản trị viên', link: '/pages/usrs', icon: 'ic_user_group'},
  {module: 'system', title: 'Hệ thống', icon: 'ic_system', link: '/pages/setting/settings'},
];

export const SETUP_ITEMS: MenuItem[] = [
];
