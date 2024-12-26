import { Component, Input } from '@angular/core';
import { ChartDataSets, ChartOptions } from 'chart.js';
import { Color, Label } from 'ng2-charts';
import { Helper } from '../../@core/helpers';

// https://www.chartjs.org/docs/2.9.4/charts/line.html
// https://stackblitz.com/edit/ng2-charts-line-template?file=src%2Fapp%2Fapp.component.html
// https://github.com/valor-software/ng2-charts
@Component({
  selector: 'ngx-chartjs-line',
  template: `
    <div style="display: block;">
      <canvas baseChart [attr.width]="width" [attr.height]="height" [chartType]="chartType" [datasets]="chartData" [labels]="chartLabels" [colors]="chartColors" [legend]="chartLegend" [options]="chartOptions"></canvas>
    </div>`,
})
export class ChartjsLineComponent {
  public chartType = 'line';
  public chartData: ChartDataSets[] = [
    {data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]},
    {data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]},
  ];
  public chartLabels: Label[] = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
  public chartOptions: ChartOptions|any = {
    responsive: true,
    gradientPosition: {y1: 100},
    scales: {
      yAxes: [{
        gridLines: {color: '#e7eaf3', drawBorder: false, zeroLineColor: '#e7eaf3'},
        ticks: {
          beginAtZero: true, min: 0, max: 100, stepSize: 20, fontSize: 11, fontColor: '#212529', padding: 0,
          // Include a dollar sign in the ticks
          callback: function(value, index, ticks) {
            return Helper.numFormatter(value, 0);
            /*if (parseInt(value, 0) >= 1000) {
              return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            } else {
              return value;
            }*/
          },
        },
      }],
      xAxes: [{
        gridLines: {display: false, drawBorder: false},
        ticks: {fontSize: 10, fontColor: '#212529', padding: 0},
      }],
    },
    tooltips: {
      // mode: 'index',
      intersect: false,
      callbacks: {
        label: function(context, data) {
          let value: string = context.value;
          const dataLabel = data.datasets[context.datasetIndex].label;
          if (parseInt(value, 0) >= 1000) value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
          return dataLabel ? (dataLabel + ': ' + value) : value;
        },
      },
    },
    hover: {mode: 'nearest', intersect: true},
  };
  public chartColors: Color[] = [
    {
      backgroundColor: ['rgba(66, 133, 244, 0.1)', 'rgba(255, 255, 255, 0.2)'],
      borderColor: '#4285f4',
      borderWidth: 2,
      pointRadius: 0,
      hoverBorderColor: '#4285f4',
      pointBackgroundColor: '#4285f4',
      pointBorderColor: '#fff',
      pointHoverRadius: 0,
    }, {
      backgroundColor: ['rgba(244, 81, 30, 0.1)', 'rgba(255, 255, 255, 0.2)'],
      borderColor: '#f4511e',
      borderWidth: 2,
      pointRadius: 0,
      hoverBorderColor: '#f4511e',
      pointBackgroundColor: '#f4511e',
      pointBorderColor: '#fff',
      pointHoverRadius: 0,
    },
  ];
  @Input() width: number = 600;
  @Input() height: number = 300;
  @Input() chartLegend: boolean = false;

  /**
   * Example
   * @param data [[1,2,3,4,5,6,7,8,9,10,11,12], [1,2,3,4,5,6,7,8,9,10,11,12], ...]
   */
  @Input() set data(data: {mode: 'day'|'week'|'month'|'year', data: {name?: string, color?: string, data: number[]}[], labels: string[]}) {
    if (data.mode === 'year' || !data.labels) {
      this.chartLabels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
    } else {
      this.chartLabels = data.labels;
    }/* else if (opt.mode === 'today' || opt.mode === 'this_week') {
      this.chartLabels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
    } else {
      this.chartLabels = opt.labels;
    }*/
    const chartColors: Color[] = [];
    const chartData: ChartDataSets[] = [];
    let max: number = 0;
    _.forEach(data.data, (it: {name?: string, color?: string, data: number[]}) => {
      const m = _.max(it.data);
      if (max < m) max = m;
      const dataset: ChartDataSets = {data: it.data};
      if (it.name) dataset.label = it.name;
      if (it.color) dataset.borderColor = it.color;
      const borderColor = it.color ? it.color : '#4285f4';
      const chartColor: Color = {borderColor: borderColor, borderWidth: 2, pointRadius: 0, pointHoverRadius: 0};
      const rgb = Helper.hexToRgb(borderColor);
      if (rgb) chartColor.backgroundColor = [`rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.2)`, 'rgba(255, 255, 255, 0.2)'];
      chartColors.push(chartColor);
      chartData.push(dataset);
    });
    if (max < 10) max = 10;
    else if (max < 100) max = 100;
    else if (max < 1000) max = 1000;
    else if (max < 10000) max = 10000;
    else if (max < 100000) max = 100000;
    else if (max < 500000) max = 500000;
    else if (max < 1000000) max = 1000000;
    else if (max < 5000000) max = 5000000;
    else if (max < 10000000) max = 10000000;
    else if (max < 50000000) max = 50000000;
    else if (max < 60000000) max = 60000000;
    else if (max < 70000000) max = 70000000;
    else if (max < 80000000) max = 80000000;
    else if (max < 90000000) max = 90000000;
    else if (max < 100000000) max = 100000000;
    else if (max < 150000000) max = 150000000;
    else if (max < 200000000) max = 200000000;
    else if (max < 250000000) max = 250000000;
    else if (max < 300000000) max = 300000000;
    else if (max < 350000000) max = 350000000;
    else if (max < 400000000) max = 400000000;
    else if (max < 450000000) max = 450000000;
    else if (max < 500000000) max = 500000000;
    else if (max < 600000000) max = 600000000;
    else if (max < 700000000) max = 700000000;
    else if (max < 800000000) max = 800000000;
    else if (max < 900000000) max = 900000000;
    else if (max < 1000000000) max = 1000000000;
    else if (max < 2000000000) max = 2000000000;
    else if (max < 3000000000) max = 3000000000;
    else if (max < 4000000000) max = 4000000000;
    else if (max < 5000000000) max = 5000000000;
    else if (max < 10000000000) max = 10000000000;
    this.chartOptions.scales.yAxes[0].ticks.max = max;
    this.chartOptions.scales.yAxes[0].ticks.stepSize = max / 5;
    this.chartOptions = _.cloneDeep(this.chartOptions);
    this.chartColors = chartColors.length ? chartColors : [{borderColor: '#4285f4', backgroundColor: ['rgba(66, 133, 244, 0.1)', 'rgba(255, 255, 255, 0.2)'], borderWidth: 2, pointRadius: 0, pointHoverRadius: 0}];
    this.chartData = chartData;
  }

  constructor() {
  }
}
