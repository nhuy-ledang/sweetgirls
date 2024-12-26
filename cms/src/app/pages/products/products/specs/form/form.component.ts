import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ProductSpecsRepository } from '../../../services';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { LanguagesRepository } from '../../../../../@core/repositories';

@Component({
  selector: 'ngx-pd-product-spec-form',
  templateUrl: './form.component.html',
})

export class ProductSpecFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductSpecsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  product: any;
  info: any | boolean;
  controls: {
    name?: AbstractControl,
    value?: AbstractControl,
    sort_order?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductSpecsRepository, languages: LanguagesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      name: [''],
      value: [''],
      sort_order: [0, Validators.compose([Validators.min(0)])],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
    this.getAllLanguage();
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
        if (this.controls.hasOwnProperty(key) && !_.includes(['sort_order'], key)) this.controls[key].setValue('');
      });
      this.controls.sort_order.setValue(0);
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
