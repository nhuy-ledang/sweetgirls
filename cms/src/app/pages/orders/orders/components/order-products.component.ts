import { AfterViewInit, Component, EventEmitter, Input, OnDestroy, OnInit, Output } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppList } from '../../../../app.base';
import { OrdersRepository } from '../../shared/services';

@Component({
  selector: 'ngx-ord-order-products',
  templateUrl: './order-products.component.html',
})
export class OrderProductsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: OrdersRepository;
  order_id: any = null;

  @Input() set info(order_id: any) {
    this.order_id = order_id;
    this.getData();
  }
  @Output() onLoad: EventEmitter<any> = new EventEmitter<any>();

  constructor(router: Router, security: Security, state: GlobalState, repository: OrdersRepository) {
    super(router, security, state, repository);
  }

  // Override fn
  protected getData(): void {
    console.log(this.order_id);
    this.data.items = [];
    this.data.loading = true;
    this.repository.getProducts(this.order_id).then((res: any) => {
      console.log(res);
      _.forEach(res.data, (item, index) => {
        item.index = index + (this.data.pageSize * (this.data.page - 1));
        this.data.items.push(item);
      });
      this.data.loading = false;
      this.onLoad.emit(this.data.items);
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
}
