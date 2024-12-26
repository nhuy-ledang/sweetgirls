import { Component, EventEmitter, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Err } from '../../../@core/entities';

@Component({
  selector: 'ngx-modal-alert',
  templateUrl: './alert.component.html',
})

export class AlertComponent {
  @ViewChild('modal', {static: false}) modal: ModalDirective;
  @Output() onResult: EventEmitter<any> = new EventEmitter<any>();

  title: string = '';
  messages: string[] = [];
  type: string = 'success'; // success/info/warning/danger
  btn: {name: string, commands: string[]} = null;
  contentList: {name: string, commands: string[]}[] = [];
  data: any = {};

  constructor(private _router: Router) {
  }

  show(data: {title: string, message: any, type?: string|'success', btn?: {name: string, commands: string[]}, list?: {name: string, commands: string[]}[]}): void {
    console.log(data);
    const d: any = _.extend({title: '', message: '', type: 'danger'}, data);
    this.title = d.title;
    this.messages = [];
    this.type = d.type ? d.type : 'success';
    this.btn = d.btn ? d.btn : null;
    this.contentList = data.list ? data.list : [];
    if (_.isArray(d.message)) {
      d.message.forEach((error) => {
        if (error instanceof Err) {
          this.messages.push(error.errorMessage);
        } else if (_.isString(error)) {
          this.messages.push(error);
        }
      });
    } else {
      this.messages.push(d.message);
    }

    if (!this.modal.isShown) {
      this.modal.show();
    }
  }

  hide(): void {
    this.modal.hide();
  }

  goDetail(): void {
    this.hide();
    this._router.navigate(this.btn.commands);
  }

  onHidden() {
    this.onResult.emit(this.data);
  }
}
