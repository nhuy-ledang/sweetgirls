import { Component, Input, OnDestroy } from '@angular/core';
import { AppList } from '../../../../app.base';
import { ActivatedRoute, Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { OrderProductsRepository } from '../../../orders/shared/services';
import { CookieVar } from '../../../../@core/services';

@Component({
  selector: 'ngx-db-com-row-product-revenues',
  templateUrl: './row-product-revenues.component.html',
})
export class ComRowProductRevenuesComponent extends AppList implements OnDestroy {
  repository: OrderProductsRepository;
  filterData: any;

  @Input() set filter(daterange: {mode: string, start_date: string, end_date: string}) {
    console.log(daterange);
    this.data.data = {q: '', model: '', name: '', category_name: '', payment_status: '', shipping_status: '', order_status: 'completed', is_invoice: '', embed: ''};
    this.data.data.start_date = daterange ? daterange.start_date : '';
    this.data.data.end_date = daterange ? daterange.end_date : '';
    this.data.data.mode = daterange ? daterange.mode : '';
    this.columnInt(this._cookie, 'order_products');
    this.getData();
  }

  columnList = [
    {id: 'model', name: 'Mã sp', checkbox: true, disabled: false},
    {id: 'name', name: 'Tên sản phẩm', checkbox: true, disabled: false},
    {id: 'category', name: 'Danh mục', checkbox: true, disabled: false},
    {id: 'quantity', name: 'Số lượng bán', checkbox: true, disabled: false},
    {id: 'discount_percent', name: 'Chiết khấu', checkbox: true, disabled: false},
    {id: 'quantity_return', name: 'Trả lại', checkbox: true, disabled: false},
    {id: 'total_return', name: 'Giá trị trả lại', checkbox: true, disabled: false},
    {id: 'discount', name: 'Giá trị giảm giá', checkbox: true, disabled: false},
    {id: 'total', name: 'Tổng tiền', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: OrderProductsRepository, private _cookie: CookieVar, private _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.data.pageSize = 10;
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  exportExcel(): void {
    const href = this.repository.exportExcel(this.data.data, this.data.sort, this.data.order);
    location.href = href;
  }

  exportLoading: boolean = false;

  exportExcelDetail(): void {
    this.exportLoading = true;
    const href = this.repository.exportExcelDetail(this.data.data, this.data.sort, this.data.order);
    const popupWindow = window.open(href, '_blank', 'width=300,height=200');
    if (popupWindow) {
      popupWindow.addEventListener('unload', () => {
        this.exportLoading = false;
      });
    }
    setTimeout(() => this.exportLoading = false, 10000);
  }

  tabs: any = {newEstimates: true, newContracts: false, newInvoices: false, newCustomers: false, newUpcoming: false};

  onSelectTab(tabActive: string): void {
    /*_.each(this.tabs, (v, k) => {
      if (k !== tabActive) this.tabs[k] = false;
    });*/
    this.tabs[tabActive] = true;
  }
}
