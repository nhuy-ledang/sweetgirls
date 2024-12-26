import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { GlobalState } from '../../../@core/utils';
import { Security } from '../../../@core/security';
import { AppList } from '../../../app.base';
import { UserSettingsRepository } from '../services';
import { UserSettingFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-user-settings',
  templateUrl: 'settings.component.html',
})
export class UserSettingsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(UserSettingFormComponent) form: UserSettingFormComponent;
  repository: UserSettingsRepository;
  settings: {name: string, items: {key: string, type: 'default'|'default_textarea'|'number'|'default_editor'|'editor_lang'|'text'|'textarea'|'image'|'list_image'|'banners'|'boolean', name: string, note?: string}[]}[] = [
    {
      name: 'Email tự động', items: [
        {key: 'config_user_rank_notification', type: 'default_editor', name: 'Email thông báo lên hạng', note: '<span>{TEN_KH} : Tên khách hàng</span><br><span>{HANG} : Hạng</span>'},
      ],
    },
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: UserSettingsRepository) {
    super(router, security, state, repository);
  }

  // Override fn
  protected getData(cb?: Function, loading?: boolean): void {
    if (loading !== false) this.data.loading = true;
    this.repository.all(false).then((res: any) => {
        this.data.items = [];
        _.forEach(this.settings, (settings) => {
          const items: any = {name: settings.name, items: []};
          _.forEach(settings.items, (st: {key: string, type: string, name: string, note?: string}) => {
            let item: any = {};
            if (res.data.hasOwnProperty(st.key)) {
              item = {key: st.key, type: st.type, name: st.name, note: st.note ? st.note : '', value: res.data[st.key]};
            } else {
              item = {key: st.key, type: st.type, name: st.name, note: st.note ? st.note : '', value: ''};
            }
            if (item.type === 'boolean') {
              item.value = Boolean(parseInt(item.value, 0));
            }
            items.items.push(item);
          });
          this.data.items.push(items);
        });
        this.data.loading = false;
        console.log(this.data.items);
      }, (errors) => {
        console.log(errors);
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

  edit(item: any): void {
    this.form.show(item);
  }

  changeProp(item: any): void {
    this.repository.create({key: item.key, value: item.value ? 1 : 0}).then((res) => {
      console.log(res.data);
    }, (errors) => {
      console.log(errors);
    });
  }

  onFormSuccess(res: any): void {
    this.getData(null, false);
  }
}
