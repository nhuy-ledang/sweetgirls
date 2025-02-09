import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class OrderShippingHistoriesRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/ord_order_shipping_histories`;
  }
}
