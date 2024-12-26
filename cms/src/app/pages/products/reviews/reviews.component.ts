import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { CookieVar, Dialog } from '../../../@core/services';
import { AppList } from '../../../app.base';
import { ReviewsRepository } from '../services';
import { ReviewFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-reviews',
  templateUrl: './reviews.component.html',
})
export class ReviewsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ReviewFormComponent) form: ReviewFormComponent;
  columnList = [
    {id: 'product', name: 'Sản phẩm', checkbox: false, disabled: false},
    {id: 'status', name: 'Tình trạng', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày tạo', checkbox: true, disabled: false},
    {id: 'updated_at', name: 'Ngày cập nhật', checkbox: true, disabled: false},
  ];
  categoryData: {loading: boolean, items: any[]} = {loading: false, items: []};
  statusList = [{id: 1, name: 'Đã duyệt'}, {id: 0, name: 'Chờ duyệt'}];

  constructor(router: Router, security: Security, state: GlobalState, repository: ReviewsRepository, cookie: CookieVar,
              private _dialog: Dialog) {
    super(router, security, state, repository);
    this.columnInt(cookie, 'rvw_list');
    this.data.sort = 'id';
    this.data.order = 'desc';
    this.data.data = {q: '', embed: '', category_id: ''};
    this.filters = {
      // category_id: {operator: '=', value: ''},
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

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa các mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'removeAll'}});
  }

  onConfirm(data: any): void {
    if (data.type === 'remove') {
      this.removeItem(data.info);
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
}
