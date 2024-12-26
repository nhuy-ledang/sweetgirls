import { Component, OnDestroy, AfterViewInit, Input } from '@angular/core';
import { ChartOptions, ChartType } from 'chart.js';
import { Color, Label, MultiDataSet } from 'ng2-charts';

// https://www.chartjs.org/docs/2.9.4/charts/line.html
// https://stackblitz.com/edit/ng2-charts-doughnut-template?file=src%2Fapp%2Fapp.component.ts
// https://github.com/valor-software/ng2-charts
@Component({
  selector: 'ngx-chartjs-doughnut',
  template: `
    <div style="display: block;">
      <canvas baseChart width="400" height="400" [chartType]="chartType" [data]="chartData" [labels]="chartLabels" [colors]="chartColors" [legend]="chartLegend" [options]="chartOptions"></canvas>
    </div>`,
})
export class ChartjsDoughnutComponent implements OnDestroy, AfterViewInit {
  public chartType: ChartType = 'doughnut';
  public chartData: MultiDataSet = [[1, 1, 1, 1, 1]];
  public chartLabels: Label[] = ['', '', '', '', ''];
  // public chartColors: Color[] = [{backgroundColor: ['#17499a', '#ffc02c', '#3986ff', '#00cfbc', '#ff4a65', '#bcbcbc', '#ff6600']}];
  public chartColors: Color[] = [{backgroundColor: ['#d50000', '#e67c73', '#f4511e', '#f6bf26', '#33b679', '#0b8043', '#039be5', '#3f51b5', '#7986cb', '#8e24aa', '#616161', '#4285f4']}];
  public chartOptions: ChartOptions|any = {responsive: true, cutoutPercentage: 70};
  @Input() chartLegend: boolean = false;

  @Input() set data(data: {labels: string[], colors?: string[], data: number[]}) {
    this.chartLabels = data.labels ? data.labels : [];
    if (data.colors && data.colors.length) this.chartColors = [{backgroundColor: data.colors}];
    const chartData = [];
    chartData.push(_.extend([], data.data));
    this.chartData = chartData;
  }

  constructor() {
  }

  ngAfterViewInit(): void {
  }

  ngOnDestroy(): void {
  }
}
