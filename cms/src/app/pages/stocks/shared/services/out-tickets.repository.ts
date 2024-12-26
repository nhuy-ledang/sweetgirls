import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Http } from '../../../../@core/services';
import { TicketsRepository } from './tickets.repository';

@Injectable()
export class OutTicketsRepository extends TicketsRepository {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sto_out_tickets`;
  }
}
