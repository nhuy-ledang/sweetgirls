import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class LeadSourcesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/lead_sources`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    LeadSourcesRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (LeadSourcesRepository.allData) {
        resolve(LeadSourcesRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/lead_source_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          LeadSourcesRepository.allData = res;
          resolve(LeadSourcesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
