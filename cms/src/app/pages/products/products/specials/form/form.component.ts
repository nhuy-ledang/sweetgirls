import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ProductsRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-pd-special-form',
  templateUrl: './form.component.html',
})

export class SpecialFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  product: any;
  info: any|boolean;
  controls: {
    price?: AbstractControl,
    priority?: AbstractControl,
    start_date?: AbstractControl,
    end_date?: AbstractControl,
  };
  bsConfig: any = {withTimepicker: true, dateInputFormat: 'DD/MM/YYYY, HH:mm', adaptivePosition: true, containerClass: 'theme-red'};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductsRepository) {
    super(router, security, state, repository);

    this.form = fb.group({
      price: [0, Validators.compose([Validators.required])],
      priority: [1, Validators.compose([Validators.min(0)])],
      start_date: [''],
      end_date: [''],
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
      if (this.controls.hasOwnProperty(key) && !_.includes(['start_date', 'end_date'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.start_date.setValue(info.start_date ? new Date(info.start_date) : '');
    this.controls.end_date.setValue(info.end_date ? new Date(info.end_date) : '');
    this.info = info;
  }

  show(product, info?: any): void {
    this.resetForm(this.form);
    this.product = product;
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      /*_.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['priority', 'date_start', 'date_end'], key)) this.controls[key].setValue('');
      });*/
      this.controls.price.setValue(0);
      this.controls.start_date.setValue('');
      this.controls.end_date.setValue('');
      this.controls.priority.setValue(1);
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
      newParams.price = params.price ? params.price : 0;
      if (params.start_date && params.start_date instanceof Date) newParams.start_date = params.start_date.format('isoDateTime');
      if (params.end_date && params.end_date instanceof Date) newParams.end_date = params.end_date.format('isoDateTime');
      this.submitted = true;
      if (this.info) {
        this.repository.updateSpecial(this.product.id, this.info.id, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        newParams['product_id'] = this.product.id;
        this.repository.createSpecial(this.product.id, newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
