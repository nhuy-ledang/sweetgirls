import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class PagesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pg_pages`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    PagesRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (PagesRepository.allData) {
        resolve(PagesRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/pg_pages_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          PagesRepository.allData = res;
          resolve(PagesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  copy(model, data, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/copy` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  copyToLayout(model, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/layout` + (loading ? '?showSpinner' : ''), {}, this.getJsonOptions());
  }

  cloneLayouts(data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_layouts` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  /*getImages(id, params?: any, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/images` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  createImage(id, data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/images` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateImage(id, image_id, data: any, loading?: boolean): Promise<any> {
    return this._http.put(`${this.url}/${id}/images/${image_id}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  removeImage(id, image_id, loading?: boolean): Promise<any> {
    return this._http.delete(`${this.url}/${id}/images/${image_id}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }*/
}
