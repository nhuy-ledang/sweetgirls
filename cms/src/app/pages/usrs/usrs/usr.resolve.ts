import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from '@angular/router';
import { UsrsRepository } from '../../../@core/repositories';
import { AppResolve } from '../../../app.resolve';

@Injectable()
export class UsrResolve extends AppResolve implements Resolve<any> {
  constructor(router: Router, repository: UsrsRepository) {
    super(router, repository);
  }

  resolve(route: ActivatedRouteSnapshot) {
    const id = route.params['usr_id'] || route.params['id'];
    console.log(id);
    const info = this.storageHelper.getOne('usr_info_' + id);
    if (info) {
      return info;
    } else {
      return this.find(id, {}, true);
    }
  }
}
