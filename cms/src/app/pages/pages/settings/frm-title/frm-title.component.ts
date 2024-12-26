import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { SettingsRepository } from '../../services';
import { ModulesRepository as CoreModulesRepository } from '../../../../@core/repositories';

@Component({
  selector: 'ngx-pg-setting-frm-title',
  templateUrl: './frm-title.component.html',
})

export class SettingFrmTitleComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  moduleData: { loading: boolean, modules: any[], pages: any[], headers: any[], footers: any[], titles: any[], fonts: any[] } = {loading: false, modules: [], pages: [], headers: [], footers: [], titles: [], fonts: []};
  value: any;

  controls: {
    pg_title_theme?: AbstractControl,
    pg_title_icon?: AbstractControl,
    pg_title_font?: AbstractControl,
    pg_title_sub_font?: AbstractControl,
  };
  themes: any[] = [
    {id: '', name: 'Mặc định (Chỉ có tiêu đề)'},
    {id: 'sub_title', name: 'Tiêu đề phụ + Tiêu đề'},
    {id: 'title_sub', name: 'Tiêu đề + Tiêu đề phụ'},
  ];
  fonts: any[] = [
    {id: '', name: 'Mặc định (Montserrat)'},
    {id: 'cormorant', name: 'Cormorant'},
    {id: 'corinthia', name: 'Corinthia'},
  ];
  previews: any = {
    theme: 'assets/previews/title/default.jpg',
    title_font: 'assets/previews/fonts/montserrat.jpg',
    sub_title_font: 'assets/previews/fonts/montserrat.jpg',
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: SettingsRepository, private _modules: CoreModulesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      pg_title_theme: [''],
      pg_title_icon: [''],
      pg_title_font: [''],
      pg_title_sub_font: [''],
    });
    this.controls = this.form.controls;
  }

  private getModules(): void {
    this.moduleData.loading = true;
    this._modules.all().then((res: any) => {
      console.log(res.data);
      this.moduleData.loading = false;
      this.moduleData = _.extend(this.moduleData, res.data);
      this.onChangeStyle();
      this.onChangeTitleFont();
      this.onChangeSubTitleFont();
    }), (errors: any) => {
      this.moduleData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info: any): void {
    const setting: any = {};
    if (info.items) _.forEach(info.items, (item: any) => {
      setting[item.key] = item.value;
      if (item.thumb_url) setting[item.key + '_thumb_url'] = item.thumb_url;
    });
    console.log(setting);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(setting.hasOwnProperty(key) && setting[key] !== null ? setting[key] : '');
      if (key === 'pg_title_icon') this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: setting[key + '_thumb_url'] ? setting[key + '_thumb_url'] : ''});
    });
    if (!this.moduleData.titles.length || !this.moduleData.fonts.length) this.getModules();
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
      const promises: any[] = [];
      _.each(params, (val, key) => {
        if (key === 'pg_title_icon') {
          const newParams: any = {key: key, value: val};
          if (this.fileSelected) {
            newParams.file_path = this.fileSelected.path;
          } else if (this.file) {
            newParams.file = this.file;
          } else if (!this.fileOpt.thumb_url) params['pg_title_icon'] = '';
          promises.push(this.repository.create(this.utilityHelper.toFormData(newParams), true));
        } else {
          promises.push(this.repository.create({key: key, value: val}, true));
        }
      });
      this.submitted = true;
      Promise.all(promises).then((res) => {
        console.log(res);
        this.handleSuccess(_.extend({edited: true}, params));
      }, (res) => {
        console.log(res);
        this.handleSuccess(_.extend({edited: true}, params));
        // this.handleError(res);
      });
    }
    console.log(params);
  }

  preview: any;
  onChangeStyle(): void {
    const md = _.find(this.moduleData.titles, {id: this.controls.pg_title_theme.value});
    console.log('this.moduleData.titles', this.moduleData.titles);
    console.log('this.controls.pg_title_theme.value', this.controls.pg_title_theme.value);
    if (md) {
      this.preview = md.preview;
    } else {
      this.preview = null;
    }
  }

  preview_title_font: any;
  onChangeTitleFont(): void {
    const md = _.find(this.moduleData.fonts, {id: this.controls.pg_title_font.value});
    if (md) {
      this.preview_title_font = md.preview;
    } else {
      this.preview_title_font = null;
    }
  }

  preview_sub_title_font: any;
  onChangeSubTitleFont(): void {
    const md = _.find(this.moduleData.fonts, {id: this.controls.pg_title_sub_font.value});
    if (md) {
      this.preview_sub_title_font = md.preview;
    } else {
      this.preview_sub_title_font = null;
    }
  }



}
