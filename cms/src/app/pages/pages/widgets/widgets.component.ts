import { AfterViewInit, Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { WidgetsRepository as CoreWidgetsRepository } from '../../../@core/repositories';
import { ConfirmComponent } from '../../../@theme/modals';
import { ImageHelper } from '../../../@core/helpers/image.helper';
import { AppList } from '../../../app.base';
import { WidgetsRepository } from '../shared/services';
import { WidgetFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pg-widgets',
  templateUrl: './widgets.component.html',
})
export class WidgetsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(WidgetFormComponent) form: WidgetFormComponent;
  repository: WidgetsRepository;
  widgetData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(router: Router, security: Security, state: GlobalState, repository: WidgetsRepository, private _widgets: CoreWidgetsRepository) {
    super(router, security, state, repository);
    this.data.pageSize = 1000;
    this.data.sort = 'name';
    this.data.order = 'asc';
    this.data.data = {q: ''};
  }

  // Override fn
  protected getData(): void {
    this.data.loading = true;
    this.widgetData.loading = true;
    Promise.all([this.repository.get(this.data, false), this._widgets.all()]).then(([res, res2]) => {
      console.log(res, res2);
      this.data.items = res.data;
      this.widgetData.items = res2.data;
      this.data.loading = false;
      this.widgetData.loading = false;
      // Add if exits
      _.forEach(this.widgetData.items, (coreItem) => {
        const widget = _.find(this.data.items, {code: coreItem.id});
        const configs = _.extend({classes: [], properties: {}}, coreItem.configs);
        if (!widget) {
          this.data.items.push({name: coreItem.name, code: coreItem.id, preview: coreItem.preview, cf_data: configs});
        } else {
          widget.preview = coreItem.preview;
          widget.cf_data = configs;
        }
      });
      // Check removed
      _.forEach(this.data.items, (item) => {
        const widget = _.find(this.widgetData.items, {id: item.code});
        item.removed = !widget;
      });
    }, (res) => {
      console.log(res);
      this.data.loading = false;
      this.widgetData.loading = false;
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
      console.log(newParams);
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

  @ViewChild('fileUpload') protected _fileUpload: ElementRef;
  protected _widgetEdit: any;

  uploadFile(item): void {
    if (!item.id) return;
    this._widgetEdit = item;
    this._fileUpload.nativeElement.value = '';
    this._fileUpload.nativeElement.click();
  }

  onFiles(): void {
    const files = this._fileUpload.nativeElement.files;
    if (files.length) ImageHelper.resizeImage(files[0]).then((file) => {
      this.repository.uploadThumbnail(this._widgetEdit, this.utilityHelper.toFormData({file: file}), true).then((res: any) => {
        // console.log(res);
        const temps: string[] = this._widgetEdit.preview.split('?');
        this._widgetEdit.preview = temps[0] + '?v=' + new Date().getTime();
      }, (res: any) => {
        this._state.notifyDataChanged('modal.alert', _.extend({type: 'danger', title: 'Cảnh báo!'}, res));
      });
    });
  }
}
