import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class MediaFoldersRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/media_folders`;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      this._http.get(`${environment.API_URL}/media_folders_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
        resolve(res);
      }), (errors) => reject(errors);
    });
  }

  moveToFolder(data, loading?: boolean): Promise<any> {
    return this._http.post(`${environment.API_URL}/media_moves` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
