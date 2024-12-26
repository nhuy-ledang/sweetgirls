export class User {
  id: number = 0;
  group_id: number = null;
  email: string = '';
  phone_number: string = '';
  username: string = '';
  display: string = '';
  gender: boolean = null;
  avatar_url: string = '';
  birthday: string;
  source_id: number = null;

  constructor(data?: any) {
    if (!_.isEmpty(data)) _.each(data, (val, key) => {
      if (this.hasOwnProperty(key)) this[key] = val;
    });
  }
}
