import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { EmailValidator } from '../../../../@theme/validators';
import { UsrsRepository } from '../../../../@core/repositories';
import { UsrGroupsRepository } from '../../shared/services';
import { AppForm } from '../../../../app.base';

@Component({
  selector: 'ngx-usr-form',
  templateUrl: './form.component.html',
})

export class UsrFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any | boolean;
  controls: {
    email?: AbstractControl,
    phone_number?: AbstractControl,
    first_name?: AbstractControl,
    birthday?: AbstractControl,
    gender?: AbstractControl,
    password?: AbstractControl,
    group_id?: AbstractControl,
    avatar?: AbstractControl,
    avatar_url?: AbstractControl,
  };
  genderList = [{id: 0, name: 'Không'}, {id: 1, name: 'Nam'}, {id: 2, name: 'Nữ'}];
  groupData: { loading: boolean, items: any[] } = {loading: false, items: []};
  passType: 'password' | 'text' = 'password';

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: UsrsRepository, private _groups: UsrGroupsRepository) {
    super(router, security, state, repository);

    this.form = fb.group({
      email: ['', Validators.compose([EmailValidator.validate, Validators.required])],
      phone_number: [''], // Validators.compose([Validators.required])],
      first_name: ['', Validators.compose([Validators.required])],
      birthday: [''],
      gender: [this.genderList[0].id],
      password: [''],
      group_id: [''],
      avatar: [''],
      avatar_url: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
    this.fileOpt.aspect_ratio = '1by1';
  }

  private getAllGroup(): void {
    this.groupData.loading = true;
    this._groups.all().then((res: any) => {
      this.groupData.loading = false;
      this.groupData.items = res.data;
    }), (errors: any) => {
      this.groupData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setValues(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && !_.includes(['gender', 'birthday'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.gender.setValue(info.gender ? info.gender : 0);
    this.controls.birthday.setValue(info.birthday ? new Date(info.birthday) : '');
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.avatar_url ? info.avatar_url : ''});
  }

  protected setInfo(info: any): void {
    this.setValues(info);
    this.info = info;
  }

  show(info?: any, cloneData?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
      // this.getInfo(info.id, false, true);
    } else {
      if (cloneData) {
        this.setValues(cloneData);
      } else {
        _.each(this.controls, (val, key) => {
          if (this.controls.hasOwnProperty(key) && !_.includes(['gender'], key)) this.controls[key].setValue('');
        });
        this.controls.gender.setValue(0);
      }
    }
    if (!this.groupData.items.length) this.getAllGroup();
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
      const newParams = _.cloneDeep(params);
      if (params.birthday && params.birthday instanceof Date) newParams.birthday = params.birthday.getIsoDate();
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      if (!params['password']) delete newParams['password'];
      this.submitted = true;
      console.log(newParams);
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
  }

  changePassType(): void {
    this.passType = this.passType === 'password' ? 'text' : 'password';
  }
}
