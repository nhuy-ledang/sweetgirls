import {Injectable} from '@angular/core';
import {environment} from '../../../environments/environment';
import {Api, Http} from '../services';
import {forkJoin} from 'rxjs';

@Injectable()
export class MediasRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/media_files`;
  }

  upload(data: any, loading?: boolean) {
    return this._http.post(`${environment.API_URL}/media/upload` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  uploads(data: any, loading?: boolean) {
    return this._http.post(`${environment.API_URL}/media/uploads` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  deletes(data: any, loading?: boolean) {
    return this._http.post(`${environment.API_URL}/media_deletes` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  moves(data: any, loading?: boolean) {
    return this._http.post(`${environment.API_URL}/media_moves` + (loading ? '?showSpinner' : ''), data, this.getJsonOptions());
  }

  chatUpload(files: File[], loading?: boolean): Promise<any> {
    return new Promise((resolve) => {
      const requests = [];
      _.forEach(files, (file: File) => {
        const formData = new FormData();
        formData.append('file', file, file.name);
        requests.push(this._http.put(`${environment.API_URL}/media/chat-image` + (loading ? '?showSpinner' : ''), formData, this.getJsonOptions()));
      });
      forkJoin(requests).subscribe(results => {
        const data = [];
        _.forEach(results, (result: any) => {
          data.push(result.data);
        });
        resolve(data);
      }, errors => resolve([]));
    });
  }
}
