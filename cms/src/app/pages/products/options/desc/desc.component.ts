import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { OptionsRepository } from '../../services';

@Component({
  selector: 'ngx-pd-option-desc',
  templateUrl: './desc.component.html',
})

export class OptionDescComponent extends AppForm implements OnInit, OnDestroy {
  repository: OptionsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    name?: AbstractControl,
    value?: AbstractControl,
  };
  lang: 'vi'|'en' = 'en';

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: OptionsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: [''],
      value: [''],
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
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.info = info;
    console.log(info);
  }

  show(info: any, lang: 'vi'|'en'): void {
    this.resetForm(this.form);
    this.info = false;
    this.lang = lang ? lang : 'en';
    if (info) {
      this.setInfo(info);
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
    super.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      newParams['lang'] = this.lang;
      this.submitted = true;
      this.repository.updateDesc(this.info, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
    console.log(params);
  }
}
