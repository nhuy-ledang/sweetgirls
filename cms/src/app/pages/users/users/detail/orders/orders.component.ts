import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../../@core/utils';
import { Security } from '../../../../../@core/security';
import { CookieVar } from '../../../../../@core/services';
import { OrdersRepository } from '../../../../orders/shared/services';
import { ConfirmComponent } from '../../../../../@theme/modals';
import { AppList } from '../../../../../app.base';
import { OrderFrmOrderStatusComponent } from '../../../../orders/orders/frm-order-status/frm-order-status.component';
import { User } from '../../../shared/entities';

@Component({
  selector: 'ngx-user-orders',
  templateUrl: './orders.component.html',
})
export class UserOrdersComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(OrderFrmOrderStatusComponent) frmOrderStatus: OrderFrmOrderStatusComponent;
  info: User = null;

  columnList = [
    {id: 'idx', name: 'Mã đơn hàng', checkbox: true, disabled: true},
    {id: 'type', name: 'Sản phẩm/dịch vụ', checkbox: true, disabled: false},
    {id: 'total', name: 'Số tiền', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Thời gian', checkbox: true, disabled: false},
    {id: 'supervisor', name: 'Phụ trách', checkbox: true, disabled: false},
    {id: 'status', name: 'Tình trạng', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: OrdersRepository, private _route: ActivatedRoute, cookie: CookieVar) {
    super(router, security, state, repository);
    this.info = this._route.parent.snapshot.data['info'];
    console.log(this.info);
    this.data.data.user_id = this.info.id;
    const q = this._route.snapshot.queryParams['q'];
    if (q) this.data.data.q = q;
    this.columnInt(cookie, 'cus_orders');
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

  remove(item: any): void {
    console.log(item);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info.master_id);
    }
  }
}
