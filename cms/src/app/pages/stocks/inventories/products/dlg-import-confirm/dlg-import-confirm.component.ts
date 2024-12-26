import { Component, EventEmitter, OnDestroy, OnInit, Output, ViewChild } from '@angular/core';
import { ModalDirective } from 'ngx-bootstrap/modal';

@Component({
  selector: 'ngx-sto-ivt-products-dlg-import-confirm',
  templateUrl: './dlg-import-confirm.component.html',
})

export class DlgImportConfirmComponent implements OnInit, OnDestroy {
  @ViewChild('modal', {static: true}) modal: ModalDirective;
  @Output() onSuccess: EventEmitter<any> = new EventEmitter<any>();

  formData: FormData;
  importData: { valid: any[], invalid: any[] } = {valid: [], invalid: []};

  constructor() {
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
  }

  show(formData: FormData, importData: any): void {
    this.formData = formData;
    this.importData = _.extend(this.importData, importData);
    console.log(this.importData);
    this.modal.show();
  }

  hide(): void {
    this.modal.hide();
  }

  onSubmit(): void {
    this.hide();
    this.onSuccess.emit({formData: this.formData});
  }
}
