import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { ReviewsRepository } from '../../services';
import { LanguagesRepository } from '../../../../@core/repositories';

@Component({
  selector: 'ngx-rvw-form',
  templateUrl: './form.component.html',
})

export class ReviewFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  controls: {
    product_id?: AbstractControl,
    rating?: AbstractControl,
    review?: AbstractControl,
    link?: AbstractControl,
    status?: AbstractControl,
  };
  curSelected: {id: number, name: string} = {id: 0, name: ''};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ReviewsRepository, languages: LanguagesRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      product_id: [''],
      rating: [''],
      review: [''],
      link: [''],
      status: [true],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
    this.getAllLanguage();
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && !_.includes([], key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.curSelected = {id: 0, name: ''};
    this.info = false;
    if (info) {
      this.setInfo(info);
      this.getInfo(info.id, {embed: ''});
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && !_.includes([], key)) this.controls[key].setValue('');
      });
    }
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(params: any, dontHide?: boolean, loading ?: boolean): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      newParams.rating = params.rating ? params.rating : 0;
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), (errors) => this.handleError(errors));
      } else {
        this.repository.create(this.utilityHelper.toFormData(newParams), loading).then((res) => this.handleSuccess(res.data, dontHide), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
