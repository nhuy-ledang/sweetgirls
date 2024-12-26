import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { UsrsRootComponent } from './usrs.component';
import { UsrResolve } from './usrs/usr.resolve';
import { UsrsComponent } from './usrs/usrs.component';
import { UsrFormComponent } from './usrs/form/form.component';
import { UsrDetailComponent } from './usrs/detail/detail.component';
import { UseDlgRolesComponent } from './usrs/dlg-roles/dlg-roles.component';
import { GroupsComponent } from './groups/groups.component';
import { GroupFormComponent } from './groups/form/form.component';
import { RolesComponent } from './roles/roles.component';
import { RoleFormComponent } from './roles/form/form.component';

const routes: Routes = [{
  path: '',
  component: UsrsRootComponent,
  children: [
    {path: 'usrs', component: UsrsComponent},
    {path: 'usrs/:id', component: UsrDetailComponent, resolve: {info: UsrResolve}},
    {path: 'groups', component: GroupsComponent},
    {path: 'roles', component: RolesComponent},
    {path: '', redirectTo: 'usrs', pathMatch: 'full'},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class UsrsRoutingModule {
}

export const routedComponents = [
  UsrsRootComponent,
  UsrsComponent,
  UsrFormComponent,
  UsrDetailComponent,
  UseDlgRolesComponent,
  GroupsComponent,
  GroupFormComponent,
  RolesComponent,
  RoleFormComponent,
];

export const providerComponents = [
  UsrResolve,
];
