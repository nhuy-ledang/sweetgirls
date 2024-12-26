import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { ProductBestsellersRepository } from '../services';
import { BestsellerProductFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-bestseller-products',
  templateUrl: './bestseller-products.component.html',
})
export class BestsellerProductsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(BestsellerProductFormComponent) form: BestsellerProductFormComponent;
  repository: ProductBestsellersRepository;

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductBestsellersRepository) {
    super(router, security, state, repository);
    this.data.sort = 'lat.created_at';
    this.data.order = 'desc';
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

  create(): void {
    this.form.show();
  }

  remove(item: any) {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa các mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'removeAll'}});
  }

  onConfirm(data: any): void {
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
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
}
