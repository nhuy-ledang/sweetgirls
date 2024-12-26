import { Injectable } from '@angular/core';

/**
 * Async modal dialog service
 * DialogService makes this app easier to test by faking this service.
 * TODO: better modal implementation that doesn't use window.confirm
 */
@Injectable()
export class Dialog {
  /**
   * Ask user to confirm an action. `message` explains the action and choices.
   * Returns promise resolving to `true`=confirm or `false`=cancel
   */
  confirm(message?: string) {
    return new Promise<boolean>((resolve, reject) =>
      resolve(window.confirm(message || 'Is it OK?')));
  }

  private $window = window;
  private popup = null;

  static getFullUrlPath(location): string {
    const isHttps = location.protocol === 'https:';
    const url = location.protocol + '//' + location.hostname + ':' + (location.port || (isHttps ? '443' : '80')) + (/^\//.test(location.pathname) ? location.pathname : '/' + location.pathname);
    return url;
  }

  private stringifyOptions(options): string {
    const parts = [];
    _.forEach(options, (value, key) => {
      parts.push(key + '=' + value);
    });
    return parts.join(',');
  }

  private parseQueryString(str): any {
    const obj = {};
    let key;
    let value;
    _.forEach((str || '').split('&'), function(keyValue) {
      if (keyValue) {
        value = keyValue.split('=');
        key = decodeURIComponent(value[0]);
        obj[key] = !_.isUndefined(value[1]) ? decodeURIComponent(value[1]) : true;
      }
    });
    return obj;
  }

  open(url, name?: string, popupOptions?: any, redirectUri?: string) {
    popupOptions = _.extend({}, popupOptions);
    const width = popupOptions.width || 500;
    const height = popupOptions.height || 500;
    const options = this.stringifyOptions({
      width: width,
      height: height,
      top: this.$window.screenY + ((this.$window.outerHeight - height) / 2.5),
      left: this.$window.screenX + ((this.$window.outerWidth - width) / 2),
    });
    const popupName = this.$window['cordova'] || this.$window.navigator.userAgent.indexOf('CriOS') > -1 ? '_blank' : name;
    this.popup = this.$window.open(url, popupName, options);
    if (this.popup && this.popup.focus) {
      this.popup.focus();
    }

    const _this = this;
    return new Promise((resolve, reject) => {
      const redirectUriParser = document.createElement('a');
      redirectUriParser.href = redirectUri;
      const redirectUriPath = Dialog.getFullUrlPath(redirectUriParser);
      const polling = setInterval(() => {
        if (!_this.popup || _this.popup.closed || _this.popup.closed === undefined) {
          clearInterval(polling);
          reject(new Error('The popup window was closed'));
        }
        try {
          const popupWindowPath = Dialog.getFullUrlPath(_this.popup.location);
          if (popupWindowPath === redirectUriPath) {
            if (_this.popup.location.search || _this.popup.location.hash) {
              const query = this.parseQueryString(_this.popup.location.search.substring(1).replace(/\/$/, ''));
              const hash = this.parseQueryString(_this.popup.location.hash.substring(1).replace(/[\/$]/, ''));
              const params = _.extend({}, query, hash);
              if (params.error) {
                reject(new Error(params.error));
              } else {
                resolve(params);
              }
            } else {
              reject(new Error('OAuth redirect has occurred but no query or hash parameters were found. ' +
                'They were either not set during the redirect, or were removed—typically by a ' +
                'routing library—before Satellizer could read it.'));
            }
            clearInterval(polling);
            _this.popup.close();
          }
        } catch (error) {
        }
      }, 500);
    });
  }
}
