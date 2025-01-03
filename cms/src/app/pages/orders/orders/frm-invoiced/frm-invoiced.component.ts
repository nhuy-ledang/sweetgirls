import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppForm } from '../../../../app.base';
import { InvoicesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-ord-frm-invoiced',
  templateUrl: './frm-invoiced.component.html',
})

export class FrmInvoicedComponent extends AppForm implements OnInit, OnDestroy {
  repository: InvoicesRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  controls: {
    date?: AbstractControl,
    company?: AbstractControl,
    tax_code?: AbstractControl,
    address?: AbstractControl,
    email?: AbstractControl,
  };
  bsConfig: any = {withTimepicker: true, dateInputFormat: 'DD/MM/YYYY, h:mm', adaptivePosition: true, containerClass: 'theme-red'};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: InvoicesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      date: [''],
      company: [''],
      tax_code: [''],
      address: [''],
      email: [''],
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
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && !_.includes(['date'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.tax_code.setValue(info.company_tax);
    this.controls.address.setValue(info.company_address);
    this.controls.date.setValue(new Date());
    this.info = info;
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    console.log(this.info);
    if (this.form.valid) {
      this.submitted = true;
      this.repository.exportVAT(this.info, params).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
    console.log(params);
  }
}
