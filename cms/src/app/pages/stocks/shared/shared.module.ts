import { NgModule } from '@angular/core';
// import { CommonModule } from '@angular/common';
// import { FormsModule, ReactiveFormsModule } from '@angular/forms';
// import { ModalModule } from 'ngx-bootstrap/modal';
// import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
// import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
// import { ThemeModule } from '../../../@theme/theme.module';
import { InTicketsRepository, InventoriesRepository, OutTicketsRepository, ReqTicketsRepository, StocksRepository, RequestsRepository, TicketsRepository, TypesRepository, OutRequestsRepository, InRequestsRepository, InventoryProductsRepository } from './services';

@NgModule({
  imports: [
    // CommonModule,
    // FormsModule,
    // ReactiveFormsModule,
    // ModalModule.forChild(),
    // BsDropdownModule.forRoot(),
    // BsDatepickerModule.forRoot(),
    // ThemeModule,
  ],
  providers: [
    InventoriesRepository,
    InventoryProductsRepository,
    StocksRepository,
    TicketsRepository,
    InTicketsRepository,
    OutTicketsRepository,
    RequestsRepository,
    InRequestsRepository,
    OutRequestsRepository,
    ReqTicketsRepository,
    TypesRepository,
  ],
})
export class StoSharedModule {
}
