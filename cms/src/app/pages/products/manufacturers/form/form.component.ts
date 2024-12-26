import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { ManufacturersRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-manufacturer-form',
  templateUrl: './form.component.html',
})
export class ManufacturerFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    name?: AbstractControl,
    commission?: AbstractControl,
    sort_order?: AbstractControl,
    status?: AbstractControl,
    image?: AbstractControl,
    alias?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ManufacturersRepository) {
    super(router, security, state, repository);

    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      commission: [''],
      sort_order: [1],
      status: [true],
      image: [''],
      alias: [''],
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
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['commission', 'sort_order', 'status'], key)) this.controls[key].setValue('');
      });
      this.controls.sort_order.setValue(0);
      this.controls.sort_order.setValue(1);
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
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }

  onChangeName(): void {
    if (!this.info || (this.info && !this.info.meta_title)) {
      if (!this.controls.name.touched) {
        this.controls.name.setValue(this.controls.name.value);
      }
    }
    if (!this.info || (this.info && !this.info.alias)) {
      if (!this.controls.alias.touched) {
        this.controls.alias.setValue(this.utilityHelper.toAlias(this.controls.name.value));
      }
    }
  }
}
