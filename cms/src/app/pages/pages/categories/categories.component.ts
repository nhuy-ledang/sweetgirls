import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { CategoriesRepository } from '../shared/services';
import { CategoryFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pg-categories',
  templateUrl: './categories.component.html',
})
export class CategoriesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(CategoryFormComponent) form: CategoryFormComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: CategoriesRepository) {
    super(router, security, state, repository);
    this.data.pageSize = 100;
    this.data.sort = 'id';
    this.data.order = 'asc';
    this.data.data = {q: '', embed: ''};
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