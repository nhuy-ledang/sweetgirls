import { User } from '../../../users/shared/entities';

export class Order {
  id: number = 0;
  master_id: number = 0;
  user_id: number = 0;
  no: string = '';
  display: string = '';
  email: string = '';
  phone_number: string = '';
  is_invoice: boolean = false;
  company: string = '';
  tax_code: string = '';
  address: string = '';
  status: string = '';
  status_name: string = '';
  order_status: string = '';
  order_status_name: string = '';
  customer: User;

  constructor(data?: any) {
    if (!_.isEmpty(data)) _.each(data, (val, key) => {
      if (this.hasOwnProperty(key)) this[key] = val;
    });
  }
}
