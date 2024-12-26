import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { MenusRepository } from '../../../@core/repositories';
import { MenuFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pg-menus',
  templateUrl: './menus.component.html',
})
export class MenusComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(MenuFormComponent) form: MenuFormComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: MenusRepository) {
    super(router, security, state, repository);
    this.data.paging = 0;
    this.data.sort = 'sort_order';
    this.data.order = 'asc';
    this.data.data = {q: '', embed: 'page,descs'};
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

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }

  select(item): void {
    console.log(item);
    if (!item.source && item.page_id) {
      this._router.navigate(['/pages/pages/pages', item.page_id, 'contents']);
    }
  }

  changeProp(item: any, propName: string): void {
    const data = {};
    data[propName] = item[propName];
    this.repository.patch(item, data).then((res) => {
      const newItem = res.data;
      item[propName] = newItem[propName];
      console.log(newItem);
      if (propName === 'is_sub' && newItem.is_sub === true) {
        _.forEach(this.data.items, (it) => {
          if (it.id !== newItem.id) it.is_sub = false;
        });
      }
    }, (errors) => {
      console.log(errors);
    });
  }
}
