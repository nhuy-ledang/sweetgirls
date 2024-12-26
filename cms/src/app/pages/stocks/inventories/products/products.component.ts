import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { CookieVar } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { InventoryProductsRepository } from '../../shared/services';
import { DlgImportConfirmComponent } from './dlg-import-confirm/dlg-import-confirm.component';

@Component({
  selector: 'ngx-sto-ivt-products',
  templateUrl: './products.component.html',
})
export class IvtProductsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(DlgImportConfirmComponent) dlgImportConfirm: DlgImportConfirmComponent;
  repository: InventoryProductsRepository;
  columnList = [
    {id: 'id', name: '#', checkbox: true, disabled: false},
    {id: 'category', name: 'Danh mục', checkbox: true, disabled: false},
    {id: 'prd_idx', name: 'Mã sản phẩm', checkbox: true, disabled: false},
    {id: 'prd_image', name: 'Hình', checkbox: true, disabled: false},
    {id: 'prd_name', name: 'Tên sản phẩm', checkbox: true, disabled: false},
    {id: 'prd_unit', name: 'ĐVT', checkbox: true, disabled: false},
    {id: 'prd_reality', name: 'Số lượng', checkbox: true, disabled: false},
    {id: 'status', name: 'Trạng thái kiểm kho', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Thời gian cân bằng kho', checkbox: true, disabled: false},
    {id: 'staff', name: 'Người kiểm', checkbox: true, disabled: false},
    {id: 'storekeeper', name: 'Thủ kho', checkbox: true, disabled: false},
    {id: 'accountant', name: 'Kế toán', checkbox: true, disabled: false},
    {id: 'review_at', name: 'Ngày xử lý', checkbox: true, disabled: false},
    {id: 'note', name: 'Ghi chú sau cuối', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: InventoryProductsRepository, protected _cookie: CookieVar) {
    super(router, security, state, repository);
    // this.data.sort = 'sto__inventory_products.id';
    // this.data.order = 'desc';
  }

  ngOnInit(): void {
    this.columnInt(this._cookie, 'sto_ivt_products');
    this.data.data = {q: '', idx: '', product_idx: '', product: '', embed: 'stock,product'};
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  onConfirm(data: any): void {
    console.log(data);
  }

  importExcel() {
    if (typeof window['timerNew'] !== 'undefined') {
      clearInterval(window['timerNew']);
      window['timerNew'] = undefined;
    }
    window['timerNew'] = undefined;

    const fileInput: HTMLInputElement = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = 'file';
    fileInput.accept = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel';

    jQuery('#form-new-excel').remove();
    jQuery('body').prepend('<form enctype="multipart/form-data" id="form-new-excel" style="display: none;"></form>');
    jQuery('#form-new-excel').append(fileInput);
    fileInput.click();

    window['timerNew'] = setInterval(() => {
      if (fileInput.value) {
        clearInterval(window['timerNew']);
        window['timerNew'] = undefined;

        const formData = new FormData(document.getElementById('form-new-excel') as HTMLFormElement);
        this.repository.importCheck(formData, true).then((res: any) => {
          this.dlgImportConfirm.show(formData, res.data);
        }, (errors) => {
          console.log(errors);
        });
      }
    }, 500);
  }

  onImport(data: {type: string, formData: FormData}) {
    console.log(data);
    this.repository.import(data.formData, true).then((res: any) => {
      console.log(res.data);
      this.data.page = 1;
      this.getData();
    }, (errors) => {
      // $rootScope.openError(errors[0].errorMessage);
    });
  }
}
