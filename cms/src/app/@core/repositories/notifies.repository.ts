import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class NotifiesRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/notify/notifications`;
  }

  getAlerts(params, loading?: boolean) {
    return this._http.get(`${environment.API_URL}/notify/notifications_alert` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  markAlerts(params, loading?: boolean) {
    return this._http.post(`${environment.API_URL}/notify/notifications_alert` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }
}
