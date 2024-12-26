import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { AppForm } from '../../../../../app.base';
import { ReviewsRepository } from '../../../services';

@Component({
  selector: 'ngx-rvw-image-form',
  templateUrl: './form.component.html',
})

export class ReviewImageFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ReviewsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  review: any;
  info: any|boolean;
  controls: {
    // image_alt?: AbstractControl,
  };
  @ViewChild('uploaderEl') uploaderEl: {init: any};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ReviewsRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      // sort_order: [0, Validators.compose([Validators.min(0)])],
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
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
    this.info = info;
  }

  show(review, info?: any): void {
    this.resetForm(this.form);
    this.review = review;
    this.info = false;
    if (info) {
      this.setInfo(info);
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
        this.repository.updateImage(this.review.id, this.info.id, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        newParams['review_id'] = this.review.id;
        this.repository.createImage(this.review.id, this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
    }
    console.log(params);
  }
}
