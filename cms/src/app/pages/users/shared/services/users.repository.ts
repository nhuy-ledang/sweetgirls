import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class UsersRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/users`;
  }
    /**
   * Lấy tất cả người dùng
   * @param {boolean} loading
   * @returns {Promise<any>}
   */
  getAllUsers(loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }
  banned(id: number, params: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/banned` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }

  getDevices(id: number, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/devices` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  removeDevices(id: number, device_id: any, loading?: boolean): Promise<any> {
    return this._http.delete(`${this.url}/${id}/devices/${device_id}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  search(params: {paging?: number, page?: number, pageSize?: number, sort?: string, order?: string, data?: any}, loading?: boolean): Promise<any> {
    return this._http.get(`${environment.API_URL}/user_search` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  exportExcel(data: any, sort: string, order: string): string {
    return this._http.getLink(`${this.url}_exports`, {data: JSON.stringify(data), sort: sort, order: order});
  }
}
