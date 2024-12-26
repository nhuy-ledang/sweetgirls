import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class WidgetsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    const urls = `${environment.API_URL}`.split('/api/');
    this.url = `${urls[0]}/api/widgets`;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (WidgetsRepository.allData) {
        resolve(WidgetsRepository.allData);
      } else {
        this._http.get(`${this.url}` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          WidgetsRepository.allData = res;
          resolve(WidgetsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
