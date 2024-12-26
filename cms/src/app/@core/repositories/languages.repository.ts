import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class LanguagesRepository extends Api {
  private static allData: any[] = [];

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sys_languages`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    LanguagesRepository.allData = [];
  }

  /**
   * Get All
   * @param loading
   * @returns {*}
   */
  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (LanguagesRepository.allData.length) {
        resolve(LanguagesRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res: any) => {
          LanguagesRepository.allData = res.data;
          resolve(LanguagesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
