import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { TabsModule } from 'ngx-bootstrap/tabs';
import { CollapseModule } from 'ngx-bootstrap/collapse';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { TooltipModule } from 'ngx-bootstrap/tooltip';
import { SortablejsModule } from 'ngx-sortablejs';
import { TagInputModule } from 'ngx-chips';
import { Daterangepicker } from 'ng2-daterangepicker';
import { ThemeModule } from '../../@theme/theme.module';
import { OrdersRoutingModule, providerComponents, routedComponents } from './orders-routing.module';
import { SharedModule } from '../shared/shared.module';
import { UserSharedModule } from '../users/shared/shared.module';
import { PdSharedModule } from '../products/shared/shared.module';
import { OrdSharedModule } from './shared/shared.module';

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
    TooltipModule.forRoot(),
    SortablejsModule,
    Daterangepicker,
    TagInputModule,
    ThemeModule,
    SharedModule,
    UserSharedModule,
    PdSharedModule,
    OrdSharedModule,
    OrdersRoutingModule,
  ],
  declarations: [
    ...routedComponents,
  ],
  providers: [
    ...providerComponents,
  ],
})
export class OrdersModule {
}
