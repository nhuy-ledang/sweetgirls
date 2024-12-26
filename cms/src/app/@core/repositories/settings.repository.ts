import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class SettingsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sys_settings`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    SettingsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (SettingsRepository.allData) {
        resolve(SettingsRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/sys_settings_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          SettingsRepository.allData = res;
          resolve(SettingsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
