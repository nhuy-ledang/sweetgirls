import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { OptionValueFormComponent } from './form/form.component';
import { OptionValuesRepository } from '../../services';
import { OptionValueDescComponent } from './desc/desc.component';

@Component({
  selector: 'ngx-pd-option-value',
  templateUrl: './values.component.html',
})
export class OptionValuesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(OptionValueFormComponent) form: OptionValueFormComponent;
  @ViewChild(OptionValueDescComponent) frmDesc: OptionValueDescComponent;
  info: any;

  @Input() set value(item: any) {
    this.info = item;
    this.data.data.option_id = item.id;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: OptionValuesRepository) {
    super(router, security, state, repository);
    this.data.sort = 'sort_order';
    this.data.order = 'asc';
    this.data.data = {q: '', option_id: 0};
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
    this.form.show(this.info);
  }

  edit(item: any): void {
    this.form.show(this.info, item);
  }

  translate(item: any, lang?: 'en'): void {
    if (!lang) lang = 'en';
    console.log(item);
    const descs = item.descs ? item.descs : [];
    let it = _.find(descs, {lang: lang});
    if (!it) it = _.cloneDeep(item);
    this.frmDesc.show(it, lang);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    }
  }
}
