import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppForm } from '../../../../app.base';
import { OrdersRepository } from '../../shared/services';

@Component({
  selector: 'ngx-ord-frm-edit-address',
  templateUrl: './frm-edit-address.component.html',
})
export class OrderFrmEditAddressComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  controls: {
    shipping_province_id?: AbstractControl,
    shipping_district_id?: AbstractControl,
    shipping_ward_id?: AbstractControl,
    shipping_address_1?: AbstractControl,
  };

  provinceData: {loading: boolean, items: any[]} = {loading: false, items: []};
  districtData: {loading: boolean, items: any[]} = {loading: false, items: []};
  wardData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState,
              private _orders: OrdersRepository) {
    super(router, security, state);
    this.form = fb.group({
      shipping_province_id: [''],
      shipping_district_id: [''],
      shipping_ward_id: [''],
      shipping_address_1: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  private getAllProvince(): void {
    
  }

  private getAllDistrict(): void {
    
  }

  private getAllWard(): void {
    
  }

  onProvinceChange(): void {
    
  }

  onDistrictChange(): void {
    
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
    if (!this.provinceData.items.length) this.getAllProvince();
    // if (!this.provinceData.items.length) setTimeout(() => this.getAllDistrict(), 500);
    // if (!this.provinceData.items.length) setTimeout(() => this.getAllWard(), 1000);
    this.onProvinceChange();
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
      this._orders.updateAddress(this.info.id, params, false).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
  }
}
