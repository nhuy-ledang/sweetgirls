import {Injectable} from '@angular/core';
import {environment} from '../../../environments/environment';
import {Api, Http} from '../services';

@Injectable()
export class MenusRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pg_menus`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    MenusRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (MenusRepository.allData) {
        resolve(MenusRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/pg_menus_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          MenusRepository.allData = res;
          resolve(MenusRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  nav(loading?: boolean): Promise<any> {
    return this._http.get(`${environment.API_URL}/pg_menus_nav` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }
}
