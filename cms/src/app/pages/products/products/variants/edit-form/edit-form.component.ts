import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ProductsRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-pd-product-variant-edit-form',
  templateUrl: './edit-form.component.html',
})
export class ProductVariantEditFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    name?: AbstractControl,
    long_name?: AbstractControl,
    model?: AbstractControl,
    is_gift?: AbstractControl,
    price?: AbstractControl,
    coins?: AbstractControl,
    weight?: AbstractControl,
    length?: AbstractControl,
    width?: AbstractControl,
    height?: AbstractControl,
    short_description?: AbstractControl,
    stock_status?: AbstractControl,
    status?: AbstractControl,
    meta_title?: AbstractControl,
    meta_description?: AbstractControl,
    meta_keyword?: AbstractControl,
    alias?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      long_name: [''],
      model: [''],
      price: [0, Validators.compose([Validators.min(0)])],
      is_gift: [true],
      coins: [0, Validators.compose([Validators.min(0)])],
      weight: [''],
      length: [''],
      width: [''],
      height: [''],
      short_description: [''],
      stock_status: [''],
      status: [true],
      meta_title: [''],
      meta_description: [''],
      meta_keyword: [''],
      alias: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  protected afterDataLoading(): void {
  }

  private getDropdownData(): void {
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  private setValues(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
  }

  protected setInfo(info: any): void {
    this.setValues(info);
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
      // this.getInfo(info.id, {embed: 'category,descs'});
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl && !_.includes(['is_gift', 'price', 'coins', 'weight', 'length', 'width', 'height', 'status'], key)) this.controls[key].setValue('');
      });
      this.controls.is_gift.setValue(true);
      this.controls.price.setValue(0);
      this.controls.coins.setValue(0);
      this.controls.weight.setValue(0);
      this.controls.length.setValue(0);
      this.controls.width.setValue(0);
      this.controls.height.setValue(0);
      this.controls.status.setValue(true);
    }
    this.getDropdownData();
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(params: any, dontHide?: boolean, loading ?: boolean): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      newParams.price = params.price ? params.price : 0;
      newParams.coins = params.coins ? params.coins : 0;
      this.submitted = true;
      this.repository.updateVariant(this.info, newParams, loading).then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), (errors) => this.handleError(errors));
    }
    console.log(params);
  }

  onChangeGift(): void {
    if (!this.controls.is_gift.value) {
      // this.controls.is_free.setValue(false);
      this.controls.coins.setValue(0);
    } else {
    }
  }

  onChangeName(): void {
    if (!this.info || (this.info && !this.info.meta_title)) {
      if (!this.controls.meta_title.touched) {
        // console.log('meta_title', this.controls.meta_title.value, this.controls.meta_title.touched);
        this.controls.meta_title.setValue(this.controls.long_name ? this.controls.long_name.value : this.controls.name.value);
      }
    }
    if (!this.info || (this.info && !this.info.alias)) {
      if (!this.controls.alias.touched) {
        // console.log('alias', this.controls.alias.value, this.controls.alias.touched);
        this.controls.alias.setValue(this.utilityHelper.toAlias(this.controls.long_name ? this.controls.long_name.value : this.controls.name.value));
      }
    }
    if (!this.info || (this.info && !this.info.long_name)) {
      if (!this.controls.long_name.touched) {
        // console.log('alias', this.controls.alias.value, this.controls.alias.touched);
        this.controls.long_name.setValue(this.controls.name.value);
      }
    }
  }
}
