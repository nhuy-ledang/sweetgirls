import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Http } from '../../../../@core/services';
import { OutTicketsRepository } from './out-tickets.repository';

@Injectable()
export class ReqTicketsRepository extends OutTicketsRepository {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sto_req_tickets`;
  }
}
