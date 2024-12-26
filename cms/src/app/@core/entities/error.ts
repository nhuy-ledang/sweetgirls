export class Err {
  errorCode: number = null;
  errorMessage: string = '...';
  errorKey: string = null;

  constructor(data: any) {
    _.each(data, (val, key) => {
      if (this.hasOwnProperty(key)) this[key] = val;
    });
  }
}
