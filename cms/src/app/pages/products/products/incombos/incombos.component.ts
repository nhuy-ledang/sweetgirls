import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { ProductIncombosRepository } from '../../services';
import { ProductIncomboFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-product-incombos',
  templateUrl: './incombos.component.html',
})
export class ProductIncombosComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: ProductIncombosRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ProductIncomboFormComponent) form: ProductIncomboFormComponent;
  info: any;

  @Input() set product(item: any) {
    console.log(item);
    this.info = item;
    this.data.data.product_id = item.id;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductIncombosRepository) {
    super(router, security, state, repository);
    this.data.data.embed = '';
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
  }

  create(item: any): void {
    this.form.show(this.info, this.data.items);
  }

  edit(item: any): void {
    this.form.show(this.info, this.data.items);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any): void {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }
}
