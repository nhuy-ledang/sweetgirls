import { Component, EventEmitter, Input, OnDestroy, OnInit, Output } from '@angular/core';
import { Router } from '@angular/router';
import { FormBuilder } from '@angular/forms';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { PageContentsRepository } from '../../../shared/services';
import { BaseModuleDescComponent } from '../../../modules/desc/base.desc.component';

@Component({
  selector: 'ngx-pg-pg-content-desc',
  templateUrl: '../../../modules/desc/desc.component.html',
})

export class PageContentDescComponent extends BaseModuleDescComponent implements OnInit, OnDestroy {
  repository: PageContentsRepository;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  @Input() set setup(d: {item: any, language: {code: string}}) {
    // console.log(d.item, d.language);
    const lang = d.language.code;
    const descs = d.item.descs ? d.item.descs : [];
    let desc = _.find(descs, {lang: lang});
    if (!desc) desc = _.cloneDeep(d.item);
    this.show(d.item, desc, lang, d.item.cf_data && d.item.cf_data.configs ? d.item.cf_data.configs : false);
  }

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: PageContentsRepository) {
    super(router, security, state, repository, fb);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }
}
