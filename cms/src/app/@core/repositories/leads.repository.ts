import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class LeadsRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/leads`;
  }
}
