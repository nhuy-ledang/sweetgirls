import { Component, EventEmitter, Input, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { ModalDirective } from 'ngx-bootstrap/modal';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { AppList } from '../../../../app.base';
import { CategoriesRepository as PdCategoriesRepository } from '../../../products/shared/services';
import { ManufacturersRepository as PdManufacturersRepository } from '../../../products/shared/services';
import { PagesRepository } from '../services';

@Component({
  selector: 'ngx-dlg-category-select',
  templateUrl: './dlg-category-select.component.html',
})

export class DlgCategorySelectComponent extends AppList implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess = new EventEmitter<{ids: number[], names: string[]}>();

  @Input() set limit(limit: boolean|number) {
    if (limit === false || (limit && typeof limit === 'number')) {
      this.data.limit = limit;
    }
  }

  source: 'pages'|'pd_manufacturers'|'pd_categories'|'pd_products';
  ids: number[];
  pageData: {loading: boolean, items: any[]} = {loading: false, items: []};
  pdManufacturerData: {loading: boolean, items: any[]} = {loading: false, items: []};
  pdCategoryData: {loading: boolean, items: any[]} = {loading: false, items: []};

  constructor(router: Router, security: Security, state: GlobalState,
              protected _pages: PagesRepository,
              protected _pdCategories: PdCategoriesRepository,
              protected _pdManufacturers: PdManufacturersRepository) {
    super(router, security, state);
  }

  protected setCategoryList(): void {
    if (this.source === 'pages') {
      this.data.items = _.cloneDeep(this.pageData.items);
    } else if (this.source === 'pd_manufacturers') {
      this.data.items = _.cloneDeep(this.pdManufacturerData.items);
    } else if (this.source === 'pd_categories' || this.source === 'pd_products') {
      this.data.items = _.cloneDeep(this.pdCategoryData.items);
      /*} else if (this.source === 'pd_products') {
        this.data.items = _.cloneDeep(this.pdCategoryData.items);*/
    } else {
      this.data.items = [];
    }
    this.data.selectList = [];
    _.forEach(this.data.items, (item) => {
      item.checkbox = _.includes(this.ids, item.id);
      if (item.checkbox) this.data.selectList.push(item);
    });
    this.data.selectAll = this.data.selectList.length === this.data.items.length;
    _.forEach(this.data.selectList, (item, index) => {
      item.selectIndex = index + 1;
    });
  }

  protected getAllPage(): void {
    this.pageData.loading = true;
    this._pages.all().then((res: any) => {
      console.log(res);
      this.pageData.loading = false;
      this.pageData.items = res.data;
      this.setCategoryList();
    }), (errors: any) => {
      this.pageData.loading = false;
      console.log(errors);
    };
  }

  protected getAllPdManufacturer(): void {
    this.pdManufacturerData.loading = true;
    this._pdManufacturers.all().then((res: any) => {
      console.log(res);
      this.pdManufacturerData.loading = false;
      this.pdManufacturerData.items = res.data;
      this.setCategoryList();
    }), (errors: any) => {
      this.pdManufacturerData.loading = false;
      console.log(errors);
    };
  }

  protected getAllPdCategory(): void {
    this.pdCategoryData.loading = true;
    this._pdCategories.all().then((res: any) => {
      console.log(res);
      this.pdCategoryData.loading = false;
      this.pdCategoryData.items = res.data;
      this.setCategoryList();
    }), (errors: any) => {
      this.pdCategoryData.loading = false;
      console.log(errors);
    };
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  show(source: 'pages'|'pd_manufacturers'|'pd_categories'|'pd_products', source_ids: string|null): void {
    this.source = source;
    this.ids = [];
    const ids: string[] = source_ids ? source_ids.split(',') : [];
    _.forEach(ids, (source_id: string) => this.ids.push(parseInt(source_id, 0)));
    console.log(source, this.ids);
    this.data.selectList = [];
    if (source === 'pages') {
      if (!this.pageData.items.length) this.getAllPage();
      else this.setCategoryList();
    } else if (source === 'pd_manufacturers') {
      if (!this.pdManufacturerData.items.length) this.getAllPdManufacturer();
      else this.setCategoryList();
    } else if (source === 'pd_categories' || source === 'pd_products') {
      if (!this.pdCategoryData.items.length) this.getAllPdCategory();
      else this.setCategoryList();
    }/* else if (source === 'pd_products') {
      if (!this.pdCategoryData.items.length) this.getAllPdCategory();
      else this.setCategoryList();
    }*/
    this.modal.show();
  }

  hide(): void {
    this.modal.hide();
  }

  checkboxClick(item: any): void {
    item.checkbox = !item.checkbox;
    return this.checkbox(item);
  }

  onSubmit(): void {
    this.hide();
    const d: {ids: number[], names: string[]} = {ids: [], names: []};
    _.forEach(this.data.selectList, (item: {id: number, name: string}) => {
      d.ids.push(item.id);
      d.names.push(item.name);
    });
    this.onSuccess.emit(d);
  }
}
