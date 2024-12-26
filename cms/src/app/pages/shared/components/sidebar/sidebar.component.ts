import { Component, EventEmitter, Input, OnChanges, OnDestroy, OnInit, Output, SimpleChanges } from '@angular/core';
import { ActivatedRoute, NavigationEnd, Router } from '@angular/router';
import { Subject } from 'rxjs';
import { SidebarService } from '../../../../@core/utils';
import { Usr } from '../../../../@core/entities';
import { MenuItem } from '../../../pages-menu';
import { MenusRepository } from '../../../../@core/repositories';

@Component({
  selector: 'ngx-sidebar',
  styleUrls: ['./sidebar.component.scss'],
  templateUrl: './sidebar.component.html',
})
export class SidebarComponent implements OnInit, OnDestroy, OnChanges {
  private destroy$: Subject<void> = new Subject<void>();
  menuItems: MenuItem[] = [];
  setupItems: MenuItem[] = [];
  menuInput: MenuItem[] = [];
  menuNav: MenuItem[] = [];
  private currentLink: string = '';

  private initMenu(menuList: MenuItem[]): any[] {
    let index = 0;
    let itemSelected: MenuItem = null;
    const menuItems: MenuItem[] = [];
    if (this.auth) _.forEach(menuList, (lv1: MenuItem) => {
      if (lv1.module && !(this.auth.isCRUD(lv1.module, 'view'))) return;
      lv1.id = ++index;
      if (lv1.link === this.currentLink) itemSelected = lv1;
      if (lv1.children) {
        const children: MenuItem[] = [];
        _.forEach(lv1.children, (lv2: MenuItem) => {
          if (lv2.module && !(this.auth.isCRUD(lv2.module, 'view'))) return;
          lv2.id = ++index;
          lv2.parent = lv1;
          if (lv2.link === this.currentLink) itemSelected = lv2;
          if (lv2.children) _.forEach(lv2.children, (lv3: MenuItem) => {
            lv3.id = ++index;
            lv3.parent = lv2;
            if (lv3.link === this.currentLink) itemSelected = lv3;
          });
          children.push(lv2);
        });
        lv1.children = children;
      }
      menuItems.push(lv1);
    });
    return [itemSelected, menuItems];
  }

  protected updateMainMenu(): void {
    if (this.menuNav) {
      _.each(this.menuInput, (item) => {
        if (item.module === 'public') item.children = _.concat(this.menuNav, item.children);
      });
    }
    const menuList: MenuItem[] = _.concat(this.menuInput);
    const [itemSelected, menuItems] = this.initMenu(menuList);
    this.menuItems = menuItems;
    if (itemSelected) this.itemMenuClick(this.menuItems, itemSelected);
  }

  @Input() set menu(menuInput: MenuItem[]) {
    this.menuInput = menuInput;
    this.updateMainMenu();
  }

  @Input() set setup(menuList: MenuItem[]) {
    const [itemSelected, menuItems] = this.initMenu(menuList);
    this.setupItems = menuItems;
    if (itemSelected) this.itemMenuClick(this.setupItems, itemSelected);
  }

  @Input() toggleState: boolean;
  @Output() hoverItem = new EventEmitter();
  @Output() toggleSubMenu = new EventEmitter();
  @Output() selectItem = new EventEmitter();
  @Output() itemClick = new EventEmitter();
  isSetup: boolean = false;

  private inSetupLink(): boolean {
    const setupLinks = [
      '/pages/pages/menus', '/pages/design', '/pages/informations', '/pages/localization', '/pages/usrs', '/pages/setting',
      '/pages/pages/widgets', '/pages/pages/modules',
      '/pages/pages/layouts', '/pages/pages/patterns', '/pages/pages/settings',
      '/pages/pages/categories', // '/pages/pages/pages',
    ];
    const ignoreLinks = ['/pages/setting/intro'];
    let isSetup: boolean = false;
    for (let i = 0; i < setupLinks.length; i++) {
      if (_.includes(this.currentLink, setupLinks[i]) && !_.includes(ignoreLinks, this.currentLink)) {
        isSetup = true;
        break;
      }
    }
    return isSetup;
  }

  auth: Usr;

  constructor(protected _router: Router, protected _sidebarService: SidebarService, private _route: ActivatedRoute, private _menus: MenusRepository) {
    this._router.events.subscribe((event: any) => {
      if (event instanceof NavigationEnd) {
        this.currentLink = event.url;
        console.log(this.currentLink);
        if (!this.isSetup) this.isSetup = this.inSetupLink();
      }
    });
    this.auth = this._route.snapshot.data['auth'];
  }

  // Get nav menus
  protected getNavMenus(): void {
    this.menuNav = [];
    this._menus.nav().then((res: any) => {
        const items: MenuItem[] = [];
        _.forEach(res.data, (item_lv1: any) => {
          if (item_lv1.is_sidebar && !item_lv1.source) {
            const child_lv2: any = [];
            _.forEach(item_lv1.children, (item_lv2: any) => {
              if (item_lv2.is_sidebar && !item_lv2.source) {
                const child_lv3: MenuItem[] = [];
                _.forEach(item_lv2.children, (item_lv3: any) => {
                  if (item_lv3.is_sidebar && !item_lv3.source) child_lv3.push({title: item_lv3.name, menuData: item_lv3});
                });
                child_lv2.push({title: item_lv2.name, children: child_lv3, menuData: item_lv2});
              }
            });
            items.push({title: item_lv1.name, icon: (item_lv1.icon ? item_lv1.icon : 'ic_category') + ' font-weight-bold', children: child_lv2, menuData: item_lv1, module: 'pages'});
          }
        });
        this.menuNav = items;
        this.updateMainMenu();
      }, (res: any) => {
        console.log(res.errors);
      },
    );
  }

  ngOnChanges(changes: SimpleChanges) {
    // Collapsed
    if (changes.toggleState) {
      if (changes.toggleState.currentValue) {
        _.forEach(this.menuItems, (lv1: MenuItem) => {
          lv1.expanded = false;
          if (lv1.children) _.forEach(lv1.children, (lv2: MenuItem) => {
            lv2.expanded = lv1.expanded;
            if (lv2.children) _.forEach(lv2.children, (lv3: MenuItem) => {
              lv3.expanded = lv1.expanded;
            });
          });
        });
      }
      if (changes.toggleState.previousValue !== undefined && changes.toggleState.currentValue) {
        console.log('expanded');
      }
    }
  }

  ngOnInit(): void {
    this.getNavMenus();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  private itemMenuCollapsed(item: MenuItem) {
    item.expanded = false;
    if (item.children) _.forEach(item.children, (menuItem: MenuItem) => {
      menuItem.expanded = false;
      this.itemMenuCollapsed(menuItem);
    });
  }

  private itemMenuExpanded(item: MenuItem) {
    item.expanded = true;
    if (item.parent) {
      this.itemMenuExpanded(item.parent);
    }
  }

  private itemMenuSelected(item: MenuItem) {
    item.selected = true;
    if (item.parent) {
      this.itemMenuSelected(item.parent);
    }
  }

  private itemMenuUnselected(item: MenuItem): void {
    item.selected = false;
    if (item.children) _.forEach(item.children, (menuItem: MenuItem) => {
      this.itemMenuUnselected(menuItem);
    });
  }

  private itemMenuClick(menuItems: MenuItem[], item: MenuItem): void {
    // Collapsed & Expanded
    if (!!item.expanded) {
      this.itemMenuCollapsed(item);
    } else {
      this.itemMenuExpanded(item);
    }

    // Collapsed other
    const children = item.parent ? item.parent.children : menuItems;
    _.forEach(children, (menuItem: MenuItem) => {
      if (menuItem.id !== item.id) this.itemMenuCollapsed(menuItem);
    });

    // Unselected
    _.forEach(menuItems, (menuItem: MenuItem) => {
      this.itemMenuUnselected(menuItem);
    });

    // Selected
    this.itemMenuSelected(item);

    this._sidebarService.toggle(false, 'menu-sidebar');
  }

  onHoverItem(item: MenuItem): void {
    this.hoverItem.emit(item);
  }

  onSelectItem(item: MenuItem): void {
    this.selectItem.emit(item);
  }

  onToggleSubMenu(menuItems: MenuItem[], item: MenuItem): void {
    if (!(item.children && item.children.length) && item.menuData) {
      console.log(item.menuData);
      if (item.menuData.page_id) {
        this._router.navigate(['/pages/pages/pages', item.menuData.page_id, 'contents']);
      }
    } else {
      this.itemMenuClick(menuItems, item);
    }
    this.toggleSubMenu.emit(item);
  }

  onItemClick(menuItems: MenuItem[], item: MenuItem): void {
    if (!(item.children && item.children.length) && item.menuData) {
      console.log(item.menuData);
      if (item.menuData.page_id) {
        this._router.navigate(['/pages/pages/pages', item.menuData.page_id, 'contents']);
      }
    } else {
      this.itemMenuClick(menuItems, item);
    }
    this.itemClick.emit(item);
    this.onSelectItem(item);
  }

  onSetupOpenClick(): void {
    this.isSetup = true;
  }

  onSetupCloseClick(): void {
    this.isSetup = false;
  }

  /*hasPermission(item: MenuItem): boolean {
    if (item.roles && item.roles.length) {
      return this.auth && this.auth.inAnyRole(item.roles);
    }

    return true;
  }*/

  hasPermissionSetting(): boolean {
    return true;
    // return this.auth && ( this.auth.isSuperAdmin() || this.auth.isAdmin() );
    // return this.auth && this.auth.isCRUD('systems', 'view');
  }
}
