import { Component, Input, OnDestroy } from '@angular/core';
import { AppList } from '../../../../app.base';
import { ActivatedRoute, Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { OrdersRepository } from '../../../orders/shared/services';
import { CookieVar } from '../../../../@core/services';

@Component({
  selector: 'ngx-db-com-row-order-revenues',
  templateUrl: './row-order-revenues.component.html',
})
export class ComRowOrderRevenuesComponent extends AppList implements OnDestroy {
  repository: OrdersRepository;
  filterData: any;

  @Input() set filter(daterange: {start_date: string, end_date: string}) {
    console.log(daterange);
    this.data.data = {q: '', invoice_no: '', payment_code: 'bank_transfer', payment_status: '', shipping_status: '', order_status: 'completed', is_invoice: '', embed: 'shipping,user'};
    this.data.data.start_date = daterange ? daterange.start_date : '';
    this.data.data.end_date = daterange ? daterange.end_date : '';
    this.columnInt(this._cookie, 'orders');
    this.getData();
  }

  columnList = [
    {id: 'idx', name: 'Mã hóa đơn', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày tạo', checkbox: true, disabled: false},
    {id: 'user', name: 'Khách hàng', checkbox: true, disabled: false},
    {id: 'user_no', name: 'Mã KH', checkbox: true, disabled: false},
    {id: 'user_email', name: 'Email', checkbox: true, disabled: false},
    {id: 'user_phone_number', name: 'Số điện thoại', checkbox: true, disabled: false},
    {id: 'user_address', name: 'Địa chỉ', checkbox: true, disabled: false},
    {id: 'sub_total', name: 'Tạm tính', checkbox: true, disabled: false},
    {id: 'discount', name: 'Giảm giá', checkbox: true, disabled: false},
    {id: 'shipping_fee', name: 'Phí vận chuyển', checkbox: true, disabled: false},
    {id: 'total', name: 'Tổng tiền', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: OrdersRepository, private _cookie: CookieVar, private _route: ActivatedRoute) {
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

  myTabs: any = {bank_transfer: true, all: false, cod: false, international: false, domestic: false};

  onSelectTab(tabActive: string): void {
    _.each(this.myTabs, (v, k) => {
      if (k !== tabActive) this.myTabs[k] = false;
    });
    this.myTabs[tabActive] = true;
    this.data.data.payment_code = tabActive === 'all' ? '' : tabActive;
    this.onFilter();
  }
}
