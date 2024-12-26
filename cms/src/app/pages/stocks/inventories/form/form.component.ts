import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppForm } from '../../../../app.base';
import { InventoriesRepository, StocksRepository } from '../../shared/services';
import { ProductsRepository } from '../../../products/shared/services';

@Component({
  selector: 'ngx-sto-inventory-form',
  templateUrl: './form.component.html',
})
export class StoInventoryFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  info: any;
  controls: {
    stock_id?: AbstractControl,
    name?: AbstractControl,
    date?: AbstractControl,
    note?: AbstractControl,
  };
  products: {id: number, idx: string, name: string, quantity: number, weight: number}[] = [];
  stockData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: InventoriesRepository, private _element: ElementRef, private _stocks: StocksRepository, private _products: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      stock_id: ['', Validators.compose([Validators.required])],
      name: [''],
      date: ['', Validators.compose([Validators.required])],
      note: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  private getAllStock() {
    this.stockData.loading = true;
    this._stocks.all().then((res: any) => {
      console.log(res);
      this.stockData.loading = false;
      this.stockData.items = res.data;
    }), (errors: any) => {
      this.stockData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.date.setValue(info.date ? new Date(info.date) : new Date());
    // Products
    this.products = [];
    if (info.products && info.products.length) {
      _.forEach(info.products, (item: any) => {
        const product = _.cloneDeep(item);
        product.id = item.product_id ? item.product_id : 0;
        this.products.push(product);
      });
    }
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.products = [];
    this.info = false;
    if (info) {
      this.setInfo(info);
      this.controls.stock_id.disable();
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['date'], key)) this.controls[key].setValue('');
      });
      this.controls.date.setValue(new Date());
      this.controls.stock_id.enable();
    }
    if (!this.stockData.items.length) this.getAllStock();
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
      delete newParams.date;
      if (params.date && params.date instanceof Date && !isNaN(params.date)) newParams.date = params.date.getIsoDate();
      const products = [];
      _.forEach(this.products, (item: any) => {
        if (item.quantity > 0) products.push({id: item.id, name: item.name, quantity: item.quantity, weight: item.weight * item.quantity});
      });
      if (!products.length) {
        return this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', errors: 'Đơn hàng chưa có sản phẩm!'});
      }
      newParams['products'] = products;
      newParams['type'] = 'in';
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
  data: {loading?: boolean, items: any[], paging: number, page: number, pageSize: number, sort: string, order: string, data: any} = {loading: false, items: [], paging: 0, page: 1, pageSize: 50, sort: 'name', order: 'asc', data: {q: '', is_search: 1}};
  private tempCache: any = {};

  protected getData(): void {
    const key = 'q=' + this.data.data.q;
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

  addProduct(item): void {
    console.log(item);
    const product = _.find(this.products, {id: item.id});
    if (!product) {
      const newItem = _.cloneDeep(item);
      newItem.quantity = 1;
      this.products = [newItem].concat(this.products);
    }
  }

  removeProduct(item): void {
    console.log(item);
    _.remove(this.products, {id: item.id});
  }

  private timer: any;

  onFilter(event): void {
    if (this.timer) {
      clearTimeout(this.timer);
      this.timer = undefined;
    }
    this.timer = setTimeout(() => {
      this.data.page = 1;
      this.getData();
    }, 800);
  }

  // </editor-fold>
  onShown(): void {
    setTimeout(() => this.inputElement.nativeElement.focus());
  }
}
