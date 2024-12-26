import { Component, Input, OnDestroy, OnInit } from '@angular/core';
import { AbstractControl, FormGroup } from '@angular/forms';

@Component({
  selector: 'ngx-pg-module-form-properties',
  templateUrl: './module-form-properties.component.html',
})
export class ModuleFormPropertiesComponent implements OnInit, OnDestroy {
  form: FormGroup;
  controls: {
    source?: AbstractControl,
    category_id?: AbstractControl,
    page_id?: AbstractControl,
    primaryColor?: AbstractControl,
    secondaryColor?: AbstractControl,
    successColor?: AbstractControl,
    imgSize?: AbstractControl,
    imgFrame?: AbstractControl,
    bgColor?: AbstractControl,
    titleColor?: AbstractControl,
    textColor?: AbstractControl,
    bgImg?: AbstractControl,
    bgSize?: AbstractControl,
    cont?: AbstractControl,
    mt?: AbstractControl,
    mb?: AbstractControl,
    pt?: AbstractControl,
    pb?: AbstractControl,
    mtxl?: AbstractControl,
    mbxl?: AbstractControl,
    ptxl?: AbstractControl,
    pbxl?: AbstractControl,
    youtube?: AbstractControl,
    col?: AbstractControl,
    spacingCol?: AbstractControl,
    row?: AbstractControl,
    textRow?: AbstractControl,
    colMb?: AbstractControl,
    spacingColMb?: AbstractControl,
    rowMb?: AbstractControl,
    textRowMb?: AbstractControl,
    button?: AbstractControl,
    textButton?: AbstractControl,
    linkButton?: AbstractControl,
    btnModuleLink?: AbstractControl,
    menu?: AbstractControl,
    textMenu?: AbstractControl,
    reverse?: AbstractControl,
  };

  @Input() set data(d: {form: FormGroup, controls: AbstractControl|any}) {
    this.form = d.form;
    this.controls = d.controls;
  }

  constructor() {
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
  }
}
