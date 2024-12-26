import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { UsrsRepository } from '../../../../@core/repositories';
import { Usr } from '../../../../@core/entities';
import { AppForm } from '../../../../app.base';
import { UsrRolesRepository } from '../../shared/services';

@Component({
  selector: 'ngx-usr-dlg-roles',
  templateUrl: './dlg-roles.component.html',
})

export class UseDlgRolesComponent extends AppForm implements OnInit, OnDestroy {
  repository: UsrsRepository;
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();
  info: Usr;
  roleList: any[] = [];
  roleData: { loading: boolean, items: any[] } = {loading: false, items: []};

  constructor(router: Router, security: Security, state: GlobalState, repository: UsrsRepository, private _roles: UsrRolesRepository) {
    super(router, security, state, repository);
  }

  private updateCheckboxes(): void {
    this.roleList = _.cloneDeep(this.roleData.items);
    if (this.info && this.info.roles) {
      _.forEach(this.roleList, (item: any) => {
        item.checked = !!_.find(this.info.roles, {id: item.id});
      });
      console.log(this.roleList);
    }
  }

  private getAllRole(): void {
    this.roleData.loading = true;
    this._roles.all().then((res: any) => {
      this.roleData.loading = false;
      this.roleData.items = res.data;
      this.updateCheckboxes();
    }), (errors: any) => {
      this.roleData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(info: Usr): void {
    console.log(info);
    this.info = info;
    this.roleList = [];
    if (!this.roleData.items.length) {
      this.getAllRole();
    } else {
      this.updateCheckboxes();
    }
    this.modal.show();
  }

  hide(): void {
    this.modal.hide();
    super.hide();
  }

  onChange(item: any): void {
    console.log(item);
  }

  onSubmit(params: any): void {
    const roles = [];
    _.forEach(this.roleList, (item: any) => {
      if (item.checked) roles.push(item.id);
    });
    console.log(roles);
    if (this.info) {
      this.submitted = true;
      this.repository.syncRoles(this.info.id, {role_ids: roles.join(',')}).then((res) => this.handleSuccess(_.extend({edited: true}, res.data)), (errors) => this.handleError(errors));
    }
  }
}
