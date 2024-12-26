import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { FlashsalesRepository } from '../../../services';
import { ProductsRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-pd-flashsale-value-form',
  templateUrl: './form.component.html',
})
export class FlashsaleValueFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  flashsale: any;
  controls: {
    product_id?: AbstractControl,
    price?: AbstractControl,
    quantity?: AbstractControl,
  };
  curSelected: {id: number, name: string} = {id: 0, name: ''};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: FlashsalesRepository, private _product: ProductsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      product_id: ['', Validators.compose([Validators.required])],
      price: ['', Validators.compose([Validators.required])],
      quantity: ['', Validators.compose([Validators.min(0)])],
    });
    this.controls = this.form.controls;
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

  show(flashsale, info?: any): void {
    this.resetForm(this.form);
    this.flashsale = flashsale;
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key)) this.controls[key].setValue('');
      });
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
      newParams['start_date'] = this.flashsale.start_date || '';
      newParams['end_date'] = this.flashsale.end_date || '';
      newParams['priority'] = 0;
      newParams['is_flashsale'] = 1;
      newParams['flashsale_id'] = this.flashsale.id;
      this.submitted = true;
      if (this.info) {
        this._product.updateSpecial(this.info.product_id, this.info.id, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this._product.createSpecial(this.controls.product_id.value, newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
