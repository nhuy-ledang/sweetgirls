import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { GiftProductsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-gift-product-form',
  templateUrl: './form.component.html',
})
export class GiftProductFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: GiftProductsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    name?: AbstractControl,
    long_name?: AbstractControl,
    model?: AbstractControl,
    price?: AbstractControl,
    weight?: AbstractControl,
    length?: AbstractControl,
    width?: AbstractControl,
    height?: AbstractControl,
    image?: AbstractControl,
    description?: AbstractControl,
    status?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: GiftProductsRepository, languages: LanguagesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      long_name: [''],
      model: [''],
      price: [0, Validators.compose([Validators.min(0)])],
      weight: [''],
      length: [''],
      width: [''],
      height: [''],
      image: [''],
      description: [''],
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
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
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
        if (this.controls.hasOwnProperty(key) && !_.includes(['status', 'weight', 'length', 'width', 'height'], key)) this.controls[key].setValue('');
      });
      this.controls.status.setValue(true);
      this.controls.weight.setValue(0);
      this.controls.length.setValue(0);
      this.controls.width.setValue(0);
      this.controls.height.setValue(0);
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
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), (errors) => this.handleError(errors));
      } else {
        this.repository.create(this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(res.data, dontHide), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
