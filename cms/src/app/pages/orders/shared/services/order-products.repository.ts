import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class OrderProductsRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/ord_orders_products`;
  }

  exportExcel(data: any, sort: string, order: string): string {
    return this._http.getLink(`${this.url}_exports`, {data: JSON.stringify(data), sort: sort, order: order});
  }

  exportExcelDetail(data: any, sort: string, order: string): string {
    return this._http.getLink(`${this.url}_export_details`, {data: JSON.stringify(data), sort: sort, order: order});
  }
}
