import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { UsrFormComponent } from '../../usrs/form/form.component';

@Component({
  selector: 'ngx-dlg-new-usr',
  templateUrl: '../../usrs/form/form.component.html',
})

export class DlgNewUsrComponent extends UsrFormComponent implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
}
