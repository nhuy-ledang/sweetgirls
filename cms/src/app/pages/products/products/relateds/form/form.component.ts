import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormArray, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ProductRelatedsRepository } from '../../../services';
import { ProductsRepository } from '../../../shared/services';
import { removeAccents } from '../../../../../@core/helpers';

@Component({
  selector: 'ngx-pd-product-related-form',
  templateUrl: './form.component.html',
})

export class ProductRelatedFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductRelatedsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  product: any;
  data: any;
  info: any | boolean;
  controls: {
    related_id?: AbstractControl,
    sort_order?: AbstractControl,
  };
  productData: {loading: boolean, items: any[]} = {loading: false, items: []};
  valueFormArray: Array<any> = [];
  filteredProducts: any[];
  searchControl: FormControl = new FormControl();

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductRelatedsRepository,
              private _products: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      related_id: [''],
      sort_order: [0, Validators.compose([Validators.min(0)])],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  private getAllProduct(): void {
    this.productData.loading = true;
    this._products.all().then((res: any) => {
      this.productData.loading = false;
      this.productData.items = res.data;
      this.filteredProducts = res.data;
    }), (errors: any) => {
      this.productData.loading = false;
      console.log(errors);
    };
  }

  onCheckboxChange(id: any, isChecked: boolean) {
    if (isChecked) {
      this.valueFormArray.push(id);
    } else {
      const index = this.valueFormArray.indexOf(id);
      this.valueFormArray.splice(index, 1);
    }
    console.log(this.valueFormArray);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.info = info;
  }

  show(product, info?: any, data?: any): void {
    this.resetForm(this.form);
    this.product = product;
    const dataList = [];
    if (data) {
      _.forEach(data, (item) => {
        dataList.push(item.related_id);
      });
      this.data = dataList;
      this.valueFormArray = this.data;
    }
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['sort_order'], key)) this.controls[key].setValue('');
      });
      this.controls.sort_order.setValue(0);
    }
    if (!this.productData.items.length) this.getAllProduct();

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
      this.controls.related_id.setValue(this.valueFormArray);
      _.update(params, 'related_id', _.constant(this.controls.related_id.value));
      const newParams = _.cloneDeep(params);
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info.id, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        newParams['product_id'] = this.product.id;
        this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }

  removeAll(): void {
    this.valueFormArray = [];
    this.data = [];
    this.filteredProducts = [];
    setTimeout(() => this.filteredProducts = this.productData.items, 1);
  }

  updateFilter(): void {
    const searchTerm = removeAccents(this.searchControl.value.toLowerCase());
    this.filteredProducts = this.productData.items.filter(product => {
      const productName = removeAccents(product.name.toLowerCase());
      // search like %paragraph%
      return productName.includes(searchTerm);
      // search like %word%
      // for (const word of searchTerm) if (!productName.includes(word)) return false; return true;
    });
  }
}
