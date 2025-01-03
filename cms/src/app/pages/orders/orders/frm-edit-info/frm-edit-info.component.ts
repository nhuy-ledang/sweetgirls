import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppForm } from '../../../../app.base';
import { OrdersRepository } from '../../shared/services';

@Component({
  selector: 'ngx-ord-frm-edit-info',
  templateUrl: './frm-edit-info.component.html',
})
export class OrderFrmEditInfoComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  controls: {
    shipping_first_name?: AbstractControl,
    shipping_phone_number?: AbstractControl,
  };

  provinceData: {loading: boolean, items: any[]} = {loading: false, items: []};
  districtData: {loading: boolean, items: any[]} = {loading: false, items: []};
  wardData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState,
              private _orders: OrdersRepository) {
    super(router, security, state);
    this.form = fb.group({
      shipping_first_name: [''],
      shipping_phone_number: [''],
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
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && !_.includes(['status'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.districtData.items = [];
    this.wardData.items = [];
    this.info = info;
  }

  show(info: any): void {
    this.resetForm(this.form);
    console.log(info);
    this.info = false;
    if (info) {
      this.setInfo(info);
      // this.getInfo(info.id);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['status'], key)) this.controls[key].setValue('');
      });
      // this.controls.status.setValue(true);
    }
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      this.submitted = true;
      this._orders.update(this.info.id, params, false).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
  }
}
