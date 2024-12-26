import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from '@angular/router';
import { AppResolve } from '../../../app.resolve';
import { PagesRepository } from '../shared/services';

@Injectable()
export class PageResolve extends AppResolve implements Resolve<any> {
  constructor(router: Router, repository: PagesRepository) {
    super(router, repository);
  }

  resolve(route: ActivatedRouteSnapshot) {
    const id = route.params['page_id'] || route.params['id'];
    console.log(id);
    const info = this.storageHelper.getOne('page_info_' + id);
    if (info) {
      return info;
    } else {
      return this.find(id, {embed: 'category,descs'}, true);
    }
  }
}
