import { Component, EventEmitter, Input, OnDestroy, OnInit, Output } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder } from '@angular/forms';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { CategoriesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-category-desc',
  templateUrl: './desc.component.html',
})

export class CategoryDescComponent extends AppForm implements OnInit, OnDestroy {
  repository: CategoriesRepository;
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
    meta_title?: AbstractControl,
    meta_description?: AbstractControl,
    meta_keyword?: AbstractControl,
    alias?: AbstractControl,
    description?: AbstractControl,
  };

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: CategoriesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: [''],
      meta_title: [''],
      meta_description: [''],
      meta_keyword: [''],
      alias: [''],
      description: [''],
    });
    this.controls = this.form.controls;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  protected setInfo(info: any): void {
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key)) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.info = info;
    console.log(info);
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
  }

  onSubmit(params: any, is_close?: boolean): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
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

  onChangeName(): void {
    if (!this.desc || (this.desc && !this.desc.meta_title)) {
      if (!this.controls.meta_title.touched) {
        // console.log('meta_title', this.controls.meta_title.value, this.controls.meta_title.touched);
        this.controls.meta_title.setValue(this.controls.name.value);
      }
    }
    if (!this.desc || (this.desc && !this.desc.alias)) {
      if (!this.controls.alias.touched) {
        // console.log('alias', this.controls.alias.value, this.controls.alias.touched);
        this.controls.alias.setValue(this.utilityHelper.toAlias(this.controls.name.value));
      }
    }
  }
}
