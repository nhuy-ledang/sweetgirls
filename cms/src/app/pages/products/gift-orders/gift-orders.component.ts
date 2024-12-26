import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { GiftOrdersRepository } from '../shared/services';
import { GiftOrderFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-gift-orders',
  templateUrl: './gift-orders.component.html',
})
export class GiftOrdersComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(GiftOrderFormComponent) form: GiftOrderFormComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: GiftOrdersRepository) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
    this.data.data = {q: '', embed: 'products'};
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

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    }
  }
}
