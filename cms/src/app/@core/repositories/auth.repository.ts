import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Api, Http } from '../services';

@Injectable()
export class AuthRepository extends Api {
  /**
   * Constructor
   * @param {Http} http
   */
  constructor(http: Http) {
    super(http);
    this.url = `${environment.API_URL}/usr_auth`;
  }

  // Check login
  auth(loading?: boolean) {
    return this._http.get(`${this.url}` + (loading ? '?showSpinner' : ''), this.getJsonOptions());
  }

  // Login
  login(params, loading?: boolean) {
    return this._http.post(`${this.url}/login` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }

  // Logout
  logout(loading?: boolean) {
    return this._http.post(`${this.url}/logout` + (loading ? '?showSpinner' : ''), {}, this.getJsonOptions());
  }

  // New password
  newPassword(params, loading?: boolean) {
    return this._http.post(`${this.url}/pw-change` + (loading ? '?showSpinner' : ''), params, this.getJsonOptions());
  }

  // Register ejabberd account
  registerEjabberd() {
    return this._http.post(`${environment.API_URL}/user_ejabberd`, {}, this.getJsonOptions());
  }
}
