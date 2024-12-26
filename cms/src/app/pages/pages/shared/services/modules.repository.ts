import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class ModulesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pg_modules`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    ModulesRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (ModulesRepository.allData) {
        resolve(ModulesRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/pg_modules_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          ModulesRepository.allData = res;
          resolve(ModulesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateConfigs(model, cf_data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.put(`${this.url}/${id}/configs` + (loading ? '?showSpinner' : ''), {cf_data: cf_data}, this.getJsonOptions());
  }

  uploadThumbnail(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/thumbnail` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  sortOrder(data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_sort_order` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
