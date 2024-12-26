import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ChartsModule } from 'ng2-charts';
import { ChartjsLineComponent } from './chartjs-line.component';
import { ChartjsDoughnutComponent } from './chartjs-doughnut.component';

@NgModule({
  imports: [
    CommonModule,
    ChartsModule,
  ],
  exports: [
    ChartjsLineComponent,
    ChartjsDoughnutComponent,
  ],
  declarations: [
    ChartjsLineComponent,
    ChartjsDoughnutComponent,
  ],
})
export class ChartjsModule {
}
