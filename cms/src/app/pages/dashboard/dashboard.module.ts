import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ThemeModule } from '../../@theme/theme.module';
import { ChartjsModule } from '../../@theme/chartjs/chartjs.module';
import { OrdSharedModule } from '../orders/shared/shared.module';
import { PdSharedModule } from '../products/shared/shared.module';
import { MrkSharedModule } from '../marketing/shared/shared.module';
import { UserSharedModule } from '../users/shared/shared.module';
import { TabsModule } from 'ngx-bootstrap/tabs';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { BsDatepickerConfig, BsDatepickerInlineConfig, BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { providerComponents, routedComponents } from './dashboard-routing.module';

@NgModule({
  imports: [
    FormsModule,
    ThemeModule,
    ChartjsModule,
    OrdSharedModule,
    PdSharedModule,
    MrkSharedModule,
    UserSharedModule,
    TabsModule.forRoot(),
    BsDropdownModule.forRoot(),
    BsDatepickerModule.forRoot(),
  ],
  declarations: [
    ...routedComponents,
  ],
  providers: [
    ...providerComponents,
    BsDatepickerConfig,
    BsDatepickerInlineConfig,
  ],
})
export class DashboardModule { }
