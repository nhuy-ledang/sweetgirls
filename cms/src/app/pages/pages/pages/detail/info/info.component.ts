import { Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../../@core/utils';
import { Security } from '../../../../../@core/security';
import { AppBase } from '../../../../../app.base';
import { PagesRepository } from '../../../shared/services';
import { PageFormComponent } from '../../form/form.component';

@Component({
  selector: 'ngx-pg-detail-info',
  templateUrl: './info.component.html',
})
export class PageDetailInfoComponent extends AppBase implements OnInit, OnDestroy {
  @ViewChild(PageFormComponent) form: PageFormComponent;
  info: any;

  constructor(router: Router, security: Security, state: GlobalState, repository: PagesRepository, protected _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.info = this._route.parent.snapshot.data['info'];
    console.log(this.info);
  }

  // Find and update data
  protected find(id: number): void {
    this.repository.find(id, {embed: 'category,descs'}, false).then((res: any) => {
        console.log(res.data);
        this.info = res.data;
        this._state.notifyDataChanged('page.info:edit', this.info);
      }, (res: any) => {
        console.log(res.errors);
      },
    );
  }

  ngOnInit(): void {
    this._state.subscribe('page.info:edit', (info: any) => {
      this.info = info;
    });
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  edit(): void {
    this.form.show(this.info);
  }

  onFormSuccess(res: any): void {
    this.find(res.id);
  }
}
