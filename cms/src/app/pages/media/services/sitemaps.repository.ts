import { Injectable } from '@angular/core';
import { environment } from '../../../../environments/environment';
import { Api, Http } from '../../../@core/services';

@Injectable()
export class SitemapsRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    let url = `${environment.API_URL}`;
    const urls = url.split('/');
    urls.splice(urls.length - 2, 2);
    url = urls.join('/');
    this.url = `${url}/sitemap`;
  }

  build(loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/build` + (loading ? '?showSpinner' : ''), {}, this.getJsonOptions());
  }
}
