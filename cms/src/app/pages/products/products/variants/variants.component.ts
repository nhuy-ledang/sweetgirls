import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { ProductsRepository } from '../../shared/services';
import { ProductVariantFormComponent } from './form/form.component';
import { ProductVariantEditFormComponent } from './edit-form/edit-form.component';

@Component({
  selector: 'ngx-pd-product-variants',
  templateUrl: './variants.component.html',
})
export class ProductVariantsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: ProductsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ProductVariantFormComponent) form: ProductVariantFormComponent;
  @ViewChild(ProductVariantEditFormComponent) editForm: ProductVariantEditFormComponent;
  info: any;
  showDetail: boolean = false;
  d: {variants: any[], values: any[]} = {variants: [], values: []};

  @Input() showTitle: boolean = true;

  @Input() set product(item: any) {
    console.log(item);
    this.showDetail = false;
    this.info = item;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductsRepository) {
    super(router, security, state, repository);
  }

  protected getVariants(cb?: Function, loading?: boolean): void {
    if (loading !== false) this.data.loading = true;
    this.repository.getVariants(this.info.id, false).then((res: any) => {
        console.log(res.data);
        this.data.items = res.data;
        this.d.variants = res.data.variants;
        this.d.values = res.data.values;
        this.data.loading = false;
      }, (res: any) => {
        console.log(res.errors);
        this.data.loading = false;
      },
    );
  }

  // Override fn
  protected getData(cb?: Function, loading?: boolean): void {
    return this.getVariants(cb, loading);
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

  curVariant: any;

  edit(variant: any, item?: any): void {
    console.log(variant, item);
    this.curVariant = variant;
    this.editForm.show(variant.product);
  }

  remove(variant: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: variant}});
  }

  detail(variant: any): void {
    this.storageHelper.set('product_info_' + variant.product.id, variant.product);
    this._router.navigate(['/pages/prd/products', variant.product.id]);
  }

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
    this.showDetail = true;
  }

  onConfirm(data: any): void {
    console.log(data.info.product);
    const curVariant = data.info;
    if (data.type === 'remove') {
      const product = curVariant.product;
      curVariant.product = null;
      this.repository.removeVariant(product, true).then((res) => {
          console.log(res);
        }, (res: any) => {
          curVariant.product = product;
          this.handleError(res);
        },
      );
    }
  }

  onFormSuccess(res: any): void {
    console.log(res);
    this.getData(null, false);
  }

  onEditFormSuccess(res: any): void {
    console.log(res);
    if (this.curVariant) this.curVariant.product = res;
    // this.getData(null, false);
  }

  toggleView(): void {
    this.data.itemSelected = null;
    this.showDetail = false;
  }
}
