import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class UsrRolesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/usr_roles`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    UsrRolesRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (UsrRolesRepository.allData) {
        resolve(UsrRolesRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/usr_roles_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          UsrRolesRepository.allData = res;
          resolve(UsrRolesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
