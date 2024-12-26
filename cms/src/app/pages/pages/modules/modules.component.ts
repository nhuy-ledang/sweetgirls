import { AfterViewInit, Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ModulesRepository as CoreModulesRepository } from '../../../@core/repositories';
import { ConfirmComponent } from '../../../@theme/modals';
import { Dialog } from '../../../@core/services';
import { ImageHelper } from '../../../@core/helpers/image.helper';
import { AppList } from '../../../app.base';
import { ModulesRepository } from '../shared/services';
import { ModuleFormComponent } from './form/form.component';
import { ModuleUserGuideComponent } from './user-guide/user-guide.component';

@Component({
  selector: 'ngx-pg-modules',
  templateUrl: './modules.component.html',
})
export class ModulesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ModuleFormComponent) form: ModuleFormComponent;
  @ViewChild(ModuleUserGuideComponent) userGuide: ModuleUserGuideComponent;
  repository: ModulesRepository;
  moduleData: {loading: boolean, modules: any[], pages: any[]} = {loading: false, modules: [], pages: []};

  constructor(router: Router, security: Security, state: GlobalState, repository: ModulesRepository, private _modules: CoreModulesRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    this.data.pageSize = 1000;
    this.data.sort = 'sort_order';
    this.data.order = 'asc';
    this.data.data = {embed: 'descs'};
  }

  // Override fn
  protected getData(): void {
    this.data.loading = true;
    this.moduleData.loading = true;
    Promise.all([this.repository.get(this.data, false), this._modules.all()]).then(([res, res2]) => {
      this.data.items = [];
      _.forEach(res.data, (item, index) => {
        item.index = index + (this.data.pageSize * (this.data.page - 1));
        this.data.items.push(item);
      });
      this.moduleData = _.extend(this.moduleData, res2.data);
      // console.log(this.moduleData);
      this.data.loading = false;
      this.moduleData.loading = false;
      // Add if exits
      _.forEach(this.moduleData.modules, (coreItem) => {
        const module = _.find(this.data.items, {code: coreItem.id});
        if (!module) {
          const layouts: any = {};
          let length = 1;
          layouts['layout1'] = {'id': 'layout1', 'name': 'Layout 1', 'preview': coreItem.configs && coreItem.configs.template_url ? coreItem.configs.template_url : ''};
          if (coreItem.layouts) _.forEach(coreItem.layouts, (layout: any) => {
            layouts[layout.id] = {'id': layout.id, 'name': layout.name ? layout.name : ('Layout ' + length++), preview: layout.preview};
          });
          const newVal = [];
          _.forEach(layouts, (item) => newVal.push(item));
          this.data.items.push({name: coreItem.name, code: coreItem.id, cf_data: coreItem, layouts: newVal});
        } else {
          module.cf_data = coreItem;
          // module.layouts = [].concat(item.layouts);
        }
      });
      // Check removed
      _.forEach(this.data.items, (item) => {
        const module = _.find(this.moduleData.modules, {id: item.code});
        item.removed = !module;
      });
    }, (res) => {
      console.log(res);
      this.data.loading = false;
      this.moduleData.loading = false;
    });
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  create(item): void {
    if (!item.id) {
      const newParams = _.cloneDeep(item);
      this.repository.create(newParams, true).then((res) => {
        _.each(res.data, (val, key) => item[key] = val);
        this.data.itemSelected = item;
        this.form.show(item);
      }, (res) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    }
  }

  edit(item: any): void {
    this.data.itemSelected = item;
    this.form.show(item);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  openPreview(item, layout: any): void {
    if (!item.id) return;
    this._dialog.open(layout.preview_url, item.name, {width: 1440, height: 768}).then((res) => {
      console.log(res);
    }, (res) => {
      console.log(res);
    });
  }

  updateUserGuide(item: any): void {
    this.data.itemSelected = item;
    this.userGuide.show(item);
  }

  updateConfig(item: any): void {
    this.repository.updateConfigs(item, item.cf_data, true).then((res) => {
      console.log(res.data);
    }, (res: any) => {
      this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
    });
  }

  updateConfigs(): void {
    const promises = [];
    _.forEach(this.data.items, (item) => {
      if (item.id) promises.push(this.repository.updateConfigs(item, item.cf_data, true));
    });
    if (promises.length) Promise.all(promises).then((res) => {
      console.log(res);
    }, (res) => {
      console.log(res);
    });
  }

  onConfirm(data: any): void {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }

  onFormSuccess(res: any): void {
    console.log(res);
    _.each(res, (val, key) => {
      this.data.itemSelected[key] = val;
    });
  }

  onUserGuideSuccess(res: any): void {
    console.log(res);
    if (this.data.itemSelected) this.data.itemSelected.user_guide = res.user_guide;
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
        _.forEach(this.data.items, (item, index) => {
          if (item.id) data[item.id] = index;
        });
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

  @ViewChild('fileUpload') protected _fileUpload: ElementRef;
  protected _moduleEdit: any;
  protected _layoutEdit: any;

  uploadFile(item, layout: any): void {
    if (!item.id) return;
    this._moduleEdit = item;
    this._layoutEdit = layout;
    this._fileUpload.nativeElement.value = '';
    this._fileUpload.nativeElement.click();
  }

  onFiles(): void {
    const files = this._fileUpload.nativeElement.files;
    if (files.length) ImageHelper.resizeImage(files[0]).then((file) => {
      this.repository.uploadThumbnail(this._moduleEdit, this.utilityHelper.toFormData({file: file, layout: this._layoutEdit.id}), true).then((res: any) => {
        // console.log(res);
        const temps: string[] = this._layoutEdit.preview.split('?');
        this._layoutEdit.preview = temps[0] + '?v=' + new Date().getTime();
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    });
  }
}
