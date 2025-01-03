import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { UserRanksRepository } from '../../shared/services';

@Component({
  selector: 'ngx-user-ranks-form',
  templateUrl: './form.component.html',
})
export class RanksFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    name?: AbstractControl,
    value?: AbstractControl,
    rank?: AbstractControl,
    status?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: UserRanksRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      value: [0, Validators.compose([Validators.min(0)])],
      rank: [1, Validators.compose([Validators.min(1)])],
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
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['value', 'status', 'rank'], key)) this.controls[key].setValue('');
      });
      this.controls.value.setValue(0);
      this.controls.rank.setValue(1);
      this.controls.status.setValue(true);
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
