import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Http } from '../../../../@core/services';
import { RequestsRepository } from './requests.repository';

@Injectable()
export class OutRequestsRepository extends RequestsRepository {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sto_out_requests`;
  }
}
