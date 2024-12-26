import { Role } from './role';

export class Usr {
  id: number = 0;
  group_id: number = null;
  email: string = '';
  username: string = '';
  display: string = '';
  gender: boolean = null;
  phone_number: string = '';
  avatar_url: string = '';
  birthday: string;
  roles?: Role[] = [];
  permissions?: any = null;

  constructor(data?: any) {
    if (!_.isEmpty(data)) {
      _.each(data, (val, key) => {
        if (this.hasOwnProperty(key) && !_.includes(['roles'], key)) this[key] = val;
      });
      if (data.roles) {
        this.roles = [];
        _.forEach(data.roles, (item: any) => this.roles.push(new Role(item)));
      }
    }
  }

  public inRole(role: string): boolean {
    if (this.roles) {
      for (let i = 0; i < this.roles.length; i++) {
        const instance = this.roles[i];
        if (instance.slug === role) return true;
      }
    }
    return false;
  }

  public inAnyRole(roles: string[]): boolean {
    for (let i = 0; i < roles.length; i++) {
      if (this.inRole(roles[i])) return true;
    }

    return false;
  }

  /**
   * Check is Supper
   *
   * @return bool
   */
  isSuperAdmin(): boolean {
    return this.inRole('super-admin');
  }

  /**
   * Check is Admin
   *
   * @return bool
   */
  isAdmin(): boolean {
    return this.inRole('admin');
  }

  /**
   * Check is Manager
   *
   * @return bool
   */
  isManager(): boolean {
    return this.inRole('manager');
  }

  /**
   * Check is Accountant
   *
   * @return bool
   */
  isAccountant(): boolean {
    return this.inRole('accountant');
  }

  /**
   * Check is Sales
   *
   * @return bool
   */
  isSales(): boolean {
    return this.inRole('sales');
  }

  /**
   * Check is User
   *
   * @return bool
   */
  isUser(): boolean {
    return this.inRole('user');
  }

  /**
   * Check is Access Admin
   *
   * @return bool
   */
  isAccessAdmin(): boolean {
    return true;
    // return this.isSuperAdmin() || this.isAdmin() || this.isManager() || this.isAccountant() || this.isSales() || this.isUser();
    // return this.inAnyRole(['super-admin', 'admin', 'poster', 'manager', 'accountant', 'user']);
  }

  /***
   * Check is CRUD
   * @param module
   * @param crud
   * @return bool
   */
  isCRUD(module, crud: 'view_own' | 'view' | 'create' | 'edit' | 'delete' | string): boolean {
    if (this.isSuperAdmin()) return true;
    let permissions = this.permissions;
    let isAllow = permissions && permissions[module] && _.includes(permissions[module], crud);
    if (!isAllow) {
      for (let i = 0; i < this.roles.length; i++) {
        const role: any = this.roles[i];
        permissions = role.permissions ? role.permissions : [];
        isAllow = permissions && permissions[module] && _.includes(permissions[module], crud);
        if (isAllow) break;
      }
    }
    return isAllow;
  }
}
