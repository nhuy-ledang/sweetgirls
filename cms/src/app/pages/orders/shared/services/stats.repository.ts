import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class StatsRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/ord_stats`;
  }

  getOverview(params: {data?: any}, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/overview` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  exportExcel(data: any): string {
    return this._http.getLink(`${this.url}_export_by_date`, {data: JSON.stringify(data)});
  }
}
