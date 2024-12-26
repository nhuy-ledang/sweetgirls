import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { GiftProductsRepository } from '../../shared/services';
import { GiftSetsRepository } from '../../shared/services';
import { AppForm } from '../../../../app.base';

interface ProductInterface {
  id?: number;
  idx?: number;
  name: string;
  price: number;
  quantity: number;
}

@Component({
  selector: 'ngx-pd-gift-set-form',
  templateUrl: './form.component.html',
})
export class GiftSetFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any;
  controls: {
    name?: AbstractControl,
    start_date?: AbstractControl,
    end_date?: AbstractControl,
    description?: AbstractControl,
    status?: AbstractControl,
  };
  products: ProductInterface[] = [];
  total: number = 0;

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: GiftSetsRepository, protected _products: GiftProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      start_date: [''],
      end_date: [''],
      description: [''],
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
      if (info.hasOwnProperty(key) && !_.includes(['start_date', 'end_date'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.start_date.setValue(info.date ? new Date(info.date) : '');
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
      delete newParams.start_date;
      delete newParams.end_date;
      if (params.start_date && params.start_date instanceof Date && !isNaN(params.start_date)) newParams.start_date = params.start_date.getIsoDate();
      if (params.end_date && params.end_date instanceof Date && !isNaN(params.end_date)) newParams.end_date = params.end_date.getIsoDate();
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
  data: {loading?: boolean, items: any[], paging: number, page: number, pageSize: number, sort: string, order: string, data: any} = {loading: false, items: [], paging: 0, page: 1, pageSize: 25, sort: 'name', order: 'asc', data: {q: '', is_search: 1, supplier_id: 0}};
  private tempCache: any = {};

  protected getData(): void {
    const key = 'q=' + this.data.data.q + '_s=' + this.data.data.supplier_id;
    if (this.tempCache[key] && this.tempCache[key].length) {
      this.data.items = this.tempCache[key];
    } else {
      this.data.loading = true;
      this._products.get(this.data, false).then((res: any) => {
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
    console.log(item);
    _.remove(this.products, {id: item.id});
    const newItem = _.cloneDeep(item);
    newItem.idx = ++this.idx;
    newItem.name = item.long_name ? item.long_name : item.name;
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
