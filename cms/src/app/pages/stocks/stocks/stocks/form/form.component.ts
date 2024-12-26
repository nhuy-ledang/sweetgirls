import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { StocksRepository, TypesRepository } from '../../../shared/services';
import { DistrictsRepository, ProvincesRepository, WardsRepository } from '../../../../localization/shared/services';
import { UsrsRepository } from '../../../../../@core/repositories';

@Component({
  selector: 'ngx-sto-form',
  templateUrl: './form.component.html',
})
export class StoStockFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    idx?: AbstractControl,
    name?: AbstractControl,
    description?: AbstractControl,
    image?: AbstractControl,
    type_id?: AbstractControl,
    phone_number?: AbstractControl,
    province_id?: AbstractControl,
    district_id?: AbstractControl,
    ward_id?: AbstractControl,
    address?: AbstractControl,
    manager_id?: AbstractControl,
    keeper_ids?: AbstractControl,
    seller_ids?: AbstractControl,
    default_place?: AbstractControl,
  };

  default_place: any;
  typeData: {loading: boolean, items: any[]} = {loading: false, items: []};
  staffData: {loading: boolean, items: any[]} = {loading: false, items: []};
  provinceData: {loading: boolean, items: any[]} = {loading: false, items: []};
  districtData: {loading: boolean, items: any[]} = {loading: false, items: []};
  wardData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: StocksRepository,
              protected _types: TypesRepository,
              private _provinces: ProvincesRepository,
              private _districts: DistrictsRepository,
              private _wards: WardsRepository,
              private _staffs: UsrsRepository) {
    super(router, security, state, repository);

    this.form = fb.group({
      idx: ['', Validators.compose([Validators.required])],
      name: [''],
      description: [''],
      image: [''],
      type_id: [''],
      phone_number: [''],
      province_id: [''],
      district_id: [''],
      ward_id: [''],
      address: [''],
      manager_id: [''],
      keeper_ids: [''],
      seller_ids: [''],
      default_place: [''],
    });
    this.controls = this.form.controls;
  }

  private getDropdownData(): void {
    this.typeData.loading = true;
    this.staffData.loading = true;
    // this.giftSetData.loading = true;
    Promise.all([this._types.all(), this._staffs.all()]).then((res) => {
      this.typeData.loading = false;
      this.typeData.items = res[0] ? res[0].data : [];
      this.staffData.loading = false;
      this.staffData.items = res[1] ? res[1].data : [];
      // this.giftSetData.loading = false;
      // this.giftSetData.items = res[2] ? res[2].data : [];
      this.afterDataLoading();
    }, (res) => {
      console.log(res);
      this.typeData.loading = false;
      this.staffData.loading = false;
      // this.giftSetData.loading = false;
    });
  }

  protected afterDataLoading(): void {
    /*const ids = [];
    if (this.info.categories) {
      const tmp = _.split(this.info.categories, ',');
      for (let i = 0; i < tmp.length; i++) ids.push(parseInt(tmp[i], 0));
    }
    // Remove and reset categories
    _.each(this.controls.categories.controls, (val, key) => {
      this.controls.categories.removeControl(key);
    });
    _.forEach(this.categoryData.items, (item: any) => {
      item.checked = ids.includes(item.id);
      this.controls.categories.addControl('category_' + item.id, new FormControl(item.checked));
    });*/
  }

  private getAllProvince(): void {
    this.provinceData.loading = true;
    this._provinces.all().then((res: any) => {
      this.provinceData.loading = false;
      this.provinceData.items = res.data;
      // this.onProvinceChange();
      this.onUpdateDefaultPlace();
    }), (errors: any) => {
      this.provinceData.loading = false;
      console.log(errors);
    };
  }

  currentProvinceId: number;
  private getAllDistrict(): void {
    if (this.controls.province_id.value) {
      this.currentProvinceId = this.controls.province_id.value;
      this.districtData.loading = true;
      console.log(this.controls.province_id.value);

      this._districts.all(this.controls.province_id.value).then((res: any) => {
        console.log(res);

        this.districtData.loading = false;
        this.districtData.items = res.data;
        // this.onDistrictChange();
      }), (errors: any) => {
        this.districtData.loading = false;
        console.log(errors);
      };
    }
  }

 currentDistrictId: number;
  private getAllWard(): void {
    if (this.controls.district_id.value) {
      this.currentDistrictId = this.controls.district_id.value;
      this.wardData.loading = true;
      console.log(this.controls.district_id.value);

      this._wards.all(this.controls.district_id.value).then((res: any) => {
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
    if (this.controls.province_id.value) {
      this.getAllDistrict();
    }
  }

  onDistrictChange(): void {
    if (this.controls.district_id.value) {
      this.getAllWard();
    }
  }

  onItemPlaceAdded(item: any): void {
    console.log(item);
    const default_place = this.default_place.map(item => item.id);
    this.controls.default_place.setValue(default_place);
  }

  onUpdateDefaultPlace(): void {
    if (this.provinceData.items.length) {
      const default_place_ids = this.info.default_place?.split(',');
      if (default_place_ids) {
        const defaultPlaceIdsAsNumbers = default_place_ids.map(id => parseInt(id, 0));
        const default_place = _.filter(this.provinceData.items, item => defaultPlaceIdsAsNumbers.includes(parseInt(item['id'], 0)));
        this.default_place = _.each(default_place, item => {
          item.display = item.name;
          item.value = item.id;
        });
      }
      console.log(this.default_place);
    }
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
    this.onUpdateDefaultPlace();
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    this.default_place = [];
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key)) this.controls[key].setValue('');
      });
    }
    this.getDropdownData();
    if (!this.provinceData.items.length) setTimeout(() => { this.getAllProvince(); }, 500);
    if (!this.districtData.items.length || (this.currentProvinceId && this.currentProvinceId !== this.info.province_id)) setTimeout(() => this.getAllDistrict(), 700);
    if (!this.wardData.items.length || (this.currentDistrictId && this.currentDistrictId !== this.info.district_id)) setTimeout(() => this.getAllWard(), 1000);
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
      if (typeof newParams['default_place'] === 'object') newParams['default_place'] = newParams['default_place'].join(',');
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
