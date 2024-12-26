import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { OptionValuesRepository } from '../../services';
import { CategoryOptionFormComponent } from './form/form.component';
import { CategoriesRepository, CategoryOptionsRepository } from '../../shared/services';

@Component({
  selector: 'ngx-pd-category-options',
  templateUrl: './options.component.html',
})
export class CategoryOptionsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: CategoriesRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(CategoryOptionFormComponent) form: CategoryOptionFormComponent;
  info: any;
  valueData: {loading: boolean, items: any[]} = {loading: false, items: []};

  @Input() set category(item: any) {
    console.log(item);
    this.info = item;
    this.data.items = this.info.optionss;
    // this.data.data.category_id = item.id;
    // this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: CategoriesRepository,
              private _categories: CategoriesRepository,
              private _values: OptionValuesRepository) {
    super(router, security, state, repository);
    this.data.data.embed = '';
  }

  ngOnInit(): void {
    // if (!this.valueData.items.length) this.getAllValue();
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
  }

  create(): void {
    this.form.show(this.info);
  }

  edit(item: any): void {
    this.form.show(this.info, item);
    // this.form.show(_.extend(this.info, {'valueList': this.valueData.items ? this.valueData.items : []}), item);
  }

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeAll(): void {
    console.log(this.data.selectList);
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa các mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'removeAll'}});
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

  onFormSuccess(res: any): void {
    console.log(res);
    if (!res.edited) {
      this.data.page = 1;
      this.getData();
    } else {
      this.data.loading = true;
      this.repository.get().then((res: any) => {
        console.log(res);
        this.data.loading = false;
        const result = _.find(res.data, ['id', this.info.id]);
        this.info = result;
        this.data.items = this.info.optionss;
      }), (errors: any) => {
        this.data.loading = false;
        console.log(errors);
      };
    }
  }

  onChoiceSuccess(ids: []): void {
    console.log(ids);
    if (ids.length) {
      this._categories.createOptions(this.data.data.category_id, {ids: ids.join(',')}, true).then((res: any) => {
          this.data.page = 1;
          this.getData();
        }, (res) => {
          console.log(res.errors);
          this.data.loading = false;
        },
      );
    }
  }

  private getAllValue(): void {
    this.valueData.loading = true;
    this._values.all().then((res: any) => {
      this.valueData.loading = false;
      this.valueData.items = res.data;
      console.log(this.valueData.items);
    }), (errors: any) => {
      this.valueData.loading = false;
      console.log(errors);
    };
  }
}
