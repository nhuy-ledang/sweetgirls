import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { FlashsaleValueFormComponent } from './form/form.component';
import { FlashsalesRepository } from '../../services';
import { ProductsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-flashsale-value',
  templateUrl: './values.component.html',
})
export class FlashsaleValuesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: FlashsalesRepository;

  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(FlashsaleValueFormComponent) form: FlashsaleValueFormComponent;
  info: any;

  @Input() set value(item: any) {
    this.info = item;
    this.data.data.flashsale_id = item.id;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: FlashsalesRepository, private _product: ProductsRepository) {
    super(router, security, state, repository);
    this.data.sort = 'sort_order';
    this.data.order = 'asc';
    this.data.data = {q: ''};
  }

  // Override fn
  protected getData(): void {
    this.data.items = [];
    this.data.loading = true;
    this.repository.getValues({start_date: this.info.start_date, end_date: this.info.end_date}).then((res: any) => {
      console.log(res);
      this.data.items = res.data;
      /*_.forEach(res.data, (item, index) => {
        item.index = index + (this.data.pageSize * (this.data.page - 1));
        this.data.items.push(item);
      });*/
      this.data.loading = false;
    }, (errors) => {
      console.log(errors);
      this.data.loading = false;
    });
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

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa các mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'removeAll'}});
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this._product.removeSpecial(data.info.product_id, data.info.id);
    } else if (data.type === 'removeAll') {
      console.log(this.data.selectList);
      const promises = [];
      _.forEach(this.data.selectList, (item) => promises.push(this._product.removeSpecial(item.product_id, item.id)));
      this.submitted = true;
      Promise.all(promises).then((res) => {
        // console.log(res);
        this.submitted = false;
        this.data.selectList = [];
        this.data.selectAll = false;
        this.data.page = 1;
      }, (res) => {
        // console.log(res);
        this.submitted = false;
        this.data.selectList = [];
        this.data.selectAll = false;
        this.data.page = 1;
      });
    }
    this.getData();
  }
}
