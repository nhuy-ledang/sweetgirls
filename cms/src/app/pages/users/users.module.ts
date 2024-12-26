import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { TabsModule } from 'ngx-bootstrap/tabs';
import { CollapseModule } from 'ngx-bootstrap/collapse';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { ThemeModule } from '../../@theme/theme.module';
import { OrdSharedModule } from '../orders/shared/shared.module';
import { UserSharedModule } from './shared/shared.module';
import { UsersRoutingModule, providerComponents, routedComponents } from './users-routing.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ModalModule.forChild(),
    BsDropdownModule.forRoot(),
    TabsModule.forRoot(),
    CollapseModule.forRoot(),
    BsDatepickerModule.forRoot(),
    ThemeModule,
    OrdSharedModule,
    UserSharedModule,
    UsersRoutingModule,
  ],
  declarations: [
    ...routedComponents,
  ],
  providers: [
    ...providerComponents,
  ],
})
export class UsersModule {
}
