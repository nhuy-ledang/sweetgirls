import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ProductOptionsRepository, OptionsRepository } from '../../../services';

@Component({
  selector: 'ngx-pd-product-option-form',
  templateUrl: './form.component.html',
})
export class ProductOptionFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductOptionsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  product: any;
  controls: {
    option_id?: AbstractControl,
  };
  optionData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductOptionsRepository, private _options: OptionsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      option_id: ['', Validators.compose([Validators.required])],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  protected afterDataLoading(): void {
  }

  private getDropdownData(): void {
    this.optionData.loading = true;
    Promise.all([this._options.all()]).then((res) => {
      this.optionData.loading = false;
      this.optionData.items = res[0] ? res[0].data : [];
      this.afterDataLoading();
    }, (res) => {
      console.log(res);
      this.optionData.loading = false;
    });
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

  show(product, info?: any): void {
    this.resetForm(this.form);
    this.product = product;
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue('');
      });
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
}
