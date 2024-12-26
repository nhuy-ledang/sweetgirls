import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { AppList } from '../../../app.base';
import { LanguagesRepository } from '../../../@core/repositories';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { LanguageFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-st-languages',
  templateUrl: 'languages.component.html',
})
export class LanguagesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(LanguageFormComponent) form: LanguageFormComponent;
  repository: LanguagesRepository;

  constructor(router: Router, security: Security, state: GlobalState, repository: LanguagesRepository) {
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
}
