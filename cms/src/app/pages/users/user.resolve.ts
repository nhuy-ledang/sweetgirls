import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from '@angular/router';
import { AppResolve } from '../../app.resolve';
import { UsersRepository } from './shared/services';

@Injectable()
export class UserResolve extends AppResolve implements Resolve<any> {
  constructor(router: Router, repository: UsersRepository) {
    super(router, repository);
  }

  resolve(route: ActivatedRouteSnapshot) {
    const id = route.params['user_id'] || route.params['id'];
    console.log(id);
    const info = this.storageHelper.getOne('user_info_' + id);
    if (info) {
      return info;
    } else {
      return this.find(id, {}, true);
    }
  }
}
