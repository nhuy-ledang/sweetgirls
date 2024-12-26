import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { Err } from '../../../../@core/entities';
import { AppList } from '../../../../app.base';
import { ProductsRepository } from '../../shared/services';
import { SpecialFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-specials',
  templateUrl: './specials.component.html',
})
export class SpecialsComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: ProductsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(SpecialFormComponent) form: SpecialFormComponent;
  info: any;
  @Input() showTitle: boolean = true;

  @Input() set product(item: any) {
    console.log(item);
    this.info = item;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: ProductsRepository) {
    super(router, security, state, repository);
  }

  protected getSpecials(): void {
    this.data.loading = true;
    this.repository.getSpecials(this.info.id, false).then((res: any) => {
        console.log(res.data);
        this.data.items = [];
        _.forEach(res.data, (item, index) => {
          item.index = index + (this.data.pageSize * (this.data.page - 1));
          this.data.items.push(item);
        });
        // this.data.totalItems = res.pagination ? res.pagination.total : 0;
        this.data.loading = false;
      }, (res: {errors: Err[], data: any}) => {
        console.log(res.errors);
        this.data.loading = false;
      },
    );
  }

  // Override fn
  protected getData(): void {
    return this.getSpecials();
  }

  ngOnInit(): void {
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
  }

  removeItem(item: any): void {
    this.submitted = true;
    this.repository.removeSpecial(this.info.id, item.id).then((res) => {
        console.log(res);
        this.submitted = false;
        this.data.page = 1;
        this.getData();
      }, (res: {errors: Err[], data: any}) => {
        this.handleError(res);
      },
    );
  }

  remove(item: any) {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }
}
