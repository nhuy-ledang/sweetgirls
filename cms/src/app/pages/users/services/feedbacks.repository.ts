import { Injectable } from '@angular/core';
import { environment } from '../../../../environments/environment';
import { Api, Http } from '../../../@core/services';

@Injectable()
export class FeedbacksRepository extends Api {
  private static allData: any = null;
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sys_feedbacks`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    FeedbacksRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (FeedbacksRepository.allData) {
        resolve(FeedbacksRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/sys_feedbacks_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          // OptionsRepository.allData = res;
          resolve(res);
        }), (errors) => reject(errors);
      }
    });
  }

  /*search(params: {paging?: number, page?: number, pageSize?: number, sort?: string, order?: string, data?: any}, loading?: boolean): Promise<any> {
    return this._http.get(`${environment.API_URL}/user_search` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }*/
}
