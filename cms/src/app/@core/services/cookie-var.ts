import { Injectable } from '@angular/core';
import { CookieService } from 'ngx-cookie';

@Injectable()
export class CookieVar {
  private storage: any = null;

  private fakeStorage() {
    console.log('Use fake localStorage for any browser that does not support it');
    this.storage = {};
    this.storage.getItem = (key) => {
      return this._cookieService.get(key);
    };
    this.storage.setItem = (key, data) => {
      return this._cookieService.put(key, data);
    };
    this.storage.removeItem = (key) => {
      return this._cookieService.remove(key);
    };
    this.storage.clear = () => {
      return this._cookieService.removeAll();
    };
  }

  constructor(private _cookieService: CookieService) {
    // Example of how to use it
    if (typeof localStorage === 'object') {
      // Safari will throw a fit if we try to use localStorage.setItem in private browsing mode.
      try {
        localStorage.setItem('localStorageTest', '1');
        localStorage.removeItem('localStorageTest');
        this.storage = localStorage;
      } catch (e) {
        this.fakeStorage();
      }
    } else {
      this.fakeStorage();
    }
  }

  private getAll(): any {
    let data = this.storage.getItem('APP_COOKIES');
    if (data) {
      try {
        data = JSON.parse(data);
      } catch (e) {
        data = {};
      }
    }
    if (!data || !(_.isObject(data) && !_.isArray(data))) {
      data = {};
    }
    return data;
  }

  private getItem(obj, keys): any {
    if (_.isArray(obj) || !keys || !keys.length) {
      return undefined;
    } else if (keys.length === 1) {
      return obj[keys[0]];
    } else {
      if (!keys[0] || !(_.isObject(obj[keys[0]]) && !_.isArray(obj[keys[0]]))) {
        return undefined;
      } else {
        obj = obj[keys[0]];
        keys.splice(0, 1);
        return this.getItem(obj, keys);
      }
    }
  }

  private setItem(data: any, obj: any, keys: any, keyBefore: string, value: any): any {
    if (_.isArray(obj) || !keys || !keys.length) {
      return undefined;
    } else if (keys.length === 1) {
      return obj[keys[0]] = value;
    } else {
      if (!keys[0]) {
        return undefined;
      } else {
        const currentKey = keys[0];
        keyBefore += (keyBefore ? '.' : '') + keys.splice(0, 1);
        if (keyBefore) {
          const oldV = this.getItem(data, keyBefore.split('.'));
          obj[currentKey] = oldV ? oldV : {};
        } else {
          obj[currentKey] = {};
        }
        return this.setItem(data, obj[currentKey], keys, keyBefore, value);
      }
    }
  }

  public get(key: string): any {
    const data = this.getAll();
    const value = this.getItem(data, key.split('.'));
    return value;
  }

  public set(key: string, value: any): any {
    const data = this.getAll();
    const newData = _.cloneDeep(data);
    this.setItem(data, newData, key.split('.'), '', value);
    return this.storage.setItem('APP_COOKIES', JSON.stringify(newData));
  }
}
