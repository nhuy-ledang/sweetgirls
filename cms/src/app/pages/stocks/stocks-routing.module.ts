import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { StocksRootComponent } from './stocks.component';
import { StoStocksComponent } from './stocks/stocks/stocks.component';
import { StoStockFormComponent } from './stocks/stocks/form/form.component';
import { InventoriesComponent } from './inventories/inventories.component';
import { StoInventoryFormComponent } from './inventories/form/form.component';
import { IvtProductsComponent } from './inventories/products/products.component';
import { DepOrdersComponent } from './deployment/orders/orders.component';
import { DepOrdersExportsComponent } from './deployment/orders/export/exports.component';
import { DepOrdersImportsComponent } from './deployment/orders/import/imports.component';
// import { DepOrderFrmTicketComponent } from './deployment/orders/frm-ticket.component';
import { DepDeploymentComponent } from './deployment/deployment/deployment.component';
import { DepRefundComponent } from './deployment/refund/refund.component';
import { DepReturnComponent } from './deployment/return/return.component';
import { DepWarrantyComponent } from './deployment/warranty/warranty.component';
import { DepReviewsComponent } from './deployment/reviews/reviews.component';
import { DepHistoriesComponent } from './deployment/histories/histories.component';
import { ImpRequestsComponent } from './import/requests/requests.component';
import { ImpRequestFormComponent } from './import/requests/form/form.component';
import { ImpTicketsComponent } from './import/tickets/tickets.component';
import { ImpTicketFormComponent } from './import/tickets/form/form.component';
import { ImpProductsComponent } from './import/products/products.component';
import { ImpHistoriesComponent } from './import/histories/histories.component';
import { ExpRequestsComponent } from './export/requests/requests.component';
import { ExpRequestFormComponent } from './export/requests/form/form.component';
import { ExpTicketsComponent } from './export/tickets/tickets.component';
import { ExpTicketFormComponent } from './export/tickets/form/form.component';
import { ExpProductsComponent } from './export/products/products.component';
import { ExpHistoriesComponent } from './export/histories/histories.component';
import { DlgImportConfirmComponent } from './inventories/products/dlg-import-confirm/dlg-import-confirm.component';
import { DlgProductComponent } from './dialogs/product/product.component';

const routes: Routes = [{
  path: '',
  component: StocksRootComponent,
  children: [
    {
      path: 'dep', children: [
        {path: 'orders', component: DepOrdersComponent},
        {path: 'orders_export', component: DepOrdersExportsComponent},
        {path: 'orders_import', component: DepOrdersImportsComponent},
        {path: 'deployment', component: DepDeploymentComponent},
        {path: 'refund', component: DepRefundComponent},
        {path: 'return', component: DepReturnComponent},
        {path: 'warranty', component: DepWarrantyComponent},
        {path: 'reviews', component: DepReviewsComponent},
        {path: 'histories', component: DepHistoriesComponent},
      ],
    },
    {
      path: 'exp', children: [
        {path: 'requests', component: ExpRequestsComponent},
        {path: 'tickets', component: ExpTicketsComponent},
        {path: 'products', component: ExpProductsComponent},
        {path: 'histories', component: ExpHistoriesComponent},
      ],
    },
    {
      path: 'imp', children: [
        {path: 'requests', component: ImpRequestsComponent},
        {path: 'tickets', component: ImpTicketsComponent},
        {path: 'products', component: ImpProductsComponent},
        {path: 'histories', component: ImpHistoriesComponent},
      ],
    },
    {path: 'stocks', component: StoStocksComponent},
    {
      path: 'ivt', children: [
        {path: 'inventories', component: InventoriesComponent},
        {path: 'products', component: IvtProductsComponent},
      ],
    },

    {path: '', redirectTo: 'stocks', pathMatch: 'full'},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class StocksRoutingModule {
}

export const routedComponents = [
  StocksRootComponent,
  DepOrdersComponent,
  // DepOrderFrmTicketComponent,
  DepOrdersExportsComponent,
  DepOrdersImportsComponent,
  DepDeploymentComponent,
  DepRefundComponent,
  DepReturnComponent,
  DepWarrantyComponent,
  DepReviewsComponent,
  DepHistoriesComponent,
  ExpTicketsComponent,
  ExpTicketFormComponent,
  ExpProductsComponent,
  ExpHistoriesComponent,
  ImpRequestsComponent,
  ImpRequestFormComponent,
  ExpRequestsComponent,
  ExpRequestFormComponent,
  ImpTicketsComponent,
  ImpTicketFormComponent,
  ImpProductsComponent,
  ImpHistoriesComponent,
  StoStocksComponent,
  StoStockFormComponent,
  InventoriesComponent,
  StoInventoryFormComponent,
  IvtProductsComponent,
  DlgImportConfirmComponent,
  DlgProductComponent,
];

export const providerComponents = [];
