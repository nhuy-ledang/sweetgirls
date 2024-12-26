import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class UserRanksRepository extends Api {
  private static allData: any[] = [];

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/user_ranks`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    UserRanksRepository.allData = [];
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (UserRanksRepository.allData.length) {
        resolve(UserRanksRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res: any) => {
          UserRanksRepository.allData = res;
          resolve(UserRanksRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
