import { Component, EventEmitter, Input, OnDestroy, OnInit, Output } from '@angular/core';
import { Router } from '@angular/router';
import { FormBuilder } from '@angular/forms';
import { Security } from '../../../../../@core/security';
import { GlobalState } from '../../../../../@core/utils';
import { CategoryModulesRepository } from '../../../services';
import { BaseModuleDescComponent } from '../../../../pages/modules/desc/base.desc.component';

@Component({
  selector: 'ngx-pd-category-module-desc',
  templateUrl: '../../../../pages/modules/desc/desc.component.html',
})

export class CategoryModuleDescComponent extends BaseModuleDescComponent implements OnInit, OnDestroy {
  repository: CategoryModulesRepository;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  @Input() set setup(d: {item: any, language: {code: string}}) {
    // console.log(d.item, d.language);
    const lang = d.language.code;
    const descs = d.item.descs ? d.item.descs : [];
    let desc = _.find(descs, {lang: lang});
    if (!desc) desc = _.cloneDeep(d.item);
    this.show(d.item, desc, lang, d.item.cf_data && d.item.cf_data.configs ? d.item.cf_data.configs : false);
  }

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: CategoryModulesRepository) {
    super(router, security, state, repository, fb);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }
}
