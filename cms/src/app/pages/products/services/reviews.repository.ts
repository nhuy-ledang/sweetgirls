import { Injectable } from '@angular/core';
import { environment } from '../../../../environments/environment';
import { Api, Http } from '../../../@core/services';

@Injectable()
export class ReviewsRepository extends Api {
  private static allData: any = null;

  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/pd_reviews`;
  }

  /**
   * Override beforeAction
   */
  protected beforeAction(): void {
    ReviewsRepository.allData = null;
  }

  all(loading?: boolean): Promise<any> {
    return new Promise((resolve, reject) => {
      if (ReviewsRepository.allData) {
        resolve(ReviewsRepository.allData);
      } else {
        this._http.get(`${environment.API_URL}/pd_reviews_all` + (loading ? '?showSpinner' : ''), this.getJsonOptions()).then((res) => {
          // OptionsRepository.allData = res;
          resolve(res);
        }), (errors) => reject(errors);
      }
    });
  }

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
