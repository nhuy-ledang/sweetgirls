import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { GlobalState } from '../../../@core/utils';
import { Security } from '../../../@core/security';
import { AppList } from '../../../app.base';
import { OrderSettingsRepository } from '../services';
import { OrderSettingFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-ord-settings',
  templateUrl: 'settings.component.html',
})
export class OrderSettingsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(OrderSettingFormComponent) form: OrderSettingFormComponent;
  repository: OrderSettingsRepository;
  settings: {name: string, items: {key: string, type: 'default'|'default_textarea'|'number'|'editor_lang'|'text'|'textarea'|'image'|'list_image'|'banners'|'boolean', name: string, note?: string}[]}[] = [
    {
      name: 'Đơn hàng', items: [
        {key: 'config_ord_invoice_prefix', type: 'default', name: 'Tiền tố Hóa đơn', note: 'Thiết lập Tiền tố cho Hóa đơn hàng (Ví dụ: 2015-00). Số thứ tự Hóa đơn hàng sẽ bắt đầu từ số 1.'},
        {key: 'config_ord_success', type: 'editor_lang', name: 'Hoàn tất mua hàng'},
      ],
    },
    {
      name: 'Vận chuyển', items: [
        {key: 'config_ord_fullname', type: 'default', name: 'Người gửi'},
        {key: 'config_ord_address', type: 'default', name: 'Địa chỉ'},
        {key: 'config_ord_phone', type: 'default', name: 'Số điện thoại'},
        {key: 'config_ord_email', type: 'default', name: 'Email'},
        // {key: 'config_ord_province', type: 'default', name: 'ID Tỉnh/Thành phố gửi hàng'},
        // {key: 'config_ord_district', type: 'default', name: 'ID Quận/Huyện gửi hàng'},
        // {key: 'config_ord_ward', type: 'default', name: 'ID Phường/Xã gửi hàng'},
        // {key: 'config_ord_latitude', type: 'default', name: 'Latitude'},
        // {key: 'config_ord_longitude', type: 'default', name: 'Longitude'},
      ],
    },
    // {
    //   name: 'Thông báo', items: [
    //     {key: 'config_ord_notification', type: 'default_textarea', name: 'Chương trình ưu đãi'},
    //   ],
    // },
    // {
    //   name: 'Coin', items: [
    //     {key: 'config_ord_cns_promotion', type: 'editor_lang', name: 'Chương trình Coin'},
    //   ],
    // },
    // {
    //   name: 'Giới thiệu bạn bè', items: [
    //     {key: 'config_ord_inv_promotion', type: 'editor_lang', name: 'Chương trình giới thiệu bạn bè'},
    //   ],
    // },
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: OrderSettingsRepository) {
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
