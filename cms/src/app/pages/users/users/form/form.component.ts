import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { LeadSourcesRepository } from '../../../../@core/repositories';
import { Err } from '../../../../@core/entities';
import { AppForm } from '../../../../app.base';
import { User } from '../../shared/entities';
import { UserGroupsRepository, UsersRepository } from '../../shared/services';

@Component({
  selector: 'ngx-user-form',
  templateUrl: './form.component.html',
})
export class UserFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  info: User|boolean;
  controls: {
    email?: AbstractControl,
    phone_number?: AbstractControl,
    first_name?: AbstractControl,
    birthday?: AbstractControl,
    gender?: AbstractControl,
    password?: AbstractControl,
  };
  @ViewChild('uploaderEl') uploaderEl: {init: any};
  genderList = [{id: 0, name: 'Không'}, {id: 1, name: 'Nam'}, {id: 2, name: 'Nữ'}];
  groupData: {loading: boolean, items: any[]} = {loading: false, items: []};
  sourceData: {loading: boolean, items: any[]} = {loading: false, items: []};
  passType: 'password'|'text' = 'password';

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: UsersRepository,
              private _groups: UserGroupsRepository, private _leadSources: LeadSourcesRepository) {
    super(router, security, state, repository);

    this.form = fb.group({
      email: [''],
      phone_number: [''],
      first_name: ['', Validators.compose([Validators.required])],
      birthday: [''],
      gender: [this.genderList[0].id],
      password: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
    this.fileOpt.aspect_ratio = '1by1';
  }

  private getAllGroup() {
    this.groupData.loading = true;
    this._groups.all().then((res: any) => {
      console.log(res);
      this.groupData.loading = false;
      this.groupData.items = res.data;
    }), (errors: any) => {
      this.groupData.loading = false;
      console.log(errors);
    };
  }

  private getAllSource() {
    this.sourceData.loading = true;
    this._leadSources.all().then((res: any) => {
      console.log(res);
      this.sourceData.loading = false;
      this.sourceData.items = res.data;
    }), (errors: any) => {
      this.sourceData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && !_.includes(['gender', 'birthday', 'group_id'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.gender.setValue(info.gender ? info.gender : 0);
    this.controls.birthday.setValue(info.birthday ? new Date(info.birthday) : '');
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.avatar_url ? info.avatar_url : ''});
    this.info = info;
  }

  show(info?: User): void {
    this.resetForm(this.form);
    this.info = info ? info : false;
    if (this.info) {
      this.setInfo(this.info);
      // this.getInfo(this.info.id, false, true);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['gender'], key)) this.controls[key].setValue('');
      });
      this.controls.gender.setValue(0);
    }
    if (!this.groupData.items.length) this.getAllGroup();
    setTimeout(() => {
      if (!this.sourceData.items.length) this.getAllSource();
    }, 500);
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  // Override fn
  protected handleError(res: {errors: Err[], data: any, message?: any}): void {
    const list: any[] = [];
    if (res.errors.length && (res.errors[0].errorKey === 'email.unique') || res.errors[0].errorKey === 'phone_number.unique') {
      res.message = `Khách hàng <b>${res.data?.display}</b> đã sử dụng số điện thoại hoặc email này (<b>${res.data.email ? res.data.email : res.data.phone_number}</b>)!`;
    } else if (res.data && _.isArray(res.data) && res.data.length) {
      /*console.log(res.data);
      _.forEach(res.data, (item: any) => {
        list.push({name: item.display, commands: ['pages/users', item.id, 'info']});
      });*/
    }
    super.handleError(_.extend({list: list}, res));
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      if (params.birthday && params.birthday instanceof Date) newParams.birthday = params.birthday.getIsoDate();
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      if (newParams['password']) delete newParams['password'];
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }

  changePassType(): void {
    this.passType = this.passType === 'password' ? 'text' : 'password';
  }
}
