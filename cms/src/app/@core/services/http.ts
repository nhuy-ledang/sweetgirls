import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs/Observable';
import { environment } from '../../../environments/environment';
import { Err } from '../entities';
import { StorageService } from './storage-service';
import { CAPP } from '../../app.constants';
import { Spinner } from './spinner';
import { UrlHelper } from '../helpers';

@Injectable()
export class Http {
  /**
   * Auth key
   *
   * @type {string}
   */
  protected authKey = 'sAuthorization';

  /**
   * Constructor
   * @param {HttpClient} _http
   */
  constructor(protected _http: HttpClient, protected _storage: StorageService, protected _spinner: Spinner) {
    this.authKey = CAPP.TOKEN_NAME;
  }

  /**
   * Configure auth
   *
   * @param opts
   */
  private configureAuth(opts: any): any {
    const headers: any = {};
    if (opts.contentType) {
      if (opts.contentType !== 'multipart/form-data') {
        headers['Content-Type'] = opts.contentType;
      }
    } else {
      headers['Content-Type'] = 'application/json';
    }
    headers['App-Env'] = 'cms';
    headers['Device-Platform'] = 'web';
    headers['Device-Token'] = headers['App-Env'] + '-gKTJYExdwPQygTB';
    const token = this._storage.getItem(this.authKey);
    if (token) {
      headers[this.authKey] = `${token}`;
    }
    opts.headers = new HttpHeaders(headers);
    opts.params = _.extend({tz: new Date().getTimezoneOffset()}, opts.params);

    return opts;
  }

  /**
   * Handle Success
   *
   * @param response
   * @param resolve
   */
  private handleSuccess(response: any, resolve) {
    this._spinner.clear();
    resolve(response.data);
  }

  /**
   * Handle Error
   *
   * @param {HttpErrorResponse} error
   * @param reject
   */
  private handleError(error: HttpErrorResponse, reject) {
    this._spinner.clear();
    let errors: Err[] = [];
    if (!error.error.errors || (error.status === 0 || error.status === 405 || error.status === 500)) {
      errors = [new Err({errorCode: 0, errorMessage: error.message})];
    } else {
      errors = error.error.errors.map(item => new Err(item));
    }
    reject({errors: errors, data: error.error.data ? error.error.data : null});
  }

  /**
   * Handle Response
   *
   * @param {Observable<Object>} obs
   * @returns {Promise}
   */
  private handleResponse(obs: Observable<Object>) {
    return new Promise((resolve, reject) => {
      obs.subscribe((response) => this.handleSuccess(response, resolve), (error: HttpErrorResponse) => this.handleError(error, reject));
    });
  }

  /**
   * Get
   *
   * @param url
   * @param {{}} opts
   * @returns {any}
   */
  get(url, opts: any = {}): Promise<any> {
    this.configureAuth(opts);
    opts.params = _.extend({ver: Math.random()}, opts.params);
    return this.handleResponse(this._http.get(url, opts));
  }

  /**
   * Post
   *
   * @param url
   * @param data
   * @param {{}} opts
   * @returns {any}
   */
  post(url, data, opts: any = {}) {
    if (data instanceof FormData) {
      opts.contentType = 'multipart/form-data';
    }
    this.configureAuth(opts);
    return this.handleResponse(this._http.post(url, data, opts));
  }

  /**
   * Put
   *
   * @param url
   * @param data
   * @param {{}} opts
   * @returns {any}
   */
  put(url, data, opts: any = {}) {
    if (data instanceof FormData) {
      opts.contentType = 'multipart/form-data';
      this.configureAuth(opts);
      return this.handleResponse(this._http.post(url, data, opts));
    } else {
      this.configureAuth(opts);
      return this.handleResponse(this._http.put(url, data, opts));
    }
  }

  /**
   * Patch
   *
   * @param url
   * @param data
   * @param {{}} opts
   * @returns {any}
   */
  patch(url, data, opts = {}) {
    this.configureAuth(opts);
    return this.handleResponse(this._http.patch(url, data, opts));
  }

  /**
   * Delete
   *
   * @param url
   * @param {{}} opts
   * @returns {any}
   */
  delete(url, opts = {}) {
    this.configureAuth(opts);
    return this.handleResponse(this._http.delete(url, opts));
  }

  /**
   * Set Token
   *
   * @param {string} token
   */
  setToken(token: string) {
    document.cookie = `${this.authKey}=${token};path=/;domain=.${environment.APP_DOMAIN}`;
    this._storage.setItem(this.authKey, `${token}`);
  }

  /**
   * Get Token
   */
  getToken() {
    return this._storage.getItem(this.authKey);
  }

  /**
   * Remove Token
   */
  removeToken() {
    document.cookie = `${this.authKey}=;path=/;domain=.${environment.APP_DOMAIN};expires=Thu, 01 Jan 1970 00:00:01 GMT;`;
    this._storage.removeItem(this.authKey);
  }

  /**
   * Get Link
   * @param url
   * @param params
   */
  getLink(url: string, params: any = {}): string {
    params[this.authKey] = this.getToken();
    return url + '?' + UrlHelper.getUrlParams(params);
  }
}
