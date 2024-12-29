import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { CookieVar } from '../../../@core/services';
import { UserFormComponent } from './form/form.component';
import { AppList } from '../../../app.base';
import { UsersRepository } from '../shared/services';

@Component({
  selector: 'ngx-user-users',
  templateUrl: './users.component.html',
})
export class UsersComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(UserFormComponent) form: UserFormComponent;
  repository: UsersRepository;
  columnList = [
    {id: 'id', name: 'Mã KH', checkbox: true, disabled: false},
    {id: 'first_name', name: 'Khách hàng', checkbox: true, disabled: false},
    {id: 'email', name: 'Email', checkbox: true, disabled: false},
    {id: 'total_orders', name: 'Tổng chi tiêu', checkbox: true, disabled: false},
    {id: 'phone_number', name: 'Số ĐT', checkbox: true, disabled: false},
    {id: 'last_login', name: 'Đăng nhập', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày tạo', checkbox: true, disabled: false},
  ];
  groupData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(router: Router, security: Security, state: GlobalState, repository: UsersRepository, cookie: CookieVar) {
    super(router, security, state, repository);
    this.columnInt(cookie, 'user_users');
    this.data.sort = 'first_name';
    this.data.order = 'asc';
    this.data.data.embed = '';
    this.filters = {
      group_id: {operator: '=', value: ''},
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

  detail(item: any): void {
    this.storageHelper.set('user_info_' + item.id, item);
    this._router.navigate(['/pages/users/users', item.id, 'info']);
  }

  remove(item: any) {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Cảnh báo', message: 'Tính năng này không được phát triển?', type: 'alert', confirmText: 'Đồng ý', cancelText: 'Không'});
  }

  exportExcel(): void {
    const href = this.repository.exportExcel(this.data.data, this.data.sort, this.data.order);
    // console.log(href);
    location.href = href;
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    }
  }
}
