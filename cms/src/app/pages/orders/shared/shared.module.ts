import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { ThemeModule } from '../../../@theme/theme.module';
import { InvoicesRepository, NetworksRepository, OrderHistoriesRepository, OrderProductsRepository, OrderShippingHistoriesRepository, OrdersRepository, StatsRepository } from './services';
import { DlgNotifyComponent, DlgOrderDetailComponent } from './modals';

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
    DlgNotifyComponent,
    DlgOrderDetailComponent,
  ],
  declarations: [
    DlgNotifyComponent,
    DlgOrderDetailComponent,
  ],
  providers: [
    OrdersRepository,
    OrderHistoriesRepository,
    OrderShippingHistoriesRepository,
    StatsRepository,
    OrderProductsRepository,
    InvoicesRepository,
    NetworksRepository,
  ],
})
export class OrdSharedModule {
}
