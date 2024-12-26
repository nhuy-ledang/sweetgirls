import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { CookieVar, Dialog } from '../../../@core/services';
import { AppList } from '../../../app.base';
import { CategoriesRepository } from '../shared/services';
import { GiftProductsRepository } from '../shared/services';
import { GiftProductFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-gift-products',
  templateUrl: './gift-products.component.html',
})
export class GiftProductsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(GiftProductFormComponent) form: GiftProductFormComponent;
  repository: GiftProductsRepository;
  columnList = [
    {id: 'image', name: 'Hình', checkbox: true, disabled: false},
    {id: 'name', name: 'Tên', checkbox: true, disabled: false},
    {id: 'price', name: 'Price', checkbox: true, disabled: false},
    {id: 'status', name: 'Tình trạng', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày tạo', checkbox: true, disabled: false},
    {id: 'updated_at', name: 'Ngày cập nhật', checkbox: true, disabled: false},
  ];
  statusList = [{id: 1, name: 'Mở'}, {id: 0, name: 'Tắt'}];

  constructor(router: Router, security: Security, state: GlobalState, repository: GiftProductsRepository, cookie: CookieVar,
              private _categories: CategoriesRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    this.columnInt(cookie, 'pd_gift_products');
    this.data.sort = 'created_at';
    this.data.order = 'desc';
    this.data.data = {q: '', embed: 'descs'};
    this.filters = {
      status: {operator: '=', value: ''},
    };
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

  remove(item: any) {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa các mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'removeAll'}});
  }

  onConfirm(data: any): void {
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    } else if (data.type === 'removeAll') {
      console.log(this.data.selectList);
      const promises = [];
      _.forEach(this.data.selectList, (item) => promises.push(this.repository.remove(item)));
      this.submitted = true;
      Promise.all(promises).then((res) => {
        // console.log(res);
        this.submitted = false;
        this.data.selectList = [];
        this.data.selectAll = false;
        this.data.page = 1;
        this.getData();
      }, (res) => {
        // console.log(res);
        this.submitted = false;
        this.data.selectList = [];
        this.data.selectAll = false;
        this.data.page = 1;
        this.getData();
      });
    }
  }

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
    this.onSelectTab(null, 'images');
  }

  toggleView(): void {
    this.data.itemSelected = null;
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

  tabs: any = {images: true, specials: false, properties: false, options: false, relateds: false, specs: false, tabs: false, modules: false};

  onSelectTab($event: any, tabActive: string): void {
    _.each(this.tabs, (val, key) => this.tabs[key] = false);
    this.tabs[tabActive] = true;
  }
}
