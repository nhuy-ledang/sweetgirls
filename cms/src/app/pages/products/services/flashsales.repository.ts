import { Injectable } from '@angular/core';
import { environment } from '../../../../environments/environment';
import { Api, Http } from '../../../@core/services';

@Injectable()
export class FlashsalesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pd_flashsales`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    FlashsalesRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (FlashsalesRepository.allData) {
        resolve(FlashsalesRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          FlashsalesRepository.allData = res;
          resolve(FlashsalesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  getValues(params?: any, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}_values` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }
}
