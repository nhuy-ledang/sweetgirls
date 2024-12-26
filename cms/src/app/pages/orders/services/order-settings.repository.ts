import { Injectable } from '@angular/core';
import { environment } from '../../../../environments/environment';
import { Api, Http } from '../../../@core/services';

@Injectable()
export class OrderSettingsRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/ord_settings`;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
        resolve(res);
      }), (errors) => reject(errors);
    });
  }
}
