// import { Component, OnInit, ViewChild } from '@angular/core';
// import { ActivatedRoute } from '@angular/router';
// import { GlobalState, SidebarService } from '../@core/utils';
// import { Usr } from '../@core/entities';
// import { MENU_ITEMS, SETUP_ITEMS } from './pages-menu';
// import { DlgPasswordComponent } from './shared/modals';

// @Component({
//   selector: 'ngx-pages',
//   styleUrls: ['./pages.component.scss'],
//   templateUrl: './pages.component.html',
// })
// export class PagesComponent implements OnInit {
//   @ViewChild(DlgPasswordComponent) dlgPassword: DlgPasswordComponent;
//   menu = MENU_ITEMS;
//   setup = SETUP_ITEMS;
//   toggleState: boolean = false;

//   constructor(private _route: ActivatedRoute, private _state: GlobalState, private _sidebarService: SidebarService) {
//   }

//   ngOnInit(): void {
//     const auth = this._route.snapshot.data['auth'];
//     console.log(auth);
//     this._sidebarService.onToggle().subscribe((data: { compact: boolean, tag: string }) => {
//       this.toggleState = data.compact;
//     });
//     this._state.subscribe('dlgPassword:show', (data: { user: Usr }) => {
//       this.dlgPassword.show(data.user);
//     });
//   }

//   miniStatus: string = '';

//   minibarToggle(status: 'mini' | 'extend' | 'close'): void {
//     this.miniStatus = status;
//   }
// }
import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState, SidebarService } from '../@core/utils';
import { Usr } from '../@core/entities';
import { MENU_ITEMS, SETUP_ITEMS } from './pages-menu';
import { DlgPasswordComponent } from './shared/modals';

@Component({
  selector: 'ngx-pages',
  styleUrls: ['./pages.component.scss'],
  templateUrl: './pages.component.html',
})
export class PagesComponent implements OnInit {
  @ViewChild(DlgPasswordComponent) dlgPassword: DlgPasswordComponent;
  menu = MENU_ITEMS;
  setup = SETUP_ITEMS;
  toggleState: boolean = false;
  isPosPage: boolean = false; // Biến điều kiện để ẩn/hiện sidebar

  constructor(
    private _route: ActivatedRoute,
    private _state: GlobalState,
    private _sidebarService: SidebarService,
    private _router: Router // Thêm Router để kiểm tra URL
  ) {}

  ngOnInit(): void {


    this._sidebarService.onToggle().subscribe((data: { compact: boolean, tag: string }) => {
      this.toggleState = data.compact;
    });

    this._state.subscribe('dlgPassword:show', (data: { user: Usr }) => {
      this.dlgPassword.show(data.user);
    });

    // Kiểm tra URL khi trang được khởi tạo
    this.isPosPage = this._router.url.includes('/pages/pos');
  }

  miniStatus: string = '';

  minibarToggle(status: 'mini' | 'extend' | 'close'): void {
    this.miniStatus = status;
  }
}

