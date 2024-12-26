import { Component, EventEmitter, Input, OnDestroy, OnInit, Output } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { MenusRepository } from '../../../../@core/repositories';

@Component({
  selector: 'ngx-pg-menu-desc',
  templateUrl: './desc.component.html',
})

export class MenuDescComponent extends AppForm implements OnInit, OnDestroy {
  repository: MenusRepository;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  desc: any|boolean;
  lang: string = 'en';

  @Input() set setup(d: {item: any, language: {code: string}}) {
    // console.log(d.item, d.language);
    const lang = d.language.code;
    const descs = d.item.descs ? d.item.descs : [];
    let desc = _.find(descs, {lang: lang});
    if (!desc) desc = _.cloneDeep(d.item);
    this.show(d.item, desc, lang);
  }

  controls: {
    name?: AbstractControl,
    link?: AbstractControl,
    is_redirect?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: MenusRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: [''],
      link: [''],
      is_redirect: [true],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info: any, desc: any, lang: string): void {
    this.resetForm(this.form);
    this.info = info;
    this.desc = desc;
    this.lang = lang ? lang : 'en';
    if (desc) {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(desc.hasOwnProperty(key) && desc[key] !== null ? desc[key] : '');
      });
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key)) this.controls[key].setValue('');
      });
    }
    this.controls.is_redirect.setValue(!info.page_id);
  }

  onSubmit(params: any, is_close?: boolean): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      if (params.is_redirect) {
      } else {
        newParams.link = null;
      }
      delete newParams.is_redirect;
      newParams['lang'] = this.lang;
      this.submitted = true;
      this.repository.updateDesc(this.info, newParams).then((res) => {
        this.showValid = false;
        this.submitted = false;
        this.onSuccess.emit({d: res.data, is_close: !!is_close});
      }, (errors) => this.handleError(errors));
    }
    console.log(params);
  }
}
