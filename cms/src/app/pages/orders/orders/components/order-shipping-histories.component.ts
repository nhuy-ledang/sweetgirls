import { AfterViewInit, Component, Input, OnDestroy, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppList } from '../../../../app.base';
import { OrderShippingHistoriesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-ord-order-shipping-histories',
  templateUrl: './order-shipping-histories.component.html',
})
export class OrderShippingHistoriesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  order: any = null;

  @Input() set info(order: any) {
    this.order = order;
    this.data.data.order_id = order.id;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: OrderShippingHistoriesRepository) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    // setTimeout(() => this.getData(), 200);
  }
}
