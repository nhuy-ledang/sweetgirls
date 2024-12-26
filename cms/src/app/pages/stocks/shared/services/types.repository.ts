import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class TypesRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sto_types`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    TypesRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (TypesRepository.allData) {
        resolve(TypesRepository.allData);
      } else {
        this._http.get(`${this.url}_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          TypesRepository.allData = res;
          resolve(TypesRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
}
