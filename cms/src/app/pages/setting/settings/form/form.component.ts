import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { AppForm } from '../../../../app.base';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository, SettingsRepository } from '../../../../@core/repositories';

@Component({
  selector: 'ngx-st-setting-form',
  templateUrl: './form.component.html',
})
export class SettingFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: {key: string, value: any, name: string, type: 'default'|'default_textarea'|'number'|'editor_lang'|'text'|'textarea'|'image'|'list_image'|'boolean'|'select_color', thumb_url?: string, note?: string, placeholder?: string} = {key: '', value: '', name: '', type: 'default'};
  name: any = '';
  value: any;
  // valueInitVi = '';
  // valueInitEn = '';
  // For image list
  images: any[] = [];
  files: any[] = [];
  fileOpts: any[] = [];
  filePaths: any[] = [];
  colors = [
    {id: 'blue', name: 'Blue'},
    {id: 'green', name: 'Green'},
    {id: 'cyan', name: 'Cyan'},
    {id: 'yellow', name: 'Yellow'},
    {id: 'red', name: 'Red'},
    {id: 'gray', name: 'Gray'},
    {id: 'gray_dark', name: 'Gray Dark'},
    {id: 'indigo', name: 'Indigo'},
    {id: 'purple', name: 'Purple'},
    {id: 'pink', name: 'Pink'},
    {id: 'orange', name: 'Orange'},
    {id: 'teal', name: 'Teal'},
    {id: 'white', name: 'White'},
    {id: 'black', name: 'Black'},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: SettingsRepository, languages: LanguagesRepository) {
    super(router, security, state, repository, languages);
  }

  ngOnInit(): void {
    this.getAllLanguage();
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info: {key: string, value: any, name: string, type: 'default'|'default_textarea'|'number'|'editor_lang'|'text'|'textarea'|'image'|'list_image'|'boolean'|'select_color', thumb_url?: string}): void {
    console.log(info);
    if (info.type === 'editor_lang' || info.type === 'text' || info.type === 'textarea') {
      const value = {vi: ''};
      if (_.isObject(info.value)) _.each(info.value, (val, key) => {
        value[key] = val ? val : '';
      });
      this.value = value;
    } else if (info.type === 'image') {
      this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
      this.value = info.value;
    } else if (info.type === 'list_image') {
      this.value = '';
      this.images = [];
      this.files = [];
      this.fileOpts = [];
      this.filePaths = [];
      _.forEach(info.value, (item) => this.addImageItem(item));
    } else {
      this.value = info.value;
    }
    this.name = info.name;
    this.info = info;
    this.modal.show();
  }

  hide(): void {
    // this.valueInitVi = '';
    // this.valueInitEn = '';
    // For image list
    this.images = [];
    this.files = [];
    this.fileOpts = [];
    this.filePaths = [];
    // this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(): void {
    this.submitted = true;
    const newParams: any = {key: this.info.key, value: this.value};
    if (this.info.type === 'boolean') newParams.value = this.value ? 1 : 0;
    if (this.info.type === 'image' || this.info.type === 'list_image') {
      if (this.info.type === 'image') {
        if (this.fileSelected) {
          newParams.file_path = this.fileSelected.path;
        } else if (this.file) {
          newParams.file = this.file;
        } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      } else if (this.info.type === 'list_image') {
        const images = [];
        _.forEach(this.images, (item) => {
          const image = {vi: {}, en: {}};
          _.each(item, (val, key) => {
            if (key.indexOf('vi_') === 0) {
              image['vi'][key.replace('vi_', '')] = val;
            } else if (key.indexOf('en_') === 0) {
              image['en'][key.replace('en_', '')] = val;
            } else {
              image[key] = val;
            }
          });
          images.push(image);
        });
        newParams.value = images;
        for (let i = 0; i < this.files.length; i++) newParams['file_' + i] = this.files[i];
        for (let i = 0; i < this.filePaths.length; i++) newParams['filepath_' + i] = this.filePaths[i] ? this.filePaths[i] : '';
        console.log(newParams);
      }
      this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    } else {
      this.repository.create(newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
  }

  /*editorChangeHandler($event, prop): void {
    this.value[prop] = $event;
    console.log($event, this.value);
  }*/

  // For image list
  addImageItem(item?: any): void {
    this.images.push({
      image: item && item.image ? item.image : '',
      sort_order: item && item.sort_order ? item.sort_order : 1,
      vi_description: item && item.vi && item.vi.description ? item.vi.description : '',
      vi_image_alt: item && item.vi && item.vi.image_alt ? item.vi.image_alt : '',
      en_description: item && item.en && item.en.description ? item.en.description : '',
      en_image_alt: item && item.en && item.en.image_alt ? item.en.image_alt : '',
    });
    this.files.push(null);
    this.fileOpts.push({thumb_url: item && item.thumb_url ? item.thumb_url : ''});
    this.filePaths.push(null);
  }

  removeImageItem(i: number): void {
    this.images.splice(i, 1);
    this.files.splice(i, 1);
    this.fileOpts.splice(i, 1);
    this.filePaths.splice(i, 1);
  }

  onImageSelected(i: number, file: File|any): void {
    console.log(i);
    if (file.type === 'select') {
      this.files[i] = null;
      this.filePaths[i] = file.path;
    } else {
      this.files[i] = file;
      this.filePaths[i] = null;
    }
  }

  onImageDeleted(i: number, event?: any): void {
    console.log(i);
    this.files[i] = null;
    this.filePaths[i] = null;
  }

  editorImageHandler($event, prop, item): void {
    item[prop] = $event;
  }
}
