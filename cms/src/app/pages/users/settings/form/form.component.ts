import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { AppForm } from '../../../../app.base';
import { UserSettingsRepository } from '../../services';

@Component({
  selector: 'ngx-user-setting-form',
  templateUrl: './form.component.html',
})
export class UserSettingFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: {key: string, value: any, name: string, type: 'default'|'default_textarea'|'number'|'default_editor'|'editor_lang'|'text'|'textarea'|'image'|'boolean', thumb_url?: string, note?: string} = {key: '', value: '', name: '', type: 'default'};
  name: any = '';
  value: any;

  constructor(router: Router, security: Security, state: GlobalState, repository: UserSettingsRepository) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info: {key: string, value: any, name: string, type: 'default'|'default_textarea'|'number'|'default_editor'|'editor_lang'|'text'|'textarea'|'image'|'boolean', thumb_url?: string}): void {
    if (info.type === 'editor_lang' || info.type === 'text' || info.type === 'textarea') {
      console.log(info);
      const value = {
        vi: _.isObject(info.value) && info.value['vi'] ? info.value['vi'] : '',
        en: _.isObject(info.value) && info.value['en'] ? info.value['en'] : '',
      };
      this.value = value;
    } else if (info.type === 'image') {
      console.log(info);
      this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: info.thumb_url ? info.thumb_url : ''});
      this.value = info.value;
    } else {
      this.value = info.value;
    }
    this.name = info.name;
    this.info = info;
    this.modal.show();
  }

  hide(): void {
    // this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(): void {
    this.submitted = true;
    const newParams: any = {key: this.info.key, value: this.value};
    if (this.info.type === 'boolean') newParams.value = this.value ? 1 : 0;
    if (this.info.type === 'image') {
      if (this.fileSelected) {
        newParams.file_path = this.fileSelected.path;
      } else if (this.file) {
        newParams.file = this.file;
      } else if (!this.fileOpt.thumb_url) newParams['image'] = '';
      this.repository.create(this.utilityHelper.toFormData(newParams)).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    } else {
      this.repository.create(newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
  }
}
