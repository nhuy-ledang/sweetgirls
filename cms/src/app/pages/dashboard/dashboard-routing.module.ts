import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardComponent } from './dashboard.component';
import { TabOrdersComponent } from './tabs/orders.component';
import {
  ComRowOrderRevenuesComponent,
  ComRowProductRevenuesComponent,
  ComRowSalesIndexComponent,
} from './components';

const routes: Routes = [{path: '', component: DashboardComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class DashboardRoutingModule {
}

export const routedComponents = [
  DashboardComponent,
  TabOrdersComponent,
  // Orders
  ComRowSalesIndexComponent,
  ComRowProductRevenuesComponent,
  ComRowOrderRevenuesComponent,
];

export const providerComponents = [];
