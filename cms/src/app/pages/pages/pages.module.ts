import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { TabsModule } from 'ngx-bootstrap/tabs';
import { CollapseModule } from 'ngx-bootstrap/collapse';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { ThemeModule } from '../../@theme/theme.module';
import { PdSharedModule } from '../products/shared/shared.module';
import { PgSharedModule } from './shared/shared.module';
import { PagesRoutingModule, providerComponents, routedComponents } from './pages-routing.module';

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
    PdSharedModule,
    PgSharedModule,
    PagesRoutingModule,
  ],
  declarations: [
    ...routedComponents,
  ],
  providers: [
    ...providerComponents,
  ],
})
export class PagesModule {
}
