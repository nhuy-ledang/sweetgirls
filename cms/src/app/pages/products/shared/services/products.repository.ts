import { Injectable } from '@angular/core';
import { environment } from '../../../../../environments/environment';
import { Api, Http } from '../../../../@core/services';

@Injectable()
export class ProductsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pd_products`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    ProductsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (ProductsRepository.allData) {
        resolve(ProductsRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/pd_products_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          ProductsRepository.allData = res;
          resolve(ProductsRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }
  checkModelCodeExists(code: string): Promise<boolean> {
    return this._http.get(`${this.url}/check-code-exists/${code}`, this.getJsonOptions());
  }
  copy(model, data, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/copy` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateDesc(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/description` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  getSpecials(id, params?: any, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/specials` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  createSpecial(id, data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/specials` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateSpecial(id, special_id, data: any, loading?: boolean): Promise<any> {
    return this._http.put(`${this.url}/${id}/specials/${special_id}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  removeSpecial(id, special_id, loading?: boolean): Promise<any> {
    return this._http.delete(`${this.url}/${id}/specials/${special_id}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  getImages(id, params?: any, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/images` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  createImage(id, data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/images` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  createImages(id, data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/images/multiple` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateImage(id, image_id, data: any, loading?: boolean): Promise<any> {
    return this._http.put(`${this.url}/${id}/images/${image_id}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  removeImage(id, image_id, loading?: boolean): Promise<any> {
    return this._http.delete(`${this.url}/${id}/images/${image_id}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  createProperties(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/properties` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  renew(id, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/renew` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  search(params: {paging?: number, pageSize?: number, sort?: string, order?: string, data?: any}, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}_search` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  getOptions(id, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/options` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  getVariants(id, params?: any, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/variants` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  createVariant(model, data: any, loading?: boolean): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.post(`${this.url}/${id}/variants` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateVariant(model, data: any, loading?: boolean, url?: string): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.put(`${url ? url : this.url}/${id}/variants` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  removeVariant(model, loading?: boolean, url?: string): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.delete(`${url ? url : this.url}/${id}/variants` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  updateQuantity(model, data: any, loading?: boolean, url?: string): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    return this._http.put(`${url ? url : this.url}/${id}/quantity` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  patchImage(model, data: any, loading?: boolean, url?: string): Promise<any> {
    const id = model instanceof Object ? model.id : model;
    this.beforeAction();
    return this._http.patch(`${url ? url : this.url}/${id}/images` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }
}
