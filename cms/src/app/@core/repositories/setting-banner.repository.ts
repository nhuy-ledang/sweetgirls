import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class SettingBannerRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sys_banner`;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      this._http.get(`${environment.API_URL}/sys_banner_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
        resolve(res);
      }), (errors) => reject(errors);
    });
  }

  updateType(data: any, loading?: boolean): Promise<any> {
    this.beforeAction();
    return this._http.post(`${this.url}/type` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
