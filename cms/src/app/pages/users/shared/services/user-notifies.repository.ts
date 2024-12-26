import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class UserNotifiesRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/user_notify/notifications`;
  }

  chatOffline(params, loading?: boolean) {
    return this._http.post(`${environment.API_URL}/user_notify/chat_offline` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }
}
