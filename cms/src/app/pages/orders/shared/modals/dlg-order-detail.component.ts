import { AfterViewInit, Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppList } from '../../../../app.base';
import { OrdersRepository } from '../services';

@Component({
  selector: 'ngx-ord-dlg-order-detail',
  templateUrl: './dlg-order-detail.component.html',
})

export class DlgOrderDetailComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: OrdersRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onClose: EventEmitter<any> = new EventEmitter<any>();
  info: any;

  constructor(router: Router, security: Security, state: GlobalState, repository: OrdersRepository) {
    super(router, security, state, repository);
  }

  // Override fn
  protected getData(): void {
    this.data.items = [];
    this.data.loading = true;
    this.repository.getProducts(this.info.id).then((res: any) => {
      console.log(res);
      _.forEach(res.data, (item, index) => {
        item.index = index + (this.data.pageSize * (this.data.page - 1));
        this.data.items.push(item);
      });
      this.data.loading = false;
    }, (errors) => {
      console.log(errors);
      this.data.loading = false;
    });
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    // setTimeout(() => this.getData(), 200);
  }

  show(info: any): void {
    this.info = _.cloneDeep(info);
    this.getData();
    this.modal.show();
  }

  hide(): void {
    this.onClose.emit(this.info);
    this.modal.hide();
  }

  changeOrderStatus(order_status: string): void {
    if (this.info.order_status !== order_status) {
      this.repository.changeOrderStatus(this.info.id, {status: order_status}, true).then((res) => {
        console.log(res.data);
        this.info.order_status = res.data.order_status;
        this.info.order_status_name = res.data.order_status_name;
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    }
  }
}
