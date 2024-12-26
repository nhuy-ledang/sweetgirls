import { AfterViewInit, Component, Input, OnDestroy, OnInit } from '@angular/core';
// import * as moment from 'moment';
import { StatsRepository } from '../../orders/shared/services';

@Component({
  selector: 'ngx-db-tab-orders',
  templateUrl: './orders.component.html',
})
export class TabOrdersComponent implements OnInit, OnDestroy, AfterViewInit {
  daterange: {mode: 'day'|'week'|'month'|'year'|'', label: string, start_date?: string, end_date?: string, start?: any, end?: any} = {mode: '', label: ''};
  stats: any = {total: 0, exhibit: 0, activity: 0, product: 0};
  private tmpStatData: any = {
    sales_index: {
      order_total: {last: 0, now: 0, percent: 0},
      sale_total: {last: 0, now: 0, percent: 0},
      sale_confirm_total: {last: 0, now: 0, percent: 0},
      revenue_total: {last: 0, now: 0, percent: 0},
      visit_total: {last: 0, now: 0, percent: 0},
      view_total: {last: 0, now: 0, percent: 0},
      conversion_total: {last: 0, now: 0, percent: 0},
    },
    potentials: {
      // Tổng giá trị đề xuất báo giá
      proposal_total: {now: 0, last: 0, percent: 0},
      // Tổng giá trị báo giá
      est_total: {now: 0, last: 0, percent: 0},
      // Doanh số không định kỳ dự kiến
      sales_non_recurring_est: {now: 0, last: 0, percent: 0},
      // Doanh số không định kỳ thực tế
      sales_non_recurring_real: {now: 0, last: 0, percent: 0},
      // Doanh thu không định kỳ thực tế
      revenue_non_recurring_real: {now: 0, last: 0, percent: 0},
      // Doanh số định kỳ dự kiến
      sales_recurring_est: {now: 0, last: 0, percent: 0},
      // Tổng doanh số hợp nhất dự kiến
      sales_est: {now: 0, last: 0, percent: 0},
      // Doanh số định kỳ thực tế
      sales_recurring_real: {now: 0, last: 0, percent: 0},
      // Doanh thu định kỳ thực tế
      revenue_recurring_real: {now: 0, last: 0, percent: 0},
      // Tổng doanh số hợp nhất thực tế
      sales_real: {now: 0, last: 0, percent: 0},
      // Tổng doanh thu hợp nhất thực tế
      revenue_real: {now: 0, last: 0, percent: 0},
    },
    incomes: {
      unpaid: {now: 0, last: 0, percent: 0},
      unpaid_del: {now: 0, last: 0, percent: 0},
      unpaid_soon: {now: 0, last: 0, percent: 0},
      unpaid_ov: {now: 0, last: 0, percent: 0},
    },
    sales_rates: {
      funnel: { // Biểu đồ phễu bán hàng
        labels: ['Đầu mối', 'Lead', 'Cơ hội', 'Chốt deal'],
        colors: ['#039be5', '#e67c73', '#f4511e', '#33b679'],
        data: [0, 0, 0, 0],
      },
      lead: {items: [], data: []}, // Biểu đồ tròn tỷ trong Lead
      lead_status: {items: [], data: []}, // Biểu đồ tròn tình trạng lead
      category: {items: [], data: []}, // Tỷ trọng bán hàng theo danh mục
      follower: {items: [], data: []}, // Tỷ trọng bán hàng theo phụ trách
      business: {items: [], data: []}, // Tỷ trọng bán hàng theo mô hình kinh doanh
      upsell: {items: [], data: []},  // MỚI - UPSELL - ĐỊNH KỲ
      methods: {items: [], data: []},  // PHƯƠNG THỨC BÁN
    },
    sales_charts: {
      lead_volatility: {mode: 'year', data: []}, // Biểu đồ biến động cơ hội
      sales_results: {mode: 'year', data: []}, // Biểu đồ kết quả doanh số/doanh thu
      sales_period: {mode: 'year', data: []}, // Biểu đồ doanh số theo kỳ
      sales_products: {mode: 'year', data: []}, // Biểu đồ kết quả kinh doanh theo danh mục sản phẩm
      sales_methods: {mode: 'category', data: [], labels: []}, // Biểu đồ kết quả kinh doanh theo phương thức bán
    },
  };
  statData: any = {};
  filterData: any = {};

  // units = {now: '', last: ''};

  @Input() set filter(daterange: {mode: 'day'|'week'|'month'|'year', label: string, start_date: string, end_date: string}) {
    this.daterange = _.extend(this.daterange, daterange);
    this.filterData = {mode: daterange.mode, start_date: daterange.start_date, end_date: daterange.end_date};
    /*// console.log(this.filterData);
    const start = new Date(daterange.start_date);
    const end = new Date(daterange.end_date);
    // this.statData.year = parseInt(start.format('yyyy'), 0);
    if (daterange.mode === 'day') {
      this.units.now = start.format('d mmm, yyyy');
      this.units.last = moment(start).subtract(1, 'day').toDate().format('d mmm, yyyy');
    } else if (daterange.mode === 'week') {
      this.units.now = start.format('d') + ' - ' + end.format('d mmm, yyyy');
      const last_start = moment(start).subtract(7, 'days').toDate();
      const last_end = moment(last_start).add(6, 'days').toDate();
      this.units.last = last_start.format('d') + ' - ' + last_end.format('d mmm, yyyy');
    } else if (daterange.mode === 'month') {
      this.units.now = start.format('mmm, yyyy');
      this.units.last = moment(start).subtract(1, 'month').toDate().format('mmm, yyyy');
    } else { // if (daterange.mode === 'year') {
      this.units.now = start.format('yyyy');
      this.units.last = moment(start).subtract(1, 'year').toDate().format('yyyy');
    }*/

    this.getStats();
  }

  constructor(private _stats: StatsRepository) {
    this.statData = _.cloneDeep(this.tmpStatData);
  }

  private getStatOverview(): void {
    this._stats.getOverview({data: this.filterData}).then((res) => {
        this.statData = _.extend(_.cloneDeep(this.tmpStatData), res.data);
        const statData = _.cloneDeep(this.tmpStatData);
      statData.sales_index = _.extend(statData.sales_index, res.data.sales_index);
      statData.sales_charts = _.extend(statData.sales_charts, res.data.sales_charts);
      /*statData.potentials = _.extend(statData.potentials, res.data.potentials);
      statData.incomes = _.extend(statData.incomes, res.data.incomes);
      statData.sales_rates = _.extend(statData.sales_rates, res.data.sales_rates);
      */
        this.statData = statData;
        console.log(statData);
        // console.log(this.statData.sales_charts);
        // console.log(this.statData.overview);
      }, (errors: any) => {
        console.log(errors);
      },
    );
  }

  private getStats(): void {
    this.getStatOverview();
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
  }

  ngAfterViewInit() {
    // setTimeout(() => this.getStats(), 200);
  }

  exportExcelByDate(): void {
    const href = this._stats.exportExcel(this.filterData);
    location.href = href;
  }
}
