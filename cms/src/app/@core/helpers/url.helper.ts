export class UrlHelper {
  /**
   * Get full domain
   *
   * @returns {string}
   */
  public static fullDomain(): string {
    return location.protocol + '//' + location.host;
  }

  /**
   * Url encoded string
   *
   * @param data
   * @returns {string}
   */
  public static toUrlEncodedString(data: any): string {
    let body = '';
    for (const key in data) {
      if (data.hasOwnProperty(key)) {
        if (body.length) {
          body += '&';
        }
        body += key + '=';
        body += encodeURIComponent(data[key]);
      }
    }
    return body;
  }

  /**
   * Url encoded string
   *
   * @param a
   * @returns {string}
   */
  public static getUrlParams(a: any): string {
    let b = '';
    for (const c in a) {
      if (c) {
        if (a[c] !== undefined && a[c] !== null) {
          b += (b !== '' ? '&' : '') + c + '=' + a[c];
        }
      }
    }
    return b;
  }
}
