import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { ConfirmComponent } from '../../../../../@theme/modals';
import { CookieVar } from '../../../../../@core/services';
import { AppList } from '../../../../../app.base';
import { InRequestsRepository, OutRequestsRepository, RequestsRepository } from '../../../shared/services';
import { ExpRequestFormComponent } from '../../../export/requests/form/form.component';
import { ImpRequestFormComponent } from '../../../import/requests/form/form.component';
import { DlgProductComponent } from '../../../dialogs/product/product.component';
import { STO_REQUEST_STATUSES, STO_SHIPPING_STATUSES } from '../../../../../app.constants';

@Component({
  selector: 'ngx-sto-dep-orders-imports',
  templateUrl: './imports.component.html',
})
export class DepOrdersImportsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ExpRequestFormComponent) formExp: ExpRequestFormComponent;
  @ViewChild(ImpRequestFormComponent) formImp: ImpRequestFormComponent;
  columnList = [
    {id: 'id', name: '#', checkbox: true, disabled: false},
    {id: 'invoice_id', name: 'Thuộc hóa đơn/đợt', checkbox: true, disabled: false},
    {id: 'content', name: 'Nội dung yêu cầu', checkbox: true, disabled: false},
    {id: 'products', name: 'Danh sách hàng hóa', checkbox: true, disabled: false},
    {id: 'quantity', name: 'Tổng SL', checkbox: true, disabled: false},
    {id: 'total', name: 'Tổng giá trị', checkbox: true, disabled: false},
    {id: 'type', name: 'Lệnh/yêu cầu', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày giờ yêu cầu', checkbox: true, disabled: false},
    {id: 'deadline_at', name: 'Ngày hạn định', checkbox: true, disabled: false},
    {id: 'estimate_at', name: 'Ngày dự kiến', checkbox: true, disabled: false},
    {id: 'reality_at', name: 'Ngày giờ thực tế', checkbox: true, disabled: false},
    {id: 'time_gap', name: 'Chênh lệch', checkbox: true, disabled: false},
    {id: 'att_files', name: 'Chứng từ đi kèm', checkbox: true, disabled: false},
    {id: 'idx', name: 'Mã lệnh', checkbox: true, disabled: false},
    {id: 'handle_type', name: 'Loại xử lý', checkbox: true, disabled: false},
    {id: 'status', name: 'Trạng thái xử lý', checkbox: true, disabled: false},
    {id: 'shipping_status', name: 'Trạng thái triển khai', checkbox: true, disabled: false},
    {id: 'ticket_idx', name: 'Mã phiếu xuất/nhập', checkbox: true, disabled: false},
    {id: 'customer_id', name: 'Bên nhận/bên giao', checkbox: true, disabled: false},
    {id: 'customer_usr_id', name: 'Liên hệ chính', checkbox: true, disabled: false},
    {id: 'customer_usr_phone_number', name: 'Số điện thoại', checkbox: true, disabled: false},
    {id: 'customer_address', name: 'Địa chỉ', checkbox: true, disabled: false},
    {id: 'customer_district', name: 'Quận/Huyện', checkbox: true, disabled: false},
    {id: 'customer_province', name: 'Tỉnh/Thành', checkbox: true, disabled: false},
    {id: 'carrier_id', name: 'Đơn vị giao nhận', checkbox: true, disabled: false},
    {id: 'carrier_supervisor_id', name: 'Phụ trách đơn giao nhận', checkbox: true, disabled: false},
    {id: 'carrier_shipper_id', name: 'Người giao nhận (tài xế)', checkbox: true, disabled: false},
    {id: 'carrier_shipper_phone_number', name: 'Số điện thoại tài xế', checkbox: true, disabled: false},
    {id: 'owner_id', name: 'Người yêu cầu', checkbox: true, disabled: false},
    {id: 'department_idx', name: 'Mã Phòng ban/ Bộ phận yêu cầu', checkbox: true, disabled: false},
    {id: 'stock_id', name: 'Kho được yêu cầu', checkbox: true, disabled: false},
    {id: 'storekeeper', name: 'Thủ kho', checkbox: true, disabled: false},
    {id: 'manager', name: 'Quản kho', checkbox: true, disabled: false},
    {id: 'note', name: 'Ghi chú', checkbox: true, disabled: false},
    {id: '', name: '', checkbox: true, disabled: false},
  ];
  statusList = [
    {id: 'pending', name: 'Chờ xử lý', color: '#677788'},
    {id: 'adjust', name: 'Cần điều chỉnh', color: '#FF802C'},
    {id: 'in_process', name: 'Đang xử lý', color: '#3986FF'},
    {id: 'completed', name: 'Xử lý hoàn tất', color: '#32D593'},
    {id: 'rejected', name: 'Từ chối', color: '#FF4A65'},
    {id: 'stored', name: 'Lưu trữ', color: '#6906A2'},
    // {id: 'canceled', name: 'Đã hủy', color: '#172228'},
  ];

  typeList = [
    {id: '', name: 'Tất cả', color: ''},
    {id: 'sale', name: 'Bán ra', color: '#3986FF'},
    {id: 'destroy', name: 'Xuất hủy', color: '#172228'},
    {id: 'return', name: 'Hoàn đổi', color: '#32D593'},
    {id: 'transfer', name: 'Chuyển kho', color: '#FF802C'},
    {id: 'donate', name: 'Xuất tặng', color: '#FF4A65'},
    {id: 'use', name: 'Xuất sử dụng', color: '#29C6E3'},
  ];

  paymentStatusList: any[] = [{id: 'in_process', name: 'Thanh toán một phần'}, {id: 'paid', name: 'Đã tất toán'}];
  stoRequestStatusList: any[] = STO_REQUEST_STATUSES;
  stoShippingStatusList: any[] = STO_SHIPPING_STATUSES;

  currentType: string = '';
  currentStatus: string = '';

  constructor(router: Router, security: Security, state: GlobalState, repository: InRequestsRepository, protected _cookie: CookieVar) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
    this.columnInt(this._cookie, 'sto_dep_orders_import');
    this.data.data = {q: '', invoice_no: '', status: '', shipping: 1, embed: 'products,stock,ticket,manager,storekeeper'};
    this.filters = {
      'ord__invoices.payment_status': {operator: '=', value: ''},
    };
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  createTicket(item: any): void {
    // this.frmTicket.show(item);
  }

  onConfirm(data: any): void {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    } else if (data.type === 'updateStatus') {
      // let repository;
      // if (data.data.type === 'in') {
      //   repository = this._inRequests;
      // } else {
      //   repository = this._outRequests;
      // }
      // repository.updateStatus(data.info, data.data, true).then((res) => {
      //   this.onFormSuccess(_.extend({edited: true}, res.data));
      // }, (res: any) => {
      //   this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      // });
    } else if (data.type === 'upload') {
      // const item: any = data.info.item;
      // const file_type: any = data.info.file_type;
      // const type: 'files' = data.info.type;
      // const files: File[] = data.data;
      // this._outTickets.fileUploads(item, this.utilityHelper.toFormData({files: files, type: file_type}), true).then((res) => {
      //   this.onFormSuccess(_.extend({edited: true}, res.data));
      // }, (res: any) => {
      //   this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      // });
    }
  }

  onTicketSuccess(res: any): void {
    console.log(res);
    this._state.notifyDataChanged('modal.success', {title: 'Thành công!', message: 'Đã tạo yêu cầu thành công!'});
  }

  createExpRequest(): void {
    this.formExp.show();
  }

  createImpRequest(): void {
    this.formImp.show();
  }

  onExpRequestSuccess(res: any): void {
    console.log(res);
    this.getData();
  }

  onImpRequestSuccess(res: any): void {
    console.log(res);
    this.getData();
  }

  changeStatus(item: any, status: any, type: 'in'|'out'): void {
    let msg = '';
    /*if (status === 'in_process') {
      msg = 'Bạn có muốn duyệt phiếu này?';
    } else if (status === 'rejected') {
      msg = 'Bạn có muốn duyệt phiếu này?';
    } else if (status === 'stored') {
      msg = 'Bạn có muốn duyệt phiếu này?';
    }*/
    msg = 'Bạn có muốn chuyển trạng thái <b>' + this.statusList.find(item => item.id === status).name + '</b> yêu cầu này?';
    this.confirm.show({title: 'Xác nhận', message: msg, type: 'alert', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'updateStatus', info: item, data: {status: status, type: type}}});
  }

  filterType(type: any): void {
    this.currentType = type;
    this.filters.out_type = {operator: '=', value: this.currentType};
    this.getData();
  }

  filterStatus(status: any): void {
    this.currentStatus = this.currentStatus !== status ? status : '';
    this.filters.status = {operator: '=', value: this.currentStatus};
    this.getData();
  }

  @ViewChild(DlgProductComponent) dlgProduct: DlgProductComponent;
  showProducts(item: any): void {
    console.log(item);
    this.dlgProduct.show(item);
  }

  getQuantityProduct(item: any): void {
    return item.reduce((total, product) => total + product.quantity, 0);
  }
}
