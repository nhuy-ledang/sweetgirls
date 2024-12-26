import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { CookieVar, Dialog } from '../../../@core/services';
import { AppList } from '../../../app.base';
import { CategoriesRepository, PagesRepository } from '../shared/services';
import { PageFormComponent } from './form/form.component';
import { DlgLayoutSelectComponent } from '../shared/modals';

@Component({
  selector: 'ngx-pg-pages',
  templateUrl: './pages.component.html',
})
export class PagesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: PagesRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(PageFormComponent) form: PageFormComponent;
  columnList = [
    {id: 'name', name: 'Tên', checkbox: true, disabled: false},
    {id: 'category', name: 'Danh mục', checkbox: true, disabled: false},
    {id: 'sort_order', name: 'Sắp xếp', checkbox: true, disabled: false},
    {id: 'status', name: 'Tình trạng', checkbox: true, disabled: false},
  ];
  categoryData: { loading: boolean, items: any[] } = {loading: false, items: []};
  statusList = [{id: 1, name: 'Đang mở'}, {id: 0, name: 'Vô hiệu hóa'}];

  constructor(router: Router, security: Security, state: GlobalState, repository: PagesRepository, cookie: CookieVar, private _categories: CategoriesRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    this.columnInt(cookie, 'pg_list');
    this.data.sort = 'sort_order';
    this.data.order = 'asc';
    this.data.data = {q: '', embed: 'category,descs'};
    this.filters = {
      category_id: {operator: '=', value: ''},
      status: {operator: '=', value: ''},
    };
  }

  private getAllCategory(): void {
    this.categoryData.loading = true;
    this._categories.all().then((res: any) => {
      this.categoryData.loading = false;
      this.categoryData.items = res.data;
    }), (errors: any) => {
      this.categoryData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
    setTimeout(() => this.getAllCategory(), 1500);
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

  copy(item: any): void {
    console.log(item);
    this.form.show(false, _.cloneDeep(item));
  }

  copyToLayout(item: any): void {
    this.repository.copyToLayout(item, true).then((res) => {
      console.log(res.data);
      this._state.notifyDataChanged('modal.success', {title: 'Thành công!', message: 'Copy đến thư viện thành công!'});
    }, (errors) => this.handleError(errors));
  }

  remove(item: any) {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }

  select(item): void {
    // console.log(item);
    this.data.itemSelected = item;
  }

  toggleView(): void {
    this.data.itemSelected = null;
  }

  changeProp(item: any, propName: string): void {
    const data = {};
    data[propName] = item[propName];
    this.repository.patch(item, data).then((res) => {
      const newItem = res.data;
      item[propName] = newItem[propName];
      console.log(newItem);
      if (propName === 'home' && newItem.home === true) {
        _.forEach(this.data.items, (it) => {
          if (it.id !== newItem.id) it.home = false;
        });
      }
    }, (errors) => {
      console.log(errors);
    });
  }

  @ViewChild(DlgLayoutSelectComponent) dlgLayout: DlgLayoutSelectComponent;

  openLib(): void {
    this.dlgLayout.show();
  }

  onLayoutSelect(res: any[]): void {
    const ids = [];
    _.forEach(res, (item: any) => ids.push(item.id));
    if (ids.length) {
      this.repository.cloneLayouts({ids: ids}, true).then((res) => {
        console.log(res.data);
        this.data.page = 1;
        this.getData();
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    }
  }
}
