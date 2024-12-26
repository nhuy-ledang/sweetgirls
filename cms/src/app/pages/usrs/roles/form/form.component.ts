import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { UsrRolesRepository } from '../../shared/services';
import { AppForm } from '../../../../app.base';

@Component({
  selector: 'ngx-usr-role-form',
  templateUrl: './form.component.html',
})

export class RoleFormComponent extends AppForm implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: any|boolean;
  permissionList: any[] = [
    {
      id: 'dashboard', name: 'Bảng tin', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'db_sale', name: 'Bảng tin > Kế toán', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'db_marketing', name: 'Bảng tin > Marketing', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'db_networks', name: 'Bảng tin > Networks', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'business', name: 'Kinh doanh', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'products', name: 'Sản phẩm', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'exchange', name: 'Giao dịch', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'stocks', name: 'Kho vận', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'orders', name: 'Giao dịch > Đơn hàng', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'exchange_settings', name: 'Giao dịch > Cài đặt', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'exchange_webhook', name: 'Giao dịch > Webhook', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'users', name: 'Khách hàng', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'user_list', name: 'Khách hàng > Danh sách', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'user_ranks', name: 'Khách hàng > Hạng thành viên', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'user_support', name: 'Khách hàng > Yêu cầu hỗ trợ', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'user_settings', name: 'Khách hàng > Cài đặt', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'marketing', name: 'Marketing', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'affiliate', name: 'Marketing > Affiliates', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'coupons', name: 'Marketing > Giảm giá', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'vouchers', name: 'Marketing > Voucher', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'wheels', name: 'Marketing > Vòng quay may mắn', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'emails', name: 'Marketing > Emails', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'public', name: 'Truyền thông', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'pages', name: 'Truyền thông > Trang', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'blogs', name: 'Truyền thông > Blogs', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'menus', name: 'Menu', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'medias', name: 'Đa phương tiện', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'informations', name: 'Thông tin', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'designs', name: 'Thiết kế', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'localizations', name: 'Địa phương', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'decentralizations', name: 'Phân quyền', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },
    {
      id: 'administrator', name: 'Phân quyền > Quản trị viên', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'admin_group', name: 'Phân quyền > Nhóm quản trị', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    {
      id: 'user_role', name: 'Phân quyền > Vai trò người dùng', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Thêm'},
        {id: 'edit', name: 'Sửa'},
        {id: 'delete', name: 'Xóa'},
      ],
    },
    /*{
      id: 'language', name: 'Ngôn ngữ', permissions: [
        {id: 'view', name: 'Xem'},
      ],
    },*/
    {
      id: 'system', name: 'Hệ thống', permissions: [
        {id: 'view', name: 'Xem'},
        {id: 'create', name: 'Sửa'},
      ],
    },
  ];
  controls: {
    name?: AbstractControl,
    permissions?: FormGroup,
  };
  perForm: FormGroup;

  constructor(fb: FormBuilder, router: Router, security: Security, state: GlobalState, repository: UsrRolesRepository) {
    super(router, security, state, repository);
    this.form = fb.group({
      name: ['', Validators.compose([Validators.required])],
      permissions: fb.group({}),
    });
    this.controls = this.form.controls;
    this.perForm = this.controls.permissions;
    this.fb = fb;
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  addControls(): void {
    const permissions: any = this.info ? this.info.permissions : {};
    console.log(permissions);
    // Remove controls
    _.each(this.controls.permissions.controls, (val, key) => {
      this.controls.permissions.removeControl(key);
    });
    // Add controls
    // this.controls.permissions.addControl(item.id, new FormControl(item.checked));
    _.forEach(this.permissionList, (item: any) => {
      const controlsConfig: any = {};
      _.forEach(item.permissions, (crud: any) => {
        controlsConfig[crud.id] = [permissions && permissions[item.id] && _.includes(permissions[item.id], crud.id)];
      });
      this.controls.permissions.addControl(item.id, this.fb.group(controlsConfig));
    });
  }

  protected setInfo(info: any): void {
    console.log(info);
    _.each(this.controls, (val, key) => {
      if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue(info.hasOwnProperty(key) && info[key] !== null ? info[key] : '');
    });
    this.info = info;
  }

  show(info?: any): void {
    this.resetForm(this.form);
    this.info = false;
    // Remove controls
    _.each(this.controls.permissions.controls, (val, key) => {
      this.controls.permissions.removeControl(key);
    });
    if (info) {
      this.setInfo(info);
    } else {
      _.each(this.controls, (val, key) => {
        if (this.controls.hasOwnProperty(key) && this.controls[key] instanceof FormControl) this.controls[key].setValue('');
      });
    }
    this.addControls();
    this.modal.show();
  }

  hide(): void {
    this.form.reset();
    this.modal.hide();
    super.hide();
  }

  onSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      const permissions = {};
      _.each(params.permissions, (module, key) => {
        permissions[key] = [];
        _.each(module, (v, k) => {
          if (v) permissions[key].push(k);
        });
      });
      const newParams = {name: params.name, permissions: permissions};
      this.submitted = true;
      if (this.info) {
        this.repository.update(this.info, newParams).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
      } else {
        this.repository.create(newParams).then((res) => this.handleSuccess(res.data), (errors) => this.handleError(errors));
      }
      console.log(newParams);
    }
    console.log(params);
  }
}
