import { Pipe, PipeTransform } from '@angular/core';


@Pipe({name: 'ago'})
export class AgoPipe implements PipeTransform {
  // time: the time
  // local: compared to what time? default: now
  // raw: wheter you want in a format of "5 minutes ago", or "5 minutes"
  transform(time, local, raw) {
    if (!time) {
      return '';
    }

    if (!local) {
      local = Date.now();
    }

    if (_.isDate(time)) {
      time = time.getTime();
    } else if (typeof time === 'string' || typeof time === 'number') {
      time = new Date(time).getTime();
    }

    if (_.isDate(local)) {
      local = local.getTime();
    } else if (typeof local === 'string') {
      local = new Date(local).getTime();
    }

    if (typeof time !== 'number' || typeof local !== 'number') {
      return;
    }

    const offset = Math.abs((local - time) / 1000);
    let span: any = [];
    const MINUTE = 60, HOUR = 3600, DAY = 86400, WEEK = 604800, MONTH = 2629744, YEAR = 31556926, DECADE = 315569260;

    if (offset <= MINUTE) {
      span = ['', raw ? 'mới đây' : 'mới đây'];
    } else if (offset < (MINUTE * 60)) {
      span = [Math.round(Math.abs(offset / MINUTE)), 'phút'];
    } else if (offset < (HOUR * 24)) {
      span = [Math.round(Math.abs(offset / HOUR)), 'giờ'];
    } else if (offset < (DAY * 7)) {
      span = [Math.round(Math.abs(offset / DAY)), 'ngày'];
    } else if (offset < (WEEK * 52)) {
      span = [Math.round(Math.abs(offset / WEEK)), 'tuần'];
    } else if (offset < (YEAR * 10)) {
      span = [Math.round(Math.abs(offset / YEAR)), 'năm'];
    } else if (offset < (DECADE * 100)) {
      span = [Math.round(Math.abs(offset / DECADE)), 'thập kỷ'];
    } else {
      span = ['', ''];
    }

    span[1] += (span[0] === 0 || span[0] > 1) ? '' : '';
    span = span.join(' ');

    if (raw === true) {
      return span;
    }

    return (time <= local) ? span + '' : ' ' + span;
  }
}
