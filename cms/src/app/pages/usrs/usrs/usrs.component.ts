import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { Err } from '../../../@core/entities';
import { UsrsRepository } from '../../../@core/repositories';
import { UsrGroupsRepository, UsrRolesRepository } from '../shared/services';
import { AppList } from '../../../app.base';
import { UsrFormComponent } from './form/form.component';
import { UseDlgRolesComponent } from './dlg-roles/dlg-roles.component';

@Component({
  selector: 'ngx-usrs',
  templateUrl: './usrs.component.html',
})
export class UsrsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: UsrsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(UsrFormComponent) form: UsrFormComponent;
  @ViewChild(UseDlgRolesComponent) dlgRole: UseDlgRolesComponent;
  groupData: { loading: boolean, items: any[] } = {loading: false, items: []};
  roleData: { loading: boolean, items: any[] } = {loading: false, items: []};

  constructor(router: Router, security: Security, state: GlobalState, repository: UsrsRepository, private _groups: UsrGroupsRepository, private _roles: UsrRolesRepository) {
    super(router, security, state, repository);
    this.data.sort = 'id';
    this.data.order = 'desc';
    this.data.data.embed = 'group,roles';
    this.data.data.role_id = '';
    this.filters = {
      group_id: {operator: '=', value: ''},
    };
  }

  private getAllGroup(): void {
    this.groupData.loading = true;
    this._groups.all().then((res: any) => {
      this.groupData.loading = false;
      this.groupData.items = res.data;
    }), (errors: any) => {
      this.groupData.loading = false;
      console.log(errors);
    };
  }

  private getAllRole(): void {
    this.roleData.loading = true;
    this._roles.all().then((res: any) => {
      this.roleData.loading = false;
      this.roleData.items = res.data;
    }), (errors: any) => {
      this.roleData.loading = false;
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
    setTimeout(() => this.getAllGroup(), 500);
    setTimeout(() => this.getAllRole(), 1000);
  }

  create(): void {
    this.form.show();
  }

  edit(item: any): void {
    this.form.show(item);
  }

  addRole(item: any): void {
    this.dlgRole.show(item);
  }

  detail(item: any): void {
    this.storageHelper.set('usr_info_' + item.id, item);
    this._router.navigate(['/pages/usrs/detail', item.id]);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  /*removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Cảnh báo', message: 'Tính năng này không được phát triển?', type: 'alert', confirmText: 'Đồng ý', cancelText: 'Không'});
  }*/

  banned(item: any) {
    if (item.status === this.CONST.USER_STATUS_STARTER) {
      return;
    }
    let message = 'Bạn có chắc nhắn muốn khóa tài khoản này?';
    if (item.status === this.CONST.USER_STATUS_BANNED) {
      message = 'Bạn có chắc nhắn muốn mở khóa tài khoản này?';
    }
    this.confirm.show({title: 'Xác nhận', message: message, type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'banned', info: item}});
  }

  onRolesSuccess(data?: any) {
    this.getData(null, false);
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    } else if (data.type === 'banned') {
      const banned: boolean = data.info.status !== this.CONST.USER_STATUS_BANNED;
      this.repository.banned(data.info.id, {banned: banned}).then((res) => {
          console.log(res.data);
          this.submitted = false;
          if (banned) {
            data.info.status = this.CONST.USER_STATUS_BANNED;
          } else {
            data.info.status = this.CONST.USER_STATUS_ACTIVATED;
          }
          this.getData(null, false);
        }, (errors: Err[]) => {
          console.log(errors);
          this.submitted = false;
          this.errors = errors;
          this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', errors: errors[0].errorMessage});
        },
      );
    }
  }
}
