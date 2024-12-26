import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ModalModule } from 'ngx-bootstrap/modal';
import { ThemeModule } from '../../../@theme/theme.module';
import { PdSharedModule } from '../../products/shared/shared.module';
import { CategoriesRepository, LayoutModulesRepository, LayoutPatternsRepository, LayoutsRepository, ModulesRepository, PageContentsRepository, PageModulesRepository, PagesRepository, WidgetsRepository } from './services';
import { DlgCategorySelectComponent, DlgLayoutSelectComponent, DlgModuleSelectComponent, DlgPatternSelectComponent, DlgWidgetSelectComponent } from './modals';
import { ModuleFormPropertiesComponent } from './components';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ModalModule.forChild(),
    ThemeModule,
    PdSharedModule,
  ],
  exports: [
    DlgModuleSelectComponent,
    DlgWidgetSelectComponent,
    DlgPatternSelectComponent,
    DlgLayoutSelectComponent,
    DlgCategorySelectComponent,
    ModuleFormPropertiesComponent,
  ],
  declarations: [
    DlgModuleSelectComponent,
    DlgWidgetSelectComponent,
    DlgPatternSelectComponent,
    DlgLayoutSelectComponent,
    DlgCategorySelectComponent,
    ModuleFormPropertiesComponent,
  ],
  providers: [
    CategoriesRepository,
    LayoutsRepository,
    LayoutModulesRepository,
    LayoutPatternsRepository,
    ModulesRepository,
    WidgetsRepository,
    PagesRepository,
    PageContentsRepository,
    PageModulesRepository,
  ],
})
export class PgSharedModule {
}
