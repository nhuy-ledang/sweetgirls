import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { GlobalState } from '../../../@core/utils';
import { Security } from '../../../@core/security';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { OrderWebhookRepository } from '../services';

@Component({
  selector: 'ngx-ord-webhook',
  templateUrl: './webhook.component.html',
})
export class OrderWebhookComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: OrderWebhookRepository) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  remove(item: any): void {
    console.log(item);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    }
  }
}
