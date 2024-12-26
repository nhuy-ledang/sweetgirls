/*
 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.
 * ex: today = new Date(); var dateString = today.format("mmmm d, yyyy HH:MM:ss");
 */
var dateFormat = (function() {
  var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|[W]|"[^"]*"|'[^']*'/g,
    timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
    timezoneClip = /[^-+\dA-Z]/g,
    pad = function(val, len) {
      val = String(val);
      len = len || 2;
      while (val.length < len) {
        val = "0" + val;
      }
      return val;
    };
  // Regexes and supporting functions are cached through closure
  return function(date, mask, utc) {
    var dF = dateFormat;
    // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
    if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
      mask = date;
      date = undefined;
    }
    // Passing date through Date applies Date.parse, if necessary
    date = (date ? new Date(date) : new Date());
    if (isNaN(date)) {
      throw new SyntaxError("invalid date");
    }
    mask = String(dF.masks[mask] || mask || dF.masks["default"]);
    // Allow setting the utc argument via the mask
    if (mask.slice(0, 4) === "UTC:") {
      mask = mask.slice(4);
      utc = true;
    }
    var _ = utc ? "getUTC" : "get",
      d = date[_ + "Date"](),
      D = date[_ + "Day"](),
      m = date[_ + "Month"](),
      y = date[_ + "FullYear"](),
      H = date[_ + "Hours"](),
      M = date[_ + "Minutes"](),
      s = date[_ + "Seconds"](),
      L = date[_ + "Milliseconds"](),
      o = utc ? 0 : date.getTimezoneOffset(),
      flags = {
        d: d,
        dd: pad(d),
        ddd: dF.i18n.dayNames[D],
        dddd: dF.i18n.dayNames[D + 7],
        m: m + 1,
        mm: pad(m + 1),
        mmm: dF.i18n.monthNames[m],
        mmmm: dF.i18n.monthNames[m + 12],
        yy: String(y).slice(2),
        yyyy: y,
        h: H % 12 || 12,
        hh: pad(H % 12 || 12),
        H: H,
        HH: pad(H),
        M: M,
        MM: pad(M),
        s: s,
        ss: pad(s),
        l: pad(L, 3),
        L: pad(L > 99 ? Math.round(L / 10) : L),
        t: H < 12 ? "a" : "p",
        tt: H < 12 ? "am" : "pm",
        T: H < 12 ? "A" : "P",
        TT: H < 12 ? "AM" : "PM",
        Z: utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
        o: (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
        S: ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10],
        W: date.getWeekNumber()
      };
    return mask.replace(token, function($0) {
      return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
    });
  };
})();
// Some common format strings
dateFormat.masks = {
  "default": "ddd mmm dd yyyy HH:MM:ss",
  //shortDate: "m/d/yy",
  shortDate: "dd/mm/yyyy",
  mediumDate: "mmm d, yyyy",
  longDate: "mmmm d, yyyy",
  fullDate: "dd/mm/yyyy  HH:MM:ss",
  shortTime: "h:MM TT",
  mediumTime: "h:MM:ss TT",
  longTime: "h:MM:ss TT Z",
  isoDate: "yyyy-mm-dd",
  isoTime: "HH:MM:ss",
  isoDateTime: "yyyy-mm-dd'T'HH:MM:ss",
  isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};
// Internationalization strings
/*dateFormat.i18n = {
  dayNames: [
    "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
    "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
  ],
  monthNames: [
    "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
    "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
  ]
};*/
dateFormat.i18n = {
  dayNames: [
    "CN", "T2", "T3", "T4", "T5", "T6", "T7",
    "Chủ Nhật", "Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7"
  ],
  monthNames: [
    "Thg1", "Thg2", "Thg3", "Thg4", "Thg5", "Thg6", "Thg7", "Thg8", "Thg9", "Thg10", "Thg11", "Thg12",
    "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
  ]
};
// For convenience...
Date.prototype.format = function(mask, utc) {
  return dateFormat(this, mask, utc);
};

Date.prototype.getWeekNumber = function() {
  const d = new Date(Date.UTC(this.getFullYear(), this.getMonth(), this.getDate()));
  const dayNum = d.getUTCDay() || 7;
  d.setUTCDate(d.getUTCDate() + 4 - dayNum);
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
  return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
};

Date.prototype.fromNow = function() {
  const now = new Date().getTime();
  const last = this.getTime();
  const seconds = Math.floor((now - last) / 1000);
  let interval = Math.floor(seconds / 31536000);
  if (interval > 1) {
    return interval + ' năm';
  }
  interval = Math.floor(seconds / 2592000);
  if (interval > 1) {
    return interval + ' tháng';
  }
  interval = Math.floor(seconds / 86400);
  if (interval > 1) {
    return interval + ' ngày';
  }
  interval = Math.floor(seconds / 3600);
  if (interval > 1) {
    return interval + ' giờ';
  }
  interval = Math.floor(seconds / 60);
  if (interval > 1) {
    return interval + ' phút';
  }
  return String(Math.floor(seconds)) + ' giây';
};

Date.prototype.getPlus = function(dayNumber) {
  const dt = new Date(this);
  dt.setDate(dt.getDate() + dayNumber);
  return dt;
};

Date.prototype.getMinus = function(dayNumber) {
  const dt = new Date(this);
  dt.setDate(dt.getDate() - dayNumber);
  return dt;
};

Date.prototype.getFirstDayInWeek = function() {
  const dayNumber = this.getDay();
  return new Date(new Date(this.format('isoDate')).getTime() - (dayNumber === 0 ? 6 : (dayNumber - 1)) * 24 * 60 * 60 * 1000);
};

Date.prototype.getLastDayInWeek = function() {
  const start = this.getFirstDayInWeek();
  return new Date(start.getTime() + 6 * 24 * 60 * 60 * 1000);
};

Date.prototype.getFirstDayInMonth = function() {
  return new Date(this.getFullYear(), this.getMonth(), 1);
};

Date.prototype.getLastDayInMonth = function() {
  return new Date(this.getFullYear(), this.getMonth() + 1, 0);
};

Date.prototype.getIsoDate = function() {
  return this.format('isoDate');
};