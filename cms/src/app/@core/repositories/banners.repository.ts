import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class BannersRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/sys_banners`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    BannersRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (BannersRepository.allData) {
        resolve(BannersRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/sys_banners_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          BannersRepository.allData = res;
          resolve(BannersRepository.allData);
        }), (errors) => reject(errors);
      }
    });
  }

  /*allWithBanners(loading?: boolean): Promise<any> {
    return this._http.get(`${environment.API_URL}/sys_banners_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions({data: {embed: 'banners'}}));
  }

  detailGet(id: number, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/detail` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  detailCreate(id: number, params: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/detail` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }

  detailUpdate(id: number, banner_image_id: number, data: any, loading?: boolean): Promise<any> {
    return this._http.put(`${this.url}/${id}/detail/${banner_image_id}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  detailRemove(id: number, banner_image_id: number, loading?: boolean): Promise<any> {
    return this._http.delete(`${this.url}/${id}/detail/${banner_image_id}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }*/

  getImages(id, params?: any, loading?: boolean): Promise<any> {
    return this._http.get(`${this.url}/${id}/images` + (loading ? '?showSpinner' : ''), this.getJsonOptions(params));
  }

  createImage(id, data: any, loading?: boolean): Promise<any> {
    return this._http.post(`${this.url}/${id}/images` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  updateImage(id, image_id, data: any, loading?: boolean): Promise<any> {
    return this._http.put(`${this.url}/${id}/images/${image_id}` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  removeImage(id, image_id, loading?: boolean): Promise<any> {
    return this._http.delete(`${this.url}/${id}/images/${image_id}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }
}
