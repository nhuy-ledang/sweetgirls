import { ExtraOptions, RouterModule, Routes } from '@angular/router';
import { NgModule } from '@angular/core';
import { LoggedIn } from './loggedIn';

export const routes: Routes = [
  {
    path: 'pages',
    loadChildren: () => import('./pages/pages.module').then(m => m.PagesModule),
    resolve: {auth: LoggedIn},
  },
  {path: 'auth', loadChildren: () => import('./auth/auth.module').then(m => m.AuthModule)},
  /*{
    path: 'auth',
    component: NbAuthComponent,
    children: [
      {path: '', component: NbLoginComponent},
      {path: 'login', component: NbLoginComponent},
      {path: 'register', component: NbRegisterComponent},
      {path: 'logout', component: NbLogoutComponent},
      {path: 'request-password', component: NbRequestPasswordComponent},
      {path: 'reset-password', component: NbResetPasswordComponent},
    ],
  },*/
  {path: '', redirectTo: 'pages/dashboard', pathMatch: 'full'},
  {path: '**', redirectTo: 'pages/dashboard'},
];

const config: ExtraOptions = {
  useHash: true,
};

@NgModule({
  imports: [RouterModule.forRoot(routes, config)],
  exports: [RouterModule],
})
export class AppRoutingModule {
}
