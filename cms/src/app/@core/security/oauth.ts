import {Injectable} from '@angular/core';
/*import {FacebookService, LoginResponse, InitParams, LoginOptions} from 'ngx-facebook';*/
import {Script} from '../services/script';

@Injectable()
export class OAuth {
  constructor(script: Script/*, protected _fb: FacebookService*/) {
    /*script.load('fbSdk').then(() => {
      let initParams: InitParams = {
        appId: environment.FACEBOOK.APP_ID,
        version: environment.FACEBOOK.APP_VERSION,
        xfbml: true,
      };
      _fb.init(initParams);
    }).catch(error => console.log(error));*/
  }

  fbLogin() {
    /*return new Promise((resolve, reject) => {
      let option: LoginOptions = {
        scope: 'email',
      };
      this._fb.login(option)
        .then((response: LoginResponse) => resolve(response.authResponse.accessToken))
        .catch((errors: any) => reject(errors));
    });*/
  }
}
