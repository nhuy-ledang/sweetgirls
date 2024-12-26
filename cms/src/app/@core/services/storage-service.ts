import { Injectable } from '@angular/core';
import { CookieService } from 'ngx-cookie';

@Injectable()
export class StorageService {
  private storage: any = null;

  private _getItem(value: string): any {
    let data: any;
    if (value) {
      try {
        data = JSON.parse(value);
      } catch (e) {
        data = undefined;
      }
    }
    if (data) {
      if (data.o && data.o.expires && (new Date(data.o.expires).getTime() < new Date().getTime())) {
        value = undefined;
      } else {
        value = data.d;
      }
    }
    return value;
  }

  private _getValue(value: any, options?: any): string {
    return JSON.stringify({d: value, o: options});
  }

  private fakeStorage() {
    console.log('Use fake localStorage for any browser that does not support it');
    this.storage = {};
    this.storage.getItem = (key) => {
      return this._getItem(this._cookieService.get(key));
    };
    this.storage.setItem = (key: string, value: any, options?: any) => {
      return this._cookieService.put(key, this._getValue(value, options));
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

  getItem(key: string): any {
    return this._getItem(this.storage.getItem(key));
  }

  setItem(key: string, value: any, options?: any) {
    return this.storage.setItem(key, this._getValue(value, options));
  }

  removeItem(key: string) {
    return this.storage.removeItem(key);
  }

  clear(): void {
    return this.storage.clear();
  }
}
