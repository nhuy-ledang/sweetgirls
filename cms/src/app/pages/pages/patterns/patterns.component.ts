import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { Dialog } from '../../../@core/services';
import { AppList } from '../../../app.base';
import { LayoutPatternsRepository } from '../shared/services';
import { PatternFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pg-patterns',
  templateUrl: './patterns.component.html',
})
export class PatternsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: LayoutPatternsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(PatternFormComponent) form: PatternFormComponent;

  constructor(router: Router, security: Security, state: GlobalState, repository: LayoutPatternsRepository, private _dialog: Dialog) {
    super(router, security, state, repository);
    this.data.sort = 'name';
    this.data.order = 'asc';
    this.data.data = {q: '', embed: 'descs'};
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  create(): void {
    this.form.show();
  }

  edit(item: any): void {
    this.form.show(item);
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
}
