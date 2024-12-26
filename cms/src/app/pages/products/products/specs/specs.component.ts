import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { ProductSpecsRepository } from '../../services';
import { ProductSpecFormComponent } from './form/form.component';
import { ProductSpecDescComponent } from './desc/desc.component';

@Component({
  selector: 'ngx-pd-product-specs',
  templateUrl: './specs.component.html',
})
export class ProductSpecsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: ProductSpecsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ProductSpecFormComponent) form: ProductSpecFormComponent;
  @ViewChild(ProductSpecDescComponent) frmDesc: ProductSpecDescComponent;
  info: any;

  @Input() set product(item: any) {
    console.log(item);
    this.info = item;
    this.data.data.product_id = item.id;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductSpecsRepository) {
    super(router, security, state, repository);
    this.data.data.embed = 'descs';
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

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa các mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'removeAll'}});
  }

  onConfirm(data: any): void {
    if (data.type === 'remove') {
      this.removeItem(data.info);
    } else if (data.type === 'removeAll') {
      console.log(this.data.selectList);
      const promises = [];
      _.forEach(this.data.selectList, (item) => promises.push(this.repository.remove(item)));
      this.submitted = true;
      Promise.all(promises).then((res) => {
        // console.log(res);
        this.submitted = false;
        this.data.selectList = [];
        this.data.selectAll = false;
        this.data.page = 1;
        this.getData();
      }, (res) => {
        // console.log(res);
        this.submitted = false;
        this.data.selectList = [];
        this.data.selectAll = false;
        this.data.page = 1;
        this.getData();
      });
    }
  }

  onDescSuccess(res: any): void {
    return this.onFormSuccess(res);
  }
}
