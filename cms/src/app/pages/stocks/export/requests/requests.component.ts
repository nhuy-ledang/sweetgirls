import { AfterViewInit, Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { CookieVar } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { OutRequestsRepository, OutTicketsRepository } from '../../shared/services';
import { ExpRequestFormComponent } from './form/form.component';
import { STO_REQUEST_STATUSES, STO_SHIPPING_STATUSES } from '../../../../app.constants';

@Component({
  selector: 'ngx-sto-exp-requests',
  templateUrl: './requests.component.html',
})
export class ExpRequestsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ExpRequestFormComponent) form: ExpRequestFormComponent;
  repository: OutRequestsRepository;
  columnList = [
    {id: 'id', name: 'ID', checkbox: true, disabled: false},
    {id: 'idx', name: 'Mã yêu cầu nhập kho', checkbox: true, disabled: false},
    {id: 'content', name: 'Nội dung yêu cầu', checkbox: true, disabled: false},
    {id: 'usr_id', name: 'Người yêu cầu', checkbox: true, disabled: false},
    {id: 'out_type', name: 'Loại hàng', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày giờ yêu cầu', checkbox: true, disabled: false},
    {id: 'invoice_id', name: 'Mã phiếu', checkbox: true, disabled: false},
    {id: 'status', name: 'Trạng thái xử lý', checkbox: true, disabled: false},
    {id: 'shipping_status', name: 'TT vận chuyển', checkbox: true, disabled: false},
    {id: 'date', name: 'Hạn xuất', checkbox: true, disabled: false},
    {id: 'date', name: 'Ngày giờ xuất thực tế', checkbox: true, disabled: false},
    {id: 'ticket_idx', name: 'Mã phiếu xuất kho', checkbox: true, disabled: false},
    {id: 'location', name: 'Nơi nhận', checkbox: true, disabled: false},
    {id: 'out_customer_id', name: 'Người nhận', checkbox: true, disabled: false},
    {id: 'products', name: 'Danh sách hàng hóa', checkbox: true, disabled: false},
    {id: 'quantity', name: 'Tổng SL', checkbox: true, disabled: false},
    {id: 'total', name: 'Tổng tiền', checkbox: true, disabled: false},
    {id: 'owner', name: 'Người lập', checkbox: true, disabled: false},
    {id: 'staff', name: 'Tài xế(nếu có)', checkbox: true, disabled: false},
    {id: 'storekeeper', name: 'Thủ kho', checkbox: true, disabled: false},
    {id: 'accountant', name: 'Kế toán', checkbox: true, disabled: false},
    {id: 'att_files', name: 'Chứng từ đi kèm', checkbox: true, disabled: false},
    {id: 'att_files', name: 'Chứng từ ký duyệt', checkbox: true, disabled: false},
  ];

  statusList = [
    // {id: 'pending', name: 'Chờ chấp nhận', color: '#677788'},
    {id: 'adjust', name: 'Cần điều chỉnh', color: '#FF802C'},
    {id: 'in_process', name: 'Chờ xuất kho', color: '#3986FF'},
    // {id: 'completed', name: 'Đã nhập', color: '#32D593'},
    {id: 'rejected', name: 'Từ chối', color: '#FF4A65'},
    {id: 'stored', name: 'Lưu trữ', color: '#6906A2'},
    // {id: 'canceled', name: 'Đã hủy', color: '#172228'},
  ];

  stoRequestStatusList: any[] = STO_REQUEST_STATUSES;
  stoShippingStatusList: any[] = STO_SHIPPING_STATUSES;

  constructor(router: Router, security: Security, state: GlobalState, repository: OutRequestsRepository, protected _outTickets: OutTicketsRepository, protected _cookie: CookieVar) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
    this.columnInt(this._cookie, 'sto_imp_requests');
    this.data.data = {q: '', embed: 'products, stock, ticket, usr, storekeeper'};
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

  changeStatus(item: any, status: 'in_process'|'rejected'|'stored'): void {
    let msg = '';
    /*if (status === 'in_process') {
      msg = 'Bạn có muốn duyệt phiếu này?';
    } else if (status === 'rejected') {
      msg = 'Bạn có muốn duyệt phiếu này?';
    } else if (status === 'stored') {
      msg = 'Bạn có muốn duyệt phiếu này?';
    }*/
    msg = 'Bạn có muốn chuyển trạng thái <b>' + this.statusList.find(item => item.id === status).name + '</b> yêu cầu này?';
    this.confirm.show({title: 'Xác nhận', message: msg, type: 'alert', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'updateStatus', info: item, data: {status: status}}});
  }

  showProducts(item: any): void {
    console.log(item);
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

  onConfirm(data: any): void {
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
      this._outTickets.fileUploads(item, this.utilityHelper.toFormData({files: files, type: file_type}), true).then((res) => {
        this.onFormSuccess(_.extend({edited: true}, res.data));
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    }
  }
}
