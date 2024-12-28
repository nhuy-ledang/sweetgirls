import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { CookieVar } from '../../../@core/services';
import { AppList } from '../../../app.base';
import { DlgStaffSelectComponent } from '../../shared/modals';
import { OrderProductsRepository, OrdersRepository } from '../shared/services';
import { DlgNotifyComponent } from '../shared/modals';
import { OrderFrmOrderStatusComponent } from './frm-order-status/frm-order-status.component';
import { OrderFrmProductComponent } from './frm-product/frm-product.component';
import { FrmInvoicedComponent } from './frm-invoiced/frm-invoiced.component';

@Component({
  selector: 'ngx-ord-orders',
  templateUrl: './orders.component.html',
})
export class OrdersComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: OrdersRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(OrderFrmProductComponent) frmProduct: OrderFrmProductComponent;
  @ViewChild(OrderFrmOrderStatusComponent) frmOrderStatus: OrderFrmOrderStatusComponent;
  @ViewChild(FrmInvoicedComponent) frmInvoiced: FrmInvoicedComponent;
  @ViewChild(DlgStaffSelectComponent) dlgStaffSelect: DlgStaffSelectComponent;
  @ViewChild(DlgNotifyComponent) dlgNotify: DlgNotifyComponent;
  columnList = [
    {id: 'idx', name: 'Mã hóa đơn', checkbox: true, disabled: false},
    {id: 'sub_total', name: 'Tạm tính', checkbox: true, disabled: false},
    {id: 'discount_total', name: 'Giảm giá', checkbox: true, disabled: false},
    {id: 'shipping_fee', name: 'Phí vận chuyển', checkbox: true, disabled: false},
    {id: 'total', name: 'Tổng tiền', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Thời gian', checkbox: true, disabled: false},
    {id: 'customer', name: 'Khách hàng', checkbox: true, disabled: false},
    {id: 'vat', name: 'Xuất VAT', checkbox: true, disabled: false},
    {id: 'wheel', name: 'VQMM', checkbox: true, disabled: false},
    {id: 'tags', name: 'Tags', checkbox: true, disabled: false},
    // {id: 'supervisor', name: 'Phụ trách', checkbox: false, disabled: false},
    {id: 'payment_status', name: 'Tình trạng thanh toán', checkbox: true, disabled: false},
    {id: 'shipping_status', name: 'Tình trạng vận chuyển', checkbox: true, disabled: false},
    {id: 'order_status', name: 'Tình trạng đơn hàng', checkbox: true, disabled: false},
    {id: 'payment_at', name: 'Ngày thanh toán', checkbox: true, disabled: false},
    {id: 'payment_method', name: 'Phương thức thanh toán', checkbox: true, disabled: false},
    {id: 'affiliate', name: 'Affiliate', checkbox: true, disabled: false},
  ];
  paymentStatusList: any[] = [
    {id: this.CONST.PAYMENT_SS_PENDING, name: 'Chờ thanh toán'},
    {id: this.CONST.PAYMENT_SS_INPROGRESS, name: 'Đang thanh toán'},
    {id: this.CONST.PAYMENT_SS_PAID, name: 'Đã thanh toán'},
    // {id: this.CONST.PAYMENT_SS_FAILED, name: 'TT thất bại'},
    // {id: this.CONST.PAYMENT_SS_UNKNOWN, name: 'Không xác định'},
    // {id: this.CONST.PAYMENT_SS_REFUNDED, name: 'Hoàn lại'},
    {id: this.CONST.PAYMENT_SS_CANCELED, name: 'Hủy thanh toán'},
  ];
  shippingStatusList: any[] = [
    {id: this.CONST.SHIPPING_SS_CREATE_ORDER, name: 'Đã tạo đơn'},
    {id: this.CONST.SHIPPING_SS_DELIVERING, name: 'Đang vận chuyển'},
    {id: this.CONST.SHIPPING_SS_DELIVERED, name: 'Đã giao hàng'},
    {id: this.CONST.SHIPPING_SS_RETURN, name: 'Trả hàng'},
  ];
  orderStatusList: any[] = [
    {id: this.CONST.ORDER_SS_PENDING, name: 'Chờ xác nhận'},
    {id: this.CONST.ORDER_SS_PROCESSING, name: 'Đang xử lý'},
    {id: this.CONST.ORDER_SS_SHIPPING, name: 'Đang giao hàng'},
    {id: this.CONST.ORDER_SS_COMPLETED, name: 'Hoàn tất'},
    {id: this.CONST.ORDER_SS_CANCELED, name: 'Hủy đơn'},
    // {id: this.CONST.ORDER_SS_RETURNING, name: 'Đang trả hàng'},
    // {id: this.CONST.ORDER_SS_RETURNED, name: 'Đã trả hàng'},
  ];
  paymentCodeList: any[] = [
    // {id: this.CONST.PAYMENT_MT_CASH, name: 'Tiền mặt'},
    {id: this.CONST.PAYMENT_MT_BANK_TRANSFER, name: 'Chuyển khoản ngân hàng'},
    {id: this.CONST.PAYMENT_MT_DOMESTIC, name: 'Domestic ATM / Internet Banking card'},
    {id: this.CONST.PAYMENT_MT_FOREIGN, name: 'Visa, Master, JCB international card'},
    {id: this.CONST.PAYMENT_MT_COD, name: 'COD'},
    // {id: this.CONST.PAYMENT_MT_DIRECT, name: 'direct'},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: OrdersRepository, private _cookie: CookieVar, private _route: ActivatedRoute, private _orderProduct: OrderProductsRepository) {
    super(router, security, state, repository);
    this.data.pageSize = 50;
  }

  private getStats(): void {
    /*this.repository.getStats(this.years.join(',')).then((res) => {
        this.stats = _.extend(this.stats, res.data);
      }, (errors: any) => {
        console.log(errors);
      },
    );*/
  }

  ngOnInit(): void {
    this.data.data = {q: '', invoice_no: '', payment_status: '', order_status: '', payment_code: '', is_invoice: '', embed: 'user,order_products'};
    this.data.data = {q: '', invoice_no: '', payment_status: '', order_status: '', payment_code: '', is_invoice: '', embed: 'user,order_products'};
    const q = this._route.snapshot.queryParams['q'];
    if (q) this.data.data.q = q;
    this.columnInt(this._cookie, 'orders');
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    this.bsSelectMode('month');
    this.bsApply();
    setTimeout(() => this.getData(() => {
      if (this.data.itemSelected) {
        this.data.itemSelected = _.find(this.data.items, {id: this.data.itemSelected.id});
        console.log(this.data.itemSelected);
      }
    }), 200);
    setTimeout(() => this.getStats(), 1000);
  }

  create(): void {
    this.frmProduct.show();
  }

  /*edit(item: any) {
    this.form.show(item);
  }*/

  detail(item: any): void {
    this.storageHelper.set('order_info_' + item.id, item);
    this._router.navigate(['/pages/ord/orders', item.id]);
  }

  remove(item: any): void {
    console.log(item);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa đơn hàng này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  toggleView(): void {
    this.data.itemSelected = null;
  }

  select(item): void {
    console.log(item);
    this.onSelectTab(null, 'info');
    this.data.itemSelected = item;
  }

  onFormProductSuccess(res: any): void {
    this.getData();
  }

  changeOrderStatus(item, order_status: string): void {
    console.log(item);
    this.frmOrderStatus.show(item, order_status);
  }

  changePaymentStatus(item: any, payment_status: 'paid'|'canceled'): void {
    console.log(item, payment_status);
    let message = 'Bạn có chắc chắn đơn hàng này đã thanh toán?';
    if (payment_status === 'canceled') {
      message = 'Bạn có chắc chắn hủy đơn hàng này?';
    }
    this.confirm.show({title: 'Xác nhận', message: message, type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'changePaymentStatus', info: item, data: {payment_status: payment_status}}});
  }

  changePaymentsStatus(): void {
    if (!this.data.selectList.length) return;
    const ids: any[] = [];
    for (let i = 0; i < this.data.selectList.length; i++) ids.push(this.data.selectList[i].id);
    const msg = ids.join(', ');
    this.confirm.show({title: 'Xác nhận', message: `Bạn có chắc chắn các đơn hàng #(${msg}) đã thanh toán?`, type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'changePaymentsStatus', data: ids}});
  }

  onOrderStatusSuccess(res: any): void {
    console.log(res);
    const item: any = res.info;
    const newParams = _.cloneDeep(res.params);
    newParams.tags = '';
    if (_.isArray(res.params.tags)) newParams.tags = res.params.tags.join(',');
    this.repository.changeOrderStatus(item, newParams, true).then((res) => {
      console.log(res.data);
      item.order_status = res.data.order_status;
      item.order_status_name = res.data.order_status_name;
      item.reason = res.data.reason;
      const itemSelected = _.find(this.data.items, {id: res.data.id});
      if (itemSelected) {
        itemSelected.order_status = res.data.order_status;
        itemSelected.order_status_name = res.data.order_status_name;
        itemSelected.tags = res.data.tags;
      }
    }, (res: any) => {
      this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
    });
  }

  createShipping(item: any): void {
    this.repository.createShipping(item, true).then((res) => {
      console.log(res.data);
      item.order_status = res.data.order_status;
      item.order_status_name = res.data.order_status_name;
      const itemSelected = _.find(this.data.items, {id: res.data.id});
      if (itemSelected) {
        itemSelected.order_status = res.data.order_status;
        itemSelected.order_status_name = res.data.order_status_name;
        // this.data.itemSelected = itemSelected;
      }
    }, (res: any) => {
      this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
    });
  }

  createShippingOrders(): void {
    // console.log(this.data.selectList);
    if (!this.data.selectList.length) return;
    const ids: any[] = [];
    for (let i = 0; i < this.data.selectList.length; i++) ids.push(this.data.selectList[i].id);
    const msg = ids.join(', ');
    this.confirm.show({title: 'Xác nhận', message: `Bạn có chắc chắn muốn tạo đơn vận chuyển cho các đơn hàng #(${msg}) này?`, type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'createShippingOrders', data: ids}});
  }

  createStoRequests(): void {
    if (!this.data.selectList.length) return;
    const ids: any[] = [];
    for (let i = 0; i < this.data.selectList.length; i++) ids.push(this.data.selectList[i].id);
    const msg = ids.join(', ');
    this.confirm.show({title: 'Xác nhận', message: `Bạn có chắc chắn muốn tạo yêu cầu xuất kho cho các đơn hàng #(${msg}) này?`, type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'createStoRequests', data: ids}});
  }

  changeShippingStatus(item: any, shipping_status: 'create_order'|'delivering'|'delivered'|'return'): void {
    console.log(item, shipping_status);
    if (item.shipping_status === shipping_status) return;
    const msg = {
      'create_order': 'đã tạo đơn',
      'delivering': 'đang vận chuyển',
      'delivered': 'đã giao hàng',
      'return': 'đã trả hàng',
    };
    const message = `Bạn có chắc chắn đơn hàng này ${msg[shipping_status]}?`;
    this.confirm.show({title: 'Xác nhận', message: message, type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'changeShippingStatus', info: item, data: {shipping_status: shipping_status}}});
  }

  protected temps: any = {itemSelected: null};

  changeSupervisor(item): void {
    console.log(item);
    this.temps.itemSelected = item;
    this.dlgStaffSelect.show(item);
  }

  changeInvoiced(item): void {
    console.log(item);
    this.temps.itemSelected = item;
    this.frmInvoiced.show(item);
  }

  onFrmInvoicedSuccess(res: any): void {
    console.log(res);
    console.log(res, this.temps.itemSelected);
    this.temps.itemSelected.invoiced = res.invoiced;
    // this.getData(null, false);
  }

  onSelectStaffs(data: {items: any[], type?: string}): void {
    if (data.items.length > 0) {
      console.log(data.items[0]);
      this.repository.supervisor(this.temps.itemSelected, {usr_id: data.items[0].id}).then((res) => {
        console.log(res.data.usr);
        this.temps.itemSelected.usr_id = res.data.usr_id;
        this.temps.itemSelected.usr = res.data.usr;
      }, (res) => {
        console.log(res);
        this._state.notifyDataChanged('modal.alert', _.extend({title: 'Cảnh báo!', type: 'danger'}, res));
      });
    }
  }

  reselect(): void {
    if (this.data.itemSelected) {
      const itemSelected = _.find(this.data.items, {id: this.data.itemSelected.id});
      this.data.itemSelected = null;
      if (itemSelected) this.select(itemSelected);
    }
  }

  exportExcel(): void {
    const href = this.repository.exportExcel(this.data.data, this.data.sort, this.data.order);
    // console.log(href);
    location.href = href;
  }

  exportLoading: boolean = false;

  exportExcelDetail(): void {
    this.exportLoading = true;
    const href = this.repository.exportExcelDetail(this.data.data, this.data.sort, this.data.order);
    const popupWindow = window.open(href, '_blank', 'width=300,height=200');
    if (popupWindow) {
      popupWindow.addEventListener('unload', () => {
        this.exportLoading = false;
      });
    }
    setTimeout(() => this.exportLoading = false, 10000);
  }

  onConfirm(data: any) {
    if (data.type === 'remove') {
      this.removeItem(data.info, null, 'remove_silent');
    } else if (data.type === 'changePaymentStatus') {
      this.repository.changePaymentStatus(data.info, {payment_status: data.data.payment_status}, true).then((res) => {
        console.log(res.data);
        this.submitted = false;
        // this.getData(() => this.reselect());
        const itemSelected = _.find(this.data.items, {id: res.data.id});
        if (itemSelected) {
          itemSelected.payment_status = res.data.payment_status;
          itemSelected.payment_status_name = res.data.payment_status_name;
          itemSelected.order_status = res.data.order_status;
          itemSelected.order_status_name = res.data.order_status_name;
        }
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    } else if (data.type === 'changePaymentsStatus') {
      const ids = data.data;
      console.log(ids);
      this.repository.changePaymentsStatus(ids, true).then((res) => {
        console.log(res.data);
        this.submitted = false;
        this.onFilter();
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    } else if (data.type === 'createShippingOrders') {
      const ids = data.data;
      console.log(ids);
      this.repository.createShippingOrders(ids, true).then((res) => {
        console.log(res.data, res.errorModels, res.exceptionArr);
        if (res.exceptionArr.length) {
          const messages = res.exceptionArr.map(item => `${item.idx}: ${item.error}`);
          messages.push('<b>Danh sách đơn lỗi:</b> <br> ' + res.exceptionArr.map(item => `${item.idx}`).join(', '));
          this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', message: messages});
        }
        this.submitted = false;
        this.onFilter();
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    } else if (data.type === 'createStoRequests') {
      const ids = data.data;
      console.log(ids);
      this.repository.createStoRequests(ids, true).then((res) => {
        console.log(res.data);
        this.submitted = false;
        this.onFilter();
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    } else if (data.type === 'changeShippingStatus') {
      this.repository.changeShippingStatus(data.info, {shipping_status: data.data.shipping_status}, true).then((res) => {
        console.log(res.data);
        this.submitted = false;
        // this.getData(() => this.reselect());
        const itemSelected = _.find(this.data.items, {id: res.data.id});
        if (itemSelected) {
          itemSelected.shipping_status = res.data.shipping_status;
          itemSelected.shipping_status_name = res.data.shipping_status_name;
          itemSelected.order_status = res.data.order_status;
          itemSelected.order_status_name = res.data.order_status_name;
        }
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    }
  }

  private stringifyOptions(options): string {
    const parts = [];
    _.forEach(options, (value, key) => {
      parts.push(key + '=' + value);
    });
    return parts.join(',');
  }

  print(item): void {
    console.log(item);
    const options = this.stringifyOptions({width: 992, height: 800, left: window.screenX + ((window.outerWidth - 992) / 2), top: window.screenY + ((window.outerHeight - 800) / 2.5)});
    console.log(options);
    let popup;
    // window.open(this.repository.getPrintLink(item.id), '_blank');
    popup = window.open(this.repository.getPrintLink(item.id), 'Bản in', options);
    if (popup && popup.focus) popup.focus();
  }

  sendMail(item: any): void {
    console.log(item);
    this.dlgNotify.show(item);
  }

  onSendMailSuccess(res: any): void {
    console.log(res);
    this.data.itemSelected.status = res.status;
    this.data.itemSelected.status_name = res.status_name;
    this._state.notifyDataChanged('modal.success', {title: 'Thành công!', message: 'Đã gởi email thành công!'});
  }

  tabs: any = {info: true, histories: false, shipping_histories: false};

  onSelectTab($event: any, tabActive: string): void {
    _.each(this.tabs, (val, key) => this.tabs[key] = false);
    this.tabs[tabActive] = true;
  }

  daterange: {mode: 'day'|'week'|'month'|'year'|'customRange'|'all', label: string, start?: any, end?: any, start_date?: string, end_date?: string} = {mode: 'all', label: 'Tất cả'};
  bsData: {mode: 'day'|'week'|'month'|'year'|'customRange'|'all', label: string, value: Date, currDate: Date, start: Date, end: Date, start_date: string, end_date: string} = {mode: 'all', label: 'Tất cả', value: new Date(), currDate: new Date(), start: new Date(), end: new Date(), start_date: '', end_date: ''};
  bsInlineRangeValue: Date[] = [new Date(), new Date()];

  private bsUpdate(): void {
    let start: Date;
    let end: Date;
    if (this.bsData.mode === 'customRange') {
      start = this.bsInlineRangeValue[0];
      end = this.bsInlineRangeValue[1];
      this.bsData.label = start.format('d') + ' - ' + end.format('d mmm, yyyy');
    } else if (this.bsData.mode === 'day') {
      start = this.bsData.currDate;
      end = start;
      this.bsData.label = start.format('d mmm, yyyy');
    } else if (this.bsData.mode === 'week') {
      start = this.bsData.currDate.getFirstDayInWeek();
      end = this.bsData.currDate.getLastDayInWeek();
      this.bsData.label = start.format('d') + ' - ' + end.format('d mmm, yyyy');
    } else if (this.bsData.mode === 'month') {
      start = this.bsData.currDate.getFirstDayInMonth();
      end = this.bsData.currDate.getLastDayInMonth();
      this.bsData.label = start.format('mmm, yyyy');
    } else if (this.bsData.mode === 'year') {
      start = new Date(String(this.bsData.currDate.getFullYear()) + '-01-01');
      end = new Date(String(this.bsData.currDate.getFullYear()) + '-12-31');
      this.bsData.label = start.format('yyyy');
    }
    this.bsData.start = start;
    this.bsData.end = end;
    this.bsData.start_date = start ? start.format('yyyy-mm-dd') : '';
    this.bsData.end_date = end ? end.format('yyyy-mm-dd') : '';
  }

  bsSelectMode(mode: 'day'|'week'|'month'|'year'|'customRange'|'all'): void {
    this.bsData.mode = mode;
    this.bsUpdate();
  }

  onBsValueChange($event: Date): void {
    this.bsData.currDate = $event;
    this.bsUpdate();
  }

  onBsRangeValueChange($event: Date[]): void {
    this.bsInlineRangeValue = $event;
    this.bsUpdate();
  }

  bsApply(): void {
    const daterange: any = {mode: this.bsData.mode, label: this.bsData.label, start: new Date(this.bsData.start), end: new Date(this.bsData.end), start_date: this.bsData.start_date, end_date: this.bsData.end_date};
    this.daterange = daterange;
    this.bsData.value = this.bsData.currDate;

    this.data.data = {...this.data.data, mode: daterange.mode, start_date: daterange.start_date, end_date: daterange.end_date};
    if (this.bsData.mode === 'all') {
      this.data.data.start_date = '';
      this.data.data.end_date = '';
      this.daterange.label = 'Tất cả';
    }
    this.getData();
  }
}
