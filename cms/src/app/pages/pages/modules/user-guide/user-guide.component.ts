import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { ModulesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pg-module-user-guide',
  templateUrl: './user-guide.component.html',
})

export class ModuleUserGuideComponent extends AppForm implements OnInit, OnDestroy {
  repository: ModulesRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    user_guide?: AbstractControl,
  };

  constructor(public fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ModulesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      user_guide: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
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

  show(info: any): void {
    this.resetForm(this.form);
    this.info = false;
    this.setInfo(info);
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
      this.submitted = true;
      this.repository.patch(this.info, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
    console.log(params);
  }
}
