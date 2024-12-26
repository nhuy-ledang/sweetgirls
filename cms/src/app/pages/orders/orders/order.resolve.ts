import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from '@angular/router';
import { OrdersRepository } from '../shared/services';
import { AppResolve } from '../../../app.resolve';

@Injectable()
export class OrderResolve extends AppResolve implements Resolve<any> {
  constructor(router: Router, repository: OrdersRepository) {
    super(router, repository);
  }

  resolve(route: ActivatedRouteSnapshot) {
    const id = route.params['order_id'] || route.params['id'];
    console.log(id);
    const info = this.storageHelper.getOne('order_info_' + id);
    if (info) {
      return info;
    } else {
      return this.find(id, {embed: 'shipping,user,products'}, true);
    }
  }
}
