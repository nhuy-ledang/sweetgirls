import { Component, EventEmitter, Input, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { Dialog } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { WidgetsRepository } from '../services';

@Component({
  selector: 'ngx-dlg-widget-select',
  templateUrl: './dlg-widget-select.component.html',
})

export class DlgWidgetSelectComponent extends AppList implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  tempCache: any = {};
  selected: any;

  constructor(router: Router, security: Security, state: GlobalState, repository: WidgetsRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    this.data.paging = 0;
    this.data.pageSize = 1000;
    this.data.sort = 'name';
    this.data.order = 'asc';
  }

  // Override fn
  protected getData(): void {
    const key = '|q=' + this.data.data.q;
    if (this.tempCache[key] && this.tempCache[key].length) {
      this.data.items = this.tempCache[key];
    } else {
      this.data.loading = true;
      this.repository.get(this.data, false).then((res: any) => {
          this.data.items = res.data;
          this.data.loading = false;
          this.tempCache[key] = this.data.items;
        }, (res: any) => {
          console.log(res.errors);
          this.data.loading = false;
        },
      );
    }
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(): void {
    this.selected = null;
    if (!this.data.items.length) this.getData();
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
    this.onSuccess.emit(this.selected);
  }

  selectClasses(item, cl: any): void {
    this.selected = {id: item.code + '_' + cl.id, item: item, cl: cl};
    console.log(this.selected);
  }
}
