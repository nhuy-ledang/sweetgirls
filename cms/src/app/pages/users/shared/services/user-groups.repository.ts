import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class UserGroupsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/user_groups`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    UserGroupsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (UserGroupsRepository.allData) {
        resolve(UserGroupsRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/user_group_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          UserGroupsRepository.allData = res;
          resolve(UserGroupsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
