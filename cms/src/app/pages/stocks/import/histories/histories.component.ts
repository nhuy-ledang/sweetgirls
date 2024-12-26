import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { CookieVar } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { TicketsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-sto-imp-histories',
  templateUrl: './histories.component.html',
})
export class ImpHistoriesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  repository: TicketsRepository;
  columnList = [
    {id: 'name', name: 'Mã', checkbox: true, disabled: false},
    {id: 'date', name: 'Ngày nhập', checkbox: true, disabled: false},
    {id: 'note', name: 'Ghi chú', checkbox: true, disabled: false},
    {id: 'total', name: 'Tổng tiền', checkbox: true, disabled: false},
    {id: 'supervisor', name: 'Người phụ trách', checkbox: true, disabled: false},
    {id: 'approve', name: 'Trạng thái', checkbox: true, disabled: false},
    {id: 'created_at', name: 'Ngày tạo', checkbox: true, disabled: false},
  ];

  constructor(router: Router, security: Security, state: GlobalState, repository: TicketsRepository, protected _cookie: CookieVar) {
    super(router, security, state, repository);
  }

  ngOnInit(): void {
    this.columnInt(this._cookie, 'sto_exp_histories');
    this.data.data = {q: '', embed: 'products,stock'};
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    // setTimeout(() => this.getData(), 200);
  }

  onConfirm(data: any): void {
    console.log(data);
  }
}
