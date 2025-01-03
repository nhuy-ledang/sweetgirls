import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { CookieVar, Dialog } from '../../../@core/services';
import { AppList } from '../../../app.base';
import { CategoriesRepository } from '../shared/services';
import { ProductsRepository } from '../shared/services';
import { ProductFormComponent } from './form/form.component';
import { ProductQuantityFormComponent } from './form/quantity-form.component';

@Component({
  selector: 'ngx-products',
  templateUrl: './products.component.html',
})
export class ProductsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ProductFormComponent) form: ProductFormComponent;
  @ViewChild(ProductQuantityFormComponent) quantityForm: ProductQuantityFormComponent;
  repository: ProductsRepository;
  columnList = [
    {id: 'image', name: 'Hình', checkbox: true, disabled: false},
    {id: 'name', name: 'Tên', checkbox: true, disabled: false},
    {id: 'category', name: 'Danh mục', checkbox: true, disabled: false},
    {id: 'price', name: 'Giá bán', checkbox: true, disabled: false},
    {id: 'coins', name: 'Coin', checkbox: true, disabled: false},
    {id: 'quantity', name: 'Số lượng', checkbox: true, disabled: false},
    {id: 'stock_status', name: 'Tình trạng', checkbox: true, disabled: false},
    {id: 'is_gift', name: 'Là quà tặng', checkbox: true, disabled: false},
    {id: 'status', name: 'Tình trạng', checkbox: true, disabled: false},
    {id: 'weight', name: 'Cân nặng', checkbox: true, disabled: false},
    {id: 'dimension', name: 'Kích thước', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày tạo', checkbox: true, disabled: false},
    {id: 'updated_at', name: 'Ngày cập nhật', checkbox: true, disabled: false},
  ];
  categoryData: {loading: boolean, items: any[]} = {loading: false, items: []};
  statusList = [{id: 1, name: 'Mở'}, {id: 0, name: 'Tắt'}];
  giftList = [{id: 1, name: 'Quà tặng'}, {id: 0, name: 'Mặc định'}];
  stockList = [{id: 'in_stock', name: 'Sẵn hàng'}, {id: 'out_of_stock', name: 'Hết hàng'}, {id: 'pre_order', name: 'Đặt trước'}];

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductsRepository, cookie: CookieVar,
              private _categories: CategoriesRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    this.columnInt(cookie, 'pd_products');
    this.data.sort = 'created_at';
    this.data.order = '';
    this.data.data = {q: '', embed: 'category', category_id: ''};
    this.filters = {
      category_id: {operator: '=', value: ''},
      stock_status: {operator: '=', value: ''},
      is_gift: {operator: '=', value: ''},
      status: {operator: '=', value: ''},
    };
  }

  private getDropdownData(): void {
    this.categoryData.loading = true;
    this._categories.all().then((res: any) => {
      this.categoryData.loading = false;
      this.categoryData.items = res.data;
    }), (errors: any) => {
      this.categoryData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(() => {
      this.getDropdownData();
    }), 200);
  }

  create(): void {
    this.form.show();
  }

  edit(item: any): void {
    this.form.show(item);
  }

  copy(item: any): void {
    console.log(item);
    this.form.show(false, _.cloneDeep(item));
  }

  renew(item: any): void {
    this.repository.renew(item.id).then((res) => {
      console.log(res.data);
      this.getData();
    }, (errors) => {
      console.log(errors);
    });
  }

  preview(item: any): void {
    this._dialog.open(item.href, item.name, {width: 1440, height: 768}).then((res) => {
      console.log(res);
    }, (res) => {
      console.log(res);
    });
  }

  remove(item: any) {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa các mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'removeAll'}});
  }

  detail(item: any): void {
    this.storageHelper.set('product_info_' + item.id, item);
    this._router.navigate(['/pages/prd/products', item.id]);
  }

  updateQuantity(item: any): void {
    this.quantityForm.show(item);
  }

  onQuantityFormSuccess(res: any): void {
    console.log(res);
    if (!res.edited) {
      this.data.page = 1;
      this.getData();
    } else {
      this.getData(null, false);
    }
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

  select(item): void {
    console.log(item);
    this.data.itemSelected = item;
    this.onSelectTab(null, 'images');
  }

  toggleView(): void {
    this.data.itemSelected = null;
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

  tabs: any = {images: true, specials: false, properties: false, options: false, variants: false, incombo: false, relateds: false, specs: false, tabs: false, modules: false};

  onSelectTab($event: any, tabActive: string): void {
    _.each(this.tabs, (val, key) => this.tabs[key] = false);
    this.tabs[tabActive] = true;
  }
}
