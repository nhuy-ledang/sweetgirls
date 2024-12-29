import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { AppList } from '../../../app.base';
import { SettingsRepository } from '../../../@core/repositories';
import { GlobalState } from '../../../@core/utils';
import { SettingFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-st-settings',
  templateUrl: 'settings.component.html',
})
export class SettingsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(SettingFormComponent) form: SettingFormComponent;
  repository: SettingsRepository;
  settings: {name: string, items: {key: string, type: 'default'|'default_textarea'|'number'|'editor_lang'|'text'|'textarea'|'image'|'list_image'|'boolean'|'select_color', name: string, note?: string, placeholder?: string}[]}[] = [
    {
      name: 'Cài đặt website', items: [
        {key: 'config_meta_title', type: 'default', name: 'Tiêu đề Web'},
        {key: 'config_meta_description', type: 'default', name: 'Meta Tag Description'},
        {key: 'config_meta_keyword', type: 'default', name: 'Meta Tag Keywords'},
        {key: 'config_icon', type: 'image', name: 'Icon/Favicon'},
        {key: 'config_logo', type: 'image', name: 'Logo'},
        {key: 'config_bg_login', type: 'image', name: 'Background Login'},
      ],
    },
    {
      name: 'Thông tin website', items: [
        {key: 'config_owner', type: 'text', name: 'Chủ sở hữu'},
        {key: 'config_name', type: 'text', name: 'Tên công ty'},
        {key: 'config_address', type: 'text', name: 'Địa chỉ'},
        {key: 'config_email', type: 'default', name: 'Email'},
        {key: 'config_hotline', type: 'default', name: 'Hotline'},
        {key: 'config_telephone', type: 'default', name: 'Phone'},
        {key: 'config_copyright', type: 'default', name: 'Copyright'},
        {key: 'config_googlemap_latlng', type: 'default', name: 'Google Map', note: '(VD: 10.8082147, 106.70713978)'},
        {key: 'config_googlemap_embed', type: 'default', name: 'Google Map Link', note: '(VD: <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!...'},
      ],
    },
    {
      name: 'Thông tin chuyển khoản', items: [
        {key: 'config_card_holder', type: 'text', name: 'Tên tài khoản'},
        {key: 'config_bank_number', type: 'default', name: 'Số tài khoản'},
        {key: 'config_bank_name', type: 'text', name: 'Tên ngân hàng'},
      ],
    },
    {
      name: 'Mạng xã hội', items: [
        {key: 'config_facebook_url', type: 'default', name: 'Facebook Url'},
        {key: 'config_youtube_url', type: 'default', name: 'Youtube Url'},
        {key: 'config_instagram_url', type: 'default', name: 'Instagram Url'},
      ],
    },
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: SettingsRepository) {
    super(router, security, state, repository);
  }

  // Override fn
  protected getData(cb?: Function, loading?: boolean): void {
    if (loading !== false) this.data.loading = true;
    this.repository.all(false).then((res: any) => {
        this.data.items = [];
        _.forEach(this.settings, (settings) => {
          const items: any = {name: settings.name, items: []};
          _.forEach(settings.items, (st: {key: string, type: string, name: string, note?: string, placeholder?: string}) => {
            let item: any = {};
            if (res.data.hasOwnProperty(st.key)) {
              item = {key: st.key, type: st.type, name: st.name, note: st.note ? st.note : '', placeholder: st.placeholder ? st.placeholder : '', value: res.data[st.key]};
            } else {
              item = {key: st.key, type: st.type, name: st.name, note: st.note ? st.note : '', placeholder: st.placeholder ? st.placeholder : '', value: ''};
            }
            if (item.type === 'boolean') {
              item.value = Boolean(parseInt(item.value, 0));
            } else if (item.type === 'image') {
              item.thumb_url = res.data[st.key + '_thumb_url'] ? res.data[st.key + '_thumb_url'] : '';
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

  ngOnDestroy() {
    super.destroy();
  }

  ngAfterViewInit() {
    setTimeout(() => this.getData(), 200);
  }

  changeProp(item: any): void {
    this.repository.create({key: item.key, value: item.value ? 1 : 0}).then((res) => {
      console.log(res.data);
    }, (errors) => {
      console.log(errors);
    });
  }

  edit(item: any) {
    this.form.show(item);
  }
}
