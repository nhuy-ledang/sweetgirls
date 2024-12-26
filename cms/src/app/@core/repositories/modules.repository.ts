import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class ModulesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    const urls = `${environment.API_URL}`.split('/api/');
    this.url = `${urls[0]}/api/modules`;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (ModulesRepository.allData) {
        resolve(ModulesRepository.allData);
      } else {
        this._http.get(`${this.url}` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          ModulesRepository.allData = res;
          resolve(ModulesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
