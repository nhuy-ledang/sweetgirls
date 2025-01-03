import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ModalModule } from 'ngx-bootstrap/modal';
import { BsDatepickerModule } from 'ngx-bootstrap/datepicker';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { ThemeModule } from '../../../@theme/theme.module';
import { UserGroupsRepository, UserNotifiesRepository, UserRanksRepository, UsersRepository, StatsRepository } from './services';
import { MessengerService, MessengerComponent, TypingComponent } from './messenger';
import { InputCustomerComponent } from './components';
import { DlgCustomerSelectComponent } from './modals';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ModalModule.forChild(),
    BsDatepickerModule.forRoot(),
    BsDropdownModule.forRoot(),
    ThemeModule,
  ],
  exports: [
    MessengerComponent,
    TypingComponent,
    InputCustomerComponent,
    DlgCustomerSelectComponent,
  ],
  declarations: [
    MessengerComponent,
    TypingComponent,
    InputCustomerComponent,
    DlgCustomerSelectComponent,
  ],
  providers: [
    MessengerService,
    UserGroupsRepository,
    UsersRepository,
    UserNotifiesRepository,
    UserRanksRepository,
    StatsRepository,
  ],
})
export class UserSharedModule {
}
