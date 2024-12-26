import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { Usr } from '../entities';
import { AuthRepository } from '../repositories';
import { Http, StorageService } from '../services';
import { GlobalState } from '../utils';

let INTERNAL_REQUEST_LOADING = null;

@Injectable()
export class Security {
  private static currentUser: Usr;

  constructor(protected _httpClient: HttpClient, protected _router: Router, protected _http: Http, protected _state: GlobalState, protected _storage: StorageService, protected _authRepo: AuthRepository) {
  }

  private loginResponse(response: any, resolve) {
    let user: Usr;
    if (response instanceof Usr) {
      user = response;
    } else {
      user = new Usr(response);
    }
    if (user.isAccessAdmin()) {
      // Set the Request Header 'Authorization'
      const token: string = response.access_token;
      if (token) {
        this._http.setToken(token);
      }

      // Set Current User
      this.setCurrentUser(user);

      // Send Message
      this._state.notifyDataChanged('security.isLogged', user);
    }
    resolve(user);
    INTERNAL_REQUEST_LOADING = null;
  }

  private loginError(response, resolve) {
    resolve(response);
    INTERNAL_REQUEST_LOADING = null;

    // Remove localStorage
    this._http.removeToken();
  }

  login(params) {
    return new Promise((resolve, reject) => this._authRepo.login(params, true).then((response: any) => this.loginResponse(response.data, resolve), errors => reject(errors)));
  }

  signup(params) {
    /*return new Promise((resolve, reject) =>
      this._authRepo.signup(params).then(
        response => this.loginResponse(response, resolve),
        errors => reject(errors)
      )
    );*/
  }

  // Sign in with social
  oauth(provider, accessToken) {
    /*return new Promise((resolve, reject) =>
      this._authRepo.oauth(provider, accessToken).then(
        response => this.loginResponse(response, resolve),
        errors => reject(errors)
      )
    );*/
  }

  oauthSignUp(country_id: number, phoneNumber: string, provider: string, accessToken: string) {
    /*return new Promise((resolve, reject) =>
      this._authRepo.oauthSignUp(country_id, phoneNumber, provider, accessToken).then(
        response => this.loginResponse(response, resolve),
        errors => reject(errors)
      )
    );*/
  }

  forgetPw(email) {
    /*return new Promise((resolve, reject) =>
      this._authRepo.forgetPw(email).then(
        response => resolve(response),
        errors => reject(errors)
      )
    );*/
  }

  // Apply new password
  newPassword(params) {
    return new Promise((resolve, reject) => this._authRepo.newPassword(params).then(
      response => resolve(response),
      errors => reject(errors),
    ));
  }

  logout() {
    this._authRepo.logout(true).then(response => console.log(response), errors => console.log(errors));
    this._state.notifyDataChanged('security.loggedOut', Security.currentUser);
    this._router.navigateByUrl('/auth/login');
    // Remove localStorage
    this._http.removeToken();
    // Remove available
    Security.currentUser = null;
  }

  // Is the current user authenticated?
  isAuthenticated(): boolean {
    return !!Security.currentUser;
  }

  setCurrentUser(user): void {
    Security.currentUser = user;
  }

  getCurrentUser(): Usr {
    return Security.currentUser;
  }

  // Ask the backend to see if a user is already authenticated - this may be from a previous session.
  private auth(resolve: any) {
    const authToken = this._http.getToken();
    if (authToken) {
      // Set the Request Header 'Authorization'
      if (!this.isAuthenticated()) {
        this._authRepo.auth().then((response: any) => this.loginResponse(response.data, resolve), () => this.loginError(false, resolve));
      } else {
        this.loginResponse(Security.currentUser, resolve);
      }
    } else {
      this.loginError(false, resolve);
    }
  }

  // Ask the backend to see if a user is already authenticated - this may be from a previous session.
  requestCurrentUser() {
    const self = this;

    function auth(resolve) {
      if (INTERNAL_REQUEST_LOADING === null) {
        INTERNAL_REQUEST_LOADING = true;
        self.auth(resolve);
      } else {
        setTimeout(() => {
          auth(resolve);
        }, 200);
      }
    }

    return new Promise((resolve) => auth(resolve));
  }
}
