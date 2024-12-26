import { Component, EventEmitter, Input, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { Dialog } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { LayoutPatternsRepository } from '../services';

@Component({
  selector: 'ngx-dlg-pattern-select',
  templateUrl: './dlg-pattern-select.component.html',
})

export class DlgPatternSelectComponent extends AppList implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  @Input() set limit(limit: boolean|number) {
    if (limit === false || (limit && typeof limit === 'number')) {
      this.data.limit = limit;
    }
  }

  tempCache: any = {};

  constructor(router: Router, security: Security, state: GlobalState, repository: LayoutPatternsRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    // this.data.limit = 1;
    this.data.paging = 0;
    this.data.pageSize = 50;
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
    this.data.selectList = [];
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

  openPreview(item, layout: any): void {
    if (!item.id) return;
    this._dialog.open(layout.preview_url, item.name, {width: 1440, height: 768}).then((res) => {
      console.log(res);
    }, (res) => {
      console.log(res);
    });
  }
}
