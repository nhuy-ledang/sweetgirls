import { Injectable } from '@angular/core';
import { DatePipe } from '@angular/common';

declare var unescape: any;

@Injectable()
export class Utils {
  protected static instance: Utils;
  protected static datePipe: DatePipe;

  constructor(protected _datePipe: DatePipe) {
    if (_datePipe) {
      Utils.datePipe = _datePipe;
    }
  }

  dateToShort(date?: any) {
    if (date instanceof Date) {
      return this._datePipe.transform(date, 'dd/MM/yyyy');
    } else if (Utils.isDate(date) && _.isString(date)) {
      return date.split('T')[0];
    }

    return date;
  }

  public static isDate(date?: any) {
    if (!date) {
      return null;
    }

    date = new Date(date);

    return (_.isDate(date) && !_.isNaN(date.getTime()));
  }

  public static dateToISOString(date?: any) {
    if (Utils.isDate(date)) {
      date = new Date(date);
      return date.toISOString();
    }

    return date;
  }

  // Fix time zone
  public static dateToLocalTime(date?: any) {
    if (!date || date === '0000-00-00') {
      return null;
    }

    if (_.isString(date)) {
      const tmp = new Date(date.split('T')[0]);
      return new Date(tmp.getTime() + tmp.getTimezoneOffset() * 60 * 1000);
    }

    return date;
  }

  // Return the instance
  public static getInstance(): Utils {
    if (!Utils.instance) {
      Utils.instance = new Utils(Utils.datePipe);
    }

    return Utils.instance;
  }

  /***
   * Get age by birthday
   * @param birthday
   * @returns {number}
   */
  public static getAge(birthday: any): number {
    const ageDate = new Date(new Date().getTime() - new Date(birthday).getTime());

    return Math.abs(ageDate.getUTCFullYear() - 1970);
  }

  /***
   * Get protocol, domain, and port from URL
   *
   * @param path
   * @returns {{host: string, port: string, path: string, protocol: string}}
   */
  public static getPathInfo(path) {
    //  create a link in the DOM and set its href
    const link = document.createElement('a');
    link.setAttribute('href', path);

    //  return an easy-to-use object that breaks apart the path
    return {
      host: link.hostname,
      port: link.port,
      path: link.pathname,
      protocol: link.protocol,
    };
  }

  /**
   *  Parse Float
   *
   * @param value
   * @param unit
   * @returns {number}
   */
  public static parseFloat(value, unit?: number): number {
    const result = parseFloat(value);
    if (isNaN(result)) {
      return 0;
    } else {
      if (!unit) {
        unit = 2;
      }
      return parseFloat(result.toFixed(unit));
    }
  }

  /***
   * Check Json
   *
   * @param text
   * @returns {boolean}
   */
  public static isJson(text) {
    // try {
    //   JSON.parse(text);
    // } catch (e) {
    //   return false;
    // }
    // return true;

    if (/^[\],:{}\s]*$/.test(text.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Convert to 24 hour
   *
   * @param time H:i AM/PM
   * @returns {string}
   */
  public static convertTo24Hour(time) {
    let hours = Number(time.match(/^(\d+)/)[1]);
    const minutes = Number(time.match(/:(\d+)/)[1]);
    const AMPM = time.match(/\s(.*)$/)[1];
    if (AMPM === 'PM' && hours < 12) hours = hours + 12;
    if (AMPM === 'AM' && hours === 12) hours = hours - 12;
    let sHours = hours.toString();
    let sMinutes = minutes.toString();
    if (hours < 10) sHours = '0' + sHours;
    if (minutes < 10) sMinutes = '0' + sMinutes;

    return sHours + ':' + sMinutes;
  }
}
