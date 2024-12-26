import { AfterViewInit, Component, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Security } from '../../../../@core/security';
import { GlobalState } from '../../../../@core/utils';
import { ConfirmComponent } from '../../../../@theme/modals';
import { Err } from '../../../../@core/entities';
import { AppList } from '../../../../app.base';
import { ProductsRepository } from '../../shared/services';
import { ImageFormComponent } from './form/form.component';

@Component({
  selector: 'ngx-pd-images',
  templateUrl: './images.component.html',
})
export class ImagesComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  repository: ProductsRepository;
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild(ImageFormComponent) form: ImageFormComponent;
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

  creates(): void {
    if (typeof window['timerNew'] !== 'undefined') {
      clearInterval(window['timerNew']);
      window['timerNew'] = undefined;
    }
    window['timerNew'] = undefined;

    const fileInput: HTMLInputElement = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = 'file';
    fileInput.accept = '.jpg,.png,image/jpeg,image/png';
    fileInput.multiple = true;

    jQuery('#form-upload').remove();
    jQuery('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"></form>');
    jQuery('#form-upload').append(fileInput);
    fileInput.click();

    window['timerNew'] = setInterval(() => {
      if (fileInput.value) {
        clearInterval(window['timerNew']);
        window['timerNew'] = undefined;

        const formData = new FormData(document.getElementById('form-upload') as HTMLFormElement);
        for (let i = 0; i < fileInput.files.length; i++) {
          formData.append('files[]', fileInput.files[i]);
        }
        console.log(this.info);
        this.repository.createImages(this.info.id, formData, true).then((res: any) => {
          this.getData();
        }, (errors) => {
          console.log(errors);
        });
      }
    }, 500);
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
      _.forEach(this.data.selectList, (item) => promises.push(this.repository.removeImage(this.info.id, item.id)));
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

  private sortTimer: any;
  sorted: boolean = true;

  onDrag(event: any): void {
    this.sorted = false;
    this.data.selectList = [];
    if (this.sortTimer) {
      clearTimeout(this.sortTimer);
      this.sortTimer = undefined;
    }
    this.sortTimer = setTimeout(() => {
      console.log('Drag End event:', event);
      // Get id, sort_order
      const sortedItems = this.data.items.map((item, index) => ({id: item.id, sort_order: index + 1}));
      this.repository.patchImage(this.info, {data: sortedItems}).then((res) => {
        console.log(res.data);
        this.data.items = res.data;
        this.sorted = true;
      }, (errors) => {
        console.log(errors);
      });
    }, 2000);
  }
}
