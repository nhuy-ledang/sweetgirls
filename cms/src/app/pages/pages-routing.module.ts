import { RouterModule, Routes } from '@angular/router';
import { NgModule } from '@angular/core';
import { PagesComponent } from './pages.component';
import { DashboardComponent } from './dashboard/dashboard.component';
// import { NotFoundComponent } from './miscellaneous/not-found/not-found.component';

const routes: Routes = [{
  path: '',
  component: PagesComponent,
  children: [
    {path: 'dashboard', component: DashboardComponent},
    {path: 'users', loadChildren: () => import('./users/users.module').then(m => m.UsersModule)},
    {path: 'usrs', loadChildren: () => import('./usrs/usrs.module').then(m => m.UsrsModule)},
    {path: 'ord', loadChildren: () => import('./orders/orders.module').then(m => m.OrdersModule)},
    {path: 'setting', loadChildren: () => import('./setting/setting.module').then(m => m.SettingModule)},
    {path: 'prd', loadChildren: () => import('./products/products.module').then(m => m.ProductsModule)},
    {path: 'media', loadChildren: () => import('./media/media.module').then(m => m.MediaModule)},
    {path: '', redirectTo: 'dashboard', pathMatch: 'full'},
    // {path: '**', component: NotFoundComponent},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class PagesRoutingModule {
}
