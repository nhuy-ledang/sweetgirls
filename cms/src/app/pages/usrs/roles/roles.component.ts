import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { GlobalState } from '../../../@core/utils';
import { Security } from '../../../@core/security';
import { UsrRolesRepository } from '../shared/services';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { RoleFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-usr-roles',
  templateUrl: './roles.component.html',
})
export class RolesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(RoleFormComponent) form: RoleFormComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: UsrRolesRepository) {
    super(router, security, state, repository);
    this.data.sort = 'id';
    this.data.order = 'asc';
    this.data.paging = 0;
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

  remove(item: any) {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }
}
