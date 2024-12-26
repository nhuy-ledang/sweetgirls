import { Pipe, PipeTransform } from '@angular/core';
import { DatePipe } from '@angular/common';

@Pipe({name: 'formatTime'})
export class TimeFormatPipe extends DatePipe implements PipeTransform {
  transform(value: any, format?: string) {
    if (value) {
      const parts = value.split(':');
      let dateObject: Date;
      if (parts.length === 3) {
        dateObject = new Date(0, 0, 0, parts[0], parts[1], parts[2]);
        return super.transform(dateObject, format || 'hh:MM');
      } else if (parts.length === 2) {
        dateObject = new Date(0, 0, 0, parts[0], parts[1], 0);
        return super.transform(dateObject, format || 'hh:MM');
      } else {
        return value;
      }
    }
    return value;
  }
}
