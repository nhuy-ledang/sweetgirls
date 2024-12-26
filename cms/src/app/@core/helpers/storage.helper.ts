const INTERNAL_STORAGE: { [key: string]: any } = {};

export class StorageHelper {
  get(prop: string): any {
    return INTERNAL_STORAGE.hasOwnProperty(prop) ? INTERNAL_STORAGE[prop] : false;
  }

  set(prop: string, value: any): any {
    return INTERNAL_STORAGE[prop] = value;
  }

  unset(prop: string): void {
    delete INTERNAL_STORAGE[prop];
  }

  getOne(prop: any): any {
    const v = this.get(prop);
    this.unset(prop);

    return v;
  }
}
