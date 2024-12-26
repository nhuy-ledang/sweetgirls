import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { UsersRootComponent } from './users.component';
import { FeedbacksRepository, UserSettingsRepository } from './services';
import { UserResolve } from './user.resolve';
import { GroupsComponent } from './groups/groups.component';
import { GroupFormComponent } from './groups/form/form.component';
import { UsersComponent } from './users/users.component';
import { UserFormComponent } from './users/form/form.component';
import { UserDetailComponent } from './users/detail/detail.component';
import { UserInfoComponent } from './users/detail/info/info.component';
import { UserOrdersComponent } from './users/detail/orders/orders.component';
import { UserPaymentsComponent } from './users/detail/payments/payments.component';
import { UserNotesComponent } from './users/detail/notes/notes.component';
import { UserNotifiesComponent } from './users/detail/notifies/notifies.component';
import { UserDiscountsComponent } from './users/detail/discounts/discounts.component';
import { UserHistoriesComponent } from './users/detail/histories/histories.component';
import { FeedbacksComponent } from './feedbacks/feedbacks.component';
import { RanksComponent } from './ranks/ranks.component';
import { RanksFormComponent } from './ranks/form/form.component';
import { UserSettingsComponent } from './settings/settings.component';
import { UserSettingFormComponent } from './settings/form/form.component';

const routes: Routes = [{
  path: '',
  component: UsersRootComponent,
  children: [
    {path: 'groups', component: GroupsComponent},
    {path: 'users', component: UsersComponent},
    {path: 'feedbacks', component: FeedbacksComponent},
    {path: 'settings', component: UserSettingsComponent},
    {
      path: 'users/:id',
      component: UserDetailComponent,
      resolve: {info: UserResolve},
      children: [
        {path: 'info', component: UserInfoComponent},
        {path: 'orders', component: UserOrdersComponent},
        {path: 'payments', component: UserPaymentsComponent},
        {path: 'notes', component: UserNotesComponent},
        {path: 'notifies', component: UserNotifiesComponent},
        {path: 'discounts', component: UserDiscountsComponent},
        {path: 'histories', component: UserHistoriesComponent},
        {path: '', redirectTo: 'info', pathMatch: 'full'},
      ],
    },
    {path: 'ranks', component: RanksComponent},
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
  GroupsComponent,
  GroupFormComponent,
  UsersComponent,
  UserFormComponent,
  UserDetailComponent,
  RanksComponent,
  RanksFormComponent,
  // Card
  UserInfoComponent,
  UserOrdersComponent,
  UserPaymentsComponent,
  UserNotesComponent,
  UserNotifiesComponent,
  UserDiscountsComponent,
  UserHistoriesComponent,
  // Feedbacks
  FeedbacksComponent,
  // Settings
  UserSettingsComponent,
  UserSettingFormComponent,
];

export const providerComponents = [
  UserResolve,
  FeedbacksRepository,
  UserSettingsRepository,
];
