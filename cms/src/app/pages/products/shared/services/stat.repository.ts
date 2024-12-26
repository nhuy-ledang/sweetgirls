import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class StatRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pd_stat`;
  }

  productRank(params?: any, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/products_rank` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  exportExcel(data: any, sort: string, order: string): string {
    return this._http.getLink(`${this.url}_products_rank_exports`, {data: JSON.stringify(data), sort: sort, order: order});
  }
}
