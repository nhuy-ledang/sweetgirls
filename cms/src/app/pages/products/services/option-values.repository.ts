import { Injectable } from '@angular/core';
import { environment } from '../../../../environments/environment';
import { Api, Http } from '../../../@core/services';

@Injectable()
export class OptionValuesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pd_option_values`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    OptionValuesRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (OptionValuesRepository.allData) {
        resolve(OptionValuesRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/pd_option_values_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          OptionValuesRepository.allData = res;
          resolve(OptionValuesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
