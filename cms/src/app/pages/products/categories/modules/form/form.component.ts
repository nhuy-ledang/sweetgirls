import { Component, ElementRef, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { LanguagesRepository, ModulesRepository as CfModulesRepository } from '../../../../../@core/repositories';
import { CategoriesRepository as PdCategoriesRepository } from '../../../../products/shared/services';
import { CategoriesRepository, ModulesRepository, PagesRepository } from '../../../../pages/shared/services';
import { CategoryModulesRepository } from '../../../services';
import { BaseModuleFormComponent } from '../../../../pages/modules/form/base.form.component';
import { DlgCategorySelectComponent, DlgModuleSelectComponent } from '../../../../pages/shared/modals';

@Component({
  selector: 'ngx-pd-category-module-form',
  templateUrl: './form.component.html',
})

export class CategoryModuleFormComponent extends BaseModuleFormComponent implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  controls: {
    name?: AbstractControl,
    title?: AbstractControl,
    sub_title?: AbstractControl,
    module_id?: AbstractControl,
    code?: AbstractControl,
    is_overwrite?: AbstractControl,
    layout?: AbstractControl,
    tile?: AbstractControl,
    short_description?: AbstractControl,
    description?: AbstractControl,
    sort_order?: AbstractControl,
    status?: AbstractControl,
    properties?: FormGroup,
    table_contents?: AbstractControl,
    table_images?: AbstractControl,
    image?: AbstractControl,
    attach?: AbstractControl,
    menu_text?: AbstractControl,
    btn_text?: AbstractControl,
    btn_link?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: CategoryModulesRepository, languages: LanguagesRepository,
              cfModules: CfModulesRepository, modules: ModulesRepository, categories: CategoriesRepository, pages: PagesRepository, pdCategories: PdCategoriesRepository) {
    super(router, security, state, repository, fb, languages, cfModules, modules, categories, pages, pdCategories);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      title: [''],
      sub_title: [''],
      code: [''],
      module_id: [''],
      is_overwrite: [false],
      layout: [''],
      tile: [''],
      short_description: [''],
      description: [''],
      sort_order: [1],
      status: [false],
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

  protected getListModules(): void {
    this.repository.get({data: {product_id: this.info.product_id}}).then((res: any) => {
      this.listModules.loading = false;
      this.listModules.items = res.data;
    }), (errors: any) => {
      this.listModules.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    this.info = info;
    console.log('setInfo', info);
    this.selectModule(info.module ? info.module : false);
    setTimeout(() => {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['layout'], key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
      });
      this.controls.layout.setValue(info.layout ? info.layout : 'layout1');
      this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
      this.bgOpts = _.extend(_.cloneDeep(this.bgOpts), {thumb_url: info.properties && info.properties.thumb_url ? info.properties.thumb_url : ''});
      this.setProperties(info);
      if (!this.controls.module_id.value && this.moduleSelected) this.controls.module_id.setValue(this.moduleSelected.id);
      this.onCfModuleChange();
    });
  }

  show(info: any): void {
    this.isCollapsed = true;
    this.resetForm(this.form);
    this.info = false;
    this.tbcForm.controls = [];
    this.files1 = [];
    this.fileOpts1 = [];
    this.filePaths1 = [];
    this.files2 = [];
    this.fileOpts2 = [];
    this.filePaths2 = [];
    this.files3 = [];
    this.fileOpts3 = [];
    this.filePaths3 = [];
    this.tbcAttaches = [];
    this.imgForm.controls = [];
    this.imgs = [];
    this.imgOpts = [];
    this.imgPaths = [];
    this.setInfo(info);
    this.getInfo(info.id, {embed: 'descs,module'});
    /*if (info) {
      this.setInfo(info);
      this.getInfo(info.id, {embed: 'descs,module'});
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl && !_.includes(['is_overwrite', 'sort_order'], key)) this.controls[key].setValue('');
      });
      this.controls.is_overwrite.setValue(false);
      this.controls.sort_order.setValue(1);
      this.controls.status.setValue(true);
    }
    this.onCfModuleChange();*/
    this.tbcForm.updateValueAndValidity();
    this.imgForm.updateValueAndValidity();
    // if (!this.cfModuleData.modules.length || !this.cfModuleData.pages.length) this.getCfModules();
    /*setTimeout(() => {
      if (!this.moduleData.items.length) this.getAllModules();
    }, 500);*/
    setTimeout(() => {
      if (!this.pageData.items.length) this.getAllPage();
    }, 1000);
    setTimeout(() => {
      if (!this.cfModuleData.buttons.length ) this.getCfModules();
    }, 1500);
    setTimeout(() => this.getListModules(), 500);
    this.modal.show();
  }

  onSubmit(params: any, dontHide?: boolean, loading ?: boolean): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = this.getSubmitParams(params);
      // if (params.is_overwrite && this.moduleSelected) newParams.code = this.moduleSelected.code;
      if (newParams.sort_order === '' || newParams.sort_order === null) newParams.sort_order = 0;
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), (errors) => this.handleError(errors));
      } else {
        /*newParams['product_id'] = this.product.id;
        this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));*/
      }
      console.log(newParams);
    }
  }

  /*loadModuleData(): void {
    if (this.moduleSelected && this.controls.module_id.value && this.controls.is_overwrite.value) {
      console.log('loadModuleData', this.moduleSelected);
      this.setProperties(this.moduleSelected);
    }
  }

  onIsOverwriteChange(): void {
    if (this.moduleSelected) {
      if (this.controls.is_overwrite.value) {
        if (this.controls.code.value !== this.moduleSelected.code) {
          this.controls.code.setValue(this.moduleSelected.code);
          this.onCfModuleChange();
        }
      }
      this.controls.code.disable();
    } else {
      this.controls.code.enable();
    }
  }

  onModuleChange($event?: any): void {
    if (this.controls.module_id.value) {
      this.moduleSelected = _.find(this.moduleData.items, {id: parseInt(this.controls.module_id.value, 0)});
      if (this.moduleSelected) {
        if (!this.controls.name.value) this.controls.name.setValue(this.moduleSelected.name);
        // this.onIsOverwriteChange();
        if (this.controls.code.value !== this.moduleSelected.code) {
          this.controls.code.setValue(this.moduleSelected.code);
          this.onCfModuleChange();
        }
        this.controls.code.disable();
      } else {
        this.controls.code.enable();
      }
      console.log('onModuleChange', this.moduleSelected);
    } else if ($event) {
      this.controls.is_overwrite.setValue(false);
      this.controls.code.enable();
    }
  }

  onFormSuccess(newVal): void {
    const module = _.find(this.moduleData.items, {id: parseInt(this.controls.module_id.value, 0)});
    if (module) {
      _.remove(this.moduleData.items, {id: parseInt(this.controls.module_id.value, 0)});
      this.moduleData.items = _.concat([newVal], this.moduleData.items);
    }
  }*/

  @ViewChild('attachFile') _attachFile: ElementRef;
  attachFile: File = null;

  @ViewChild(DlgModuleSelectComponent) dlgModule: DlgModuleSelectComponent;

  changeModule(): void {
    this.dlgModule.show();
  }

  onModuleSelect(res: any[]): void {
    if (res.length) {
      const module: any = res[0];
      if (this.controls.code.value !== module.code) {
        this.selectModule(module);
        setTimeout(() => {
          this.controls.code.setValue(module.code);
          this.controls.layout.setValue(module.layout);
          this.controls.module_id.setValue(module.id);
          this.onCfModuleChange();
        });
      }
    }
  }

  @ViewChild(DlgCategorySelectComponent) dlgCategory: DlgCategorySelectComponent;

  openCategory(): void {
    this.dlgCategory.show(this.propControls.source.value, this.propControls.source_ids.value);
  }

  onCategorySelect(d: {ids: number[], names: string[]}): void {
    this.propControls.source_ids.setValue(d.ids.join(','));
    this.propControls.source_names.setValue(d.names.join(','));
  }
}
