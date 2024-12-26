import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository, ModulesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { CategoriesRepository, LayoutsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pg-ly-form',
  templateUrl: './form.component.html',
})

export class LayoutFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    category_id?: AbstractControl,
    name?: AbstractControl,
    description?: AbstractControl,
    image?: AbstractControl,
  };
  categoryData: {loading: boolean, items: any[]} = {loading: false, items: []};
  moduleData: {loading: boolean, modules: any[], pages: any[]} = {loading: false, modules: [], pages: []};

  constructor(public fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: LayoutsRepository, languages: LanguagesRepository,
              private _categories: CategoriesRepository, private _modules: ModulesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      category_id: [''],
      name: ['', Validators.compose([Validators.required])],
      description: [''],
      image: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  private getAllCategory(): void {
    this.categoryData.loading = true;
    this._categories.all().then((res: any) => {
      console.log(res);
      this.categoryData.loading = false;
      this.categoryData.items = res.data;
    }), (errors: any) => {
      this.categoryData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
    this.getAllLanguage();
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.category_id.setValue(info.category_id ? info.category_id : '');
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
      this.getInfo(info.id, {embed: 'category,descs'});
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && this.controls[key]) this.controls[key].setValue('');
      });
    }
    if (!this.categoryData.items.length) this.getAllCategory();
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
      console.log(newParams);
    }
    console.log(params);
  }
}
