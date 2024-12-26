import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { CookieVar } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { InvoicesRepository } from '../../../orders/shared/services';

@Component({
  selector: 'ngx-sto-dep-histories',
  templateUrl: './histories.component.html',
})
export class DepHistoriesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  columnList = [
    {id: 'id', name: '#', checkbox: true, disabled: false},
    {id: 'idx', name: 'Mã hóa đơn', checkbox: true, disabled: false},
    {id: 'order_no', name: 'Mã phụ lục', checkbox: true, disabled: false},
    {id: 'customer', name: 'Khách hàng', checkbox: true, disabled: false},
    {id: 'num_of_pay', name: 'Số đợt TT', checkbox: true, disabled: false},
    {id: 'start_date', name: 'Phát hành', checkbox: true, disabled: false},
    {id: 'end_date', name: 'Hạn thanh toán', checkbox: true, disabled: false},
    {id: 'end_at', name: 'Kết thúc', checkbox: true, disabled: false},
    {id: 'is_vat', name: 'VAT', checkbox: true, disabled: false},
    {id: 'paid', name: 'Đã thanh toán', checkbox: true, disabled: false},
    {id: 'unpaid', name: 'Còn lại', checkbox: true, disabled: false},
    {id: 'total', name: 'Tổng tiền', checkbox: true, disabled: false},
    {id: 'status', name: 'Tình trạng', checkbox: true, disabled: false},
    {id: 'owner', name: 'Tạo bởi', checkbox: true, disabled: false},
    {id: 'approved', name: 'Duyệt phát hành', checkbox: true, disabled: false},
    {id: 'seller', name: 'Phụ trách', checkbox: true, disabled: false},
    {id: 'file', name: 'Chứng từ', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: InvoicesRepository, protected _cookie: CookieVar, protected _route: ActivatedRoute) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
    this.columnInt(this._cookie, 'sto_dep_deployment');
    this.data.data = {q: '', invoice_no: '', order_no: '', is_recurring: '', embed: ''};
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    // setTimeout(() => this.getData(), 200);
  }

  onConfirm(data: any): void {
    console.log(data);
  }
}
