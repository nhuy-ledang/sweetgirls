import { Pipe, PipeTransform } from '@angular/core';

@Pipe({name: 'formatDate'})
export class DateFormatPipe implements PipeTransform {
  transform(value: any, format?: string) {
    if (value) {
      return value === '0000-00-00' ? '' : new Date(value).format(format || 'shortDate');
    }
    return value;
  }
}
