import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { AppList } from '../../../app.base';
import { SitemapsRepository } from '../services';

@Component({
  selector: 'ngx-sitemaps',
  templateUrl: './sitemaps.component.html',
})
export class SitemapsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: SitemapsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: SitemapsRepository) {
    super(router, security, state, repository);
    /*this.data.sort = 'id';
    this.data.order = 'asc';*/
    this.data.data = {q: ''};
  }

  protected appendData(res: any): void {
    console.log(res.files);
    this.data.items = [];
    if (res.files) {
      _.forEach(res.files, (item, index) => {
        this.data.items.push({index: index, url: item});
      });
    }
  }

  // Override fn
  protected getData(): void {
    this.data.loading = true;
    this.repository.get(this.data, false).then((res: any) => {
        this.appendData(res);
        this.data.loading = false;
      }, (res: any) => {
        console.log(res.errors);
        this.data.loading = false;
      },
    );
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  build(): void {
    this.repository.build(true).then((res: any) => {
      this.appendData(res);
    }, (res: any) => {
      console.log(res.errors);
    });
  }
}
