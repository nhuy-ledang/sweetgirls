import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import { StorageHelper } from './@core/helpers';
import { Api } from './@core/services';
import { Err } from './@core/entities';
import { CERROR_CODES } from './app.constants';

export class AppResolve {
  storageHelper: StorageHelper = new StorageHelper();

  constructor(protected _router: Router, protected _repository: Api) {
  }

  protected handleErrors(res: {errors: Err[], data: any}): void {
    if (res.errors.length && res.errors[0].errorCode === CERROR_CODES.AUTH_FAILED.errorCode) {
      this._router.navigate(['/auth/login']);
      return;
    } else {
      this._router.navigate(['/']);
      return;
    }
  }

  protected find(id: number, data?: any, loading?: boolean): Observable<any>|Promise<any>|any {
    return this._repository.find(id, data, loading).then((res: any) => {
        return res.data;
      }, (errors) => this.handleErrors(errors),
    );
  }
}
