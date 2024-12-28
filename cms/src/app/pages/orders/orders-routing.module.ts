import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { OrderSettingsRepository, OrderWebhookRepository } from './services';
import { OrdersRootComponent } from './orders.component';
import { OrderResolve } from './orders/order.resolve';
import { OrdersComponent } from './orders/orders.component';
import { OrderProductsComponent } from './orders/components/order-products.component';
import { OrderHistoriesComponent } from './orders/components/order-histories.component';
import { OrderShippingHistoriesComponent } from './orders/components/order-shipping-histories.component';
import { OrderFrmOrderStatusComponent } from './orders/frm-order-status/frm-order-status.component';
import { OrderFrmProductComponent } from './orders/frm-product/frm-product.component';
import { OrderDetailComponent } from './orders/detail/detail.component';

const routes: Routes = [{
  path: '',
  component: OrdersRootComponent,
  children: [
    {path: 'orders', component: OrdersComponent},
    {path: '', redirectTo: 'orders', pathMatch: 'full'},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class OrdersRoutingModule {
}

export const routedComponents = [
  OrdersRootComponent,
  OrdersComponent,
  OrderFrmProductComponent,
  OrderProductsComponent,
  OrderHistoriesComponent,
  OrderFrmOrderStatusComponent,
  OrderDetailComponent,
];

export const providerComponents = [
  OrderResolve,
  OrderSettingsRepository,
  OrderWebhookRepository,
];
