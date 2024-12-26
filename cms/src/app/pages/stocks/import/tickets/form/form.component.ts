import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../../@core/utils';
import { Security } from '../../../../../@core/security';
import { AppForm } from '../../../../../app.base';
import { UsrsRepository } from '../../../../../@core/repositories';
import { ProductsRepository } from '../../../../products/shared/services';
import { StocksRepository, InTicketsRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-sto-imp-ticket-form',
  templateUrl: './form.component.html',
})
export class ImpTicketFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any;
  controls: {
    idx?: AbstractControl,
    stock_id?: AbstractControl,
    in_type?: AbstractControl,
    in_supplier_id?: AbstractControl,
    supplier?: AbstractControl,
    in_stock_id?: AbstractControl,
    staff_id?: AbstractControl,
    storekeeper_id?: AbstractControl,
    accountant_id?: AbstractControl,
    note?: AbstractControl,
  };
  stockData: {loading: boolean, items: any[]} = {loading: false, items: []};
  staffData: {loading: boolean, items: any[]} = {loading: false, items: []};
  products: {id: number, product_id: number, name: string, unit: string, weight: number, ord_quantity: number, quantity: number, price: number}[] = [];
  total: number = 0;
  inTypes: {id: string, name: string}[] = [
    {id: 'purchase', name: 'Mua vào'},
    {id: 'produce', name: 'Tự sản xuất'},
    {id: 'transfer', name: 'Chuyển nội bộ'},
    {id: 'return', name: 'Hàng hoàn'},
  ];
  suppSelected: {id: number, name: string} = {id: 0, name: ''};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: InTicketsRepository,
              private _element: ElementRef, private _stocks: StocksRepository, private _staffs: UsrsRepository, private _products: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      idx: [''],
      stock_id: ['', Validators.compose([Validators.required])],
      in_type: ['', Validators.compose([Validators.required])],
      in_supplier_id: [''],
      supplier: [''],
      in_stock_id: [''],
      staff_id: [''],
      storekeeper_id: [''],
      accountant_id: [''],
      note: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  protected afterDataLoading(): void {

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
      this.controls.in_supplier_id.setValidators([Validators.required]);
      this.controls.in_stock_id.clearValidators();
    } else if (this.controls.in_type.value === 'transfer') {
      this.controls.in_supplier_id.clearValidators();
      this.controls.in_stock_id.setValidators([Validators.required]);
    } else {
      this.controls.in_supplier_id.clearValidators();
      this.controls.in_stock_id.clearValidators();
    }
    this.controls.in_supplier_id.updateValueAndValidity();
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
