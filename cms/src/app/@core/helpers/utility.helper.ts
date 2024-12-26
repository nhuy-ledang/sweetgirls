export class UtilityHelper {
  parseString(value: any): string {
    return typeof value === 'number' ? value.toString() : value;
  }

  toFormData(params): FormData {
    const formData = new FormData();
    for (const key in params) {
      if (params.hasOwnProperty(key)) {
        let value = params[key];
        if (value === null) value = '';
        if (key === 'files') {
          for (let i = 0; i < value.length; i++) {
            if (value[i]) {
              formData.append('files[]', value[i], value[i].name);
            }
          }
        } else if (value && (key === 'file' || (value instanceof Blob || value instanceof File))) {
          formData.append(key, value, value.name);
        } else if (_.isArray(value)) {
          formData.append(key, JSON.stringify(value));
        } else if (_.isObject(value)) {
          // Convert to array?
          formData.append(key, JSON.stringify(value));
        } else if (_.isBoolean(value)) {
          formData.append(key, value ? '1' : '0');
        } else {
          formData.append(key, value);
        }
      }
    }

    return formData;
  }

  utf8ToAscii(str): string {
    if (!str) return '';
    const $unicode = {
      'A': 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
      'a': 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
      'D': 'Đ',
      'd': 'đ',
      'E': 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
      'e': 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
      'I': 'Í|Ì|Ỉ|Ĩ|Ị',
      'i': 'í|ì|ỉ|ĩ|ị',
      'o': 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
      'O': 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
      'U': 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
      'u': 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
      'Y': 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
      'y': 'ý|ỳ|ỷ|ỹ|ỵ',
      '': '́|̉|̀|̣|̃',
    };
    _.each($unicode, function($uni, $nonUnicode) {
      const letters = $uni.split('|');
      _.forEach(letters, function(letter) {
        str = str.replaceAll(letter, $nonUnicode, str);
      });
    });

    str = str.replace(/\s\s+/g, ' ').trim();

    return str;
  }

  /*utf8ToAscii2(str): string {
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
    str = str.replace(/đ/g, 'd');
    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, 'A');
    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, 'E');
    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, 'I');
    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, 'O');
    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, 'U');
    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, 'Y');
    str = str.replace(/Đ/g, 'D');
    return str
  }*/

  toAlias(str): string {
    let ascii = this.utf8ToAscii(str);
    ascii = ascii.replace(/\s+/g, '-').toLowerCase();

    return ascii;
  }
}
