import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class UsrGroupsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/usr_groups`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    UsrGroupsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (UsrGroupsRepository.allData) {
        resolve(UsrGroupsRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/usr_group_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          UsrGroupsRepository.allData = res;
          resolve(UsrGroupsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
