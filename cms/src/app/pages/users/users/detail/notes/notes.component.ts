import { AfterViewInit, Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../../@core/utils';
import { Security } from '../../../../../@core/security';
import { OrdersRepository } from '../../../../orders/shared/services';
import { AppList } from '../../../../../app.base';
import { User } from '../../../shared/entities';

@Component({
  selector: 'ngx-user-notes',
  templateUrl: './notes.component.html',
})
export class UserNotesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  info: User = null;

  constructor(router: Router, security: Security, state: GlobalState, repository: OrdersRepository, protected _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.info = this._route.parent.snapshot.data['info'];
    console.log(this.info);
    this.data.data.user_id = this.info.id;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    // setTimeout(() => this.getData(), 200);
  }
}
