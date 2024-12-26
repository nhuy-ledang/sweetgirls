import { Helper } from './helper';

declare global {
  interface Date {
    format(format): string;
    getWeekNumber(): number;
    fromNow(): string;
    getPlus(dayNumber: number): Date;
    getFirstDayInWeek(): Date;
    getLastDayInWeek(): Date;
    getFirstDayInMonth(): Date;
    getLastDayInMonth(): Date;
    getIsoDate(): string;
    getIsoTime(): string;
  }
}

export class DateTimeHelper {
  /**
   * Format with seconds
   */
  public static formatWithSeconds(seconds) {
    let format = '';
    if (seconds < 60) {
      format = seconds + ' secs';
    } else if (seconds < 3600) {
      const mins = Math.floor(seconds / 60);
      const secs = seconds % 60;
      format = mins + ' mins ' + secs + ' secs';
    } else if (seconds < 86400) {
      const hours = Math.floor(seconds / 3600);
      const odd = seconds % 3600;
      const mins = Math.floor(odd / 60);
      format = hours + ' hrs ' + mins + ' mins';
    } else {
      const days = Math.floor(seconds / 86400);
      const odd = seconds % 86400;
      const hours = Math.floor(odd / 3600);
      format = days + ' days ' + hours + ' hrs';
    }

    return format;
  }

  /**
   * Format with seconds
   */
  public static fromNow(date: any): string {
    return new Date(date).fromNow();
  }

  public static datePlus(date, dayNumber): Date {
    return new Date(date).getPlus(dayNumber);
  }

  public static dateFirstDayInMonth(date): Date {
    return new Date(date).getFirstDayInMonth();
  }

  public static dateLastDayInMonth(date) {
    return new Date(date).getLastDayInMonth();
  }

  public static dateFirstDayInWeek(date): Date {
    return new Date(date).getFirstDayInWeek();
  }

  public static dateLastDayInWeek(date): Date {
    return new Date(date).getLastDayInWeek();
  }

  public static dateInNumber(start, end): number {
    console.log(start, end);
    const s = new Date(start);
    const e = new Date(end);

    console.log(s, e);

    return Math.round(e.getTime() - s.getTime()) / 1000 / (24 * 60 * 60);
  }

  public static getDateRangeOfWeek(weekNo, year): any[] {
    if (!year) {
      year = new Date().getFullYear();
    }
    const f = DateTimeHelper.dateFirstDayInWeek(String(year) + '-01-01');
    const d = new Date((weekNo - 1) * 7 * 24 * 60 * 60 * 1000 + f.getTime());
    let s;
    if (f.getWeekNumber() !== 1) {
      d.setDate(d.getDate() + 7);
    }
    s = d.format('isoDate');
    d.setDate(d.getDate() + 6);
    return [s, d.format('isoDate')];
  }

  public static timePlus(timeString, minute): string {
    const timeArr = timeString.split(':');
    if (timeArr.length < 2) {
      return '00:00';
    }
    const times = parseInt(timeArr[0], 0) * 60 + parseInt(timeArr[1], 0) + minute;
    return Helper.pad(Math.floor(times / 60) % 24, 2) + ':' + Helper.pad(times % 60, 2);
  }

  public static timeMinus(timeString, minute): string {
    const timeArr = timeString.split(':');
    if (timeArr.length < 2) {
      return '00:00';
    }
    let times = parseInt(timeArr[0], 0) * 60 + parseInt(timeArr[1], 0) - minute;
    if (times < 0) {
      times = 24 * 60 + times;
    }
    return Helper.pad(Math.floor(times / 60) % 24, 2) + ':' + Helper.pad(times % 60, 2);
  }

  public static timeMinutes(timeString): number {
    const timeArr = timeString.split(':');
    if (timeArr.length < 2) {
      return parseInt(timeArr[0], 0) * 60;
    } else {
      return parseInt(timeArr[0], 0) * 60 + parseInt(timeArr[1], 0);
    }
  }

  // Calculation of no. of days between two date
  public static inDays(date1, date2): number {
    // To set two dates to two variables
    date1 = new Date(date1);
    date2 = new Date(date2);

    // To calculate the time difference of two dates
    const diff = date2.getTime() - date1.getTime();

    // To calculate the no. of days between two dates
    return Math.abs(Math.ceil(diff / (1000 * 3600 * 24)));
  }

  public static inMinutes(hours1, hours2): number {
    return Math.abs(DateTimeHelper.timeMinutes(hours2) - DateTimeHelper.timeMinutes(hours1));
  }

  /***********************************************
   **************** DATE *************************
   **********************************************/

  /***
   * Get Dates
   *
   * @param date
   * @returns [y, m, d]
   */
  public static getDates(date: any): Array<any> {
    const d = new Date(date);
    if (d.toString() === 'Invalid Date') {
      return [null, null, null];
    } else {
      return [d.getFullYear().toString(), (d.getMonth() + 1).toString(), d.getDate().toString()];
    }
  }

  public static getTimeShort(timeString: string): string {
    if (!timeString) return '';
    const parts = timeString.split(':');
    if (parts.length >= 2) {
      return parts[0] + 'h' + (parseInt(parts[1], 0) === 0 ? '' : parts[1]);
    } else {
      return timeString;
    }
  }
}
