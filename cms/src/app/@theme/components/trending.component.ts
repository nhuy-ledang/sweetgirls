import { Component, Input } from '@angular/core';

@Component({
  selector: 'ngx-trending',
  template: `<span class="font-weight-bold" [ngClass]="{'text-primary':percent>=0,'text-danger':percent<0}"><span *ngIf="percent"><span [ngClass]="{'ic_chevron_double_up':percent>0,'ic_chevron_double_down':percent<0}"></span>&nbsp;</span>{{ percentTxt }}</span>`,
})
export class TrendingComponent {
  percent: number = 0;
  percentTxt: string = '';

  @Input() set value(percent: number) {
    this.percent = percent;
    this.percentTxt = String(Math.abs(percent)) + '%';
  }

  constructor() {
  }
}
