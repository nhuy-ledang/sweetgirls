import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { TabsModule } from 'ngx-bootstrap/tabs';
import { CollapseModule } from 'ngx-bootstrap/collapse';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { TooltipModule } from 'ngx-bootstrap/tooltip';
import { TagInputModule } from 'ngx-chips';
import { SortablejsModule } from 'ngx-sortablejs';
import { ThemeModule } from '../../@theme/theme.module';
import { UsrSharedModule } from '../usrs/shared/shared.module';
import { PdSharedModule } from '../products/shared/shared.module';
// import { SupSharedModule } from '../suppliers/shared/shared.module';
import { OrdSharedModule } from '../orders/shared/shared.module';
// import { CusSharedModule } from '../customers/shared/shared.module';
// import { OpeSharedModule } from '../operating/shared/shared.module';
import { StoSharedModule } from './shared/shared.module';
import { StocksRoutingModule, providerComponents, routedComponents } from './stocks-routing.module';
import { LocSharedModule } from '../localization/shared/shared.module';
import { ExpRequestFormComponent } from './export/requests/form/form.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    TagInputModule,
    SortablejsModule,
    ModalModule.forChild(),
    BsDropdownModule.forRoot(),
    TabsModule.forRoot(),
    CollapseModule.forRoot(),
    BsDatepickerModule.forRoot(),
    TooltipModule.forRoot(),
    ThemeModule,
    UsrSharedModule,
    PdSharedModule,
    // SupSharedModule,
    OrdSharedModule,
    // CusSharedModule,
    // OpeSharedModule,
    StoSharedModule,
    StocksRoutingModule,
    LocSharedModule,
  ],
  exports: [
    ExpRequestFormComponent,
  ],
  declarations: [
    ...routedComponents,
  ],
  providers: [
    ...providerComponents,
  ],
})
export class StocksModule {
}
