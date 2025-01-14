import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { LanguagesRepository } from '../../../../@core/repositories';
import { AppForm } from '../../../../app.base';
import { CategoriesRepository, ManufacturersRepository, ProductsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-product-form',
  templateUrl: './form.component.html',
})
export class ProductFormComponent extends AppForm implements OnInit, OnDestroy {
  repository: ProductsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @ViewChild('formTabs', {static: false}) formTabs?: TabsetComponent;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  copyData: any|boolean;
  controls: {
    model?: AbstractControl,
    category_id?: AbstractControl,
    manufacturer_id?: AbstractControl,
    name?: AbstractControl,
    unit?: AbstractControl,
    price?: AbstractControl,
    short_description?: AbstractControl,
    description?: AbstractControl,
    stock_status?: AbstractControl,
    status?: AbstractControl,
    alias?: AbstractControl,
    image?: AbstractControl,
  };
  categoryData: {loading: boolean, items: any[]} = {loading: false, items: []};
  manufacturerData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: ProductsRepository, languages: LanguagesRepository,
              private _categories: CategoriesRepository, private _manufacturers: ManufacturersRepository) {
    super(router, security, state, repository, languages);
    this.form = fb.group({
      category_id: [''],
      manufacturer_id: ['', Validators.compose([Validators.required])],
      model: [''],
      name: ['', Validators.compose([Validators.required])],
      long_name: [''],
      unit: [''],
      price: [0, Validators.compose([Validators.min(0)])],
      is_gift: [true],
      is_coin_exchange: [false],
      no_cod: [false],
      // is_free: [false],
      coins: [0, Validators.compose([Validators.min(0)])],
      weight: [''],
      length: [''],
      width: [''],
      height: [''],
      gift_set_id: [''],
      short_description: [''],
      description: [''],
      properties: [''],
      user_guide: [''],
      tag: [''],
      link: [''],
      stock_status: ['in_stock'],
      status: [true],
      meta_title: [''],
      meta_description: [''],
      meta_keyword: [''],
      alias: [''],
      image: [''],
      banner: [''],
      categories: fb.group({}),
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  protected afterDataLoading(): void {
    const ids = [];
    if (this.info.categories) {
      const tmp = _.split(this.info.categories, ',');
      for (let i = 0; i < tmp.length; i++) ids.push(parseInt(tmp[i], 0));
    }
  }

  private getDropdownData(): void {
    this.categoryData.loading = true;
    this.manufacturerData.loading = true;
    Promise.all([this._categories.all(), this._manufacturers.all()]).then((res) => {
      this.categoryData.loading = false;
      this.categoryData.items = res[0] ? res[0].data : [];
      this.manufacturerData.loading = false;
      this.manufacturerData.items = res[1] ? res[1].data : [];
      this.afterDataLoading();
    }, (res) => {
      console.log(res);
      this.categoryData.loading = false;
      this.manufacturerData.loading = false;
    });
  }

  ngOnInit(): void {
    this.getAllLanguage();
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  private setValues(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    if (!info.category_id) this.controls.category_id.setValue('');
    if (!info.manufacturer_id) this.controls.manufacturer_id.setValue('');
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
  }

  protected setInfo(info: any): void {
    this.setValues(info);
    this.info = info;
  }

  private generateRandomCode(): string {
    // Tạo số ngẫu nhiên 10 chữ số
    const min = 1000000000; 
    const max = 9999999999; 
    return Math.floor(min + Math.random() * (max - min)).toString();
  }

  show(info?: any, copyData?: any): void {
    this.resetForm(this.form);
    this.info = false;
    this.copyData = copyData ? copyData : false;
    
    if (info) {
      this.setInfo(info);
    } else {
      if (this.copyData) {
        this.setValues(this.copyData);
      } else {
        // Reset các control về giá trị mặc định trước
        _.each(this.controls, (val, key) => {
          if (this.controls.hasOwnProperty(key) && 
              this.controls[key] instanceof FormControl && 
              !_.includes(['price', 'status'], key)) {
            this.controls[key].setValue('');
          }
        });

        this.controls.price.setValue(0);
        this.controls.status.setValue(true);

        // Tạo mã ngẫu nhiên và set giá trị
        const code = this.generateRandomCode();
        this.controls.model.setValue(code);
      }
    }
    
    this.getDropdownData();
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  async onSubmit(params: any, dontHide?: boolean, loading ?: boolean): Promise<void> {
    this.showValid = true;
    if (this.form.valid) {
      try {
        // Kiểm tra mã trùng lặp trước khi submit
        const modelCode = this.controls.model.value;
        
        // Tìm trong danh sách sản phẩm hiện có
        const existingProducts = await this.repository.all();
        const exists = existingProducts.data.some(product => 
          product.model === modelCode && 
          (!this.info || (this.info && this.info.id !== product.id))
        );

        // Nếu đang thêm mới và mã đã tồn tại
        if (!this.info && exists) {
          this._state.notifyDataChanged('modal.success', {
            title: 'Thất bại!', 
            message: 'Mã đã tồn tại, vui lòng nhập mã mới!'
          });
          return;
        }

        // Nếu đang cập nhật và mã đã thay đổi và trùng với mã khác
        if (this.info && modelCode !== this.info.model && exists) {
          this._state.notifyDataChanged('modal.success', {
            title: 'Thất bại!', 
            message: 'Mã đã tồn tại, vui lòng nhập mã mới!'
          });
          return;
        }

        const newParams = _.cloneDeep(params);
        newParams.price = params.price ? params.price : 0;
        newParams.coins = params.coins ? params.coins : 0;
        if (this.fileSelected) {
          newParams.file_path = this.fileSelected.path;
        } else if (this.file) {
          newParams.file = this.file;
        } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
        if (this.bnSelected) {
          newParams.bn_path = this.bnSelected.path;
        } else if (this.bnFile) {
          newParams.bn_file = this.bnFile;
        } else if (!this.bnOpt.thumb_url) newParams['banner'] = '';
        const categories = [];
        _.each(params.categories, (val, key) => {
          const id = parseInt(key.replace('category_', ''), 0);
          if (val) categories.push(id);
        });
        newParams.categories = categories.join(',');
        this.submitted = true;

        if (this.info) {
          this.repository.update(this.info, this.utilityHelper.toFormData(newParams), loading)
            .then((res) => this.handleSuccess(_.extend({edited: true}, res.data), dontHide), 
                  (errors) => this.handleError(errors));
        } else if (this.copyData) {
          this.repository.copy(this.copyData, this.utilityHelper.toFormData(newParams), loading)
            .then((res) => this.handleSuccess(res.data, dontHide), 
                  (errors) => this.handleError(errors));
        } else {
          this.repository.create(this.utilityHelper.toFormData(newParams), loading)
            .then((res) => this.handleSuccess(res.data, dontHide), 
                  (errors) => this.handleError(errors));
        }
      } catch (error) {
        console.error('Error during submission:', error);
        // Chỉ hiển thị thông báo lỗi nếu thực sự có lỗi từ API
        if (error.status) {
          this._state.notifyDataChanged('modal.success', {
            title: 'Thất bại!', 
            message: 'Đã xảy ra lỗi trong quá trình xử lý!'
          });
        }
      }
    }
  }
}
