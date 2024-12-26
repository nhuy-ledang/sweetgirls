import { ModuleWithProviders, NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { ThemeModule } from '../../@theme/theme.module';
import { DlgPasswordComponent, DlgStaffSelectComponent } from './modals';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ModalModule.forChild(),
    BsDropdownModule.forRoot(),
    BsDatepickerModule.forRoot(),
    ThemeModule,
  ],
  exports: [
    DlgStaffSelectComponent,
    DlgPasswordComponent,
  ],
  declarations: [
    DlgStaffSelectComponent,
    DlgPasswordComponent,
  ],
  providers: [],
})
export class SharedModule {
  static forRoot(): ModuleWithProviders<SharedModule> {
    return {
      ngModule: SharedModule,
      providers: [],
    };
  }
}
