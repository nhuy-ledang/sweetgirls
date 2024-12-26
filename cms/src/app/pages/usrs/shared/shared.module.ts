import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { ThemeModule } from '../../../@theme/theme.module';
import { UsrGroupsRepository, UsrRolesRepository } from './services';
import { DlgNewUsrComponent } from './modals';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ModalModule.forChild(),
    BsDatepickerModule.forRoot(),
    ThemeModule,
  ],
  exports: [
    DlgNewUsrComponent,
  ],
  declarations: [
    DlgNewUsrComponent,
  ],
  providers: [
    UsrGroupsRepository,
    UsrRolesRepository,
  ],
})
export class UsrSharedModule {
}
