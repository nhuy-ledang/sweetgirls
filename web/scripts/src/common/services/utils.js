angular.module('services.utils', [])

.factory('utils', function ($filter, $q) {
  var service = {
    _ : {
      remove: function(arrItems, objFilter) {
        return _.without(arrItems, _.findWhere(arrItems, objFilter));
      }
    },

    dateToUTC: function (dateString) {
      var dateParts = dateString.split(" ");
      var UTCDate = dateParts[0] + "T" + dateParts[1];
      return UTCDate;
    },

    dateToShort: function (dateString) {
      if (dateString) {
        var dateParts = dateString.split(" ");
        return dateParts[0];
      }
      return "";
    },

    checkDay: function (d) {
      var now = new Date();
      var day = new Date(d);

      if (now.getYear() === day.getYear() && now.getMonth() === day.getMonth() &&
        now.getDate() === day.getDate()) {
        return true;
      }

      return false;
    },

    // check if date is in current week
    checkWeek: function (d) {

      // Get the first day of the week from current date
      var now = new Date();
      var day = now.getDay(),
        diff = now.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is sunday

      var monday = new Date(now.setDate(diff));

      var dateIn = new Date(d);

      // Difference between today and start of this week
      var diffMs = new Date(dateIn.getYear(), dateIn.getMonth(), dateIn.getDate()) - new Date(monday.getYear(), monday.getMonth(), monday.getDate());

      // Convert difference to days (1 day = 86400000 ms)
      // Round up (e.g. 7.5 = 8 days)
      var diffDays = Math.ceil(diffMs / 86400000);

      if (diffDays >= 0 && diffDays < 7) {
        return true;
      }

      return false;
    },

    // check if date is in current month
    checkMonth: function (d) {
      var dateNow = new Date();
      var dateIn = new Date(d);

      if (dateIn.getYear() === dateNow.getYear() && dateIn.getMonth() === dateNow.getMonth()) {
        return true;
      }

      return false;
    },

    // Check if date is in current year
    checkYear: function (d) {
      var dateNow = new Date();
      var dateIn = new Date(d);

      if (dateIn.getYear() === dateNow.getYear()) {
        return true;
      }

      return false;
    },

    // sorted by property
    sort: function (arrayList, property) {
      if (!angular.isArray(arrayList)) {
        return [];
      } else {
        var newArray = $filter('orderBy')(arrayList, property);
        return newArray.filter(function (n) {
          return n !== undefined;
        });
      }
    },

    sort2: function (arrayList, field, reverse) {
      arrayList.sort(function (a, b) {
        return (a[field] > b[field] ? 1 : -1);
      });
      if (reverse) {
        arrayList.reverse();
      }
    },

    sortAdvance: function (arrayList, field, reverse) {
      var filtered = [];
      angular.forEach(arrayList, function (item) {
        filtered.push(item);
      });
      filtered.sort(function (a, b) {
        return (a[field] > b[field] ? 1 : -1);
      });
      if (reverse) {
        filtered.reverse();
      }
      return filtered;
    },

    // Formatting a Javascript Date for MySQL
    formatDate: function (date) {
      return date.getFullYear() + '-' + service.Lz(date.getMonth() + 1) + '-' + service.Lz(date.getDate());
    },

    // check modified datetime
    modified: function (time) {
      if (!time) {
        return '';
      }
      var start = new Date(time);
      var now = new Date();
      var string = '';

      if (start.getYear() < now.getYear()) {
        var year = now.getYear() - start.getYear();
        string = year + ' year' + (year > 1 ? 's' : '');
      }
      else if (start.getMonth() < now.getMonth()) {
        var month = now.getMonth() - start.getMonth();
        string = month + ' month' + (month > 1 ? 's' : '');
      }
      else if (start.getDate() < now.getDate()) {
        var date = now.getDate() - start.getDate();
        string = date + ' day' + (date > 1 ? 's' : '');
      }
      else if (start.getHours() < now.getHours()) {
        var hour = now.getHours() - start.getHours();
        string = hour + ' hour' + (hour > 1 ? 's' : '');
      }
      else if (start.getMinutes() < now.getMinutes()) {
        var min = now.getMinutes() - start.getMinutes();
        string = min + ' minute' + (min > 1 ? 's' : '');
      }
      else {
        var sec = now.getSeconds() - now.getSeconds();
        string = sec + ' second' + (sec > 1 ? 's' : '');
      }

      return string + ' ago';
    },

    // local
    Lz: function (x) {

      return (x < 0 || x >= 10 ? "" : "0") + x;
    },

    getWeekNumber: function (d) {

      // Copy date so don't modify original
      d = new Date(+d);
      d.setHours(0, 0, 0);

      // Set to nearest Thursday: current date + 4 - current day number
      // Make Sunday's day number 7
      d.setDate(d.getDate() + 4 - (d.getDay() || 7));

      // Get first day of year
      var yearStart = new Date(d.getFullYear(), 0, 1);

      // Calculate full weeks to nearest Thursday
      var weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);

      // Return array of year and week number
      return weekNo;
    },

    // get date from Year-Week-Day
    getDateFormYWD: function (y, w, d) {
      var DOb = new Date(+y, 0, 4);
      if (isNaN(DOb)) {
        return false;
      }
      //  var D = DOb.getDay() ; if (D==0) D=7
      var D = DOb.getDay() || 7; // ISO
      DOb.setDate(DOb.getDate() + 7 * (w - 1) + (d - D));

      return DOb;
    },

    // get date name
    getDateName: function (date) {
      var day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', "Sunday"];

      return day[new Date(date).getDay()];
    },

    // get month name
    getMonthName: function (date) {
      var month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

      return month[new Date(date).getMonth()];
    },

    // Helper function for checking if a variable is an integer
    isInteger: function (possibleInteger) {
      return Object.prototype.toString.call(possibleInteger) !== "[object Array]" && /^[\d]+$/.test(possibleInteger);
    },

    // clone object
    clone: function (object) {
      var temp = {};

      childNode(temp, object);

      function childNode(obj, object) {
        for (var key in object) {
          if (typeof(object[key]) === 'object') {
            if (object[key] === null) {
              obj[key] = null;
            }
            else {
              if (object[key].length !== undefined) {
                obj[key] = [];
              }
              else {
                obj[key] = {};
              }
              childNode(obj[key], object[key]);
            }
          } else {
            obj[key] = object[key];
          }
        }
      }

      return temp;
    },

    // file types and check type of file
    fileTypes: ['JPG', 'JPEG', 'PNG', 'GIF', 'TIF', 'PDF', 'DOC', 'DOCX', 'XLS', 'XLSX'],
    checkFileType: function (file) {
      var doc = ['pdf', 'doc', 'docs', 'xls', 'xlsx'],
        image = ['jpg', 'jpeg', 'png', 'gif', 'tif'];

      for (var i = 0; i < image.length; i++) {
        if (file.name.toLowerCase().indexOf(image[i].toLowerCase()) !== -1) {
          return 'picture';
        }
      }
      return 'doc';
    },

    // base64 encode
    base64Encode: function (binary) {
      //return btoa(unescape(encodeURIComponent(binary)));
      var CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
      var out = "", i = 0, len = binary.length, c1, c2, c3;
      while (i < len) {
        c1 = binary.charCodeAt(i++) & 0xff;
        if (i == len) {
          out += CHARS.charAt(c1 >> 2);
          out += CHARS.charAt((c1 & 0x3) << 4);
          out += "==";
          break;
        }
        c2 = binary.charCodeAt(i++);
        if (i == len) {
          out += CHARS.charAt(c1 >> 2);
          out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
          out += CHARS.charAt((c2 & 0xF) << 2);
          out += "=";
          break;
        }
        c3 = binary.charCodeAt(i++);
        out += CHARS.charAt(c1 >> 2);
        out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
        out += CHARS.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
        out += CHARS.charAt(c3 & 0x3F);
      }
      return out;
    },

    // format number
    formatNumber: function (number) {
      if (number === null || !number) {
        return '00';
      }

      number = parseFloat(number);
      if (number < 10) {
        return '0' + number;
      }
      return number;
    },

    // get years list
    getYears: function () {
      // start: 2003 and end 3 years from current years
      var start = 2003;
      var end = new Date().getFullYear() + 3;

      var array = [];
      for (var i = start; i <= end; i++) {
        array.push({year: i.toString()});
      }

      return array;
    },

    //is Undefined or Null
    isNullOrUndefined: function (input) {
      if (angular.isUndefined(input) || input === null) {
        return true;
      }
      return false;
    },

    // Convert time format from minute integer
    convertTime: function (timeInteger) {
      var h = Math.floor(timeInteger / 60),
        m = timeInteger % 60,
        a = 'AM';
      if (h > 12) {
        h -= 12;
        a = 'PM';
      }
      // Check 0h
      if (h === 0) {
        h = 12;
        a = 'AM';
      }
      if (m.toString().length === 1) {
        m = '0' + m.toString();
      }

      return h.toString() + ':' + m.toString() + ' ' + a.toString();
    },

    // Revert minuter integer from hh:ii:aa
    revertTime: function (timeString) {
      var timeArr = timeString.split(/[.: ]/);
      var h = parseInt(timeArr[0]);
      var m = parseInt(timeArr[1]);
      var a = timeArr[2];

      if (a === 'PM') {
        h += 12;
      }

      // Check 0h
      if (h === 12 && a === 'AM') {
        h = 0;
      }

      return h * 60 + m;
    },
    // Email validation
    validateEmail: function (email) {
      var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    },

    // encode for string, object, array
    encodeURIComponent: function (object) {
      if (typeof object === "string") {
        return encodeURIComponent(object);
      } else if (typeof object === "object") {
        if (angular.isArray(object)) {
          var arrs = [];
          for (var i = 0; i < object.length; i++) {
            arrs.push(service.encodeURIComponent(object[i]));
          }
          return arrs;
        } else {
          for (var key in object) {
            object[key] = service.encodeURIComponent(object[key]);
          }
          return object;
        }
      } else {
        return object;
      }
    },

    dataURItoBlob: function (dataURI) {
      // convert base64/URLEncoded data component to raw binary data held in a string
      var byteString;
      if (dataURI.split(',')[0].indexOf('base64') >= 0) {
        byteString = atob(dataURI.split(',')[1]);
      } else {
        byteString = unescape(dataURI.split(',')[1]);
      }

      // separate out the mime component
      var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

      // write the bytes of the string to a typed array
      var ia = new Uint8Array(byteString.length);
      for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
      }

      return new Blob([ia], {type: mimeString});
    },

    resizeImage: function (file, MAX_SIZE) {
      var deferred = $q.defer();
      MAX_SIZE = MAX_SIZE || 1170;
      var filename = file.name;
      if (window.File && window.FileReader && window.FileList && window.Blob) {
        var fileType = file.type;

        var reader = new FileReader();
        reader.onloadend = function () {
          var tempImg = new Image();
          tempImg.src = reader.result;
          tempImg.onload = function () {
            var w = tempImg.width;
            var h = tempImg.height;
            if (w > h) {
              if (w > MAX_SIZE) {
                h *= MAX_SIZE / w;
                w = MAX_SIZE;
              }
            } else {
              if (h > MAX_SIZE) {
                w *= MAX_SIZE / h;
                h = MAX_SIZE;
              }
            }

            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext("2d");
            ctx.drawImage(this, 0, 0, w, h);
            var dataURL = canvas.toDataURL(fileType);
            var file = service.dataURItoBlob(dataURL);
            file.name = filename;
            deferred.resolve(file);
          };
        };
        reader.readAsDataURL(file);
      } else {
        deferred.resolve(file);
      }
      return deferred.promise;
    },

    fileToDataURL: function(file) {
      var deferred = $q.defer();
      if (window.File && window.FileReader && window.FileList && window.Blob) {
        var fileType = file.type;
        var reader = new FileReader();
        reader.onloadend = function() {
          var tempImg = new Image();
          tempImg.src = reader.result;
          tempImg.onload = function() {
            var canvas = document.createElement('canvas');
            canvas.width = tempImg.width;
            canvas.height = tempImg.height;
            var ctx = canvas.getContext("2d");
            ctx.drawImage(this, 0, 0);
            var dataURL = canvas.toDataURL(fileType);
            deferred.resolve(dataURL);
          };
        };
        reader.readAsDataURL(file);
      } else {
        deferred.resolve(false);
      }

      return deferred.promise;
    },

    replaceSpace: function(val) {
      return val.replace(/ +(?= )/g, '').replace(/ /g, '-');
    },

    revertSpace: function(val) {
      return val.replace(/-/g, ' ');
    },

    replaceFolderName: function(name) {
      return this.replaceSpace(name).toLowerCase();
    },

    timeRanges: function () {
      var ranges = [];
      for (var i = 0; i <= 23; i++) {
        for (var j = 0; j <= 59; j = j + 15) {
          var m = i.toString();
          var s = j.toString();
          if (i < 10) {
            m = '0' + m;
          }
          if (j < 10) {
            s = '0' + s;
          }
          ranges.push(m + ':' + s);
        }
      }

      return ranges;
    },

    fallbackCopyTextToClipboard: function(text) {
      var textArea = document.createElement("textarea");
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();
      try {
        var successful = document.execCommand('copy');
        // var msg = successful ? 'successful' : 'unsuccessful';
        // console.log('Fallback: Copying text command was ' + msg);
      } catch(err) {
        // console.error('Fallback: Oops, unable to copy', err);
      }
      document.body.removeChild(textArea);
    },

    copyTextToClipboard: function(text) {
      if (!navigator.clipboard) {
        this.fallbackCopyTextToClipboard(text);
        return;
      }
      navigator.clipboard.writeText(text).then(function() {
        // console.log('Async: Copying to clipboard was successful!');
      }, function(err) {
        // console.error('Async: Could not copy text: ', err);
      });
    },

    joinText: function(arr, prop) {
      var output = [];
      prop = prop ? prop : 'name';
      _.forEach(arr, function(item) {
        output.push(item[prop]);
      });
      return output.join(', ');
    },

    // Reset Form
    resetForm: function(scopeForm) {
      // Reset all errors
      for(var att in scopeForm.$error) {
        if(scopeForm.$error.hasOwnProperty(att)) {
          scopeForm.$setValidity(att, true);
        }
      }

      // Reset validation's state
      scopeForm.$setPristine(true);
    },

    toFormData: function(params) {
      var formData = new FormData();
      for (var key in params) {
        if (params.hasOwnProperty(key)) {
          var value = params[key];
          if (key === 'file' && value) {
            formData.append(key, value, value.name);
          } else if (key === 'files') {
            for (var i = 0; i < value.length; i++) {
              if (value[i]) {
                formData.append('files[]', value[i], value[i].name);
              }
            }
          } else if (_.isArray(value)) {
            formData.append(key, JSON.stringify(value));
          } else {
            formData.append(key, value);
          }
        }
      }

      return formData;
    },
  };

  return service;
});
