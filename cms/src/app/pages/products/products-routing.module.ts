import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ProductsRootComponent } from './products.component';
import { SettingsRepository, ProductSpecsRepository, ProductModulesRepository, OptionsRepository, ProductOptionsRepository, ProductRelatedsRepository, CategoryModulesRepository, OptionValuesRepository, ProductLatestRepository, ProductBestsellersRepository, ReviewsRepository, ProductVariantsRepository, FlashsalesRepository, ProductIncombosRepository } from './services';
import { CategoriesComponent } from './categories/categories.component';
import { CategoryFormComponent } from './categories/form/form.component';
import { ManufacturersComponent } from './manufacturers/manufacturers.component';
import { ManufacturerFormComponent } from './manufacturers/form/form.component';
import { ProductResolve } from './products/product.resolve';
import { ProductsComponent } from './products/products.component';
import { ProductFormComponent } from './products/form/form.component';
import { ImagesComponent } from './products/images/images.component';
import { ImageFormComponent } from './products/images/form/form.component';
import { ProductOptionsComponent } from './products/options/options.component';
import { ProductOptionFormComponent } from './products/options/form/form.component';
import { ProductVariantsComponent } from './products/variants/variants.component';
import { ProductVariantFormComponent } from './products/variants/form/form.component';
import { ProductVariantEditFormComponent } from './products/variants/edit-form/edit-form.component';
import { SettingsComponent } from './settings/settings.component';
import { SettingFormComponent } from './settings/form/form.component';
import { ProductQuantityFormComponent } from './products/form/quantity-form.component';
import { OptionsComponent } from './options/options.component';
import { OptionDescComponent } from './options/desc/desc.component';
import { OptionFormComponent } from './options/form/form.component';
import { OptionValuesComponent } from './options/values/values.component';
import { OptionValueFormComponent } from './options/values/form/form.component';
import { OptionValueDescComponent } from './options/values/desc/desc.component';
import { ManufacturersRepository, ProductsRepository } from './shared/services';

const routes: Routes = [{
  path: '',
  component: ProductsRootComponent,
  children: [
    {path: 'products', component: ProductsComponent},
    {path: 'manufacturers', component: ManufacturersComponent},
    {path: 'categories', component: CategoriesComponent},
    {path: 'options', component: OptionsComponent},
    {path: 'settings', component: SettingsComponent},
    {path: '', redirectTo: 'products', pathMatch: 'full'},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class ProductsRoutingModule {
}

export const routedComponents = [
  ProductsRootComponent,
  CategoriesComponent,
  CategoryFormComponent,
  ManufacturersComponent,
  ManufacturerFormComponent,
  ProductsComponent,
  ProductFormComponent,
  ProductQuantityFormComponent,
  ImagesComponent,
  ImageFormComponent,
  ProductOptionsComponent,
  ProductOptionFormComponent,
  ProductVariantsComponent,
  ProductVariantFormComponent,
  ProductVariantEditFormComponent,
  OptionsComponent,
  OptionFormComponent,
  OptionDescComponent,
  OptionValuesComponent,
  OptionValueFormComponent,
  OptionValueDescComponent,
  SettingsComponent,
  SettingFormComponent,
];

export const providerComponents = [
  SettingsRepository,
  CategoryModulesRepository,
  ManufacturersRepository,
  OptionsRepository,
  OptionValuesRepository,
  FlashsalesRepository,
  ProductResolve,
  ProductsRepository,
  ProductRelatedsRepository,
  ProductOptionsRepository,
  ProductVariantsRepository,
  ProductSpecsRepository,
  ProductModulesRepository,
  ProductLatestRepository,
  ProductBestsellersRepository,
  ReviewsRepository,
  ProductIncombosRepository,
];
