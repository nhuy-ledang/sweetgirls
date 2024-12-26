import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from '@angular/router';
import { AppResolve } from '../../../app.resolve';
import { ProductsRepository } from '../shared/services';

@Injectable()
export class ProductResolve extends AppResolve implements Resolve<any> {
  constructor(router: Router, repository: ProductsRepository) {
    super(router, repository);
  }

  resolve(route: ActivatedRouteSnapshot) {
    const id = route.params['product_id'] || route.params['id'];
    console.log(id);
    const info = this.storageHelper.getOne('product_info_' + id);
    if (info) {
      info.isCaching = true;
      return info;
    } else {
      return this.find(id, {embed: ''}, true);
    }
  }
}
