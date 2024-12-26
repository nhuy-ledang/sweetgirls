import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, Validators } from '@angular/forms';
import { AppForm } from '../../../../app.base';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { Api } from '../../../../@core/services';

export abstract class BaseModuleDescComponent extends AppForm {
  repository: any;
  onSuccess: any;
  info: any|boolean;
  desc: any|boolean;
  lang: string = 'en';

  controls: {
    name?: AbstractControl,
    title?: AbstractControl,
    sub_title?: AbstractControl,
    short_description?: AbstractControl,
    description?: AbstractControl,
    table_contents?: AbstractControl,
    menu_text?: AbstractControl,
    btn_text?: AbstractControl,
    btn_link?: AbstractControl,
  };
  tbcForm: AbstractControl|any;
  files1: any[] = [];
  fileOpts1: any[] = [];
  filePaths1: any[] = [];
  files2: any[] = [];
  fileOpts2: any[] = [];
  filePaths2: any[] = [];
  files3: any[] = [];
  fileOpts3: any[] = [];
  filePaths3: any[] = [];
  tbcAttaches: any[] = [];
  imgForm: AbstractControl|any;
  imgs: any[] = [];
  imgOpts: any[] = [];
  imgPaths: any[] = [];
  editorOpt: any = {toolbar1: false};
  configs = {short_description: true, description: true, table_contents: {image1: true, image2: true, image3: true, name: true, short_description: true, link: true, description: true}, table_images: {image: true, name: true, short_description: true, description: true}};

  constructor(router: Router, security: Security, state: GlobalState, repository: Api, fb: FormBuilder) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      title: [''],
      sub_title: [''],
      short_description: [''],
      description: [''],
      table_contents: fb.array([]),
      table_images: fb.array([]),
      menu_text: [''],
      btn_text: [''],
      btn_link: [''],
    });
    this.controls = this.form.controls;
    this.tbcForm = this.form.controls.table_contents;
    this.imgForm = this.form.controls.table_images;
    this.fb = fb;
  }

  show(info: any, desc: any, lang: string, configs?: any): void {
    this.resetForm(this.form);
    this.info = info;
    this.desc = desc;
    this.lang = lang ? lang : 'en';
    console.log(configs);
    if (configs) this.configs = _.extend(this.configs, configs);
    if (desc) {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(desc.hasOwnProperty(key) && desc[key] !== null ? desc[key] : '');
      });
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue('');
      });
    }
    // Set table_contents
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
    this.tbcForm.controls = [];
    if (!desc.table_contents) desc.table_contents = [];
    _.forEach(desc.table_contents, (item) => this.addRow(item));
    this.tbcForm.updateValueAndValidity();
    // Set table_images
    this.imgs = [];
    this.imgOpts = [];
    this.imgPaths = [];
    this.imgForm.controls = [];
    if (!desc.table_images) desc.table_images = [];
    _.forEach(desc.table_images, (item) => this.addImgRow(item));
    this.imgForm.updateValueAndValidity();
    console.log(info);
  }

  onSubmit(params: any, is_close?: boolean): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      const table_contents = [];
      _.forEach(params.table_contents, (item: any) => {
        delete item['attach_url'];
        table_contents.push(item);
      });
      newParams['table_contents'] = table_contents;
      for (let i = 0; i < this.files1.length; i++) {
        newParams['file_' + i] = this.files1[i];
      }
      for (let i = 0; i < this.filePaths1.length; i++) {
        newParams['filepath_' + i] = this.filePaths1[i] ? this.filePaths1[i] : '';
      }
      for (let i = 0; i < this.files2.length; i++) {
        newParams['file2_' + i] = this.files2[i];
      }
      for (let i = 0; i < this.filePaths2.length; i++) {
        newParams['filepath2_' + i] = this.filePaths2[i] ? this.filePaths2[i] : '';
      }
      for (let i = 0; i < this.files3.length; i++) {
        newParams['file3_' + i] = this.files3[i];
      }
      for (let i = 0; i < this.filePaths3.length; i++) {
        newParams['filepath3_' + i] = this.filePaths3[i] ? this.filePaths3[i] : '';
      }
      for (let i = 0; i < this.tbcAttaches.length; i++) {
        newParams['attache_' + i] = this.tbcAttaches[i];
      }
      const table_images = [];
      _.forEach(params.table_images, (item: any) => table_images.push(item));
      newParams['table_images'] = table_images;
      for (let i = 0; i < this.imgs.length; i++) {
        newParams['img_' + i] = this.imgs[i];
      }
      for (let i = 0; i < this.imgPaths.length; i++) {
        newParams['imgpath_' + i] = this.imgPaths[i] ? this.imgPaths[i] : '';
      }
      newParams['lang'] = this.lang;
      console.log(newParams);
      this.submitted = true;
      this.repository.updateDesc(this.info, this.utilityHelper.toFormData(newParams)).then((res) => {
        this.showValid = false;
        this.submitted = false;
        this.onSuccess.emit({d: res.data, is_close: !!is_close});
      }, (errors) => this.handleError(errors));
    }
    console.log(params);
  }

  // <editor-fold desc="Table content">
  addRow(item?: any): void {
    this.tbcForm.push(this.fb.group({
      name: [item && item.name ? item.name : ''],
      short_description: [item && item.short_description ? item.short_description : ''],
      description: [item && item.description ? item.description : ''],
      image: [item && item.image ? item.image : ''],
      image2: [item && item.image2 ? item.image2 : ''],
      image3: [item && item.image3 ? item.image3 : ''],
      link: [item && item.link ? item.link : ''],
      attach: [item && item.attach ? item.attach : ''],
      attach_url: [item && item.attach_url ? item.attach_url : ''],
    }));
    this.files1.push(null);
    this.fileOpts1.push({thumb_url: item && item.thumb_url ? item.thumb_url : ''});
    this.filePaths1.push(null);
    this.files2.push(null);
    this.fileOpts2.push({thumb_url: item && item.thumb_url2 ? item.thumb_url2 : ''});
    this.filePaths2.push(null);
    this.files3.push(null);
    this.fileOpts3.push({thumb_url: item && item.thumb_url3 ? item.thumb_url3 : ''});
    this.filePaths3.push(null);
    this.tbcAttaches.push(null);
  }

  removeRow(i: number): void {
    this.tbcForm.removeAt(i);
    this.files1.splice(i, 1);
    this.fileOpts1.splice(i, 1);
    this.filePaths1.splice(i, 1);
    this.files2.splice(i, 1);
    this.fileOpts2.splice(i, 1);
    this.filePaths2.splice(i, 1);
    this.files3.splice(i, 1);
    this.fileOpts3.splice(i, 1);
    this.filePaths3.splice(i, 1);
    this.tbcAttaches.splice(i, 1);
  }

  onImageSelected(i: number, file: File|any): void {
    if (file.type === 'select') {
      this.files1[i] = null;
      this.filePaths1[i] = file.path;
    } else {
      this.files1[i] = file;
      this.filePaths1[i] = null;
    }
  }

  onImageDeleted(i: number, event?: any): void {
    this.files1[i] = null;
    this.filePaths1[i] = null;
  }

  onImageSelected2(i: number, file: File|any): void {
    if (file.type === 'select') {
      this.files2[i] = null;
      this.filePaths2[i] = file.path;
    } else {
      this.files2[i] = file;
      this.filePaths2[i] = null;
    }
  }

  onImageDeleted2(i: number, event?: any): void {
    this.files2[i] = null;
    this.filePaths2[i] = null;
  }

  onImageSelected3(i: number, file: File|any): void {
    if (file.type === 'select') {
      this.files3[i] = null;
      this.filePaths3[i] = file.path;
    } else {
      this.files3[i] = file;
      this.filePaths3[i] = null;
    }
  }

  onImageDeleted3(i: number, event?: any): void {
    this.files3[i] = null;
    this.filePaths3[i] = null;
  }

  onTbcAttach(i: number, $event: any): void {
    const files = $event.currentTarget.files;
    this.tbcAttaches[i] = files.length ? files[0] : null;
  }

  removeTbcAttach(i: number, $event: any, controls: any): void {
    controls.attach_url.setValue('');
    controls.attach.setValue('');
  }

  // </editor-fold>

  // Table images
  addImgRow(item?: any): void {
    this.imgForm.push(this.fb.group({
      name: [item && item.name ? item.name : ''],
      short_description: [item && item.short_description ? item.short_description : ''],
      link: [item && item.link ? item.link : ''],
      description: [item && item.description ? item.description : ''],
      image: [item && item.image ? item.image : ''],
    }));
    this.imgs.push(null);
    this.imgOpts.push({thumb_url: item && item.thumb_url ? item.thumb_url : ''});
    this.imgPaths.push(null);
  }

  removeImgRow(i: number): void {
    this.imgForm.removeAt(i);
    this.imgs.splice(i, 1);
    this.imgOpts.splice(i, 1);
    this.imgPaths.splice(i, 1);
  }

  onImgSelected(i: number, file: File|any): void {
    if (file.type === 'select') {
      this.imgs[i] = null;
      this.imgPaths[i] = file.path;
    } else {
      this.imgs[i] = file;
      this.imgPaths[i] = null;
    }
  }

  onImgDeleted(i: number, event?: any): void {
    this.imgs[i] = null;
    this.imgPaths[i] = null;
  }

  // End table images
}
