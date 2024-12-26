import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { EmailValidator } from '../../../../@theme/validators';
import { AppForm } from '../../../../app.base';
import { OrdersRepository } from '../services';

@Component({
  selector: 'ngx-ord-dlg-notify',
  templateUrl: './dlg-notify.component.html',
})

export class DlgNotifyComponent extends AppForm implements OnInit, OnDestroy {
  repository: OrdersRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  info: any;
  controls: {
    email?: AbstractControl,
    cc?: AbstractControl,
    attached?: AbstractControl,
    content?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: OrdersRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      email: ['', Validators.compose([Validators.required, EmailValidator.validate])],
      cc: [''],
      attached: [true],
      content: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info: any): void {
    this.resetForm(this.form);
    this.info = info;
    this.controls.email.setValue(info.email);
    this.controls.cc.setValue('');
    this.controls.attached.setValue(true);
    this.controls.content.setValue('');
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
      this.submitted = true;
      const newParams = _.cloneDeep(params);
      this.repository.sendMail(this.info.master_id, newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
    }
    console.log(params);
  }
}
