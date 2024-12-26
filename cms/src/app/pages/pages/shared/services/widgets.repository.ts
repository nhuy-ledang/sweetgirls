import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class WidgetsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pg_widgets`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    WidgetsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (WidgetsRepository.allData) {
        resolve(WidgetsRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          WidgetsRepository.allData = res;
          resolve(WidgetsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  updateConfigs(model, cf_data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.put(`${this.url}/${id}/configs` + (loading ? '?showSpinner' : ''), {cf_data: cf_data}, this.getJsonOptions());
  }

  uploadThumbnail(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/thumbnail` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
