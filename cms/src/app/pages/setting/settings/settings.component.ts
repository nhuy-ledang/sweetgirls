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
      name: 'Affiliate', items: [
        {key: 'config_affiliate_note', type: 'editor_lang', name: 'Lưu ý đăng ký aff'},
        {key: 'config_affiliate_notify', type: 'editor_lang', name: 'Thông báo đến aff'},
      ],
    },
    // {
    //   name: 'OnePay', items: [
    //     {key: 'config_onepay_terms', type: 'editor_lang', name: 'Điều khoản và điều kiện'},
    //   ],
    // },
    // {
    //   name: 'Máy chủ', items: [
    //     {key: 'config_maintenance', type: 'boolean', name: 'Chế độ bảo trì'},
    //     {key: 'config_tracking_day', type: 'number', name: 'Thời gian lưu cookie', note: 'Số ngày lưu cookie >= 1'},
    //     {key: 'config_suffix_url', type: 'default', name: 'Thêm đuôi Seo Url', note: 'VD: html, tpl, php, ...'},
    //     {key: 'config_mail_alert_email', type: 'default_textarea', name: 'Mail nhận thông báo', note: 'Các mail cách nhau bằng dấu \' , \''},
    //   ],
    // },
    {
      name: 'Cài đặt website', items: [
        {key: 'config_meta_title', type: 'default', name: 'Tiêu đề Web'},
        {key: 'config_meta_description', type: 'default', name: 'Meta Tag Description'},
        {key: 'config_meta_keyword', type: 'default', name: 'Meta Tag Keywords'},
        {key: 'config_icon', type: 'image', name: 'Icon/Favicon'},
        {key: 'config_logo', type: 'image', name: 'Logo'},
        {key: 'config_image', type: 'image', name: 'Hình ảnh'},
        {key: 'config_bg_login', type: 'image', name: 'Background Login'},
        // {key: 'config_icon_marker', type: 'image', name: 'Icon Marker <br><small>(Hiển thị ở Map)</small>', note: '50 x 50px'},
        // {key: 'config_icon_vendor', type: 'image', name: 'Icon Đại Lý <br><small>(Hiển thị ở Footer)</small>'},
        // {key: 'config_watermark_lg', type: 'image', name: 'Watermark lg', note: 'Height: 128px, PNG file'},
        // {key: 'config_watermark_md', type: 'image', name: 'Watermark md', note: 'Height: 64px, PNG file'},
        // {key: 'config_watermark_sm', type: 'image', name: 'Watermark sm', note: 'Height: 32px, PNG file'},
      ],
    },
    {
      name: 'Thông tin website', items: [
        {key: 'config_owner', type: 'text', name: 'Chủ sở hữu'},
        {key: 'config_name', type: 'text', name: 'Tên công ty'},
        {key: 'config_address', type: 'text', name: 'Địa chỉ'},
        {key: 'config_tax', type: 'text', name: 'Mã số thuế'},
        {key: 'config_email', type: 'default', name: 'Email'},
        {key: 'config_hotline', type: 'default', name: 'Hotline'},
        {key: 'config_telephone', type: 'default', name: 'Phone'},
        {key: 'config_fax', type: 'default', name: 'Số Fax'},
        {key: 'config_open', type: 'default', name: 'Thời gian mở cửa', note: 'Nhập giờ mở cửa / hoạt động.'},
        {key: 'config_comment', type: 'editor_lang', name: 'Comment/Slogan'},
        {key: 'config_copyright', type: 'default', name: 'Copyright'},
        {key: 'config_googlemap_latlng', type: 'default', name: 'Google Map', note: '(VD: 10.8082147, 106.70713978)'},
        {key: 'config_googlemap_embed', type: 'default', name: 'Google Map Link', note: '(VD: <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!...'},
        {key: 'config_scripts', type: 'textarea', name: 'Scripts'},
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
      name: 'Giao diện', items: [
        {key: 'config_theme', type: 'default', name: 'Giao diện mặc định'},
        {key: 'config_theme_directory', type: 'default', name: 'Thư mục giao diện'},
        {key: 'config_color_primary', type: 'select_color', name: 'Màu chính'},
        {key: 'config_color_secondary', type: 'select_color', name: 'Màu thứ 2'},
        {key: 'config_color_success', type: 'select_color', name: 'Màu thứ 3'},
      ],
    },
    {
      name: 'App', items: [
        {key: 'config_appstore', type: 'default', name: 'App Store'},
        {key: 'config_chplay', type: 'default', name: 'Google play'},
      ],
    },
    {
      name: 'Vòng quay may mắn', items: [
        {key: 'config_wheel', type: 'image', name: 'Vòng quay'},
        {key: 'config_wheel_bg', type: 'image', name: 'Ảnh nền'},
        {key: 'config_wheel_order_status', type: 'boolean', name: 'Hiện khi hoàn tất ĐH'},
        {key: 'config_wheel_order_total', type: 'default', name: 'Giá trị hóa đơn'},
      ],
    },
    {
      name: 'Tính năng', items: [
        {key: 'config_user_invite_status', type: 'boolean', name: 'Giới thiệu bạn bè'},
        {key: 'config_login_popup', type: 'boolean', name: 'Popup đăng nhập'},
        {key: 'config_login_first', type: 'boolean', name: 'Đăng nhập trước khi mua hàng'},
      ],
    },
    {
      name: 'Đăng nhập với xã hội', items: [
        {key: 'config_google_client_id', type: 'default', name: 'Google App Id'},
        {key: 'config_google_api_key', type: 'default', name: 'Google Api Key'},
        {key: 'config_google_client_secret', type: 'default', name: 'Google Client Secret'},
        {key: 'config_facebook_app_id', type: 'default', name: 'Facebook App Id'},
        {key: 'config_facebook_version', type: 'default', name: 'Facebook Version'},
      ],
    },
    {
      name: 'Mạng xã hội', items: [
        {key: 'config_contact_status', type: 'boolean', name: 'Chat nhanh'},
        {key: 'config_facebook_url', type: 'default', name: 'Facebook Url'},
        {key: 'config_zalo_url', type: 'default', name: 'Zalo Url'},
        {key: 'config_youtube_url', type: 'default', name: 'Youtube Url'},
        {key: 'config_instagram_url', type: 'default', name: 'Instagram Url'},
        {key: 'config_linkedin_url', type: 'default', name: 'Linkedin Url'},
        {key: 'config_alibaba_url', type: 'default', name: 'Alibaba Url'},
        {key: 'config_tiktok_url', type: 'default', name: 'Tiktok Url'},
      ],
    },
    {
      name: 'Ngôn ngữ', items: [
        {key: 'config_language_status', type: 'boolean', name: 'Chế độ đa ngữ'},
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
