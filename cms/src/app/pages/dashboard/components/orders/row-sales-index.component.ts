import { Component, Input } from '@angular/core';

@Component({
  selector: 'ngx-db-com-row-sales-index',
  templateUrl: './row-sales-index.component.html',
})
export class ComRowSalesIndexComponent {
  statData: any = {
    order_total: {last: 0, now: 0, percent: 0},
    order_cancel_total: {last: 0, now: 0, percent: 0},
    visit_total: {last: 0, now: 0, percent: 0},
    view_total: {last: 0, now: 0, percent: 0},
    conversion_total: {last: 0, now: 0, percent: 0},
    gpm_total: {last: 0, now: 0, percent: 0},
    affiliate_total: {last: 0, now: 0, percent: 0},
  };

  @Input() set data(data: any) {
    this.statData = _.extend(this.statData, data);
  }

  constructor() {
  }
}
