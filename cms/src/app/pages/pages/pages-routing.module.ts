import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SettingsRepository } from './services';
import { PagesRootComponent } from './pages.component';
import { CategoriesComponent } from './categories/categories.component';
import { CategoryFormComponent } from './categories/form/form.component';
import { ModulesComponent } from './modules/modules.component';
import { ModuleFormComponent } from './modules/form/form.component';
import { ModuleDescComponent } from './modules/desc/desc.component';
import { ModuleUserGuideComponent } from './modules/user-guide/user-guide.component';
import { WidgetsComponent } from './widgets/widgets.component';
import { WidgetFormComponent } from './widgets/form/form.component';
import { LayoutsComponent } from './layouts/layouts.component';
import { LayoutFormComponent } from './layouts/form/form.component';
import { LayoutDescComponent } from './layouts/desc/desc.component';
import { LayoutModulesComponent } from './layouts/modules/modules.component';
import { LayoutModuleFormComponent } from './layouts/modules/form/form.component';
import { LayoutModuleDescComponent } from './layouts/modules/desc/desc.component';
import { PagesComponent } from './pages/pages.component';
import { PageFormComponent } from './pages/form/form.component';
import { PageDescComponent } from './pages/desc/desc.component';
import { PageContentsComponent } from './pages/contents/contents.component';
import { PageContentFormComponent } from './pages/contents/form/form.component';
import { PageContentDescComponent } from './pages/contents/desc/desc.component';
import { PageResolve } from './pages/page.resolve';
import { PageDetailComponent } from './pages/detail/detail.component';
import { PageDetailInfoComponent } from './pages/detail/info/info.component';
import { PageDetailContentsComponent } from './pages/detail/contents/contents.component';
import { PageModulesComponent } from './pages/modules/modules.component';
import { PageModuleDescComponent } from './pages/modules/desc/desc.component';
import { PageModuleFormComponent } from './pages/modules/form/form.component';
import { MenusComponent } from './menus/menus.component';
import { MenuDescComponent } from './menus/desc/desc.component';
import { MenuFormComponent } from './menus/form/form.component';
import { SettingsComponent } from './settings/settings.component';
import { SettingFormComponent } from './settings/form/form.component';
import { SettingFrmTitleComponent } from './settings/frm-title/frm-title.component';
import { PatternsComponent } from './patterns/patterns.component';
import { PatternDescComponent } from './patterns/desc/desc.component';
import { PatternFormComponent } from './patterns/form/form.component';

const routes: Routes = [{
  path: '',
  component: PagesRootComponent,
  children: [
    {path: 'modules', component: ModulesComponent},
    {path: 'widgets', component: WidgetsComponent},
    {path: 'layouts', component: LayoutsComponent},
    {path: 'patterns', component: PatternsComponent},
    {path: 'pages', component: PagesComponent},
    {
      path: 'pages/:id', component: PageDetailComponent, resolve: {info: PageResolve}, children: [
        {path: 'info', component: PageDetailInfoComponent},
        {path: 'contents', component: PageDetailContentsComponent},
      ],
    },
    {path: 'categories', component: CategoriesComponent},
    {path: 'settings', component: SettingsComponent},
    {path: 'menus', component: MenusComponent},
    // {path: 'detail/:id', component: DetailComponent, resolve: {info: OrderResolve}},
    // {path: '', redirectTo: 'list', pathMatch: 'full'},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class PagesRoutingModule {
}

export const routedComponents = [
  PagesRootComponent,
  CategoriesComponent,
  CategoryFormComponent,
  ModulesComponent,
  ModuleFormComponent,
  ModuleDescComponent,
  ModuleUserGuideComponent,
  WidgetsComponent,
  WidgetFormComponent,
  LayoutsComponent,
  LayoutFormComponent,
  LayoutDescComponent,
  LayoutModulesComponent,
  LayoutModuleFormComponent,
  LayoutModuleDescComponent,
  PatternsComponent,
  PatternFormComponent,
  PatternDescComponent,
  PagesComponent,
  PageFormComponent,
  PageDescComponent,
  PageContentsComponent,
  PageContentFormComponent,
  PageContentDescComponent,
  PageDetailComponent,
  PageDetailInfoComponent,
  PageDetailContentsComponent,
  PageModulesComponent,
  PageModuleFormComponent,
  PageModuleDescComponent,
  MenusComponent,
  MenuFormComponent,
  MenuDescComponent,
  SettingsComponent,
  SettingFormComponent,
  SettingFrmTitleComponent,
];

export const providerComponents = [
  PageResolve,
  SettingsRepository,
];
