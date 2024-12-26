import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class StocksRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sto_stocks`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    StocksRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (StocksRepository.allData) {
        resolve(StocksRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          StocksRepository.allData = res;
          resolve(StocksRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
