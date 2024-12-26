import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { SettingsRepository } from '../../services';
import { PagesRepository } from '../../../pages/shared/services';

@Component({
  selector: 'ngx-pd-setting-form',
  templateUrl: './form.component.html',
})

export class SettingFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: { key: string, value: any, name: string, type: 'default' | 'editor_lang' | 'text' | 'textarea' | 'image' | 'boolean' | 'select_theme' | 'link_page' | 'colors' | 'frame', thumb_url?: string, note?: string, placeholder?: string } = {key: '', value: '', name: '', type: 'default'};
  name: any = '';
  value: any;
  valueInitVi = '';
  valueInitEn = '';
  pageData: { loading: boolean, items: any[] } = {loading: false, items: []};
  themes = [
    {id: 'theme_1', name: 'Giao diện 1'},
    {id: 'theme_2', name: 'Giao diện 2'},
    {id: 'theme_3', name: 'Giao diện 3'},
    {id: 'theme_4', name: 'Giao diện 4'},
    {id: 'theme_5', name: 'Giao diện 5'},
    {id: 'theme_6', name: 'Giao diện 6'},
    {id: 'theme_7', name: 'Giao diện 7'},
    {id: 'theme_8', name: 'Giao diện 8'},
  ];

  colors = [
    {id: 'primary', name: 'Màu chính'},
    {id: 'secondary', name: 'Màu thứ 2'},
    {id: 'success', name: 'Màu thứ 3'},
    {id: 'white', name: 'White'},
    {id: 'black', name: 'Black'},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: SettingsRepository, private _pages: PagesRepository) {
    super(router, security, state, repository);
  }

  private getAllPage(): void {
    this.pageData.loading = true;
    this._pages.all().then((res: any) => {
      console.log(res);
      this.pageData.loading = false;
      this.pageData.items = res.data;
    }), (errors: any) => {
      this.pageData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info: { key: string, value: any, name: string, type: 'default' | 'editor_lang' | 'text' | 'textarea' | 'image' |  'boolean' | 'select_theme' | 'link_page' | 'colors' | 'frame', thumb_url?: string }): void {
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
    } else {
      this.value = info.value;
    }
    this.name = info.name;
    this.info = info;
    if (this.info.type === 'link_page' && !this.pageData.items.length) this.getAllPage();
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
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    } else {
      this.repository.create(newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
  }

  editorChangeHandler($event, prop): void {
    this.value[prop] = $event;
    console.log($event, this.value);
  }
}
