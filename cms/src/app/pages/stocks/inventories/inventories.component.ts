import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { CookieVar } from '../../../@core/services';
import { AppList } from '../../../app.base';
import { InventoriesRepository } from '../shared/services';
import { StoInventoryFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-sto-inventories',
  templateUrl: './inventories.component.html',
})
export class InventoriesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: InventoriesRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(StoInventoryFormComponent) form: StoInventoryFormComponent;
  columnList = [
    {id: 'name', name: 'Tên', checkbox: true, disabled: false},
    {id: 'date', name: 'Ngày nhập', checkbox: true, disabled: false},
    {id: 'note', name: 'Ghi chú', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày tạo', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: InventoriesRepository, cookie: CookieVar, private _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.columnInt(cookie, 'stock_inventories');
    const q = this._route.snapshot.queryParams['q'];
    if (q) this.data.data.q = q;
    this.data.data.embed = 'products';
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  create(): void {
    this.form.show();
  }

  // <editor-fold desc="List Menu">
  edit(item: any): void {
    console.log(item);
    this.form.show(item);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  toggleView(): void {
    this.data.itemSelected = null;
  }

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
  }

  // </editor-fold>

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }
}
