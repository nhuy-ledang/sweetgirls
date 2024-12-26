import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class ManufacturersRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pd_manufacturers`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    ManufacturersRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (ManufacturersRepository.allData) {
        resolve(ManufacturersRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/pd_manufacturers_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          ManufacturersRepository.allData = res;
          resolve(ManufacturersRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
