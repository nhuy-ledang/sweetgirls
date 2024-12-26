import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { UsrsRepository } from '../../../../@core/repositories';
import { Security } from '../../../../@core/security';
import { Usr } from '../../../../@core/entities';
import { AppForm } from '../../../../app.base';

@Component({
  selector: 'ngx-dlg-password',
  templateUrl: './dlg-password.component.html',
})

export class DlgPasswordComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: Usr;
  controls: {
    password?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: UsrsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      password: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: Usr): void {
    console.log(info);
    this.controls.password.setValue('');
    this.info = info;
  }

  show(info: Usr): void {
    this.resetForm(this.form);
    this.info = info;
    this.setInfo(this.info);
    // this.getInfo(this.info.id, false, true);
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  protected handleSuccess(res): void {
    super.handleSuccess(res);
    this._state.notifyDataChanged('modal.success', {title: 'Thành công!', message: 'Cập nhật mật khẩu thành công!'});
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      console.log(params);
      this.submitted = true;
      this._security.newPassword(params).then((res: any) => {
        console.log(res);
        this.handleSuccess(_.extend({edited: true}, res.data));
      }, (errors) => this.handleError(errors));
    }
  }
}
