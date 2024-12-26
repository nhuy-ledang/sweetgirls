import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { OrderSettingsRepository, OrderWebhookRepository } from './services';
import { OrdersRootComponent } from './orders.component';
import { OrderResolve } from './orders/order.resolve';
import { OrdersComponent } from './orders/orders.component';
import { OrderProductsComponent } from './orders/components/order-products.component';
import { OrderHistoriesComponent } from './orders/components/order-histories.component';
import { OrderShippingHistoriesComponent } from './orders/components/order-shipping-histories.component';
import { OrderDetailComponent } from './orders/detail/detail.component';
import { OrderFrmOrderStatusComponent } from './orders/frm-order-status/frm-order-status.component';
import { OrderFrmEditAddressComponent } from './orders/frm-edit-address/frm-edit-address.component';
import { OrderFrmProductComponent } from './orders/frm-product/frm-product.component';
import { OrderSettingsComponent } from './settings/settings.component';
import { OrderSettingFormComponent } from './settings/form/form.component';
import { OrderWebhookComponent } from './webhook/webhook.component';
import { FrmInvoicedComponent } from './orders/frm-invoiced/frm-invoiced.component';
import { OrderFrmEditInfoComponent } from './orders/frm-edit-info/frm-edit-info.component';

const routes: Routes = [{
  path: '',
  component: OrdersRootComponent,
  children: [
    {path: 'orders', component: OrdersComponent},
    {path: 'orders/:id', component: OrderDetailComponent, resolve: {info: OrderResolve}},
    {path: 'settings', component: OrderSettingsComponent},
    {path: 'webhook', component: OrderWebhookComponent},
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
  OrderDetailComponent,
  OrderFrmOrderStatusComponent,
  OrderFrmEditAddressComponent,
  OrderFrmEditInfoComponent,
  OrderFrmProductComponent,
  OrderProductsComponent,
  OrderHistoriesComponent,
  OrderShippingHistoriesComponent,
  OrderSettingsComponent,
  OrderSettingFormComponent,
  OrderWebhookComponent,
  FrmInvoicedComponent,
];

export const providerComponents = [
  OrderResolve,
  OrderSettingsRepository,
  OrderWebhookRepository,
];
