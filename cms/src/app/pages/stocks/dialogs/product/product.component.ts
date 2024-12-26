import { Component, EventEmitter, Input, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppList } from '../../../../app.base';

@Component({
  selector: 'ngx-sto-dlg-product',
  templateUrl: './product.component.html',
})
export class DlgProductComponent extends AppList implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Input() set item(item: any) {
    console.log(item);
    this.info = item;
  }
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any;

  constructor(router: Router, security: Security, state: GlobalState) {
    super(router, security, state);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info?: any): void {
    this.info = info;
    this.modal.show();
  }

  hide(): void {
    this.modal.hide();
  }
}
