import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ModulesRepository as CoreModulesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { SettingsRepository } from '../../services';

@Component({
  selector: 'ngx-pg-setting-form',
  templateUrl: './form.component.html',
})

export class SettingFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: { key: string, value: any, name: string, type: 'default' | 'editor_lang' | 'text' | 'number' | 'textarea' | 'image' | 'boolean' | 'header_theme' | 'footer_theme' | 'header_cont' | 'footer_cont' | 'style_transform' | 'colors' | 'button_theme' | 'dot_theme'| 'arrow_theme', thumb_url?: string, note?: string, placeholder?: string } = {key: '', value: '', name: '', type: 'default'};
  name: any = '';
  value: any;
  valueInitVi = '';
  valueInitEn = '';
  colors = [
    {id: 'primary', name: 'Màu chính'},
    {id: 'secondary', name: 'Màu thứ 2'},
    {id: 'success', name: 'Màu thứ 3'},
    {id: 'white', name: 'White'},
    {id: 'black', name: 'Black'},
  ];
  moduleData: { loading: boolean, modules: any[], pages: any[], headers: any[], footers: any[], buttons: any[], dots: any[], arrows: any[] } = {loading: false, modules: [], pages: [], headers: [], footers: [], buttons: [], dots: [], arrows: []};

  constructor(router: Router, security: Security, state: GlobalState, repository: SettingsRepository, private _modules: CoreModulesRepository) {
    super(router, security, state, repository);
  }

  private getModules(): void {
    this.moduleData.loading = true;
    this._modules.all().then((res: any) => {
      console.log(res.data);
      this.moduleData.loading = false;
      this.moduleData = _.extend(this.moduleData, res.data);
      this.onStyleChange(this.info.type);
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

  show(info: { key: string, value: any, name: string, type: 'default' | 'editor_lang' | 'text' | 'number' | 'textarea' | 'image' | 'boolean' | 'header_theme' | 'footer_theme' | 'header_cont' | 'footer_cont' | 'style_transform' | 'colors' | 'button_theme' | 'dot_theme' | 'arrow_theme', thumb_url?: string }): void {
    console.log(info);
    if (info.type === 'editor_lang' || info.type === 'text' || info.type === 'textarea') {
      const value = {
        vi: _.isObject(info.value) && info.value['vi'] ? info.value['vi'] : '',
        en: _.isObject(info.value) && info.value['en'] ? info.value['en'] : '',
      };
      this.value = value;
      this.valueInitVi = value.vi;
      this.valueInitEn = value.en;
    } else if (info.type === 'image') {
      console.log(info);
      this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
      this.value = info.value;
      this.fileSelected = info.value;
    } else {
      this.value = info.value;
    }
    this.name = info.name;
    this.info = info;
    if (info.type === 'header_theme' || info.type === 'footer_theme' || info.type === 'button_theme' || info.type === 'dot_theme' || info.type === 'arrow_theme') {
      if (!this.moduleData.headers.length || !this.moduleData.footers.length) this.getModules();
    }
    this.modal.show();
  }

  hide(): void {
    this.valueInitVi = '';
    this.valueInitEn = '';
    // this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(): void {
    this.submitted = true;
    const newParams: any = {key: this.info.key, value: this.value};
    if (this.info.type === 'boolean') {
      newParams.value = this.value ? 1 : 0;
    }
    if (this.info.type === 'image') {
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['value'] = '';
      this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    } else {
      this.repository.create(newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
  }

  editorChangeHandler($event, prop): void {
    this.value[prop] = $event;
    console.log($event, this.value);
  }

  preview: any;
  onStyleChange(pos?: any): void {
    if (this.moduleData.headers && pos === 'header_theme') {
      const md = _.find(this.moduleData.headers, {id: this.value});
      if (md) {
        this.preview = md.preview;
      } else {
        this.preview = null;
      }
    } else if (this.moduleData.footers && pos === 'footer_theme') {
      const md = _.find(this.moduleData.footers, {id: this.value});
      if (md) {
        this.preview = md.preview;
      } else {
        this.preview = null;
      }
    } else if (this.moduleData.buttons && pos === 'button_theme') {
      const md = _.find(this.moduleData.buttons, {id: this.value});
      if (md) {
        this.preview = md.preview;
      } else {
        this.preview = null;
      }
    } else if (this.moduleData.dots && pos === 'dot_theme') {
      const md = _.find(this.moduleData.dots, {id: this.value});
      if (md) {
        this.preview = md.preview;
      } else {
        this.preview = null;
      }
    } else if (this.moduleData.arrows && pos === 'arrow_theme') {
      const md = _.find(this.moduleData.arrows, {id: this.value});
      if (md) {
        this.preview = md.preview;
      } else {
        this.preview = null;
      }
    } else {
      this.preview = null;
    }
  }
}
