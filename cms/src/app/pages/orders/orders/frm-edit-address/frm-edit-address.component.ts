import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppForm } from '../../../../app.base';
import { DistrictsRepository, ProvincesRepository, WardsRepository } from '../../../localization/shared/services';
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
              private _orders: OrdersRepository,
              private _provinces: ProvincesRepository,
              private _districts: DistrictsRepository,
              private _wards: WardsRepository) {
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
    this.provinceData.loading = true;
    this._provinces.all().then((res: any) => {
      this.provinceData.loading = false;
      this.provinceData.items = res.data;
      // this.onProvinceChange();
    }), (errors: any) => {
      this.provinceData.loading = false;
      console.log(errors);
    };
  }

  private getAllDistrict(): void {
    if (this.controls.shipping_province_id.value) {
      this.districtData.loading = true;
      console.log(this.controls.shipping_province_id.value);

      this._districts.all(this.controls.shipping_province_id.value, 'vt_province_id').then((res: any) => {
        console.log(res);

        this.districtData.loading = false;
        this.districtData.items = res.data;
        this.onDistrictChange();
      }), (errors: any) => {
        this.districtData.loading = false;
        console.log(errors);
      };
    }
  }

  private getAllWard(): void {
    if (this.controls.shipping_district_id.value) {
      this.wardData.loading = true;
      console.log(this.controls.shipping_district_id.value);

      this._wards.all(this.controls.shipping_district_id.value, 'vt_district_id').then((res: any) => {
        console.log(res);

        this.wardData.loading = false;
        this.wardData.items = res.data;
      }), (errors: any) => {
        this.wardData.loading = false;
        console.log(errors);
      };
    }
  }

  onProvinceChange(): void {
    if (this.controls.shipping_province_id.value) {
      this.getAllDistrict();
      const md = _.find(this.districtData.items, {province_id: this.controls.shipping_province_id.value});
      // if (md) {
      //   this.updateConfigs(md.configs);
      //   if (!this.controls.name.value) this.controls.name.setValue(md.name);
      // }
      console.log(md);
    }
  }

  onDistrictChange(): void {
    if (this.controls.shipping_district_id.value) {
      this.getAllWard();
      const md = _.find(this.wardData.items, {district_id: this.controls.shipping_district_id.value});
      // if (md) {
      //   this.updateConfigs(md.configs);
      //   if (!this.controls.name.value) this.controls.name.setValue(md.name);
      // }
      console.log(md);
    }
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
