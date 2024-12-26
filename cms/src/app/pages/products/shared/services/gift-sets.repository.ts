import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class GiftSetsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/gift_sets`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    GiftSetsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (GiftSetsRepository.allData) {
        resolve(GiftSetsRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          GiftSetsRepository.allData = res;
          resolve(GiftSetsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
