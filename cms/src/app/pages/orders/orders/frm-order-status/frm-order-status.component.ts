import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppForm } from '../../../../app.base';

@Component({
  selector: 'ngx-ord-frm-order-status',
  templateUrl: './frm-order-status.component.html',
})
export class OrderFrmOrderStatusComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  controls: {
    status?: AbstractControl,
    comment?: AbstractControl,
    tags?: AbstractControl,
  };
  orderStatusList = [
    {id: this.CONST.ORDER_SS_PENDING, name: 'Chờ xác nhận'},
    {id: this.CONST.ORDER_SS_PROCESSING, name: 'Đang xử lý'},
    {id: this.CONST.ORDER_SS_SHIPPING, name: 'Đang giao hàng'},
    {id: this.CONST.ORDER_SS_COMPLETED, name: 'Hoàn tất'},
    {id: this.CONST.ORDER_SS_CANCELED, name: 'Hủy đơn'},
    // {id: this.CONST.ORDER_SS_RETURNING, name: 'Đang trả hàng'},
    // {id: this.CONST.ORDER_SS_RETURNED, name: 'Đã trả hàng'},
  ];

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState) {
    super(router, security, state);
    this.form = fb.group({
      status: [this.orderStatusList[0].id],
      comment: [''],
      tags: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info: any, order_status: string): void {
    this.resetForm(this.form);
    console.log(info);
    this.info = info;
    this.controls.status.setValue(order_status);
    this.controls.comment.setValue('');
    this.controls.tags.setValue(info.tags);
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) this.handleSuccess({info: this.info, params: params});
  }
}
