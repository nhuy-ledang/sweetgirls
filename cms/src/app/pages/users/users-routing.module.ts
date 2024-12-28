import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { UsersRootComponent } from './users.component';
import { FeedbacksRepository, UserSettingsRepository } from './services';
import { UserResolve } from './user.resolve';
import { UsersComponent } from './users/users.component';
import { UserFormComponent } from './users/form/form.component';
import { UserSettingsComponent } from './settings/settings.component';
import { UserSettingFormComponent } from './settings/form/form.component';

const routes: Routes = [{
  path: '',
  component: UsersRootComponent,
  children: [
    {path: 'users', component: UsersComponent},
    {path: 'settings', component: UserSettingsComponent},
    {path: '', redirectTo: 'list', pathMatch: 'full'},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class UsersRoutingModule {
}

export const routedComponents = [
  UsersRootComponent,
  UsersComponent,
  UserFormComponent,
  // Settings
  UserSettingsComponent,
  UserSettingFormComponent,
];

export const providerComponents = [
  UserResolve,
  FeedbacksRepository,
  UserSettingsRepository,
];
