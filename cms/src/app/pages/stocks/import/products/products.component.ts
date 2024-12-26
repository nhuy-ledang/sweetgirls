import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { CookieVar } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { InTicketsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-sto-imp-products',
  templateUrl: './products.component.html',
})
export class ImpProductsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  repository: InTicketsRepository;
  columnList = [
    {id: 'id', name: '#', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày giờ', checkbox: true, disabled: false},
    {id: 'category', name: 'Danh mục', checkbox: true, disabled: false},
    {id: 'prd_idx', name: 'Mã sản phẩm', checkbox: true, disabled: false},
    {id: 'prd_image', name: 'Hình ảnh', checkbox: true, disabled: false},
    {id: 'prd_name', name: 'Tên sản phẩm', checkbox: true, disabled: false},
    {id: 'prd_short', name: 'Đặc điểm/thuộc tính/Quy cách', checkbox: true, disabled: false},
    {id: 'tp_content', name: 'Ghi chú sản phẩm', checkbox: true, disabled: false},
    {id: 'prd_unit', name: 'ĐVT', checkbox: true, disabled: false},
    {id: 'tp_quantity', name: 'Tổng SL', checkbox: true, disabled: false},
    {id: 'tp_total', name: 'Số tiền', checkbox: true, disabled: false},
    {id: 'idx', name: 'Mã phiếu', checkbox: true, disabled: false},
    {id: 'in_type', name: 'Loại', checkbox: true, disabled: false},
    {id: 'from', name: 'Nơi xuất đến', checkbox: true, disabled: false},
    {id: 'stock', name: 'Kho nhập', checkbox: true, disabled: false},
    {id: 'staff', name: 'Người giao', checkbox: true, disabled: false},
    {id: 'storekeeper', name: 'Thủ kho', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: InTicketsRepository, protected _cookie: CookieVar) {
    super(router, security, state, repository);
    this.data.sort = 'sto__tickets.id';
    this.data.order = 'desc';
  }

  // Override fn
  protected getData(cb?: Function, loading?: boolean): void {
    return super.getData(cb, loading, null, 'getProducts');
  }

  ngOnInit(): void {
    this.columnInt(this._cookie, 'sto_imp_products');
    this.data.data = {q: '', idx: '', product_idx: '', product: '', embed: 'in_supplier,in_stock,stock,staff,storekeeper'};
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  onConfirm(data: any): void {
    console.log(data);
  }
}
