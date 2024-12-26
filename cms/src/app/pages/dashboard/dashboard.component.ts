import { Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { AppBase } from '../../app.base';
import { Router } from '@angular/router';
import { Security } from '../../@core/security';
import { GlobalState } from '../../@core/utils';
import { TabOrdersComponent } from './tabs/orders.component';
import { TabMarketingComponent } from './tabs/marketing.component';

@Component({
  selector: 'ngx-dashboard',
  templateUrl: './dashboard.component.html',
})
export class DashboardComponent extends AppBase implements OnInit, OnDestroy {
  @ViewChild(TabOrdersComponent) TabOrders: TabOrdersComponent;
  @ViewChild(TabMarketingComponent) TabMarketing: TabMarketingComponent;
  daterange: {mode: 'day'|'week'|'month'|'year'|'customRange'|'', label: string, start?: any, end?: any, start_date?: string, end_date?: string} = {mode: '', label: ''};
  bsData: {mode: 'day'|'week'|'month'|'year'|'customRange'|'', label: string, value: Date, currDate: Date, start: Date, end: Date, start_date: string, end_date: string} = {mode: '', label: 'Hôm nay', value: new Date(), currDate: new Date(), start: new Date(), end: new Date(), start_date: '', end_date: ''};
  bsInlineRangeValue: Date[] = [new Date(), new Date()];

  constructor(router: Router, security: Security, state: GlobalState) {
    super(router, security, state);
    this.bsSelectMode('month');
    this.bsApply();
  }

  ngOnInit(): void {
  }

  ngOnDestroy() {
  }

  tabs: any = {accountant: true, marketing: false, networks: (!this.isSuperAdmin() && this.isCRUD('db_networks', 'view') && !this.isCRUD('db_sale', 'view') && !this.isCRUD('db_marketing', 'view'))};

  onSelectTab(tabActive: string): void {
    _.each(this.tabs, (v, k) => {
      if (k !== tabActive) this.tabs[k] = false;
    });
    this.tabs[tabActive] = true;
  }

  private bsUpdate(): void {
    let start: Date;
    let end: Date;
    if (this.bsData.mode === 'customRange') {
      console.log(this.bsInlineRangeValue);
      start = this.bsInlineRangeValue[0];
      end = this.bsInlineRangeValue[1];
      this.bsData.label = start.format('d') + ' - ' + end.format('d mmm, yyyy');
    } else if (this.bsData.mode === 'day') {
      start = this.bsData.currDate;
      end = start;
      this.bsData.label = start.format('d mmm, yyyy');
    } else if (this.bsData.mode === 'week') {
      start = this.bsData.currDate.getFirstDayInWeek();
      end = this.bsData.currDate.getLastDayInWeek();
      this.bsData.label = start.format('d') + ' - ' + end.format('d mmm, yyyy');
    } else if (this.bsData.mode === 'month') {
      start = this.bsData.currDate.getFirstDayInMonth();
      end = this.bsData.currDate.getLastDayInMonth();
      this.bsData.label = start.format('mmm, yyyy');
    } else if (this.bsData.mode === 'year') {
      start = new Date(String(this.bsData.currDate.getFullYear()) + '-01-01');
      end = new Date(String(this.bsData.currDate.getFullYear()) + '-12-31');
      this.bsData.label = start.format('yyyy');
    }
    this.bsData.start = start;
    this.bsData.end = end;
    this.bsData.start_date = start.format('yyyy-mm-dd');
    this.bsData.end_date = end.format('yyyy-mm-dd');
  }

  bsSelectMode(mode: 'day'|'week'|'month'|'year'|'customRange'): void {
    this.bsData.mode = mode;
    this.bsUpdate();
  }

  onBsValueChange($event: Date): void {
    this.bsData.currDate = $event;
    this.bsUpdate();
  }

  onBsRangeValueChange($event: Date[]): void {
    this.bsInlineRangeValue = $event;
    this.bsUpdate();
  }

  bsApply(): void {
    const daterange: any = {mode: this.bsData.mode, label: this.bsData.label, start: new Date(this.bsData.start), end: new Date(this.bsData.end), start_date: this.bsData.start_date, end_date: this.bsData.end_date};
    this.daterange = daterange;
    this.bsData.value = this.bsData.currDate;
  }
}
