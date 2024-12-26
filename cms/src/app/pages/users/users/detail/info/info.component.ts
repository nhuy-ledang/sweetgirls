import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { GlobalState } from '../../../../../@core/utils';
import { Security } from '../../../../../@core/security';
import { EmailValidator } from '../../../../../@theme/validators';
import { AppForm } from '../../../../../app.base';
import { User } from '../../../shared/entities';
import { UsersRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-user-info-card',
  templateUrl: './info.component.html',
})
export class UserInfoComponent extends AppForm implements OnInit, OnDestroy {
  info: User = null;

  controls: {
    email?: AbstractControl,
    phone_number?: AbstractControl,
    first_name?: AbstractControl,
    address?: AbstractControl,
    birthday?: AbstractControl,
  };

  protected setInfo(info: User): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && !_.includes(['gender', 'birthday', 'status'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.birthday.setValue(info.birthday ? new Date(info.birthday) : '');
  }

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: UsersRepository, private _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.form = fb.group({
      email: ['', Validators.compose([EmailValidator.validate])],
      phone_number: ['', Validators.compose([Validators.required])],
      first_name: ['', Validators.compose([Validators.required])],
      address: [''],
      birthday: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;

    this.info = this._route.parent.snapshot.data['info'];
    console.log(this.info);
    this.setInfo(this.info);
  }

  getInfo(id: number): void {
    this.repository.find(id, {}, false).then((res: any) => {
      console.log(res.data);
      _.each(res.data, (val, key) => this.info[key] = val);
    }, (errors) => console.log(errors));
  }

  ngOnInit(): void {
    this.getInfo(this.info.id);
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected handleSuccess(res): void {
    console.log(res);
    this.showValid = false;
    this.submitted = false;
    this._state.notifyDataChanged('modal.success', {title: 'Thành công!', message: 'Cập nhật thành công!'});
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      if (typeof params.birthday === 'object') params.birthday = params.birthday.getIsoDate();
      this.submitted = true;
      this.repository.update(this.info, this.utilityHelper.toFormData(params)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
    console.log(params);
  }
}
