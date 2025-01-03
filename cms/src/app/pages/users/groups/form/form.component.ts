import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { UserGroupsRepository } from '../../shared/services';
import { AppForm } from '../../../../app.base';

@Component({
  selector: 'ngx-user-group-form',
  templateUrl: './form.component.html',
})

export class GroupFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  info: any|boolean;
  controls: {
    name?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: UserGroupsRepository) {
    super(router, security, state, repository);

    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
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

  show(info?: any, defaultParams?: any): void {
    this.resetForm(this.form);
    this.info = info ? info : false;
    if (this.info) {
      this.setInfo(this.info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key)) this.controls[key].setValue('');
      });
    }
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, params).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this.repository.create(params).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
