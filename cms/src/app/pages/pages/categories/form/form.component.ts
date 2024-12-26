import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { CategoriesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pg-category-form',
  templateUrl: './form.component.html',
})

export class CategoryFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: CategoriesRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any | boolean;
  controls: {
    parent_id?: AbstractControl,
    name?: AbstractControl,
    sort_order?: AbstractControl,
    status?: AbstractControl,
  };
  parentData: { loading: boolean, items: any[] } = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: CategoriesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      parent_id: [''],
      name: ['', Validators.compose([Validators.required])],
      sort_order: [1],
      status: [true],
    });
    this.controls = this.form.controls;
  }

  private getAllParent(): void {
    this.parentData.loading = true;
    this.repository.all().then((res: any) => {
      console.log(res);
      this.parentData.loading = false;
      this.parentData.items = res.data;
    }), (errors: any) => {
      this.parentData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && !_.includes(['parent_id'], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.controls.parent_id.setValue(info.parent_id ? info.parent_id : '');
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes(['sort_order', 'status'], key)) this.controls[key].setValue('');
      });
      this.controls.sort_order.setValue(1);
      this.controls.status.setValue(true);
    }
    if (!this.parentData.items.length) this.getAllParent();
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
      if (this.info) {
        this.repository.update(this.info, params).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this.repository.create(params).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
