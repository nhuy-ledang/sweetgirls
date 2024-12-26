export class Role {
  id: number = 0;
  slug: string = '';
  name: string = '';
  permissions?: any = null;

  constructor(data?: any) {
    if (!_.isEmpty(data)) _.each(data, (val, key) => {
      if (this.hasOwnProperty(key)) this[key] = val;
    });
  }
}
