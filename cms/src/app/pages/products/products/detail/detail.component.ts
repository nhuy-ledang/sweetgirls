import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { ProductsRepository } from '../../shared/services';
import { ProductFormComponent } from '../form/form.component';
import { ProductVariantEditFormComponent } from '../variants/edit-form/edit-form.component';

@Component({
  selector: 'ngx-pd-product-detail',
  templateUrl: './detail.component.html',
})
export class ProductDetailComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ProductFormComponent) form: ProductFormComponent;
  @ViewChild(ProductVariantEditFormComponent) variantForm: ProductVariantEditFormComponent;
  info: any;
  hideBreadcrumb: boolean = false;

  @Input() set product(item: any) {
    console.log(item);
    this.info = item;
    this.hideBreadcrumb = true;
    this.onSelectTab(null, 'specials');
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductsRepository, private _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.info = this._route.snapshot.data['info'];
    console.log(this.info);
  }

  private getInfo(id: number): void {
    this.repository.find(id, {embed: ''}, false).then((res: any) => {
      console.log(res.data);
      _.each(res.data, (val, key) => this.info[key] = val);
    }, (errors) => console.log(errors));
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    if (this.info.isCaching) this.getInfo(this.info.id);
  }

  edit(): void {
    console.log(this.info);
    if (!this.info.master_id) {
      this.form.show(this.info);
    } else {
      this.variantForm.show(this.info);
    }
  }

  /*remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }*/

  protected onUpdated(data: any): void {
    _.each(data, (val, key) => {
      if (this.info.hasOwnProperty(key)) this.info[key] = val;
    });
    console.log(data);
  }

  onFormSuccess(res: any): void {
    this.onUpdated(res);
  }

  onConfirm(data: any) {
    if (data.type === 'remove') {
    }
  }

  tabs: any = {specials: false, images: true};

  onSelectTab($event: any, tabActive: string): void {
    _.each(this.tabs, (val, key) => this.tabs[key] = false);
    this.tabs[tabActive] = true;
  }
}
