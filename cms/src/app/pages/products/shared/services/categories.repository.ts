import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class CategoriesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pd_categories`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    CategoriesRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (CategoriesRepository.allData) {
        resolve(CategoriesRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          CategoriesRepository.allData = res;
          resolve(CategoriesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  createProperties(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/properties` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  createOptions(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/options` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
