import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormArray, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ProductsRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-pd-product-variant-form',
  templateUrl: './form.component.html',
})
export class ProductVariantFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  product: any;
  controls: {
    options?: AbstractControl,
  };
  optForm: FormArray|any;
  optionData: {loading: boolean, items: any[]} = {loading: false, items: []};
  optionValueData: any = {};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      options: fb.array([]),
    });
    this.controls = this.form.controls;
    this.optForm = this.form.controls.options;
    this.fb = fb;
  }

  private addOption(item: any): void {
    this.optionValueData[item.option_id] = item.values;
    this.optForm.push(this.fb.group({
      option_id: [item.option_id],
      option_name: [item.name],
      option_value_id: ['', Validators.compose([Validators.required])],
    }));
  }

  private getOptions(): void {
    this.optionData.loading = true;
    this.repository.getOptions(this.product.id, true).then((res) => {
      // console.log(res.data);
      this.optionData.loading = false;
      this.optionData.items = res.data;
      _.forEach(res.data, (item) => this.addOption(item));
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

  show(product): void {
    this.resetForm(this.form);
    this.optForm.controls = [];
    this.product = product;
    this.optionData.items = [];
    this.optionValueData = {};
    this.getOptions();
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
      const options: {option_id: number, option_value_id: number}[] = [];
      for (let i = 0; i < params.options.length; i++) {
        options.push({option_id: params.options[i].option_id, option_value_id: parseInt(params.options[i].option_value_id, 0)});
      }
      this.submitted = true;
      this.repository.createVariant(this.product, {options: options}).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
    }
  }
}
