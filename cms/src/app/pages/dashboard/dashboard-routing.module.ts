import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardComponent } from './dashboard.component';
import { TabOrdersComponent } from './tabs/orders.component';
import {
  ComRowMarketingIndexComponent,
  ComRowOrderStatusComponent,
  ComRowProductRankComponent,
  ComRowBuyerRankComponent,
  ComRowUserBirthdayComponent,
  ComRowDiscountRankComponent,
  ComRowOrderRevenuesComponent,
  ComRowProductRevenuesComponent,
  ComRowSalesIndexComponent,
} from './components';
import { TabMarketingComponent } from './tabs/marketing.component';
import { TabNetworksComponent } from './tabs/networks.component';

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
  TabMarketingComponent,
  TabNetworksComponent,
  // Orders
  ComRowSalesIndexComponent,
  ComRowProductRevenuesComponent,
  ComRowOrderRevenuesComponent,
  // Marketing
  ComRowMarketingIndexComponent,
  ComRowOrderStatusComponent,
  ComRowProductRankComponent,
  ComRowDiscountRankComponent,
  ComRowBuyerRankComponent,
  ComRowUserBirthdayComponent,
];

export const providerComponents = [];
