import { Injectable } from '@angular/core';
import { Http } from './http';

@Injectable()
export class Api {
  protected url: string;

  /**
   * Constructor
   * @param {Http} _http
   */
  constructor(protected _http: Http) {
  }

  /**
   * Clear static data
   */
  protected beforeAction(): void {
  }

  protected getExpires(day?: number, hour?: number): Date {
    day = day || 0;
    hour = hour || 0;
    hour = day * 24 + hour;
    hour = hour || 24;

    return new Date((new Date()).getTime() + hour * 3600 * 1000);
  }

  /**
   * Get Json options
   * @param data
   * @param opts
   */
  protected getJsonOptions(data?: any, opts?: any): any {
    opts = _.extend({contentType: 'application/json', withCredentials: false}, opts);
    if (data) {
      const params: any = {};
      _.forEach(data, (v, k) => {
        if (typeof v !== 'object') params[k] = v;
      });
      if (!_.isEmpty(data.data)) {
        // params['data'] = encodeURIComponent(JSON.stringify(data.data));
        params['data'] = JSON.stringify(data.data);
      }
      opts['params'] = params;
    }
    return opts;
  }

  get(params?: {paging?: number, page?: number, pageSize?: number, sort?: string, order?: string, data?: any}, loading?: boolean, url?: string): Promise<any> {
    return this._http.get(`${url ? url : this.url}` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  find(id: number, data?: any, loading?: boolean, url?: string): Promise<any> {
    const params: any = {};
    if (data) params.data = data;
    return this._http.get(`${url ? url : this.url}/${id}` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  create(data: any, loading?: boolean, url?: string): Promise<any> {
    this.beforeAction();
    return this._http.post(`${url ? url : this.url}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  update(model, data: any, loading?: boolean, url?: string): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    this.beforeAction();
    return this._http.put(`${url ? url : this.url}/${id}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  patch(model, data: any, loading?: boolean, url?: string): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    this.beforeAction();
    return this._http.patch(`${url ? url : this.url}/${id}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  remove(model, loading?: boolean, url?: string): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    this.beforeAction();
    return this._http.delete(`${url ? url : this.url}/${id}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }
}
