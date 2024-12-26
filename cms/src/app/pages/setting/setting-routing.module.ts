import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SettingRootComponent } from './setting.component';
import { SettingsComponent } from './settings/settings.component';
import { SettingFormComponent } from './settings/form/form.component';
import { LanguagesComponent } from './languages/languages.component';
import { LanguageFormComponent } from './languages/form/form.component';

const routes: Routes = [{
  path: '',
  component: SettingRootComponent,
  children: [
    {path: 'settings', component: SettingsComponent},
    {path: 'languages', component: LanguagesComponent},
    {path: '', redirectTo: 'settings', pathMatch: 'full'},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class SettingRoutingModule {
}

export const routedComponents = [
  SettingRootComponent,
  SettingsComponent,
  SettingFormComponent,
  LanguagesComponent,
  LanguageFormComponent,
];
