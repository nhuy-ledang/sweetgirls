import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class RequestsRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sto_requests`;
  }

  /**
   * Update Status
   * @param model
   * @param data
   * @param loading
   */
  updateStatus(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/status` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
