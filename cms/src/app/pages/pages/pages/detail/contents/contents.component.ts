import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../../@core/utils';
import { Security } from '../../../../../@core/security';
import { AppBase } from '../../../../../app.base';
import { PagesRepository } from '../../../shared/services';

@Component({
  selector: 'ngx-pg-detail-contents',
  templateUrl: './contents.component.html',
})
export class PageDetailContentsComponent extends AppBase implements OnInit, OnDestroy {
  info: any;

  constructor(router: Router, security: Security, state: GlobalState, repository: PagesRepository, protected _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.info = this._route.parent.snapshot.data['info'];
    console.log(this.info);
  }

  ngOnInit(): void {
    this._state.subscribe('page.info:edit', (info: any) => {
      this.info = info;
    });
  }

  ngOnDestroy(): void {
    super.destroy();
  }
}
