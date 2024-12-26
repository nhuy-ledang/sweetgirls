import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { Dialog } from '../../../@core/services';
import { AppList } from '../../../app.base';
import { CategoriesRepository } from '../shared/services';
import { CategoryFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-categories',
  templateUrl: './categories.component.html',
})
export class CategoriesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(CategoryFormComponent) form: CategoryFormComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: CategoriesRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    this.data.pageSize = 100;
    this.data.sort = 'id';
    this.data.order = 'asc';
    this.data.data = {q: '', embed: 'descs'};
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  create(): void {
    this.form.show();
  }

  edit(item: any): void {
    this.form.show(item);
  }

  preview(item: any): void {
    this._dialog.open(item.href, item.name, {width: 1440, height: 768}).then((res) => {
      console.log(res);
    }, (res) => {
      console.log(res);
    });
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  changeProp(item: any, propName: string): void {
    console.log(item);
    const data = {};
    data[propName] = item[propName];
    this.repository.patch(item, data).then((res) => {
      console.log(res.data);
    }, (errors) => {
      console.log(errors);
    });
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
  }

  toggleView(): void {
    this.data.itemSelected = null;
  }

  onMetaSuccess(res: any): void {
    return this.onFormSuccess(res);
  }

  onDescSuccess(res: any): void {
    return this.onFormSuccess(res);
  }

  tabs: any = {properties: true, options: false};

  onSelectTab($event: any, tabActive: string): void {
    _.each(this.tabs, (val, key) => this.tabs[key] = false);
    this.tabs[tabActive] = true;
  }
}
