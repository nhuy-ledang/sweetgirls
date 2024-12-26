import { AfterViewInit, Component, ElementRef, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { AbstractControl, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Security } from '../../../@core/security';
import { GlobalState } from '../../../@core/utils';
import { ConfirmComponent } from '../../../@theme/modals';
import { ModalComponent, ModalService } from '../../../@theme/modal';
import { Err } from '../../../@core/entities';
import { FilemanagerRepository, MediaFoldersRepository, MediasRepository } from '../../../@core/repositories';
import { ImageHelper } from '../../../@core/helpers/image.helper';
import { AppList } from '../../../app.base';

@Component({
  selector: 'ngx-filemanager',
  templateUrl: './filemanager.component.html',
})
export class FilemanagerComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  resultData: {breadcrumbs?: any[], files?: any[], folders?: any[], tree?: any} = {};

  constructor(router: Router, security: Security, state: GlobalState, repository: FilemanagerRepository,
              private _formBuilder: FormBuilder, private _medias: MediasRepository, private _modalService: ModalService, private _folders: MediaFoldersRepository) {
    super(router, security, state, repository);
    this.data.pageSize = 50;
    this.data.sort = 'id';
    this.data.order = 'desc';
    this.data.data = {q: ''};
  }

  // Override fn
  protected getData(): void {
    this.data.loading = true;
    this.repository.get(this.data, false).then((res: any) => {
        console.log(res.data);
        this.resultData = _.extend({breadcrumbs: [], files: [], folders: [], tree: {}}, res.data);
        this.data.totalItems = res.pagination ? res.pagination.total : 0;
        this.data.loading = false;
      }, (res: {errors: Err[], data: any}) => {
        console.log(res.errors);
        this.data.loading = false;
      },
    );
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit(): void {
    setTimeout(() => this.getData(), 200);
  }

  actParent(): void {

  }

  actRefresh(): void {
    this.getData();
  }

  @ViewChild('fileUpload') protected _fileUpload: ElementRef;

  actUpload(): void {
    this._fileUpload.nativeElement.value = '';
    this._fileUpload.nativeElement.click();
  }

  // Upload file
  onFiles(): void {
    const files = this._fileUpload.nativeElement.files;
    ImageHelper.resizeImages(files).then((files) => {
      console.log(files);
      this._medias.uploads(this.utilityHelper.toFormData({files: files, folder_id: this.data.data.folder_id ? this.data.data.folder_id : ''}), true).then((res: any) => {
        console.log(res);
        this.onFilter();
      }, (res: {errors: Err[], data: any}) => {
        console.log(res.errors);
        this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', errors: res.errors[0].errorMessage});
      });
    });
  }

  @ViewChild('modalFolder') private _modal: ModalComponent;
  info: any;
  form: FormGroup;
  controls: {
    name?: AbstractControl,
  };
  showValid: boolean = false;

  protected buildForm(): void {
    this.showValid = false;
    this.submitted = false;
    if (!this.form) {
      this.form = this._formBuilder.group({
        name: ['', Validators.compose([Validators.required])],
      });
      this.controls = this.form.controls;
    } else {
      this.form.reset();
      this.controls.name.setValue('');
    }
  }

  actFolder(): void {
    this.buildForm();
    this._modalService.open(this._modal.id);
  }

  actFolderClose(): void {
    this.showValid = false;
    this.submitted = false;
    this._modalService.close(this._modal.id);
  }

  actFolderSubmit(params: any): void {
    this.showValid = true;
    if (this.form.valid) {
      const newParams = _.cloneDeep(params);
      if (this.data.data.folder_id) newParams['parent_id'] = this.data.data.folder_id;
      this.submitted = true;
      this._folders.create(newParams).then((res) => {
        console.log(res);
        this.actFolderClose();
        this.onFilter();
      }, (errors) => this.handleError(errors));
    }
  }

  private isSelectAll: boolean = false;

  actSelectAll(): void {
    this.isSelectAll = !this.isSelectAll;
    _.forEach(this.resultData.folders, (item) => {
      item.checkbox = this.isSelectAll;
    });
    _.forEach(this.resultData.files, (item) => {
      item.checkbox = this.isSelectAll;
    });
  }

  @ViewChild('delModal') private _delModal: ModalComponent;

  actDelete(): void {
    this._modalService.open(this._delModal.id);
  }

  actDeleteClose(): void {
    this.showValid = false;
    this.submitted = false;
    this._modalService.close(this._delModal.id);
  }

  private handleActDelete(): void {
    const folders = _.filter(this.resultData.folders, {checkbox: true});
    const folder_ids = [];
    _.forEach(folders, (item) => {
      folder_ids.push(item.id);
    });
    const files = _.filter(this.resultData.files, {checkbox: true});
    const file_ids = [];
    _.forEach(files, (item) => {
      file_ids.push(item.id);
    });
    console.log(folder_ids, file_ids);
    this._medias.deletes({folders: folder_ids, files: file_ids}, true).then((res: any) => {
      console.log(res);
      this.onFilter();
    }, (res: {errors: Err[], data: any}) => {
      console.log(res.errors);
      this._state.notifyDataChanged('modal.alert', {type: 'danger', title: 'Cảnh báo!', errors: res.errors[0].errorMessage});
    });
  }

  actDeleteSubmit(): void {
    this.handleActDelete();
    this.actDeleteClose();
  }

  selectBreadcrumb(item: any): void {
    console.log(item);
    this.data.data.folder_id = item.folder_id ? item.folder_id : '';
    this.onFilter();
  }

  selectFolder(item: any): void {
    console.log(item);
    this.data.data.folder_id = item.id;
    this.onFilter();
  }

  onTickFolder(): void {
    const folders = _.filter(this.resultData.folders, {checkbox: true});
    console.log(folders);
  }

  onTickFile(): void {
    const files = _.filter(this.resultData.files, {checkbox: true});
    console.log(files);
  }

  @ViewChild('mvModal') private _mvModal: ModalComponent;
  temps: any = {folder_id: '', name: '', watermark: false, position: 'top-left', x: 0, y: 10};
  positionList: any[] = [{id: 'top-left', name: 'Top Left'}, {id: 'top-right', name: 'Top Right'}, {id: 'bottom-left', name: 'Bottom Left'}, {id: 'bottom-right', name: 'Bottom Right'}];

  actMove(): void {
    this._modalService.open(this._mvModal.id);
  }

  actMoveClose(): void {
    this.showValid = false;
    this.submitted = false;
    this._modalService.close(this._mvModal.id);
  }

  actMoveSubmit(): void {
    const folder_id = this.temps.folder_id ? parseInt(this.temps.folder_id, 0) : false;
    if (folder_id) {
      const folders = _.filter(this.resultData.folders, {checkbox: true});
      const folder_ids = [];
      _.forEach(folders, (item) => {
        folder_ids.push(item.id);
      });
      const files = _.filter(this.resultData.files, {checkbox: true});
      const file_ids = [];
      _.forEach(files, (item) => {
        file_ids.push(item.id);
      });
      console.log(folder_ids, file_ids);
      this.submitted = true;
      this._medias.moves({folder_id: folder_id === -1 ? 0 : folder_id, files: file_ids, folders: folder_ids}).then((res) => {
        console.log(res);
        this.actMoveClose();
        this.onFilter();
      }, (errors) => this.handleError(errors));
    }
  }

  @ViewChild('edModal') private _edModal: ModalComponent;

  actEdit(item): void {
    console.log(item);
    this.data.itemSelected = item;
    this.temps.name = item.name;
    this.temps.watermark = !!item.watermark;
    this.temps.position = item.position;
    this.temps.x = item.x;
    this.temps.y = item.y;
    this._modalService.open(this._edModal.id);
  }

  actEditClose(): void {
    this.showValid = false;
    this.submitted = false;
    this._modalService.close(this._edModal.id);
  }

  actEditSubmit(): void {
    if (this.temps.name) {
      this.submitted = true;
      if (this.data.itemSelected.type === 'directory') {
        this._folders.update(this.data.itemSelected, {name: this.temps.name}).then((res) => {
          console.log(res);
          this.data.itemSelected.name = this.temps.name;
          this.actEditClose();
        }, (errors) => this.handleError(errors));
      } else {
        this._medias.update(this.data.itemSelected, {name: this.temps.name, watermark: this.temps.watermark ? 1 : 0, position: this.temps.position, x: this.temps.x, y: this.temps.y}).then((res) => {
          console.log(res.data);
          /*this.data.itemSelected.name = this.temps.name;
          this.data.itemSelected.watermark = !!this.temps.watermark;*/
          _.each(res.data, (val, key) => {
            if (this.data.itemSelected && this.data.itemSelected.hasOwnProperty(key)) this.data.itemSelected[key] = val;
          });
          this.actEditClose();
        }, (errors) => this.handleError(errors));
      }
    }
  }
}
