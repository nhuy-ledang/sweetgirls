import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class PageContentsRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pg_page_contents`;
  }

  copy(model, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/copy` + (loading ? '?showSpinner' : ''), {}, this.getJsonOptions());
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateImage(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/images` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  copyToPattern(model, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/pattern` + (loading ? '?showSpinner' : ''), {}, this.getJsonOptions());
  }

  sortOrder(data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_sort_order` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  cloneModules(data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_modules` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  clonePatterns(data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_patterns` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
