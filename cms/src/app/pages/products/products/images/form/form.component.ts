import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ProductsRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-pd-image-form',
  templateUrl: './form.component.html',
})

export class ImageFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  product: any;
  info: any|boolean;
  controls: {
    image_alt?: AbstractControl,
    sort_order?: AbstractControl,
  };
  @ViewChild('uploaderEl') uploaderEl: {init: any};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductsRepository) {
    super(router, security, state, repository);

    this.form = fb.group({
      image_alt: [''],
      sort_order: [0, Validators.compose([Validators.min(0)])],
    });
    this.controls = this.form.controls;
    this.fb = fb;
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
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
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
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      this.submitted = true;
      if (this.info) {
        this.repository.updateImage(this.product.id, this.info.id, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        newParams['product_id'] = this.product.id;
        this.repository.createImage(this.product.id, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
