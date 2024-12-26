import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { GlobalState } from '../../../@core/utils';
import { Security } from '../../../@core/security';
import { AppList } from '../../../app.base';
import { SettingFormComponent } from './form/form.component';
import { SettingsRepository } from '../services';

@Component({
  selector: 'ngx-pd-settings',
  templateUrl: 'settings.component.html',
})
export class SettingsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(SettingFormComponent) form: SettingFormComponent;
  repository: SettingsRepository;
  settings: { name: string, items: { key: string, type: 'default' | 'editor_lang' | 'text' | 'textarea' | 'image' | 'boolean' | 'select_theme' | 'link_page' | 'colors' | 'frame', name: string, note?: string, placeholder?: string }[] }[] = [
    {
      name: 'Chung', items: [
        {key: 'pd_id', type: 'link_page', name: 'Liên kết đến trang'},
        {key: 'pd_theme_detail', type: 'select_theme', name: 'Giao diện sản phẩm'},
        {key: 'pd_category_status', type: 'boolean', name: 'Hiển thị cột danh mục'},
        {key: 'pd_manufacturer_status', type: 'boolean', name: 'Hiển thị cột thương hiệu'},
        {key: 'pd_cart_status', type: 'boolean', name: 'Giỏ hàng'},
        {key: 'pd_category_primary_color', type: 'colors', name: 'Màu chủ đạo'},
        {key: 'pd_category_bg_image', type: 'image', name: 'Hình nền'},
        {key: 'pd_frame', type: 'frame', name: 'Khung ảnh'},
        {key: 'pd_policy', type: 'editor_lang', name: 'Chính sách'},
        {key: 'pd_info', type: 'editor_lang', name: 'Thông tin thêm'},
        {key: 'pd_banner', type: 'image', name: 'Banner quảng cáo'},

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
          _.forEach(settings.items, (st: { key: string, type: string, name: string, note?: string, placeholder?: string }) => {
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
