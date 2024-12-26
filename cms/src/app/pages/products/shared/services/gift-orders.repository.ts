import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class GiftOrdersRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/gift_orders`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    GiftOrdersRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (GiftOrdersRepository.allData) {
        resolve(GiftOrdersRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          GiftOrdersRepository.allData = res;
          resolve(GiftOrdersRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
