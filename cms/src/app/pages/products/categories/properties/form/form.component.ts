import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormArray, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { CategoriesRepository, CategoryPropertiesRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-pd-category-property-form',
  templateUrl: './form.component.html',
})

export class CategoryPropertyFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: CategoriesRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  category: any;
  valueChecked: any;
  info: any|boolean;
  columns: any = {};
  rememberLoginControl: any = {};

  controls: {
    properties?: AbstractControl,
    // value?: AbstractControl,
  };
  propertyData: {loading: boolean, items: any[]} = {loading: false, items: []};
  valueData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: CategoriesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      properties: fb.array([]),
      // value: fb.array([]),
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  /*private getAllProperty(): void {
    this.propertyData.loading = true;
    this._properties.all().then((res: any) => {
      this.propertyData.loading = false;
      this.propertyData.items = res.data;
      console.log(this.propertyData.items);
    }), (errors: any) => {
      this.propertyData.loading = false;
      console.log(errors);
    };
  }*/

  /*private getAllValue(): void {
    this.valueData.loading = true;
    this._values.all().then((res: any) => {
      this.valueData.loading = false;
      this.valueData.items = res.data;
    }), (errors: any) => {
      this.valueData.loading = false;
      console.log(errors);
    };
  }*/

  /*private getValueProperty(id): void {
    this.valueData.loading = true;
    this._values.get({data: {property_id: id}}, false).then((res: any) => {
      this.valueData.loading = false;
      this.valueData.items = res.data;
    }), (errors: any) => {
      this.valueData.loading = false;
      console.log(errors);
    };
  }*/

  get valueForms() {
    return this.form.get('properties') as FormArray;
    // return this.form.get('value') as FormArray;
  }

  onCheckboxChange(e) {
    if (e.target.checked) {
      this.valueForms.push(new FormControl(Number(e.target.value)));
    } else {
      const index = this.valueForms.controls.findIndex(x => x.value === Number(e.target.value));
      this.valueForms.removeAt(index);
    }
  }

  onChange() {
    this.valueForms.clear();
    this.valueChecked = false;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.valueForms.clear();
    if (info.properties) {
      _.each(_.split(info.properties, ','), (val, key) => {
        this.valueForms.push(new FormControl(Number(val)));
      });
      this.valueChecked = true;
    }
    this.info = info;
  }

  show(category, info?: any): void {
    this.resetForm(this.form);
    // this.valueData.items = category.valueList;
    this.category = category;
    this.info = false;
    if (category) {
      console.log('Set info');
      this.setInfo(category);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['sort_order'], key) && this.controls[key] instanceof FormControl) this.controls[key].setValue('');
      });
      // this.controls.sort_order.setValue(0);
    }
    // if (!this.propertyData.items.length) this.getAllProperty();
    // if (!this.valueData.items.length) this.getAllValue();
    this.modal.show();
  }

  hide(): void {
    this.resetForm(this.form);
    this.onChange();
    this.valueChecked = false;
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    console.log(this.category);
    console.log(this.form);
    console.log(this.form.valid);
    if (this.form.valid) {
      _.update(params, 'properties', _.constant(_.toString(params.properties)));
      const newParams = _.cloneDeep(params);
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      this.submitted = true;
      if (this.category) {
        this.repository.createProperties(this.category.id, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      }
      /*else {
        newParams['category_id'] = this.category.id;
        this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }*/
    }
    console.log(params);
  }

  /*onPropertyAdd(): void {
    this.formProperty.show();
  }

  property: any;
  onPropertyValueAdd(): void {
    // this.formPropertyValue.show({id: this.controls.property_id.value});
  }

  onFormPropertySuccess(newVal): void {
    this.getAllProperty();
  }*/

  /*onFormPropertyValueSuccess(newVal): void {
    this.getAllValue();
  }*/
}
