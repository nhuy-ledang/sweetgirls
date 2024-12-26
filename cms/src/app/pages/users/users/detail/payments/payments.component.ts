import { AfterViewInit, Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../../@core/utils';
import { Security } from '../../../../../@core/security';
import { CookieVar } from '../../../../../@core/services';
import { OrdersRepository } from '../../../../orders/shared/services';
import { AppList } from '../../../../../app.base';
import { User } from '../../../shared/entities';

@Component({
  selector: 'ngx-user-payments',
  templateUrl: './payments.component.html',
})
export class UserPaymentsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  info: User = null;
  columnList = [
    {id: 'idx', name: 'Hóa đơn', checkbox: true, disabled: true},
    {id: 'product', name: 'Sản phẩm/dịch vụ', checkbox: true, disabled: false},
    {id: 'total', name: 'Số tiền', checkbox: true, disabled: false},
    {id: 'status', name: 'Tình trạng', checkbox: true, disabled: false},
    {id: 'payment_method', name: 'Phương thức thanh toán', checkbox: true, disabled: false},
    {id: 'payment_at', name: 'Ngày thanh toán', checkbox: true, disabled: false},
    {id: 'supervisor', name: 'Phụ trách', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: OrdersRepository, private _route: ActivatedRoute, cookie: CookieVar) {
    super(router, security, state, repository);
    this.info = this._route.parent.snapshot.data['info'];
    console.log(this.info);
    this.data.data.user_id = this.info.id;
    const q = this._route.snapshot.queryParams['q'];
    if (q) this.data.data.q = q;
    this.columnInt(cookie, 'cus_payments');
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  detail(item: any) {
    this.storageHelper.set('order_info_' + item.id, item);
    this._router.navigate(['/pages/ord/orders', item.id]);
  }

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
  }
}
