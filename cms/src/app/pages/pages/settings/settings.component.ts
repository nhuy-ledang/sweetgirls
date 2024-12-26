import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { GlobalState } from '../../../@core/utils';
import { Security } from '../../../@core/security';
import { AppList } from '../../../app.base';
import { SettingsRepository } from '../services';
import { SettingFormComponent } from './form/form.component';
import { SettingFrmTitleComponent } from './frm-title/frm-title.component';

@Component({
  selector: 'ngx-pg-settings',
  templateUrl: 'settings.component.html',
})
export class SettingsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(SettingFormComponent) form: SettingFormComponent;
  @ViewChild(SettingFrmTitleComponent) frmTitle: SettingFrmTitleComponent;
  repository: SettingsRepository;
  settings: {id?: string, name: string, items: {key: string, type: 'default'|'editor_lang'|'text'|'number'|'textarea'|'image'|'boolean'|'header_theme'|'footer_theme'|'header_cont'|'footer_cont'|'style_transform'|'colors'|'button_theme'|'dot_theme'|'arrow_theme', name: string, note?: string, placeholder?: string}[]}[] = [
    {
      name: 'Header', items: [
        {key: 'pg_header_theme', type: 'header_theme', name: 'Giao diện header'},
        {key: 'pg_header_cont', type: 'header_cont', name: 'Container header'},
        {key: 'pg_header_top_status', type: 'boolean', name: 'Hiển thị header top'},
        {key: 'pg_header_bg_color', type: 'colors', name: 'Màu nền 1'},
        {key: 'pg_header_text_color', type: 'colors', name: 'Màu chữ 1'},
        {key: 'pg_header_bg_color2', type: 'colors', name: 'Màu nền 2'},
        {key: 'pg_header_text_color2', type: 'colors', name: 'Màu chữ 2'},
        {key: 'pg_submenu_width', type: 'number', name: 'Chiều rộng Submenu Item', placeholder: '100px'},
        {key: 'pg_logo_invert', type: 'boolean', name: 'Logo trắng'},
        {key: 'pg_logo_height', type: 'number', name: 'Chiều cao logo', placeholder: '55px'},
      ],
    },
    {
      name: 'Footer', items: [
        {key: 'pg_footer_theme', type: 'footer_theme', name: 'Giao diện footer'},
        {key: 'pg_footer_cont', type: 'footer_cont', name: 'Container footer'},
        {key: 'pg_footer_bg', type: 'image', name: 'Background'},
        {key: 'pg_footer_logo_invert', type: 'boolean', name: 'Logo trắng'},
        {key: 'pg_footer_bg_color', type: 'colors', name: 'Màu nền'},
        {key: 'pg_footer_text_color', type: 'colors', name: 'Màu chữ'},
        {key: 'pg_footer_primary_color', type: 'colors', name: 'Màu chủ đạo'},
      ],
    },
    {
      name: 'Container', items: [
        {key: 'pg_container', type: 'number', name: 'Mặc định', placeholder: '1200px'},
        {key: 'pg_container_sm', type: 'number', name: 'Nhỏ', placeholder: '1000px'},
        {key: 'pg_container_md', type: 'number', name: 'Trung bình', placeholder: '1400px'},
        {key: 'pg_container_lg', type: 'number', name: 'Lớn', placeholder: '90%'},
      ],
    },
    {
      id: 'title', name: 'Tiêu đề', items: [
        {key: 'pg_title_theme', type: 'default', name: 'Kiểu tiêu đề'},
        {key: 'pg_title_icon', type: 'image', name: 'Icon tiêu đề'},
        {key: 'pg_title_font', type: 'default', name: 'Font tiêu đề'},
        {key: 'pg_title_sub_font', type: 'default', name: 'Font tiêu đề phụ'},
      ],
    },
    {
      name: 'Button', items: [
        {key: 'pg_button_theme', type: 'button_theme', name: 'Kiểu button'},
      ],
    },
    {
      name: 'Dot', items: [
        {key: 'pg_dot_theme', type: 'dot_theme', name: 'Kiểu dot'},
      ],
    },
    {
      name: 'Arrow', items: [
        {key: 'pg_arrow_theme', type: 'arrow_theme', name: 'Kiểu arrow'},
      ],
    },
    {
      name: 'Desktop', items: [
        {key: 'pg_heading_size', type: 'number', name: 'Heading Size', placeholder: '48px'},
        {key: 'pg_heading_margin', type: 'number', name: 'Heading Margin', placeholder: '30px'},
        {key: 'pg_heading_line', type: 'number', name: 'Heading Line Height', placeholder: '1.4'},
        {key: 'pg_heading_transform', type: 'style_transform', name: 'Heading Transform'},
        {key: 'pg_title_size', type: 'number', name: 'Title Size', placeholder: '48px'},
        {key: 'pg_title_margin', type: 'number', name: 'Title Margin', placeholder: '30px'},
        {key: 'pg_title_line', type: 'number', name: 'Title Line Height', placeholder: '1.4'},
        {key: 'pg_title_transform', type: 'style_transform', name: 'Title Transform'},
        {key: 'pg_description_size', type: 'number', name: 'Description Size', placeholder: '18px'},
        {key: 'pg_description_margin', type: 'number', name: 'Description Margin', placeholder: '15px'},
        {key: 'pg_description_line', type: 'number', name: 'Description Line Height', placeholder: '1.4'},
        {key: 'pg_container_space_top', type: 'number', name: 'Container Space Top', placeholder: '85px'},
        {key: 'pg_container_space_bottom', type: 'number', name: 'Container Space Bottom', placeholder: '85px'},
      ],
    },
    {
      name: 'Mobile', items: [
        {key: 'pg_heading_size_mb', type: 'number', name: 'Heading Size', placeholder: '41'},
        {key: 'pg_heading_margin_mb', type: 'number', name: 'Heading Margin', placeholder: '26px'},
        {key: 'pg_heading_line_mb', type: 'number', name: 'Heading Line Height', placeholder: '1.4'},
        {key: 'pg_heading_transform_mb', type: 'style_transform', name: 'Heading Transform'},
        {key: 'pg_title_size_mb', type: 'number', name: 'Title Size', placeholder: '41'},
        {key: 'pg_title_margin_mb', type: 'number', name: 'Title Margin', placeholder: '26px'},
        {key: 'pg_title_line_mb', type: 'number', name: 'Title Line Height', placeholder: '1.4'},
        {key: 'pg_title_transform_mb', type: 'style_transform', name: 'Title Transform'},
        {key: 'pg_description_size_mb', type: 'number', name: 'Description Size', placeholder: '15px'},
        {key: 'pg_description_margin_mb', type: 'number', name: 'Description Margin', placeholder: '13px'},
        {key: 'pg_description_line_mb', type: 'number', name: 'Description Line Height', placeholder: '1.4'},
        {key: 'pg_container_space_top_mb', type: 'number', name: 'Container Space Top', placeholder: '50px'},
        {key: 'pg_container_space_bottom_mb', type: 'number', name: 'Container Space Bottom', placeholder: '50px'},
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
          if (settings.id) items.id = settings.id;
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

  parentEdit(item: any): any {
    if (!item.id) return;
    console.log(item);
    if (item.id === 'title') {
      this.frmTitle.show(item);
    }
  }

  edit(item: any) {
    this.form.show(item);
  }
}
