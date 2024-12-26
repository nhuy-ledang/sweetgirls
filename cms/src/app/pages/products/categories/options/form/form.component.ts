import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormArray, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { OptionsRepository, OptionValuesRepository } from '../../../services';
import { OptionFormComponent } from '../../../options/form/form.component';
import { OptionValueFormComponent } from '../../../options/values/form/form.component';
import { CategoriesRepository, CategoryOptionsRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-pd-category-option-form',
  templateUrl: './form.component.html',
})

export class CategoryOptionFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: CategoriesRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild(OptionFormComponent) formOption: OptionFormComponent;
  @ViewChild(OptionValueFormComponent) formOptionValue: OptionValueFormComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  category: any;
  valueChecked: any;
  info: any|boolean;
  columns: any = {};
  rememberLoginControl: any = {};

  controls: {
    options?: AbstractControl,
    // value?: AbstractControl,
  };
  optionData: {loading: boolean, items: any[]} = {loading: false, items: []};
  valueData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: CategoriesRepository,
              private _options: OptionsRepository,
              private _values: OptionValuesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      options: fb.array([]),
      // value: fb.array([]),
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  private getAllOption(): void {
    this.optionData.loading = true;
    this._options.all().then((res: any) => {
      this.optionData.loading = false;
      this.optionData.items = res.data;
      console.log(this.optionData.items);
    }), (errors: any) => {
      this.optionData.loading = false;
      console.log(errors);
    };
  }

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

  /*private getValueOption(id): void {
    this.valueData.loading = true;
    this._values.get({data: {option_id: id}}, false).then((res: any) => {
      this.valueData.loading = false;
      this.valueData.items = res.data;
    }), (errors: any) => {
      this.valueData.loading = false;
      console.log(errors);
    };
  }*/

  get valueForms() {
    return this.form.get('options') as FormArray;
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
    if (info.options) {
      _.each(_.split(info.options, ','), (val, key) => {
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
    if (!this.optionData.items.length) this.getAllOption();
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
      _.update(params, 'options', _.constant(_.toString(params.options)));
      const newParams = _.cloneDeep(params);
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      this.submitted = true;
      if (this.category) {
        this.repository.createOptions(this.category.id, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      }
      /*else {
        newParams['category_id'] = this.category.id;
        this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }*/
    }
    console.log(params);
  }

  onOptionAdd(): void {
    this.formOption.show();
  }

  option: any;
  onOptionValueAdd(): void {
    // this.formOptionValue.show({id: this.controls.option_id.value});
  }

  onFormOptionSuccess(newVal): void {
    this.getAllOption();
  }

  /*onFormOptionValueSuccess(newVal): void {
    this.getAllValue();
  }*/
}
