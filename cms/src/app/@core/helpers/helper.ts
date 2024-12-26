// import { FileUploaderOptions } from 'ng2-file-upload';
// import * as moment from 'moment/moment';
export class Helper {
  /**
   * Find position top
   */
  /*public static findPosTop(obj) {
    let ele = obj;
    let top = 0;
    do {
      top += ele.offsetTop;
      ele = ele.offsetParent;
    } while (ele);
    return top;
  }*/

  /**
   * Scroll to first error
   */
  /*public static scrollToFirstError() {
    let that = this;
    setTimeout(() => {
      let ele = document.querySelector('.has-error');
      if (!ele) {
        ele = document.querySelector('.ng-invalid:not(form)');
      }
      if (ele) {
        let eleRect = ele.getBoundingClientRect();
        let main = document.getElementById('main');
        if (main) {
          let bodyRect = main.getBoundingClientRect();
          let top = this.findPosTop(ele) - bodyRect.top - 35;
          document.getElementById('main').scrollTo(0, top);
        } else {
          let bodyRect = document.body.getBoundingClientRect();
          let top = eleRect.top - bodyRect.top - 35;
          window.scrollTo(0, top);
        }
      }
    });
  }*/

  /**
   * Get uploader options
   *
   * @param options any
   * @returns {FileUploaderOptions}
   */
  /*public static getUploaderOptions(options: {
    documentCategoryID: number,
    referenceID: number,
    documentUploadedByID: number,
    documentFolderID?: number,
    removeAfterUpload?: boolean,
    queueLimit?: number
  }) {
    let apiOptions = {
      documentCategoryID: options.documentCategoryID,
      referenceID: options.referenceID,
      documentUploadedByID: options.documentUploadedByID
    };

    if (options.documentFolderID) {
      apiOptions['documentFolderID'] = options.documentFolderID;
    }

    let apiUrl = process.env.apiUrl + '/document?' + UrlHelper.toUrlEncodedString(apiOptions);

    let uploaderOptions: FileUploaderOptions = {
      url: apiUrl,
      headers: [{name: 'Authorization', value: `Bearer ${this.getToken()}`}],
      removeAfterUpload: options.removeAfterUpload ? options.removeAfterUpload : false,
      isHTML5: true
    };

    if (options.queueLimit) {
      uploaderOptions.queueLimit = options.queueLimit;
    }

    return uploaderOptions;
  }*/

  /**
   * Get SCORM uploader options
   *
   * @returns {FileUploaderOptions}
   */

  /*public static getSCORMUploaderOptions(scormUrl: string) {
    let uploaderOptions: FileUploaderOptions = {
      url: scormUrl,
      queueLimit: 1
    };

    return uploaderOptions;
  }*/

  /**
   * Format the date before saving
   *
   * @param date
   * @param {number} timeZoneHourOffSet
   * @returns {string}
   */
  /*public static getDateToSave(date: any, timeZoneHourOffSet?: number): string {
    if (!!timeZoneHourOffSet) {
      return moment(date).add('hours', -1 * timeZoneHourOffSet).format(environment.FORMAT_DATE_TIME);
    }

    return moment(date).format(environment.FORMAT_DATE_TIME);
  }*/

  /**
   * this will return an array containing all the
   * dates between start date and an end date
   */
  /*public static getDateArray(start, end) {
    let arr = [];
    let dt = new Date(end);
    while (dt >= start) {
      arr.push(moment(new Date(dt)).format('YYYY-MM-DD'));
      dt.setDate(dt.getDate() - 1);
    }
    return arr;
  }*/

  /***
   * Get Time List
   * @returns {Array}
   */
  /*public static getTimes(): Array<{ key: string, value: { hour: number, minute: number } }> {
    let timeResult = [];
    let x = 30; //minutes interval
    let times = []; // time array
    let tt = 0; // start time
    let ap = ['AM', 'PM']; // AM-PM

    //loop to increment the time and push results in array
    for (let i = 0; tt < 24 * 60; i++) {
      let hh = Math.floor(tt / 60); // getting hours of day in 0-24 format
      let mm = (tt % 60); // getting minutes of the hour in 0-55 format
      times[i] = ('0' + (hh % 12)).slice(-2) + ':' + ('0' + mm).slice(-2) + ap[Math.floor(hh / 12)]; // pushing data in array in [00:00 - 12:00 AM/PM format]
      tt = tt + x;

      timeResult.push({
        key: moment(times[i], ['h:mmA']).format('HH:mmA'),
        value: {
          hour: hh,
          minute: mm
        }
      });
    }

    return timeResult;
  }*/

  /**
   * Get hour form 00:00AM
   * @param time
   * @returns {number}
   */
  /*public static getHour(time) {
    if (!time) {
      return 0;
    }
    return +moment.utc(time, ['h:mmA']).format('HH');
  }*/

  /**
   * Get munite from 00:00AM
   * @param time
   * @returns {number}
   */

  /*public static getMinute(time) {
    if (!time) {
      return 0;
    }

    return +moment.utc(time, ['h:mmA']).format('mm');
  }*/

  /***
   * Round number
   * @param {number} value
   * @param {number} decimals
   * @returns {number}
   */
  public static round(value: number, decimals: number): number {
    return Number(Math.round(parseFloat(value + 'e' + decimals)) + 'e-' + decimals);
  }

  /*public static replaceMentionMessage(message: string) {
    let listStartMentions = this.getPositions(message, 'mention-');
    let listEndMentions = this.getPositions(message, '-mention');

    if (listStartMentions.length <= 0) {
      return message;
    }

    let newMessage = message;

    for (let index = 0; index < listStartMentions.length; index++) {
      const startMention = listStartMentions[index];
      const endMention = listEndMentions[index];

      let mention = message.substring(startMention + 8, endMention);
      let mentionData = mention.split('|');
      let newMention = '<a href="javascript:void(0);" (click)="showContactDetail(' + mentionData[1] + ')">@' + mentionData[0] + '</a>';
      newMessage = newMessage.replace(`mention-${mention}-mention`, newMention);
    }

    return newMessage;
  }*/

  /*public static replaceEditMentionMessage(message: string) {
    let listStartMentions = this.getPositions(message, 'mention-');
    let listEndMentions = this.getPositions(message, '-mention');

    if (listStartMentions.length <= 0) {
      return message;
    }

    let newMessage = message;

    for (let index = 0; index < listStartMentions.length; index++) {
      const startMention = listStartMentions[index];
      const endMention = listEndMentions[index];

      let mention = message.substring(startMention + 8, endMention);
      let mentionData = mention.split('|');
      let newMention = '@' + mentionData[0];
      newMessage = newMessage.replace(`mention-${mention}-mention`, newMention);
    }

    return newMessage;
  }*/

  private static getPositions(str, value) {
    const indexes = [];
    if (!str || str === '') {
      return indexes;
    }
    let index = 0;
    while (index !== -1) {
      index = str.indexOf(value, index);
      if (index !== -1) {
        indexes.push(index);
        index = index + value.length;
      }
    }

    return indexes;
  }

  public static runFnc(a: any, b?: any): void {
    if (typeof a === 'function') a(b);
  }

  public static pad(val, len): string {
    val = String(val);
    len = len || 2;
    while (val.length < len) {
      val = '0' + val;
    }
    return val;
  }

  // Converts number to string representation with K and M.
  // toFixed(d) returns a string that has exactly 'd' digits
  // after the decimal place, rounding if necessary.
  // https://www.codegrepper.com/code-examples/javascript/convert+number+to+k+and+m+javascript
  // https://stackoverflow.com/questions/9461621/format-a-number-as-2-5k-if-a-thousand-or-more-otherwise-900
  public static numFormatter(num, digits): string {
    const si = [
      {value: 1, symbol: ''},
      {value: 1E3, symbol: 'k'}, // K
      {value: 1E6, symbol: 'tr'}, // M
      {value: 1E9, symbol: 'tỷ'}, // B or G
      {value: 1E12, symbol: 'nghìn tỷ'}, // T
      {value: 1E15, symbol: 'P'},
      {value: 1E18, symbol: 'E'},
    ];
    const rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
    let i;
    for (i = si.length - 1; i > 0; i--) {
      if (num >= si[i].value) {
        break;
      }
    }
    return (num / si[i].value).toFixed(digits).replace(rx, '$1') + si[i].symbol;
  }

  public static bytesToSize(bytes): string {
    if (!bytes) return '';
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = this.round(Math.floor(Math.log(bytes) / Math.log(1024)), 0);
    if (i === 0) return `${bytes} ${sizes[i]})`;
    return `${(bytes / (1024 ** i)).toFixed(1)} ${sizes[i]}`;
  }

  public static hexToRgb(hex): {r: number, g: number, b: number}|null {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {r: parseInt(result[1], 16), g: parseInt(result[2], 16), b: parseInt(result[3], 16)} : null;
  }
}
