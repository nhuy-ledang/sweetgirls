import { Component, ViewChild, Output, EventEmitter, ChangeDetectorRef } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';

@Component({
  selector: 'ngx-modal-confirm',
  templateUrl: './confirm.component.html',
})

export class ConfirmComponent {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onConfirm: EventEmitter<any> = new EventEmitter<any>();

  title: string = '';
  message: string = '';
  type: string = 'success';
  btnConfirmText: string = 'DELETE';
  private btnConfirm: {name: string, commands: string[]} = null;
  btnCancelText: string = 'KEEP';
  private data: any = {};
  customClass: string = '';
  private callback: Function;

  constructor(private _router: Router, private _ref: ChangeDetectorRef) {
  }

  show(data: {title: string, message: string, type?: 'success'|'delete'|'alert', confirmText?: string, confirmBtn?: {name: string, commands: string[]}, cancelText?: string, data?: {type?: string, info?: any, data?: any, className?: string, customClass?: string, callback?: Function}}): void {
    console.log(data);
    const d: any = _.extend({title: '', message: '', type: 'success'}, data);
    this.title = d.title;
    this.message = d.message;
    this.type = d.type;
    this.btnConfirmText = d.confirmText ? d.confirmText : 'DELETE';
    this.btnCancelText = d.cancelText ? d.cancelText : 'KEEP';
    this.btnConfirm = d.confirmBtn ? d.confirmBtn : null;
    if (this.btnConfirm) this.btnConfirmText = this.btnConfirm.name;
    const custom: any = _.extend({}, d.data);
    this.customClass = custom.className ? custom.className : (custom.customClass ? custom.customClass : '');
    this.callback = custom.callback;
    this.data = custom;
    setTimeout(() => {
      if (!this.modal.isShown) {
        this.modal.show();
      } else {
        this.hide();
        this.modal.show();
      }
      this._ref.markForCheck();
      this._ref.detectChanges();
    });
  }

  hide(): void {
    this.modal.hide();
  }

  confirm(): void {
    this.modal.hide();
    if (this.btnConfirm) {
      this._router.navigate(this.btnConfirm.commands);
    } else {
      this.onConfirm.emit(this.data);
      if (this.callback && this.callback instanceof Function) this.callback();
    }
  }

  onHidden(): void {
    this.modal.hide();
  }
}
