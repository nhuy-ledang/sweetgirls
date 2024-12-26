import { Component, EventEmitter, Input, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppList } from '../../../../app.base';
import { LayoutsRepository } from '../services';

@Component({
  selector: 'ngx-dlg-layout-select',
  templateUrl: './dlg-layout-select.component.html',
})

export class DlgLayoutSelectComponent extends AppList implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  @Input() set limit(limit: boolean|number) {
    if (limit === false || (limit && typeof limit === 'number')) {
      this.data.limit = limit;
    }
  }

  tempCache: any = {};

  constructor(router: Router, security: Security, state: GlobalState, repository: LayoutsRepository) {
    super(router, security, state, repository);
    this.data.limit = 1;
    this.data.paging = 0;
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
    this.onSuccess.emit(this.data.selectList);
  }
}
