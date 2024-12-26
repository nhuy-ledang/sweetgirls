import { FormBuilder, FormControl, FormGroup } from '@angular/forms';
import { NavigationStart, Router } from '@angular/router';
import { Subscription } from 'rxjs';
import { Options } from 'sortablejs';
import { TabsetComponent } from 'ngx-bootstrap/tabs';
import { Err, Usr } from './@core/entities';
import { Security } from './@core/security';
import { Api, CookieVar } from './@core/services';
import { StorageHelper, UtilityHelper } from './@core/helpers';
import { GlobalState } from './@core/utils';
import { LanguagesRepository } from './@core/repositories';
import { CONSTANTS } from './app.constants';

let identifier = 0;

export abstract class AppBase {
  protected subscriptions: Array<Subscription> = [];
  protected utilityHelper: UtilityHelper = new UtilityHelper();
  protected storageHelper: StorageHelper = new StorageHelper();
  protected repository: Api;
  identifier: number = 0;
  CONST: any = CONSTANTS;
  submitted: boolean = false;
  errors: Err[] = [];
  auth: Usr;

  // Global config
  bsConfig: any = {dateInputFormat: 'DD/MM/YYYY', adaptivePosition: true, containerClass: 'theme-red'};

  dateOptions: {minDate?: any, maxDate?: any, date?: any, type?: string|'birthday'|'future'} = {type: 'future'};
  langData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(protected _router: Router, protected _security: Security, protected _state: GlobalState, repository?: Api, protected _languages?: LanguagesRepository) {
    this.identifier = ++identifier;
    this.auth = _security.getCurrentUser();
    this.repository = repository;

    this._router.events.subscribe((event) => {
      if (event instanceof NavigationStart) {
        // console.log(event);
      }
    });
  }

  destroy(): void {
    this.subscriptions.forEach((subscription: Subscription) => subscription.unsubscribe());
  }

  getStorage(prop?: any) {
    return this.storageHelper.get(prop);
  }

  setStorage(prop: string, value: any) {
    return this.storageHelper.set(prop, value);
  }

  unsetStorage(prop: string) {
    return this.storageHelper.unset(prop);
  }

  addSubscription(subscription: Subscription): void {
    this.subscriptions.push(subscription);
  }

  protected handleError(res: {errors: Err[], data: any}): void {
    console.log(res);
    this.errors = res.errors;
    this.submitted = false;
    this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
  }

  isNumber(val): boolean {
    return typeof val === 'number';
  }

  isArray(val): boolean {
    return _.isArray(val);
  }

  isObject(val): boolean {
    return _.isObject(val) && !_.isArray(val);
  }

  // Permission
  isSuperAdmin(): boolean {
    // return this.auth && this.auth.isSuperAdmin();
    return this.inAnyRole(['super-admin']);
  }

  isAdmin(): boolean {
    // return this.auth && this.auth.isAdmin();
    return this.inAnyRole(['admin']);
  }

  inAnyRole(roles: string[]): boolean {
    return this.auth && this.auth.inAnyRole(roles);
  }

  /***
   * Check is CRUD
   * @param module
   * @param crud
   * @return bool
   */
  isCRUD(module, crud: 'view_own'|'view'|'create'|'edit'|'delete'|string): boolean {
    return this.auth.isCRUD(module, crud);
  }

  protected getAllLanguage(): void {
    this.langData.loading = true;
    this._languages.all().then((res: any) => {
      // console.log('getAllLanguage', res);
      this.langData.loading = false;
      this.langData.items = res;
    }), (errors: any) => {
      this.langData.loading = false;
      console.log(errors);
    };
  }
}

export abstract class AppList extends AppBase {
  data: {selectAll?: boolean, selectList: any[], itemSelected: any, limit: number|boolean, loading?: boolean, items: any[], paging: number, page: number, pageSize: number, totalItems: number, maxSize: number, sort: string, order: string, data: any} = {
    selectAll: false,
    selectList: [],
    itemSelected: null,
    limit: false,
    loading: false,
    items: [],
    paging: 1,
    page: 1,
    pageSize: 25,
    totalItems: 0,
    maxSize: 10,
    sort: 'id',
    order: 'desc',
    data: {q: ''},
  };
  filters: any = {};

  /*** Index function ***/
  protected setDataFilter() {
    if (this.data.data.filter) delete this.data.data.filter;
    if (!_.isEmpty(this.filters)) this.data.data.filter = this.filters;
  }

  // Find and update data
  protected find(id: number): void {
    this.repository.find(id, this.data.data, false).then((res: any) => {
        // console.log(res.data);
        const newItem = res.data;
        const index = _.findIndex(this.data.items, {id: newItem.id});
        if (index > -1) {
          this.data.items[index] = newItem;
          if (this.data.itemSelected && this.data.itemSelected.id === newItem.id) this.data.itemSelected = newItem;
        }
      }, (res: {errors: Err[], data: any}) => {
        console.log(res.errors);
      },
    );
  }

  // Function for override
  protected afterDataLoading(): void {
  }

  // Get data table
  protected getData(cb?: Function, loading?: boolean, repository?: Api, method?: string|'get'): void {
    this.setDataFilter();
    if (loading !== false) this.data.loading = true;
    (repository ? repository : this.repository)[method ? method : 'get'](this.data, false).then((res: any) => {
        // console.log(res.data);
        // this.data.items = res.data;
        this.data.items = [];
        _.forEach(res.data, (item, index) => {
          item.index = index + (this.data.pageSize * (this.data.page - 1));
          this.data.items.push(item);
        });
        this.data.totalItems = res.pagination ? res.pagination.total : 0;
        if (this.data.itemSelected) this.data.itemSelected = _.find(this.data.items, {id: this.data.itemSelected.id});
        this.data.loading = false;
        if (typeof cb === 'function') cb();
        this.afterDataLoading();
      }, (res: {errors: Err[], data: any}) => {
        console.log(res.errors);
        this.data.loading = false;
      },
    );
  }

  // <editor-fold desc="Pagination">
  setPage(pageNo: number): void {
    this.data.page = pageNo;
  }

  pageChanged(event?: {page: number, itemsPerPage: number}): void {
    if (this.data.page !== event.page || this.data.pageSize !== event.itemsPerPage) {
      this.data.page = event.page;
      this.data.pageSize = event.itemsPerPage;
      this.getData();
    }
  }

  pageSizeChanged(pageSize: number): void {
    this.data.pageSize = pageSize;
    this.getData();
  }

  // </editor-fold>

  // <editor-fold desc="Select">
  checkbox(item: any): void {
    setTimeout(() => {
      const check = _.find(this.data.selectList, {id: item.id});
      if (item.checkbox && !check) {
        if (this.data.limit && this.data.selectList.length >= this.data.limit) {
          console.log('Limit: ' + this.data.limit);
          this.data.selectList = [item];
        } else {
          this.data.selectList.push(item);
        }
      } else if (!item.checkbox && check) {
        _.remove(this.data.selectList, {id: item.id});
      }
      this.data.selectAll = this.data.selectList.length === this.data.items.length;
      _.forEach(this.data.selectList, (item, index) => item.selectIndex = index + 1);
    });
  }

  isSelected(item: any): boolean {
    return !!_.find(this.data.selectList, {id: item.id});
  }

  selectAll(): void {
    this.data.selectList = [];
    for (let i = 0; i < this.data.items.length; i++) {
      this.data.items[i].checkbox = this.data.selectAll;
      if (this.data.selectAll) this.data.selectList.push(this.data.items[i]);
    }
  }

  // </editor-fold>

  // <editor-fold desc="Sort">
  setSortBy(sort?: string, order?: string): void {
    this.data.sort = sort ? sort : 'id';
    this.data.order = order ? order : 'desc';
  }

  sortBy(sort: string): void {
    this.setSortBy(sort, this.data.order === 'desc' ? 'asc' : 'desc');
    this.getData();
  }

  sortClass(sort: string): string {
    let classes = 'sortable';
    if (this.data.sort === sort) classes += ' sort-' + this.data.order;
    return classes;
  }

  // </editor-fold>

  // <editor-fold desc="Columns">
  protected cookie: CookieVar;
  protected moduleName: string = '...';
  reorder: string[] = [];
  private columnArchive: {id: string, name: string, checkbox?: boolean, disabled?: boolean, order?: number}[] = [];
  columnList: {id: string, name: string, checkbox?: boolean, disabled?: boolean, order?: number}[] = [];
  columns: any = {};

  columnCheckbox(): void {
    const reorder: string[] = [];
    _.forEach(this.columnList, (item: any) => {
      reorder.push(item.id);
      if (!item.disabled) this.columns[item.id] = item.checkbox;
    });
    this.reorder = reorder;
    this.cookie.set(this.moduleName + '.table', {reorder: reorder, columns: this.columns});
  }

  columnInt(cookie: CookieVar, moduleName: string): void {
    this.columnArchive = _.cloneDeep(this.columnList);
    this.cookie = cookie;
    this.moduleName = moduleName;
    const data: {reorder: string[], columns: any} = _.extend({reorder: [], columns: {}}, this.cookie.get(this.moduleName + '.table'));
    this.columns = _.extend({}, data.columns);
    if (!data.reorder.length) for (let i = 0; i < this.columnList.length; i++) data.reorder.push(this.columnList[i].id);
    for (let i = 0; i < this.columnList.length; i++) {
      if (_.isBoolean(this.columns[this.columnList[i].id])) {
        this.columnList[i].checkbox = !!this.columns[this.columnList[i].id];
      } else {
        this.columns[this.columnList[i].id] = this.columnList[i].checkbox;
      }
      this.columnList[i].order = data.reorder.indexOf(this.columnList[i].id);
    }
    this.columnList.sort((a, b) => {
      return (a.order > b.order ? 1 : -1);
    });
    const reorder: string[] = [];
    for (let i = 0; i < this.columnList.length; i++) reorder.push(this.columnList[i].id);
    this.reorder = reorder;
  }

  columnReset(): void {
    this.columnList = _.cloneDeep(this.columnArchive);
    this.columnCheckbox();
  }

  optReorder: Options = {group: '...', forceFallback: true, dragoverBubble: true, onSort: () => this.columnCheckbox()};

  // </editor-fold>

  // <editor-fold desc="Other fn">
  removeItem(item: any, repository?: Api|null, method?: string|'remove'|'remove_silent') {
    let loading: boolean = true;
    // let silent: boolean = false;
    if (method === 'remove_silent') {
      method = 'remove';
      // _.remove(this.data.items, {id: item.id});
      loading = false;
      // silent = true;
      item.is_deleted = true;
    }
    this.data.itemSelected = null;
    this.submitted = true;
    (repository ? repository : this.repository)[method ? method : 'remove'](item, loading).then((res) => {
        console.log(res);
        this.submitted = false;
        // this.data.page = 1;
        this.getData(null, loading);
      }, (res: {errors: Err[], data: any}) => {
        // if (silent) this.getData(null, loading);
        delete item.is_deleted;
        this.handleError(res);
      },
    );
  }

  removeAll(): void {
    console.log(this.data.selectList);
  }

  // </editor-fold>

  // <editor-fold desc="Events">
  private timer: any;

  onFilter(event?: any): void {
    this.data.selectList = [];
    this.data.selectAll = false;
    if (this.timer) {
      clearTimeout(this.timer);
      this.timer = undefined;
    }
    this.timer = setTimeout(() => {
      this.data.page = 1;
      this.getData();
    }, 800);
  }

  onFormSuccess(res: any): void {
    console.log(res);
    if (!res.edited) {
      this.data.page = 1;
      this.getData();
    } else {
      this.getData(null, false);
    }
  }

  // </editor-fold>

  onDatePickerSuccess(): void {
    this.getData();
  }
}

export abstract class AppForm extends AppBase {
  showValid: boolean = false;
  onSuccess: any;
  info: any;
  fb: FormBuilder;
  form: FormGroup;

  /*** Reset validator form ***/
  protected resetForm(form: FormGroup): void {
    this.submitted = false;
    this.showValid = false;
    _.forEach(form.controls, function(control, name) {
      control.markAsUntouched();
      control.markAsPristine();
      if (control instanceof FormGroup) {
        if (name === 'passwords') {
          control.reset();
        }
        // _.forEach(control.controls, function (c) {
        //   c.markAsUntouched(true);
        //   c.markAsPristine(true);
        // });
      } else if (control instanceof FormControl) {

      }
    });
  }

  // Set info
  protected setInfo(info: any): void {
    console.log(info);
  }

  // Error info
  protected errorInfo(res: {errors: any[], data: any}): void {
    console.log(res);
  }

  // Get info
  protected getInfo(id: number, data?: any, loading?: boolean): void {
    this.repository.find(id, data, loading).then((res: any) => this.setInfo(res.data), (res: any) => this.errorInfo(res));
  }

  show(...args): void {
  }

  hide(): void {
    this.showValid = false;
    this.submitted = false;
    this.tabs = {};
    this.selectTab(0);
    // Clear cache
    this.file = null;
    this.fileSelected = null;
    this.fileOpt = _.extend(_.cloneDeep(this.fileOpt), {thumb_url: ''});
    this.icFile = null;
    this.icSelected = null;
    this.icOpt = _.extend(_.cloneDeep(this.icOpt), {thumb_url: ''});
    this.bnFile = null;
    this.bnSelected = null;
    this.bnOpt = _.extend(_.cloneDeep(this.bnOpt), {thumb_url: ''});
  }

  protected handleSuccess(res: any, dontHide?: boolean): void {
    this.submitted = false;
    if (dontHide !== true) this.hide();
    this.onSuccess.emit(res);
  }

  // <editor-fold desc="Upload file">
  file: File = null;
  fileSelected: {path: string} = null;
  fileOpt: any = {thumb_url: '', aspect_ratio: '16by9'};

  onFileSelected(event: File|any): void {
    if (event.type === 'select') {
      this.fileSelected = event;
      this.file = null;
    } else {
      this.fileSelected = null;
      this.file = event;
    }
  }

  onFileDeleted(event): void {
    this.file = null;
    this.fileSelected = null;
    this.fileOpt.thumb_url = '';
  }

  // Icon
  icOpt: any = {thumb_url: '', aspect_ratio: '16by9'};
  icFile: File = null;
  icSelected: {path: string} = null;

  onIcSelected(event: File|any): void {
    if (event.type === 'select') {
      this.icSelected = event;
      this.icFile = null;
    } else {
      this.icSelected = null;
      this.icFile = event;
    }
  }

  onIcDeleted(event): void {
    this.icFile = null;
    this.icSelected = null;
    this.icOpt.thumb_url = '';
  }

  // Banner
  bnOpt: any = {thumb_url: '', aspect_ratio: '16by9'};
  bnFile: File = null;
  bnSelected: {path: string} = null;

  onBnSelected(event: File|any): void {
    if (event.type === 'select') {
      this.bnSelected = event;
      this.bnFile = null;
    } else {
      this.bnSelected = null;
      this.bnFile = event;
    }
  }

  onBnDeleted(event): void {
    this.bnFile = null;
    this.bnSelected = null;
    this.bnOpt.thumb_url = '';
  }

  // </editor-fold>

  formTabs?: TabsetComponent;
  tabs: any = {};

  selectTab(tabId: number): void {
    if (this.formTabs?.tabs[tabId]) this.formTabs.tabs[tabId].active = true;
  }

  onSelectTab(tabActive: string): void {
    this.tabs[tabActive] = true;
  }

  onDescSuccess($event: {d: any, is_close: boolean}): void {
    const newInfo: any = $event.d;
    if ($event.is_close) {
      this.handleSuccess(_.extend({edited: true}, newInfo));
    } else {
      const cloneInfo: any = _.cloneDeep(this.info);
      _.each(cloneInfo, (val, key) => {
        if (newInfo.hasOwnProperty(key)) cloneInfo[key] = newInfo[key];
      });
      this.info = cloneInfo;
      console.log(this.info);
      this._state.notifyDataChanged('modal.success', {title: 'Thành công!', message: 'Đã cập nhật thành công!'});
    }
  }
}
