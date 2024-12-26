import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { CategoriesRepository, LayoutsRepository } from '../shared/services';
import { LayoutFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pg-layouts',
  templateUrl: './layouts.component.html',
})
export class LayoutsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(LayoutFormComponent) form: LayoutFormComponent;
  categoryData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(router: Router, security: Security, state: GlobalState, repository: LayoutsRepository,
              private _categories: CategoriesRepository) {
    super(router, security, state, repository);
    this.data.sort = 'name';
    this.data.order = 'asc';
    this.data.data = {q: '', embed: 'category,descs', category_id: ''};
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
    this.data.itemSelected = item;
  }

  toggleView(): void {
    this.data.itemSelected = null;
  }
}
