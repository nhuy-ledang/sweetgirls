import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { CookieVar } from '../../../../@core/services';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { StocksRepository } from '../../shared/services';
import { StoStockFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-sto-stocks',
  templateUrl: './stocks.component.html',
})
export class StoStocksComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(StoStockFormComponent) form: StoStockFormComponent;
  columnList = [
    {id: 'image', name: 'Hình', checkbox: true, disabled: false},
    {id: 'idx', name: 'Mã kho', checkbox: true, disabled: false},
    {id: 'name', name: 'Tên kho', checkbox: true, disabled: false},
    {id: 'description', name: 'Mô tả', checkbox: true, disabled: false},
    {id: 'type_id', name: 'Loại kho', checkbox: true, disabled: false},
    {id: 'phone_number', name: 'Số điện thoại', checkbox: true, disabled: false},
    {id: 'province_id', name: 'Tỉnh/thành', checkbox: true, disabled: false},
    {id: 'district_id', name: 'Quận/huyện', checkbox: true, disabled: false},
    {id: 'ward_id', name: 'Phường/xã', checkbox: true, disabled: false},
    {id: 'address', name: 'Địa chỉ', checkbox: true, disabled: false},
    {id: 'manager_id', name: 'Quản kho', checkbox: true, disabled: false},
    {id: 'keeper_ids', name: 'Thủ kho', checkbox: true, disabled: false},
    {id: 'seller_ids', name: 'Phân quyền bán hàng', checkbox: true, disabled: false},
    {id: 'default_place', name: 'Tỉnh/thành mặc định giao từ kho này', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: StocksRepository, private _cookie: CookieVar) {
    super(router, security, state, repository);
    this.data.sort = 'id';
    this.data.order = 'asc';
    this.data.data = {q: ''};
  }

  ngOnInit(): void {
    this.columnInt(this._cookie, 'stocks');
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

  edit(item: any): void {
    this.form.show(item);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }
}
