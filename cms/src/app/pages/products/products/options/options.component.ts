import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { ProductOptionsRepository } from '../../services';
import { ProductOptionFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-product-options',
  templateUrl: './options.component.html',
})
export class ProductOptionsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: ProductOptionsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ProductOptionFormComponent) form: ProductOptionFormComponent;
  info: any;

  @Input() set product(item: any) {
    console.log(item);
    this.info = item;
    this.data.data.product_id = item.id;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductOptionsRepository) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
  }

  create(): void {
    this.form.show(this.info);
  }

  edit(item: any): void {
    this.form.show(this.info, item);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any): void {
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    }
  }
}
