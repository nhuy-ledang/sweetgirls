import { AfterViewInit, Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { CookieVar } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { InTicketsRepository } from '../../shared/services';
import { ImpTicketFormComponent } from './form/form.component';
import { DlgProductComponent } from '../../dialogs/product/product.component';
import { STO_IN_TYPES, STO_TICKET_STATUSES } from '../../../../app.constants';

@Component({
  selector: 'ngx-sto-imp-tickets',
  templateUrl: './tickets.component.html',
})
export class ImpTicketsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ImpTicketFormComponent) form: ImpTicketFormComponent;
  repository: InTicketsRepository;
  columnList = [
    {id: 'id', name: '#', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày giờ', checkbox: true, disabled: false},
    {id: 'idx', name: 'Mã phiếu', checkbox: true, disabled: false},
    {id: 'status', name: 'Trạng thái xử lý', checkbox: true, disabled: false},
    {id: 'in_type', name: 'Loại', checkbox: true, disabled: false},
    {id: 'from', name: 'Nơi xuất đến', checkbox: true, disabled: false},
    {id: 'stock', name: 'Kho nhập', checkbox: true, disabled: false},
    {id: 'staff', name: 'Người giao', checkbox: true, disabled: false},
    {id: 'owner', name: 'Người lập', checkbox: true, disabled: false},
    {id: 'storekeeper', name: 'Thủ kho', checkbox: true, disabled: false},
    {id: 'accountant', name: 'Kế toán trưởng', checkbox: true, disabled: false},
    {id: 'products', name: 'Danh sách hàng hóa', checkbox: true, disabled: false},
    {id: 'quantity', name: 'Tổng SL', checkbox: true, disabled: false},
    {id: 'total', name: 'Tổng tiền', checkbox: true, disabled: false},
    {id: 'note', name: 'Ghi chú', checkbox: true, disabled: false},
    {id: 'att_files', name: 'Chứng từ đi kèm', checkbox: true, disabled: false},
    {id: 'cert_files', name: 'Chứng từ ký duyệt', checkbox: true, disabled: false},
  ];

  stoTicketStatusList: any[] = STO_TICKET_STATUSES;
  stoInTypeStatusList: any[] = STO_IN_TYPES;

  constructor(router: Router, security: Security, state: GlobalState, repository: InTicketsRepository, protected _cookie: CookieVar) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
    this.columnInt(this._cookie, 'sto_imp_tickets');
    this.data.data = {q: '', embed: 'request,usr'};
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

  edit(item: any): void {
    console.log(item);
    this.form.show(item);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  changeStatus(item: any, status: 'rejected'|'completed'): void {
    let msg = '';
    if (status === 'rejected') {
      msg = 'Bạn có muốn từ chối phiếu này?';
    } else {
      msg = 'Bạn có muốn duyệt phiếu này?';
    }
    this.confirm.show({title: 'Xác nhận', message: msg, type: 'alert', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'updateStatus', info: item, data: {status: status}}});
  }

  @ViewChild(DlgProductComponent) dlgProduct: DlgProductComponent;
  showProducts(item: any): void {
    console.log(item);
    this.dlgProduct.show(item.request || item);
  }

  @ViewChild('fileUpload') protected _fileUpload: ElementRef;
  protected _fileItem: {item: any, file_type: 'att'|'cert', type: 'files'};

  upload(item, file_type: 'att'|'cert'): void {
    this._fileItem = {item: item, file_type: file_type, type: 'files'};
    this._fileUpload.nativeElement.value = '';
    this._fileUpload.nativeElement.click();
  }

  onFiles(): void {
    const files: File[] = this._fileUpload.nativeElement.files;
    if (files.length) {
      const names: string[] = [];
      _.forEach(files, (file: File) => names.push(file.name));
      this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn upload file <b>' + names.join(', ') + '</b>?', type: 'alert', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'upload', info: this._fileItem, data: files}});
    }
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    } else if (data.type === 'updateStatus') {
      this.repository.updateStatus(data.info, data.data, true).then((res) => {
        this.onFormSuccess(_.extend({edited: true}, res.data));
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    } else if (data.type === 'upload') {
      const item: any = data.info.item;
      const file_type: any = data.info.file_type;
      const type: 'files' = data.info.type;
      const files: File[] = data.data;
      this.repository.fileUploads(item, this.utilityHelper.toFormData({files: files, type: file_type}), true).then((res) => {
        this.onFormSuccess(_.extend({edited: true}, res.data));
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    }
  }
}
