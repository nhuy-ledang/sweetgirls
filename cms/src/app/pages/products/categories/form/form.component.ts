import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { CategoriesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-category-form',
  templateUrl: './form.component.html',
})

export class CategoryFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: CategoriesRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any | boolean;
  controls: {
    name?: AbstractControl,
    sort_order?: AbstractControl,
    status?: AbstractControl,
    image?: AbstractControl,
  };
  parentData: { loading: boolean, items: any[] } = {loading: false, items: []};
  layoutList = [{id: 'layout2', name: 'Giao diện 2'}, {id: 'layout2', name: 'Giao diện 3'}];
  layoutSelected: any;

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: CategoriesRepository, languages: LanguagesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      sort_order: [1],
      status: [true],
      image: [''],
    });
    this.controls = this.form.controls;
  }

  private getAllParent(): void {
    this.parentData.loading = true;
    this.repository.all().then((res: any) => {
      console.log(res);
      this.parentData.loading = false;
      this.parentData.items = res.data;
      if (this.parentData.items.length) this.layoutSelected = this.parentData.items[0];
    }), (errors: any) => {
      this.parentData.loading = false;
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
      if (this.controls.hasOwnProperty(key) && !_.includes(['parent_id'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    // this.controls.parent_id.setValue(info.parent_id ? info.parent_id : '');
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
    // this.icOpt = _.extend(_.cloneDeep(this.icOpt), {thumb_url: info.icon_url ? info.icon_url : ''});
    // this.bnOpt = _.extend(_.cloneDeep(this.bnOpt), {thumb_url: info.banner_url ? info.banner_url : ''});
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    this.layoutSelected = info ? info : false;
    if (info) {
      this.setInfo(info);
      this.getInfo(info.id, {embed: 'descs'});
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['sort_order', 'status', 'show'], key)) this.controls[key].setValue('');
      });
      this.controls.sort_order.setValue(1);
      this.controls.status.setValue(true);
      // this.controls.show.setValue(true);
    }
    if (!this.parentData.items.length) this.getAllParent();
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
      newParams.status = params.status ? 1 : 0;
      newParams.show = params.show ? 1 : 0;
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), (errors) => this.handleError(errors));
      } else {
        this.repository.create(this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(res.data, dontHide), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
