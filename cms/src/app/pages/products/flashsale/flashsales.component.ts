import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { FlashsalesRepository } from '../services';
import { FlashsaleFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-flashsales',
  templateUrl: './flashsales.component.html',
})
export class FlashsalesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(FlashsaleFormComponent) form: FlashsaleFormComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: FlashsalesRepository) {
    super(router, security, state, repository);
    this.data.sort = 'name';
    this.data.order = 'asc';
    this.data.data = {q: ''};
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
    this.onSelectTab('values');
  }

  toggleView(): void {
    this.data.itemSelected = null;
    this.onSelectTab('values');
  }

  create(): void {
    this.form.show();
  }

  edit(item: any): void {
    this.form.show(item);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }

  onDescSuccess(res: any): void {
    return this.onFormSuccess(res);
  }

  tabs: any = {values: true};

  onSelectTab(tabActive: string): void {
    _.each(this.tabs, (val, key) => this.tabs[key] = false);
    this.tabs[tabActive] = true;
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
