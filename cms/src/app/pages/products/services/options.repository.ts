import { Injectable } from '@angular/core';
import { environment } from '../../../../environments/environment';
import { Api, Http } from '../../../@core/services';

@Injectable()
export class OptionsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pd_options`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    OptionsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (OptionsRepository.allData) {
        resolve(OptionsRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          OptionsRepository.allData = res;
          resolve(OptionsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  getValues(id, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/values` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }
}
