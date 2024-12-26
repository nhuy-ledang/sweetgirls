import { Component, EventEmitter, Input, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppList } from '../../../../app.base';
import { UsersRepository } from '../services';

@Component({
  selector: 'ngx-dlg-customer-select',
  templateUrl: './dlg-customer-select.component.html',
})

// Not check
export class DlgCustomerSelectComponent extends AppList implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  @Input() set limit(limit: boolean|number) {
    if (limit === false || (limit && typeof limit === 'number')) {
      this.data.limit = limit;
    }
  }

  type: string;

  constructor(router: Router, security: Security, state: GlobalState, repository: UsersRepository) {
    super(router, security, state, repository);
    this.data.limit = 1;
    this.data.paging = 0;
  }

  // Override fn
  /* protected getData(cb?: Function, loading?: boolean, repository?: Api, method?: string | 'get'): void {
     return super.getData(null, false, null, 'search');
   }*/

  ngOnInit(): void {
  }

  ngOnDestroy() {
    super.destroy();
  }

  show(type?: string): void {
    this.type = type;
    /*this.data.selectList = [];
    this.data.items = [];
    this.data.data = {q: ''};
    setTimeout(() => this.getData(), 200);*/
    this.modal.show();
  }

  hide(): void {
    this.modal.hide();
  }

  checkboxClick(item: any): void {
    item.checkbox = !item.checkbox;
    return this.checkbox(item);
  }

  onSubmit(): void {
    this.hide();
    this.onSuccess.emit(this.data.selectList);
  }
}
