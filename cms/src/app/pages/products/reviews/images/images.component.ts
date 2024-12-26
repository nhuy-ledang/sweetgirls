import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { Err } from '../../../../@core/entities';
import { AppList } from '../../../../app.base';
import { ReviewsRepository } from '../../services';
import { ReviewImageFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-rvw-images',
  templateUrl: './images.component.html',
})
export class ReviewImagesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: ReviewsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ReviewImageFormComponent) form: ReviewImageFormComponent;
  info: any;

  @Input() set review(item: any) {
    console.log(item);
    this.info = item;
    this.getData();
  }

  constructor(router: Router, security: Security, state: GlobalState, repository: ReviewsRepository) {
    super(router, security, state, repository);
  }

  protected getImages(): void {
    this.data.loading = true;
    this.repository.getImages(this.info.id, false).then((res: any) => {
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
    return this.getImages();
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

  remove(item: any): void {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  removeItem(item: any): void {
    this.submitted = true;
    this.repository.removeImage(this.info.id, item.id).then((res) => {
        console.log(res);
        this.submitted = false;
        this.data.page = 1;
        this.getData();
      }, (res: {errors: Err[], data: any}) => {
        this.handleError(res);
      },
    );
  }

  onConfirm(data: any) {
    console.log(data);
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }
}
