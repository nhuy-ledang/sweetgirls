import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ModalModule } from 'ngx-bootstrap/modal';
import { ThemeModule } from '../../@theme/theme.module';
import { SettingRoutingModule, routedComponents } from './setting-routing.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ModalModule.forChild(),
    ThemeModule,
    SettingRoutingModule,
  ],
  declarations: [
    ...routedComponents,
  ],
  providers: [],
})
export class SettingModule {
}
