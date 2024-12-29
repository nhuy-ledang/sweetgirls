import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppForm } from '../../../../app.base';
import { ProductsRepository } from '../../../products/shared/services';
import { OrdersRepository } from '../../shared/services';
// import { DistrictsRepository, ProvincesRepository, WardsRepository } from '../../../localization/shared/services';

@Component({
  selector: 'ngx-ord-frm-product',
  templateUrl: './frm-product.component.html',
})
export class OrderFrmProductComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  repository: OrdersRepository;
  info: any;
  controls: {
    user_id?: AbstractControl,
    first_name?: AbstractControl,
    email?: AbstractControl,
    phone_number?: AbstractControl,
    payment_code?: AbstractControl,
    tax_code?: AbstractControl,
    address?: AbstractControl,
    // status?: AbstractControl,
    comment?: AbstractControl,
  };
  userSelected: {id: ''|number, name: string} = {id: '', name: ''};
  paymentMethodList: {id: string, name: string}[] = [{id: 'cod', name: 'COD'}, {id: 'bank_transfer', name: 'Chuyển khoản ngân hàng'}];
  products: {id: number, name: string, quantity: number, price: number, weight: number, master_id: number, gift_set_id: number}[] = [];
  discount_type: 'F'|'P' = 'P';
  discount: number = 0;
  adjustment: number = 0;
  sub_total: number = 0;
  total: number = 0;
  provinceData: {loading: boolean, items: any[]} = {loading: false, items: []};
  districtData: {loading: boolean, items: any[]} = {loading: false, items: []};
  wardData: {loading: boolean, items: any[]} = {loading: false, items: []};
  hasShippingFee: boolean = false;

  /*statusList: any[] = [
    {id: 'draft', name: 'Nháp'},
    {id: 'sent', name: 'Đã gởi'},
    {id: 'rejected', name: 'Từ chối'},
    {id: 'accepted', name: 'Chấp nhận'},
  ];*/

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: OrdersRepository,
              private _element: ElementRef,
              private _products: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      user_id: [''],
      first_name: ['', Validators.compose([Validators.required])],
      email: [''],
      phone_number: ['', Validators.compose([Validators.required])],
      payment_code: [''],
      tax_code: [''],
      address: [''],
      // status: [this.statusList[0].id],
      comment: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  private calcTotal() {
    let total = 0;
    _.forEach(this.products, (item: any) => {
      total += item.price * item.quantity;
    });
    this.sub_total = total;
    let discount_value = 0;
    if (this.discount_type === this.CONST.DISCOUNT_AMOUNT) {
      discount_value = this.discount;
    } else if (this.discount_type === this.CONST.DISCOUNT_PERCENT) {
      discount_value = Math.floor(total * this.discount / 100);
    }
    total -= discount_value;
    total += this.adjustment;

    this.total = total;
    this.needUpateShippingFee();
  }

  protected setInfo(info: any): void {
    _.each(this.controls, (val, key) => {
      if (info.hasOwnProperty(key)) this.controls[key].setValue(info[key]);
    });
    // Products
    this.products = [];
    if (info.products && info.products.length) {
      _.forEach(info.products, (item: any) => {
        const product = _.cloneDeep(item);
        product.id = item.product_id ? item.product_id : 0;
        this.addProduct(product);
      });
    }
    this.userSelected.id = info.user_id;
    if (info.user) {
      this.userSelected.name = info.user.display + (info.user.company ? (' - ' + info.user.company) : '');
    } else {
      this.userSelected.name = info.first_name + (info.company ? (' - ' + info.company) : '');
    }
    this.discount_type = info.discount_type;
    this.discount = info.discount;
    this.adjustment = info.adjustment;
    this.calcTotal();
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.products = [];
    this.discount_type = 'P';
    this.discount = 0;
    this.adjustment = 0;
    this.sub_total = 0;
    this.total = 0;
    this.info = false;
    if (info) {
      this.setInfo(info);
      this.getInfo(info.id, {embed: 'products'}, false);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['payment_code', 'is_invoice'], key)) this.controls[key].setValue('');
      });
      this.controls.payment_code.setValue(this.paymentMethodList[0].id);
    }
    if (!this.provinceData.items.length) this.getAllProvince();
    this.modal.show();
  }

  hide(): void {
    this.userSelected = {id: '', name: ''};
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      newParams.is_invoice = params.is_invoice ? 1 : 0;
      const products = [];
      _.forEach(this.products, (item: any) => {
        if (item.quantity > 0) products.push({id: item.id, name: item.name, model: item.model, price: item.price, quantity: item.quantity});
      });
      if (!products.length) {
        return this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', errors: 'Báo giá chưa có sản phẩm!'});
      }
      newParams['products'] = products;
      newParams['discount_type'] = this.discount_type;
      newParams['discount'] = this.discount;
      newParams['adjustment'] = this.adjustment;
      newParams['order_type'] = 'product';
      newParams['shipping_fee'] = 0;
      this.submitted = true;
      if (!this.info) {
        this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      } else {
        this.repository.update(this.info, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }

  onCustomerSelected($event?: any): void {
    if ($event) {
      if ($event.display) {
        this.controls.first_name.setValue($event.display);
        this.bindingName();
      }
      if ($event.phone_number) {
        this.controls.phone_number.setValue($event.phone_number);
        this.bindingPhoneNumber();
      }
      if ($event.email) this.controls.email.setValue($event.email);
      this.calcTotal();
      this.needUpateShippingFee();
    }
  }

  // <editor-fold desc="Add Product">
  data: {loading?: boolean, items: any[], paging: number, page: number, pageSize: number, sort: string, order: string, data: any} = {
    loading: false,
    items: [],
    paging: 0,
    page: 1,
    pageSize: 25,
    sort: 'name',
    order: 'asc',
    data: {
      q: '',
      embed: '',
      filter_child: 1,
    },
  };
  private tempCache: any = {};

  protected getData(): void {
    const key = 'q=' + this.data.data.q;
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

  addProduct(item: any): void {
    console.log(item);
    if (!item.status/* || item.quantity === 0*/) return;
    const product = _.find(this.products, {id: item.id});
    if (!product) {
      const newItem = _.cloneDeep(item);
      newItem.price = item.special ? item.special : item.price;
      newItem.quantity = 1;
      this.products = [newItem].concat(this.products);
      this.calcTotal();
    }
  }

  removeProduct(item): void {
    console.log(item);
    _.remove(this.products, {id: item.id});
    this.calcTotal();
  }

  selectDiscountType(discount_type): void {
    this.discount_type = discount_type;
    this.onChangeDiscount();
  }

  needUpateShippingFee(): void {
    this.hasShippingFee = false;
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

  onProductChange(item): void {
    console.log(item);
    this.calcTotal();
  }

  onChangeDiscount($event?: any): void {
    this.calcTotal();
  }

  onChangeAdjustment($event?: any): void {
    this.calcTotal();
  }

  onShown(): void {
    setTimeout(() => this.inputElement.nativeElement.focus());
  }

  private getAllProvince(): void {
    
  }

  private getAllDistrict(): void {
    
  }

  private getAllWard(): void {
    
  }

  onProvinceChange(): void {
    
  }

  onDistrictChange(): void {
    
  }

  bindingName(): void {
  }

  bindingPhoneNumber(): void {
  }

  onGetShippingFee(): void {
  }
}
