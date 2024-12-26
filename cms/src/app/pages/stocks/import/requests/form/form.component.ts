import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../../@core/utils';
import { Security } from '../../../../../@core/security';
import { AppForm } from '../../../../../app.base';
import { UsrsRepository } from '../../../../../@core/repositories';
import { ProductsRepository } from '../../../../products/shared/services';
import { StocksRepository, InRequestsRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-sto-imp-request-form',
  templateUrl: './form.component.html',
})
export class ImpRequestFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any;
  controls: {
    owner_id?: AbstractControl,
    stock_id?: AbstractControl,
    in_type?: AbstractControl,
    content?: AbstractControl,
    attach?: AbstractControl,
    customer_id?: AbstractControl,
    storekeeper_id?: AbstractControl,
    manager_id?: AbstractControl,
    in_stock_id?: AbstractControl,
    note?: AbstractControl,
  };
  stockData: {loading: boolean, items: any[]} = {loading: false, items: []};
  staffData: {loading: boolean, items: any[]} = {loading: false, items: []};
  managerData: {loading: boolean, items: any[]} = {loading: false, items: []};
  keeperData: {loading: boolean, items: any[]} = {loading: false, items: []};
  products: {id: number, product_id: number, name: string, unit: string, weight: number, ord_quantity: number, quantity: number, price: number}[] = [];
  total: number = 0;
  inTypes: {id: string, name: string}[] = [
    {id: 'purchase', name: 'Mua vào'},
    {id: 'produce', name: 'Tự sản xuất'},
    {id: 'transfer', name: 'Chuyển nội bộ'},
    {id: 'return', name: 'Hàng hoàn'},
  ];
  suppSelected: {id: number, name: string} = {id: 0, name: ''};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: InRequestsRepository,
              private _element: ElementRef, private _stocks: StocksRepository, private _staffs: UsrsRepository, private _products: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      owner_id: [''],
      stock_id: ['', Validators.compose([Validators.required])],
      in_type: [''],
      content: [''],
      attach: [''],
      customer_id: [''],
      storekeeper_id: [''],
      manager_id: [''],
      in_stock_id: [''],
      note: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  protected afterDataLoading(): void {
    if (!this.info.stock_id) this.controls.stock_id.setValue(this.stockData.items[0]?.id);
    if (!this.info.manager_id) this.controls.manager_id.setValue(this.managerData.items[0]?.id);
    if (!this.info.storekeeper_id) this.controls.storekeeper_id.setValue(this.keeperData.items[0]?.id);
    this.changeStock();
  }

  protected getDropdownData(): void {
    this.stockData.loading = true;
    this.staffData.loading = true;
    Promise.all([this._stocks.all(), this._staffs.all()]).then((res) => {
      this.stockData.loading = false;
      this.stockData.items = res[0] ? res[0].data : [];
      this.staffData.loading = false;
      this.staffData.items = res[1] ? res[1].data : [];
      this.afterDataLoading();
    }, (res) => {
      console.log(res);
      this.stockData.loading = false;
      this.staffData.loading = false;
    });
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
    // Products
    this.products = [];
    if (info.products && info.products.length) {
      _.forEach(info.products, (item: any) => {
        const newItem = _.cloneDeep(item);
        newItem.due_date = newItem.due_date ? new Date(newItem.due_date) : new Date().getPlus(30);
        this.products.push(newItem);
      });
    }
    if (info.in_supplier) this.suppSelected = {id: info.in_supplier.id, name: info.in_supplier.company};
    this.changeQuantity();
    this.onFromTypeChange();
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.products = [];
    this.info = false;
    this.suppSelected = {id: 0, name: ''};
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['in_type'], key)) this.controls[key].setValue('');
      });
      this.controls.in_type.setValue(this.inTypes[0].id);
      this.onFromTypeChange();
    }
    this.getDropdownData();
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
      const products = [];
      _.forEach(this.products, (item: any) => {
        const newItem = _.cloneDeep(item);
        delete newItem.due_date;
        if (item.due_date && item.due_date instanceof Date && !isNaN(item.due_date)) newItem.due_date = item.due_date.getIsoDate();
        if (item.quantity > 0) products.push(newItem);
      });
      if (!products.length) return this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', errors: 'Đơn hàng chưa có sản phẩm!'});
      newParams['products'] = products;
      newParams['type'] = 'in';
      newParams['stock_idx'] = this.stockData.items.find(item => item.id === parseInt(params.stock_id, 0))?.idx;
      this.submitted = true;
      if (!this.info) {
        newParams['date'] = new Date().format('yyyy-mm-dd HH:MM:ss');
        this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      } else {
        this.repository.update(this.info, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }

  onFromTypeChange(): void {
    if (this.controls.in_type.value === 'purchase') {
      // this.controls.in_supplier_id.setValidators([Validators.required]);
      this.controls.in_stock_id.clearValidators();
    } else if (this.controls.in_type.value === 'transfer') {
      // this.controls.in_supplier_id.clearValidators();
      this.controls.in_stock_id.setValidators([Validators.required]);
    } else {
      // this.controls.in_supplier_id.clearValidators();
      this.controls.in_stock_id.clearValidators();
    }
    // this.controls.in_supplier_id.updateValueAndValidity();
    this.controls.in_stock_id.updateValueAndValidity();
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

  changeStock(): void {
    if (this.controls.stock_id.value) {
      const stock = _.find(this.stockData.items, {id: parseInt(this.controls.stock_id.value, 0)});
      if (stock) {
        this.managerData.items = _.filter(this.staffData.items, item => item.id === stock.manager_id);
        this.controls.manager_id.setValue(this.managerData.items[0]?.id);

        const keeperIdsArray = stock.keeper_ids.split(',').map(id => parseInt(id, 10));
        this.keeperData.items = _.filter(this.staffData.items, item => keeperIdsArray.includes(item.id));
        this.controls.storekeeper_id.setValue(this.keeperData.items[0]?.id);
      }
    }
    console.log(this.managerData.items);
    console.log(this.keeperData.items);
  }

  changeQuantity(): void {
    let total = 0;
    _.forEach(this.products, (item) => {
      total += item.price * item.quantity;
    });
    this.total = total;
  }

  addProduct(item): void {
    console.log(item);
    const product = _.find(this.products, {product_id: item.id});
    if (!product) {
      const newItem = _.cloneDeep(item);
      newItem.id = 0;
      newItem.product_id = item.id;
      newItem.price = item.price_im;
      newItem.ord_quantity = 1;
      newItem.quantity = 1;
      newItem.shipment = '';
      newItem.due_date = new Date().getPlus(30);
      newItem.code = '';
      this.products = [newItem].concat(this.products);
      this.changeQuantity();
    }
  }

  addAllProduct(): void {
    this._products.all().then((res: any) => {
      console.log(res.data);
      _.each(res.data, item => item.product_id = item.id);
      this.products = res.data;
      console.log(res.data);
      this.changeQuantity();
    }), (errors: any) => {
      console.log(errors);
    };
  }

  removeProduct(item): void {
    console.log(item);
    _.remove(this.products, {product_id: item.id});
    this.changeQuantity();
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
