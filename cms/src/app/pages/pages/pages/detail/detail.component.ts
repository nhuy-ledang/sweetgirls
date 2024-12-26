import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppList } from '../../../../app.base';
import { PagesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pg-detail',
  templateUrl: './detail.component.html',
})
export class PageDetailComponent extends AppList implements OnInit, OnDestroy {
  private _id: number;
  info: any | boolean;
  isCollapsed: boolean = false;

  constructor(router: Router, security: Security, state: GlobalState, repository: PagesRepository, protected _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.info = this._route.snapshot.data['info'];
    console.log(this.info);
  }

  // Find and update data
  /*protected find(id: number): void {
    this.repository.find(id, {embed: 'category,descs'}, false).then((res: any) => {
        console.log(res.data);
        this.info = res.data;
        this._state.notifyDataChanged('page.info:edit', this.info);
      }, (res: any) => {
        console.log(res.errors);
      },
    );
  }*/

  ngOnInit(): void {
    // For a static snapshot of the route...
    /*const id = this._route.snapshot.paramMap.get('id');
    if (id) this._id = parseInt(id, 0);
    console.log(this._id);*/
    this._route.paramMap.subscribe(paramMap => {
      this.info = this._route.snapshot.data['info'];
      this._state.notifyDataChanged('page.info:edit', this.info);
    });
    this._state.subscribe('page.info:edit', (info: any) => {
      this.info = info;
    });
  }

  ngOnDestroy(): void {
    super.destroy();
  }
}
