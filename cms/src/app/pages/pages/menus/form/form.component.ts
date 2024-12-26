import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { LanguagesRepository, MenusRepository } from '../../../../@core/repositories';
import { PagesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pg-menu-form',
  templateUrl: './form.component.html',
})

export class MenuFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: MenusRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any | boolean;
  controls: {
    parent_id?: AbstractControl,
    page_id?: AbstractControl,
    name?: AbstractControl,
    icon?: AbstractControl,
    image?: AbstractControl,
    source?: AbstractControl,
    link?: AbstractControl,
    sort_order?: AbstractControl,
    status?: AbstractControl,
    is_redirect?: AbstractControl,
    is_sidebar?: AbstractControl,
    is_header?: AbstractControl,
    is_footer?: AbstractControl,
  };
  parentData: { loading: boolean, items: any[] } = {loading: false, items: []};
  pageData: { loading: boolean, items: any[] } = {loading: false, items: []};
  sourceList = [{id: 'product', name: 'Sản phẩm'}, {id: 'project', name: 'Dự án'}, {id: 'news', name: 'Tin tức'}, {id: 'library', name: 'Thư viện'}];

  constructor(public fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: MenusRepository, languages: LanguagesRepository, private _pages: PagesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      parent_id: [''],
      page_id: [''],
      name: ['', Validators.compose([Validators.required])],
      icon: [''],
      image: [''],
      source: [''],
      link: [''],
      sort_order: [1],
      status: [true],
      is_redirect: [true],
      is_sidebar: [false],
      is_header: [true],
      is_footer: [false],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  private getAllParent(): void {
    this.parentData.loading = true;
    this.repository.all().then((res: any) => {
      console.log(res);
      this.parentData.loading = false;
      this.parentData.items = res.data;
    }), (errors: any) => {
      this.parentData.loading = false;
      console.log(errors);
    };
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
    this.getAllLanguage();
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.parent_id.setValue(info.parent_id ? info.parent_id : '');
    this.onSourceChange();
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['sort_order', 'status', 'is_redirect', 'is_sidebar', 'is_header', 'is_footer'], key)) this.controls[key].setValue('');
      });
      this.controls.sort_order.setValue(1);
      this.controls.status.setValue(true);
      this.controls.is_redirect.setValue(false);
      this.controls.is_sidebar.setValue(false);
      this.controls.is_header.setValue(true);
      this.controls.is_footer.setValue(false);
    }
    if (!this.pageData.items.length) this.getAllPage();
    setTimeout(() => {
      if (!this.parentData.items.length) this.getAllParent();
    }, 500);
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
      newParams.parent_id = params.parent_id ? params.parent_id : 0;
      newParams.page_id = params.page_id ? params.page_id : null;
      newParams.source = params.source ? params.source : null;
      newParams.status = params.status ? 1 : 0;
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      if (params.is_redirect) {
        newParams.page_id = null;
      } else {
        newParams.link = null;
      }
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

  onSourceChange(): void {
    if (this.controls.source.value) {
      this.controls.is_redirect.setValue(false);
    }
  }
}
