import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { ProductsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-product-quantity-form',
  templateUrl: './quantity-form.component.html',
})
export class ProductQuantityFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  copyData: any|boolean;
  controls: {
    quantity?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductsRepository, languages: LanguagesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      quantity: ['', Validators.compose([Validators.required])],
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
        if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl && !_.includes(['quantity'], key)) this.controls[key].setValue('');
      });
    }
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(params: any, loading ?: boolean): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      this.submitted = true;
      if (this.info) {
        this.repository.updateQuantity(this.info, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
