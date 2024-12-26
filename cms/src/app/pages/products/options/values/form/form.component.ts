import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { OptionValuesRepository } from '../../../services';

@Component({
  selector: 'ngx-pd-option-value-form',
  templateUrl: './form.component.html',
})
export class OptionValueFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  option: any;
  controls: {
    name?: AbstractControl,
    option_id?: AbstractControl,
    sort_order?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: OptionValuesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      option_id: [''],
      sort_order: [1],
      image: [''],
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

  show(option, info?: any): void {
    this.resetForm(this.form);
    this.option = option;
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['sort_order'], key)) this.controls[key].setValue('');
      });
      this.controls.sort_order.setValue(1);
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
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        newParams['option_id'] = this.option.id;
        this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
