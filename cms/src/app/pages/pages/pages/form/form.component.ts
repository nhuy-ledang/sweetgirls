import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository, ModulesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { CategoriesRepository, LayoutsRepository, PagesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pg-form',
  templateUrl: './form.component.html',
})

export class PageFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: PagesRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any | boolean;
  copyData: any | boolean;
  controls: {
    category_id?: AbstractControl,
    layout_id?: AbstractControl,
    style?: AbstractControl,
    name?: AbstractControl,
    short_description?: AbstractControl,
    description?: AbstractControl,
    is_sub?: AbstractControl,
    is_land?: AbstractControl,
    sort_order?: AbstractControl,
    status?: AbstractControl,
    meta_title?: AbstractControl,
    meta_description?: AbstractControl,
    meta_keyword?: AbstractControl,
    alias?: AbstractControl,
    image?: AbstractControl,
    icon?: AbstractControl,
    banner?: AbstractControl,
    properties?: any,
    table_contents?: any,
  };
  tbcControls: any;
  propControls: any;
  categoryData: { loading: boolean, items: any[] } = {loading: false, items: []};
  layoutData: { loading: boolean, items: any[] } = {loading: false, items: []};
  moduleData: { loading: boolean, modules: any[], pages: any[] } = {loading: false, modules: [], pages: []};

  constructor(public fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: PagesRepository, languages: LanguagesRepository,
              private _categories: CategoriesRepository, private _layouts: LayoutsRepository, private _modules: ModulesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      category_id: [''],
      layout_id: [''],
      style: [''],
      name: ['', Validators.compose([Validators.required])],
      short_description: [''],
      description: [''],
      is_sub: [false],
      is_land: [false],
      sort_order: [1],
      status: [true],
      meta_title: [''],
      meta_description: [''],
      meta_keyword: [''],
      alias: [''],
      image: [''],
      icon: [''],
      banner: [''],
      properties: fb.group({
        /*background: ['#FFFFFF'],
        bg: [''],
        button1: ['#FFFFFF'],
        button2: ['#FFFFFF'],
        header_white: [false],
        icon: [''],
        image: [''],*/
      }),
      table_contents: fb.group({
        /*vi_name: [''],
        vi_meta_title: [''],
        vi_meta_keyword: [''],
        vi_meta_description: [''],
        vi_short_description: [''],
        vi_description: [''],
        en_name: [''],
        en_meta_title: [''],
        en_meta_keyword: [''],
        en_meta_description: [''],
        en_short_description: [''],
        en_description: [''],*/
      }),
    });
    this.controls = this.form.controls;
    this.tbcControls = this.controls.table_contents.controls;
    this.propControls = this.controls.properties.controls;
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

  private getAllLayout(): void {
    this.layoutData.loading = true;
    this._layouts.all().then((res: any) => {
      console.log(res);
      this.layoutData.loading = false;
      this.layoutData.items = res.data;
    }), (errors: any) => {
      this.layoutData.loading = false;
      console.log(errors);
    };
  }

  private getModules(): void {
    this.moduleData.loading = true;
    this._modules.all().then((res: any) => {
      console.log(res);
      this.moduleData.loading = false;
      this.moduleData = _.extend(this.moduleData, res.data);
    }), (errors: any) => {
      this.moduleData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
    this.getAllLanguage();
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  private setValues(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.category_id.setValue(info.category_id ? info.category_id : '');
    this.controls.style.setValue(info.style ? info.style : '');
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
    this.icOpt = _.extend(_.cloneDeep(this.icOpt), {thumb_url: info.icon_url ? info.icon_url : ''});
    this.bnOpt = _.extend(_.cloneDeep(this.bnOpt), {thumb_url: info.banner_url ? info.banner_url : ''});
    // Set table_contents
    if (typeof info.table_contents === 'string') info.table_contents = JSON.parse(info.table_contents);
    // Set properties
    if (typeof info.properties === 'string') info.properties = JSON.parse(info.properties);
    _.each(this.propControls, (val, key) => {
      if (this.propControls.hasOwnProperty(key)) {
        // if (key === 'header_white') info.properties[key] = !!parseInt(info.properties[key], 0);
        this.propControls[key].setValue(info.properties[key] !== null ? info.properties[key] : '');
      }
    });
    /*if (info.properties) {
      if (info.properties.banner_thumb_url) {
        this.fileOpts1[0].thumb_url = info.properties.banner_thumb_url;
      }
    }*/
    this.onStyleChange();
  }

  protected setInfo(info: any): void {
    this.setValues(info);
    this.info = info;
  }

  show(info?: any, copyData?: any): void {
    this.resetForm(this.form);
    this.info = false;
    this.copyData = copyData ? copyData : false;
    if (info) {
      this.setInfo(info);
      this.getInfo(info.id, {embed: 'category,descs'});
      this.controls.layout_id.clearValidators();
    } else {
      if (this.copyData) {
        this.setValues(this.copyData);
      } else {
        _.each(this.controls, (val, key) => {
          if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl && !_.includes(['is_sub', 'is_land', 'sort_order', 'status'], key)) this.controls[key].setValue('');
        });
        this.controls.is_sub.setValue(false);
        this.controls.is_land.setValue(false);
        this.controls.sort_order.setValue(1);
        this.controls.status.setValue(true);
        // Set properties
        _.each(this.propControls, (val, key) => {
          if (this.propControls.hasOwnProperty(key)) this.propControls[key].setValue('');
        });
        // Set table_contents
        _.each(this.tbcControls, (val, key) => {
          if (this.tbcControls.hasOwnProperty(key)) this.tbcControls[key].setValue('');
        });
        this.onStyleChange();
      }
      // this.controls.layout_id.setValidators(Validators.compose([Validators.required]));
    }
    this.controls.layout_id.updateValueAndValidity();
    if (!this.categoryData.items.length) this.getAllCategory();
    setTimeout(() => {
      if (!info && !copyData && !this.layoutData.items.length) this.getAllLayout();
    }, 500);
    setTimeout(() => {
      if (!this.moduleData.modules.length || !this.moduleData.pages.length) this.getModules();
    }, 1000);
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
      if (this.icSelected) {
        newParams.ic_path = this.icSelected.path;
      } else if (this.icFile) {
        newParams.ic_file = this.icFile;
      } else if (!this.icOpt.thumb_url) newParams['icon'] = '';
      if (this.bnSelected) {
        newParams.bn_path = this.bnSelected.path;
      } else if (this.bnFile) {
        newParams.bn_file = this.bnFile;
      } else if (!this.bnOpt.thumb_url) newParams['banner'] = '';
      /*const properties = {};
      _.each(params.properties, (val, key) => {
        if (key === 'header_white') {
          properties[key] = !!val ? 1 : 0;
        } else {
          properties[key] = val;
        }
      });
      newParams['properties'] = properties;
      const table_contents = {vi: {}, en: {}};
      _.each(params.table_contents, (val, key) => {
        if (key.indexOf('vi_') === 0) {
          table_contents['vi'][key.replace('vi_', '')] = val;
        } else if (key.indexOf('en_') === 0) {
          table_contents['en'][key.replace('en_', '')] = val;
        }
      });
      newParams['table_contents'] = table_contents;*/
      newParams.status = params.status ? 1 : 0;
      console.log(newParams);
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), (errors) => this.handleError(errors));
      } else if (this.copyData) {
        this.repository.copy(this.copyData, this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(res.data, dontHide), (errors) => this.handleError(errors));
      } else {
        this.repository.create(this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(res.data, dontHide), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }

  onStyleChange(): void {
    _.each(this.tbcControls, (val, key) => {
      this.controls.table_contents.removeControl(key);
    });
    if (this.info && this.info.table_contents) {
      _.each(this.tbcControls, (val, key) => {
        if (this.tbcControls.hasOwnProperty(key)) {
          let code, field;
          if (key.indexOf('vi_') === 0) {
            code = 'vi';
            field = key.replace('vi_', '');
          } else if (key.indexOf('en_') === 0) {
            code = 'en';
            field = key.replace('en_', '');
          }
          if (code && this.info.table_contents && this.info.table_contents[code] && this.info.table_contents[code][field] !== null) {
            this.tbcControls[key].setValue(this.info.table_contents[code][field]);
          } else {
            this.tbcControls[key].setValue('');
          }
        }
      });
    }
  }

  onFileDeleted(event): void {
    this.controls.image.setValue('');
    return super.onFileDeleted(event);
  }

  onIcDeleted(event): void {
    this.controls.icon.setValue('');
    return super.onIcDeleted(event);
  }

  // Banner
  bnOpt: any = {thumb_url: '', aspect_ratio: '16by9'};
  bnFile: File = null;
  bnSelected: { path: string } = null;

  onBnSelected(event: File | any): void {
    if (event.type === 'select') {
      this.bnSelected = event;
      this.bnFile = null;
    } else {
      this.bnSelected = null;
      this.bnFile = event;
    }
  }

  onBnDeleted(event): void {
    this.controls.banner.setValue('');
    this.bnFile = null;
    this.bnOpt.thumb_url = '';
    this.bnSelected = null;
  }

  onChangeName(): void {
    if (!this.info || (this.info && !this.info.meta_title)) {
      if (!this.controls.meta_title.touched) {
        // console.log('meta_title', this.controls.meta_title.value, this.controls.meta_title.touched);
        this.controls.meta_title.setValue(this.controls.name.value);
      }
    }
    if (!this.info || (this.info && !this.info.alias)) {
      if (!this.controls.alias.touched) {
        // console.log('alias', this.controls.alias.value, this.controls.alias.touched);
        this.controls.alias.setValue(this.utilityHelper.toAlias(this.controls.name.value));
      }
    }
  }
}
