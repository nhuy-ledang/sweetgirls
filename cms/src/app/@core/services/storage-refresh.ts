import { Injectable } from '@angular/core';
import { StorageService } from './storage-service';

@Injectable()
export class StorageRefresh {
  constructor(protected _storage: StorageService) {
  }

  getItem(key: string, type: any) {
    let data: any = this._storage.getItem(key);
    let value;
    if (data && type !== 'string') {
      try {
        data = JSON.parse(data);
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

  setItem(key: string, value: any, options: any) {
    value = JSON.stringify({d: value, o: options});
    return this._storage.setItem(key, value);
  }

  removeItem(key: string) {
    return this._storage.removeItem(key);
  }

  clear(): void {
    return this._storage.clear();
  }
}
