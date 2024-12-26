import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { FlashsalesRepository } from '../../services';

@Component({
  selector: 'ngx-pd-flashsale-form',
  templateUrl: './form.component.html',
})
export class FlashsaleFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    name?: AbstractControl,
    start_date?: AbstractControl,
    end_date?: AbstractControl,
    status?: AbstractControl,
  };
  bsConfig: any = {withTimepicker: true, dateInputFormat: 'DD/MM/YYYY, HH:mm', adaptivePosition: true, containerClass: 'theme-red'};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: FlashsalesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      start_date: ['', Validators.compose([Validators.required])],
      end_date: ['', Validators.compose([Validators.required])],
      status: [1],
    });
    this.controls = this.form.controls;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && !_.includes(['start_date', 'end_date'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.start_date.setValue(info.start_date ? new Date(info.start_date) : '');
    this.controls.end_date.setValue(info.end_date ? new Date(info.end_date) : '');
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['status'], key)) this.controls[key].setValue('');
      });
      this.controls.start_date.setValue('');
      this.controls.end_date.setValue('');
      this.controls.status.setValue(1);
    }
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
      if (params.start_date && params.start_date instanceof Date) newParams.start_date = params.start_date.format('isoDateTime');
      if (params.end_date && params.end_date instanceof Date) newParams.end_date = params.end_date.format('isoDateTime');
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
