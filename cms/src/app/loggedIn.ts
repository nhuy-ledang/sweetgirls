import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { Security } from './@core/security';

@Injectable()
export class LoggedIn implements Resolve<any> {
  constructor(protected _router: Router, protected _security: Security) {
  }

  resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | Promise<any> | any {
    return this._security.requestCurrentUser().then(
      () => {
        if (this._security.isAuthenticated()) {
          return this._security.getCurrentUser();
        } else {
          console.log(state.url);
          this._router.navigate(['/auth/login']);
        }
      },
    );
  }
}
