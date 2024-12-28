import { AfterViewInit, Component, ElementRef, Input, OnDestroy, OnInit, Renderer2, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { OrderFrmOrderStatusComponent } from '../frm-order-status/frm-order-status.component';
import { OrdersRepository } from '../../shared/services';

@Component({
  selector: 'ngx-ord-detail',
  templateUrl: './detail.component.html',
})
export class OrderDetailComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: OrdersRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(OrderFrmOrderStatusComponent) frmOrderStatus: OrderFrmOrderStatusComponent;
  @ViewChild('viewport') viewport: ElementRef;
  info: any = null;
  products: any = null;
  productsLoaded: boolean = false;
  hideBreadcrumb: boolean = false;

  @Input() set item(order: any) {
    this.info = order;
    this.hideBreadcrumb = true;
    this.productsLoaded = false;
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: OrdersRepository,
              private _renderer2: Renderer2, private _el: ElementRef, private _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.info = this._route.snapshot.data['info'];
    console.log(this.info);
  }

  private getInfo(id: number): void {
    this.repository.find(id, {embed: 'user,products'}, false).then((res: any) => {
      console.log(res.data);
      // _.each(res.data, (val, key) => this.info[key] = val);
      this.info = res.data;
    }, (errors) => console.log(errors));
  }

  // Override fn
  /*protected getData(): void {
    console.log(this.info);
    this.data.loading = true;
    this.repository.getProducts(this.info.id).then((res: any) => {
      console.log(res);
      this.data.items = res.data;
      this.data.loading = false;
    }, (errors) => {
      console.log(errors);
      this.data.loading = false;
    });
  }*/

  ngOnInit(): void {
    if (!this.info.user || !this.info.products) this.getInfo(this.info.id);
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit() {
    // setTimeout(() => this.getData(), 200);
  }

  changeOrderStatus(): void {
    // this.frmOrderStatus.show(this.info);
  }

  onFrmOrderStatusSuccess(res: any): void {
    console.log(res);
    // _.each(res, (val, key) => this.info[key] = val);
  }

  onFrmEditAddressSuccess(res: any): void {
    console.log(res);
    this.info = res;
  }

  onFrmEditInfoSuccess(res: any): void {
    console.log(res);
    this.info = res;
  }

  createStoRequest(item: any): void {
    const info = [];
    info['invoice_id'] = item.id;
    info['out_type'] = 'sale';
    info['platform'] = 'website';
    info['content'] = 'Xuất bán cho khách hàng';
    info['products'] = this.products;
    console.log(info);
    console.log(item);
    // this.frmStoRequest.show(info, true);
  }

  onFormStoRequestSuccess(res: any): void {
    console.log(res);
    this.info.sto_request_id = res.id;
    this.info.order_status = res.invoice?.order_status;
  }

  onOrderProductsLoad(res: any): void {
    this.products = res;
    this.productsLoaded = true;
  }

  changeSupervisor(): void {
    console.log(this.info);
  }
}
