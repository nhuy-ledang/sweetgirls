import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { TabsModule } from 'ngx-bootstrap/tabs';
import { CollapseModule } from 'ngx-bootstrap/collapse';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { TimepickerConfig } from 'ngx-bootstrap/timepicker';
import { getTimepickerConfig } from '../../@theme/config/custom-bs-datepicker.config';
import { TooltipModule } from 'ngx-bootstrap/tooltip';
import { SortableModule } from 'ngx-bootstrap/sortable';
import { ThemeModule } from '../../@theme/theme.module';
import { PdSharedModule } from './shared/shared.module';
import { ProductsRoutingModule, providerComponents, routedComponents } from './products-routing.module';

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
    SortableModule.forRoot(),
    ThemeModule,
    PdSharedModule,
    ProductsRoutingModule,
  ],
  declarations: [
    ...routedComponents,
  ],
  providers: [
    { provide: TimepickerConfig, useFactory: getTimepickerConfig },
    ...providerComponents,
  ],
})
export class ProductsModule {
}
