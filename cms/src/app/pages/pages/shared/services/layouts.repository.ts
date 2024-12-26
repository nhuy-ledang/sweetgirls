import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class LayoutsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pg_layouts`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    LayoutsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (LayoutsRepository.allData) {
        resolve(LayoutsRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/pg_layouts_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          LayoutsRepository.allData = res;
          resolve(LayoutsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  updateMeta(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/meta` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
