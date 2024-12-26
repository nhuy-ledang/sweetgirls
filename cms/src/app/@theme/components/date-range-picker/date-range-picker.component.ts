import {Component, Input, Output, EventEmitter} from '@angular/core';

@Component({
  selector: 'ngx-date-range-picker',
  templateUrl: './date-range-picker.html',
})
export class DateRangePickerComponent {
  @Input() data: any;
  @Input() options: any;
  @Output() applyEvent: EventEmitter<any> = new EventEmitter();

  daterange: { mode: 'day' | 'week' | 'month' | 'year' | 'customRange' | 'all', label: string, start?: any, end?: any, start_date?: string, end_date?: string } = { mode: 'all', label: 'Tất cả' };
  bsData: { mode: 'day' | 'week' | 'month' | 'year' | 'customRange' | 'all', label: string, value: Date, currDate: Date, start: Date, end: Date, start_date: string, end_date: string } = { mode: 'all', label: 'Tất cả', value: new Date(), currDate: new Date(), start: new Date(), end: new Date(), start_date: '', end_date: '' };
  bsInlineRangeValue: Date[] = [new Date(), new Date()];

  private bsUpdate(): void {
    let start: Date;
    let end: Date;
    if (this.bsData.mode === 'customRange') {
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
    this.bsData.start_date = start ? start.format('yyyy-mm-dd') : '';
    this.bsData.end_date = end ? end.format('yyyy-mm-dd') : '';
  }

  bsSelectMode(mode: 'day' | 'week' | 'month' | 'year' | 'customRange' | 'all'): void {
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
    const daterange: any = { mode: this.bsData.mode, label: this.bsData.label, start: new Date(this.bsData.start), end: new Date(this.bsData.end), start_date: this.bsData.start_date, end_date: this.bsData.end_date };
    this.daterange = daterange;
    this.bsData.value = this.bsData.currDate;
    this.data.data = { ...this.data.data, mode: daterange.mode, start_date: daterange.start_date, end_date: daterange.end_date };
    if (this.bsData.mode === 'all') {
      this.data.data.start_date = '';
      this.data.data.end_date = '';
      this.daterange.label = 'Tất cả';
    }
    this.applyEvent.emit();
  }
}
