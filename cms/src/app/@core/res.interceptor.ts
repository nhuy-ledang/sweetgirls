import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor, HttpErrorResponse } from '@angular/common/http';
import { Observable } from 'rxjs';
import 'rxjs/add/operator/do';

@Injectable()
export class ResInterceptor implements HttpInterceptor {
  constructor(protected _router: Router) {
  }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const that = this;
    return next.handle(request).do((event: HttpEvent<any>) => {
      // console.log(event);
    }, (error: HttpErrorResponse) => {
      if (error.status === 401) {
        console.log('redirect to the login route');
        that._router.navigate(['/auth/login']);
      }
    });
  }
}
