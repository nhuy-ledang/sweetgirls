import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository, ModulesRepository as CfModulesRepository } from '../../../../@core/repositories';
import { CategoriesRepository as PdCategoriesRepository } from '../../../products/shared/services';
import { CategoriesRepository, ModulesRepository, PagesRepository } from '../../shared/services';
import { BaseModuleFormComponent } from './base.form.component';
import { DlgCategorySelectComponent } from '../../shared/modals';

@Component({
  selector: 'ngx-pg-module-form',
  templateUrl: './form.component.html',
})

export class ModuleFormComponent extends BaseModuleFormComponent implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  controls: {
    idx?: AbstractControl,
    name?: AbstractControl,
    title?: AbstractControl,
    sub_title?: AbstractControl,
    code?: AbstractControl,
    layout?: AbstractControl,
    tile?: AbstractControl,
    short_description?: AbstractControl,
    description?: AbstractControl,
    properties?: FormGroup,
    table_contents?: AbstractControl,
    table_images?: AbstractControl,
    image?: AbstractControl,
    attach?: AbstractControl,
    menu_text?: AbstractControl,
    btn_text?: AbstractControl,
    btn_link?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ModulesRepository, languages: LanguagesRepository,
              cfModules: CfModulesRepository, modules: ModulesRepository, categories: CategoriesRepository, pages: PagesRepository, pdCategories: PdCategoriesRepository) {
    super(router, security, state, repository, fb, languages, cfModules, modules, categories, pages, pdCategories);
    this.form = fb.group({
      idx: ['', Validators.compose([Validators.required])],
      name: ['', Validators.compose([Validators.required])],
      title: [''],
      sub_title: [''],
      code: [''],
      layout: [''],
      tile: [''],
      short_description: [''],
      description: [''],
      properties: this.propForm,
      table_contents: fb.array([]),
      table_images: fb.array([]),
      image: [''],
      attach: [''],
      menu_text: [''],
      btn_text: [''],
      btn_link: [''],
    });
    this.controls = this.form.controls;
    this.tbcForm = this.form.controls.table_contents;
    this.imgForm = this.form.controls.table_images;
    this.fb = fb;
  }

  ngOnInit(): void {
    this.getAllLanguage();
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected getCfModules(): void {
    // Clear parent code
  }

  protected setInfo(info: any): void {
    this.info = info;
    console.log('setInfo', info);
    this.selectModule(info);
    setTimeout(() => {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['layout'], key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
      });
      this.controls.layout.setValue(info.layout ? info.layout : 'layout1');
      this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
      this.bgOpts = _.extend(_.cloneDeep(this.bgOpts), {thumb_url: info.properties && info.properties.thumb_url ? info.properties.thumb_url : ''});
      this.setProperties(info);
      this.onCfModuleChange();
    });
  }

  onSubmit(params: any, dontHide?: boolean, loading ?: boolean): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = this.getSubmitParams(params);
      this.submitted = true;
      this.repository.update(this.info, this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), (errors) => this.handleError(errors));
      console.log(newParams);
    }
  }

  @ViewChild('attachFile') _attachFile: ElementRef;

  @ViewChild(DlgCategorySelectComponent) dlgCategory: DlgCategorySelectComponent;

  openCategory(): void {
    this.dlgCategory.show(this.propControls.source.value, this.propControls.source_ids.value);
  }

  onCategorySelect(d: {ids: number[], names: string[]}): void {
    this.propControls.source_ids.setValue(d.ids.join(','));
    this.propControls.source_names.setValue(d.names.join(','));
  }
}
