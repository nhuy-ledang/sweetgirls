import { ModuleWithProviders, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { CollapseModule } from 'ngx-bootstrap/collapse';
import { PaginationModule } from 'ngx-bootstrap/pagination';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { NgSelect2Module } from 'ng-select2';
import { APP_COMPONENTS } from './components';
import { APP_PIPES } from './pipes';
import { EmailValidator, EqualPasswordsValidator, PasswordValidator } from './validators';
import { AlertComponent, ConfirmComponent, FilemanagerComponent } from './modals';
import { APP_DIRECTIVES } from './directives';
import { ModalComponent } from './modal';

const APP_SERVICES = [
];
const APP_MODALS = [
  AlertComponent,
  ConfirmComponent,
  FilemanagerComponent,
];
const APP_VALIDATORS = [
  EmailValidator,
  EqualPasswordsValidator,
  PasswordValidator,
];

@NgModule({
  imports: [
    CommonModule,
    RouterModule,
    FormsModule,
    ReactiveFormsModule,
    TranslateModule,
    ModalModule.forChild(),
    BsDropdownModule.forRoot(),
    CollapseModule.forRoot(),
    PaginationModule.forRoot(),
    BsDatepickerModule.forRoot(),
    NgSelect2Module,
  ],
  exports: [
    CommonModule,
    TranslateModule,
    ...APP_PIPES,
    ...APP_COMPONENTS,
    ...APP_MODALS,
    ...APP_DIRECTIVES,
    ModalComponent,
  ],
  declarations: [
    ...APP_PIPES,
    ...APP_COMPONENTS,
    ...APP_MODALS,
    ...APP_DIRECTIVES,
    ModalComponent,
  ],
  providers: [
    ...APP_SERVICES,
    ...APP_VALIDATORS,
  ],
})
export class ThemeModule {
  static forRoot(): ModuleWithProviders<ThemeModule> {
    return {
      ngModule: ThemeModule,
      providers: [],
    };
  }
}
