import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class OrdersRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/ord_orders`;
  }

  update(id, data: any, loading?: boolean): Promise<any> {
    return this._http.put(`${this.url}/${id}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateAddress(id, data: any, loading?: boolean): Promise<any> {
    return this._http.put(`${this.url}/${id}/address` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  sendMail(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/send-mail` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  getPrintLink(id): string {
    // return this._http.getLink(`${this.url}/${id}/print`);
    return `${this.url}/${id}/print`;
  }

  /**
   * Get Products
   * @param id
   * @param loading
   */
  getProducts(id, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/products` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  getShippingFee(params?: any, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}_get_shipping_fee` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  /**
   * Confirm order status
   * @param model
   * @param data
   * @param loading
   */
  changeOrderStatus(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/order_status` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  changePaymentStatus(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/payment_status` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  changePaymentsStatus(ids: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_change_payments_status` + (loading ? '?showSpinner' : ''), {ids: ids}, this.getJsonOptions());
  }

  changeShippingStatus(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/shipping_status` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
  createShipping(model, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/create_shipping` + (loading ? '?showSpinner' : ''), {}, this.getJsonOptions());
  }

  createShippingOrders(ids: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_create_shipping` + (loading ? '?showSpinner' : ''), {ids: ids}, this.getJsonOptions());
  }

  createStoRequests(ids: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_create_requests` + (loading ? '?showSpinner' : ''), {ids: ids}, this.getJsonOptions());
  }

  /**
   * Invoiced
   * @param model
   * @param data
   * @param loading
   */
  invoiced(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/invoiced` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  /**
   * Change supervisor
   * @param model
   * @param data
   * @param loading
   */
  supervisor(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/supervisor` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  /**
   * Get Stats
   * @param years
   * @param loading
   */
  getStats(years?: string, loading?: boolean): Promise<any> {
    return this._http.get(`${environment.API_URL}/ord_stats` + '?years=' + years + (loading ? '&showSpinner' : ''), this.getJsonOptions({}));
  }

  exportVAT(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/vat` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  exportExcel(data: any, sort: string, order: string): string {
    return this._http.getLink(`${this.url}_exports`, {data: JSON.stringify(data), sort: sort, order: order});
  }

  exportExcelDetail(data: any, sort: string, order: string): string {
    return this._http.getLink(`${this.url}_exports_details`, {data: JSON.stringify(data), sort: sort, order: order});
  }

  exportExcelProduct(data: any, sort: string, order: string): string {
    return this._http.getLink(`${this.url}_products_exports`, {data: JSON.stringify(data), sort: sort, order: order});
  }

  // /**
  //  * Get Report Orders
  //  * @param params
  //  * @param loading
  //  */
  // getReportOrders(params: { paging?: number, page?: number, pageSize?: number, sort?: string, order?: string, data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/orders` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Products
  //  * @param params
  //  * @param loading
  //  */
  // getReportProducts(params: { paging?: number, page?: number, pageSize?: number, sort?: string, order?: string, data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/products` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Customers Group
  //  * @param params
  //  * @param loading
  //  */
  // getReportCustomerGroups(params: { paging?: number, page?: number, pageSize?: number, sort?: string, order?: string, data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/customer_groups` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Customers
  //  * @param params
  //  * @param loading
  //  */
  // getReportCustomers(params: { paging?: number, page?: number, pageSize?: number, sort?: string, order?: string, data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/customers` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Product All
  //  * @param params
  //  * @param loading
  //  */
  // getReportProductAll(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/products_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Product All
  //  * @param params
  //  * @param loading
  //  */
  // getReportStaffs(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/staffs` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Handling Staffs
  //  * @param params
  //  * @param loading
  //  */
  // getReportHandlingStaffs(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/handling_staffs` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Processed Staffs
  //  * @param params
  //  * @param loading
  //  */
  // getReportProcessedStaffs(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/processed_staffs` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Revenues
  //  * @param params
  //  * @param loading
  //  */
  // getReportRevenues(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/revenues` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Report Discounts
  //  * @param params
  //  * @param loading
  //  */
  // getReportDiscounts(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_report/discounts` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Stat Revenue Percent
  //  * @param params
  //  * @param loading
  //  */
  // getStatRevenuePercent(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_stat/revenue_percent` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Stat Payment Methods
  //  * @param params
  //  * @param loading
  //  */
  // getStatPaymentMethods(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_stat/payment_methods` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Stat Revenues
  //  * @param params
  //  * @param loading
  //  */
  // getStatRevenues(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_stat/revenues` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Stat Orders
  //  * @param params
  //  * @param loading
  //  */
  // getStatOrders(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_stat/orders` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Stat Users
  //  * @param params
  //  * @param loading
  //  */
  // getStatUsers(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_stat/users` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }
  //
  // /**
  //  * Get Stat Overview
  //  * @param params
  //  * @param loading
  //  */
  // getStatOverview(params: { data?: any }, loading?: boolean): Promise<any> {
  //   return this._http.get(`${environment.API_URL}/ord_stat/overview` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  // }

  createPos(data: any, loading?: boolean, url?: string): Promise<any> {
    this.beforeAction();
    return this._http.post(`${this.url}_pos` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
