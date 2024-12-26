import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class InventoryProductsRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sto_inventory_products`;
  }

  importCheck(params: FormData, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_import_check` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }

  import(params: FormData, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}_import` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }
}
