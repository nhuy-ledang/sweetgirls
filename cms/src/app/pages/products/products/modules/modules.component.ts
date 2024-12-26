import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { Dialog } from '../../../../@core/services';
import { AppList } from '../../../../app.base';
import { ProductModulesRepository } from '../../services';
import { ProductModuleFormComponent } from './form/form.component';
import { DlgModuleSelectComponent, DlgPatternSelectComponent } from '../../../pages/shared/modals';

@Component({
  selector: 'ngx-pd-modules',
  templateUrl: './modules.component.html',
})
export class ProductModulesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: ProductModulesRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ProductModuleFormComponent) form: ProductModuleFormComponent;
  @ViewChild(DlgModuleSelectComponent) dlgModule: DlgModuleSelectComponent;
  info: any;

  @Input() set product(item: any) {
    this.info = item;
    this.data.data.product_id = item.id;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductModulesRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    this.data.sort = 'name';
    this.data.order = 'asc';
    this.data.data = {product_id: 0, embed: 'descs,module'};
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
  }

  create(): void {
    // this.form.show(this.info);
    this.dlgModule.show();
  }

  edit(item: any): void {
    this.form.show(item);
  }

  copy(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn copy mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'copy', info: item}});
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa các mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'removeAll'}});
  }

  openPreview(item): void {
    if (!item.id) return;
    this._dialog.open(item.preview_url, item.name, {width: 1440, height: 768}).then((res) => {
      console.log(res);
    }, (res) => {
      console.log(res);
    });
  }

  onConfirm(data: any): void {
    if (data.type === 'remove') {
      this.removeItem(data.info);
    } else if (data.type === 'removeAll') {
      console.log(this.data.selectList);
      const promises = [];
      _.forEach(this.data.selectList, (item) => promises.push(this.repository.remove(item)));
      this.submitted = true;
      Promise.all(promises).then((res) => {
        // console.log(res);
        this.submitted = false;
        this.data.selectList = [];
        this.data.selectAll = false;
        this.data.page = 1;
        this.getData();
      }, (res) => {
        // console.log(res);
        this.submitted = false;
        this.data.selectList = [];
        this.data.selectAll = false;
        this.data.page = 1;
        this.getData();
      });
    } else if (data.type === 'copy') {
      this.repository.copy(data.info, false).then((res) => {
        console.log(res.data);
        this.getData();
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    }
  }

  changeProp(item: any, propName: string): void {
    console.log(item);
    const data = {};
    data[propName] = item[propName];
    this.repository.patch(item, data).then((res) => {
      console.log(res.data);
    }, (errors) => {
      console.log(errors);
    });
  }

  private sortTimer: any;

  private sortRun(): void {
    if (this.sortTimer) {
      clearTimeout(this.sortTimer);
      this.sortTimer = undefined;
    }
    this.sortTimer = setTimeout(() => {
      if (this.data.items.length > 1) {
        const data = {};
        _.forEach(this.data.items, (item, index) => data[item.id] = index);
        console.log(data);
        this.repository.sortOrder({order: data}).then((res) => {
          console.log(res.data);
        }, (res: any) => {
          console.log(res);
          // this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
        });
      }
    }, 2000);
  }

  sortOrder(item: any, sort_order: number, char: '-'|'+'): void {
    if (char === '-' && sort_order <= 0) return;
    if (char === '+' && sort_order >= this.data.items.length - 1) return;
    const temp = char === '-' ? this.data.items[sort_order - 1] : this.data.items[sort_order + 1];
    if (char === '-') {
      this.data.items[sort_order - 1] = item;
    } else {
      this.data.items[sort_order + 1] = item;
    }
    this.data.items[sort_order] = temp;
    // Update sort order
    this.sortRun();
  }

  onModuleSelect(res: any[]): void {
    const ids = [];
    _.forEach(res, (item: any) => ids.push(item.id));
    if (ids.length) this.repository.cloneModules({product_id: this.info.id, ids: ids}, true).then((res) => {
      console.log(res.data);
      this.data.page = 1;
      this.getData();
    }, (res: any) => {
      this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
    });
  }

  @ViewChild(DlgPatternSelectComponent) dlgPattern: DlgPatternSelectComponent;

  openLib(): void {
    this.dlgPattern.show();
  }

  onPatternSelect(res: any[]): void {
    const ids = [];
    _.forEach(res, (item: any) => ids.push(item.id));
    if (ids.length) this.repository.clonePatterns({product_id: this.info.id, ids: ids}, true).then((res) => {
      console.log(res.data);
      this.data.page = 1;
      this.getData();
    }, (res: any) => {
      this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
    });
  }
}

