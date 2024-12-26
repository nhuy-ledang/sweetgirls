import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { FeedbacksRepository } from '../services';

@Component({
  selector: 'ngx-feedbacks',
  templateUrl: 'feedbacks.component.html',
})

export class FeedbacksComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  statusList = [{id: 1, name: 'Đã phản hồi'}, {id: 0, name: 'Chưa phản hồi'}];

  constructor(router: Router, security: Security, state: GlobalState, repository: FeedbacksRepository) {
    super(router, security, state, repository);
    this.data.data.embed = 'user';
    this.filters = {
      status: {operator: '=', value: ''},
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy() {
    super.destroy();
  }

  ngAfterViewInit() {
    setTimeout(() => this.getData(), 200);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any) {
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
