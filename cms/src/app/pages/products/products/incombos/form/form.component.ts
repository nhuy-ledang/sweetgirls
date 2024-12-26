import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormArray, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ProductIncombosRepository } from '../../../services';
import { ProductsRepository } from '../../../shared/services';
import { removeAccents } from '../../../../../@core/helpers';

interface ProductInterface {
  id?: number;
  idx?: number;
  name: string;
  quantity: number;
}

@Component({
  selector: 'ngx-pd-product-incombo-form',
  templateUrl: './form.component.html',
})

export class ProductIncomboFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductIncombosRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  info: any|boolean;
  controls: {
    incombo_id?: AbstractControl,
    quantity?: AbstractControl,
  };
  products: ProductInterface[] = [];

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductIncombosRepository,
              private _products: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      incombo_id: [''],
      quantity: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info?: any, data?: any): void {
    this.resetForm(this.form);
    this.info = info;
    console.log(data);
    _.each(this.controls, (val, key) => {
      if (info.hasOwnProperty(key) && !_.includes([], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    // Products
    this.products = [];
    if (data && data.length) {
      _.forEach(data, (item: any) => {
        const product = _.cloneDeep(item);
        product.id = item.incombo_id ? item.incombo_id : 0;
        this.addProduct(product);
      });
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
    console.log(params);
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      this.submitted = true;
      // Product
      const products = [];
      _.forEach(this.products, (item: any) => {
        if (item.quantity > 0) products.push({id: item.id, name: item.name, price: item.price, quantity: item.quantity});
      });
      newParams['products'] = products;
      newParams['product_id'] = this.info.id;
      this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
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
    newItem.name = item.name;
    newItem.quantity = item.quantity ? item.quantity : 1;
    this.products = [newItem].concat(this.products).reverse();
  }

  removeProduct(item): void {
    _.remove(this.products, {idx: item.idx});
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
  }

  onShown(): void {
    setTimeout(() => this.inputElement.nativeElement.focus());
  }
}
