import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { ProductBestsellersRepository } from '../../services';

@Component({
  selector: 'ngx-pd-bestseller-product-form',
  templateUrl: './form.component.html',
})
export class BestsellerProductFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductBestsellersRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  controls: {
    product_id?: AbstractControl,
  };
  curSelected: {id: number, name: string} = {id: 0, name: ''};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductBestsellersRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      product_id: [0, Validators.compose([Validators.min(0)])],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(): void {
    this.resetForm(this.form);
    this.curSelected = {id: 0, name: ''};
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue('');
    });
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
      this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
    }
    console.log(params);
  }
}
