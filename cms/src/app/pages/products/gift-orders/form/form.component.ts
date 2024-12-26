import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { ProductsRepository } from '../../shared/services';
import { GiftOrdersRepository } from '../../shared/services';
import { AppForm } from '../../../../app.base';

interface ProductInterface {
  id?: number;
  idx?: number;
  name: string;
  price: number;
  quantity: number;
}

@Component({
  selector: 'ngx-pd-gift-order-form',
  templateUrl: './form.component.html',
})
export class GiftOrderFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any;
  controls: {
    name?: AbstractControl,
    amount?: AbstractControl,
    description?: AbstractControl,
    start_date?: AbstractControl,
    end_date?: AbstractControl,
    limited?: AbstractControl,
    status?: AbstractControl,
  };
  products: ProductInterface[] = [];
  total: number = 0;
  bsConfig: any = {withTimepicker: true, dateInputFormat: 'DD/MM/YYYY, H:mm', adaptivePosition: true, containerClass: 'theme-red'};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: GiftOrdersRepository, protected _products: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      amount: ['', Validators.compose([Validators.required])],
      description: [''],
      start_date: [''],
      end_date: [''],
      limited: [''],
      status: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected calcTotal(): void {
    let total = 0;
    _.forEach(this.products, (item: any) => {
      total += item.price * item.quantity;
    });
    this.total = total;
  }

  protected setValues(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.start_date.setValue(info.start_date ? new Date(info.start_date) : '');
    this.controls.end_date.setValue(info.end_date ? new Date(info.end_date) : '');
    // Products
    this.products = [];
    if (info.products && info.products.length) {
      _.forEach(info.products, (item: any) => {
        const product = _.cloneDeep(item);
        product.id = item.product_id ? item.product_id : 0;
        this.addProduct(product);
      });
    }
    this.calcTotal();
  }

  protected setInfo(info: any): void {
    this.setValues(info);
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    this.products = [];
    if (info) {
      this.setInfo(info);
      // this.getInfo(info.id, false);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['status'], key)) this.controls[key].setValue('');
      });
      this.controls.status.setValue(false);
      this.controls.start_date.setValue('');
      this.controls.end_date.setValue('');
    }
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      if (params.start_date && params.start_date instanceof Date) newParams.start_date = params.start_date.format('isoDateTime');
      if (params.end_date && params.end_date instanceof Date) newParams.end_date = params.end_date.format('isoDateTime');
      const products = [];
      _.forEach(this.products, (item: any) => {
        if (item.quantity > 0) products.push({id: item.id, name: item.name, price: item.price, quantity: item.quantity});
      });
      if (!products.length) return this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', errors: 'Chưa có sản phẩm!'});
      newParams['products'] = products;
      this.submitted = true;
      if (!this.info) {
        this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      } else {
        this.repository.update(this.info, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }

  // <editor-fold desc="Add Product">
  data: {loading?: boolean, items: any[], paging: number, page: number, pageSize: number, sort: string, order: string, data: any} = {loading: false, items: [], paging: 0, page: 1, pageSize: 25, sort: 'name', order: 'asc', data: {q: '', is_search: 1, is_free: 0}};
  private tempCache: any = {};

  protected getData(): void {
    const key = 'q=' + this.data.data.q + 'is_free=' + this.data.data.is_free;
    if (this.tempCache[key] && this.tempCache[key].length) {
      this.data.items = this.tempCache[key];
    } else {
      this.data.loading = true;
      this._products.search(this.data, false).then((res: any) => {
          this.data.items = res.data;
          this.data.loading = false;
          this.tempCache[key] = this.data.items;
        }, (errors) => {
          console.log(errors);
          this.data.loading = false;
        },
      );
    }
  }

  private idx: number = 0;

  addProduct(item): void {
    _.remove(this.products, {id: item.id});
    if (item.stock_status === 'out_of_stock') return this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', errors: `Sản phẩm "${item.name ? item.name : item.long_name}" đã hết hàng!`});
    const newItem = _.cloneDeep(item);
    newItem.idx = ++this.idx;
    newItem.name = item.long_name ? item.long_name : item.name;
    newItem.name_display = item.name_display ? item.name_display :  item.name;
    newItem.quantity = item.quantity ? item.quantity : 1;
    this.products = [newItem].concat(this.products);
    this.calcTotal();
  }

  removeProduct(item): void {
    _.remove(this.products, {idx: item.idx});
    this.calcTotal();
  }

  private timer: any;

  onFilter(event): void {
    if (this.timer) {
      clearTimeout(this.timer);
      this.timer = undefined;
    }
    this.timer = setTimeout(() => this.getData(), 800);
  }

  // </editor-fold>

  onProductChange(item): void {
    console.log(item);
    item.price = parseFloat(item.price);
    this.calcTotal();
  }

  onShown(): void {
    setTimeout(() => this.inputElement.nativeElement.focus());
  }
}
