import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { delay, mergeMap, materialize, dematerialize } from 'rxjs/operators';
import { Spinner } from './services';

@Injectable()
export class ReqInterceptor implements HttpInterceptor {
  constructor(protected _spinner: Spinner) {
  }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const {url, method, headers, body} = request;
    // const urls = url.split(/[?&]+/);
    // const dest = urls[0];
    const that = this;
    // wrap in delayed observable to simulate server api call
    return of(null)
      .pipe(mergeMap(addReqInterceptor))
      .pipe(materialize()) // call materialize and dematerialize to ensure delay even if an error is thrown (https://github.com/Reactive-Extensions/RxJS/issues/648)
      .pipe(delay(500))
      .pipe(dematerialize());

    function addReqInterceptor() {
      const showSpinner = !!url.includes('showSpinner');
      if (showSpinner) {
        that._spinner.load();
      }
      // url.endsWith('/users/authenticate') && method === 'POST';
      // pass through any requests not handled above
      return next.handle(request);
    }
  }
}
