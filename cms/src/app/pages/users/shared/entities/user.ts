export class User {
  id: number = 0;
  group_id: number = null;
  email: string = '';
  username: string = '';
  display: string = '';
  gender: boolean = null;
  phone_number: string = '';
  avatar_url: string = '';
  birthday: string;

  constructor(data?: any) {
    if (!_.isEmpty(data)) _.each(data, (val, key) => {
      if (this.hasOwnProperty(key)) this[key] = val;
    });
  }
}
