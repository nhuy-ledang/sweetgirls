import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class UsrsRepository extends Api {
  private static allData: any[] = [];

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/usrs`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    UsrsRepository.allData = [];
  }

  /**
   * Get All
   * @param loading
   * @returns {*}
   */
  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (UsrsRepository.allData.length) {
        resolve(UsrsRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/usrs_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res: any) => {
          UsrsRepository.allData = res;
          resolve(UsrsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  /**
   * Banned
   * @param id
   * @param params
   * @param loading
   */
  banned(id: number, params: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/banned` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }

  /**
   * Update Staff Roles
   *
   * @param id
   * @param data
   * @param loading
   */
  syncRoles(id, data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/roles` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
