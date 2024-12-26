import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { IncludedProductsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-included-product-form',
  templateUrl: './form.component.html',
})
export class IncludedProductFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: IncludedProductsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    name?: AbstractControl,
    price?: AbstractControl,
    short_description?: AbstractControl,
    status?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: IncludedProductsRepository, languages: LanguagesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      price: [0, Validators.compose([Validators.min(0)])],
      short_description: [''],
      status: [true],
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

  show(info?: any, copyData?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
      // this.getInfo(info.id, {embed: 'descs'});
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['status'], key)) this.controls[key].setValue('');
      });
      this.controls.status.setValue(true);
    }
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
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, newParams, loading).then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), (errors) => this.handleError(errors));
      } else {
        this.repository.create(newParams, loading).then((res) => this.handleSuccess(res.data, dontHide), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
