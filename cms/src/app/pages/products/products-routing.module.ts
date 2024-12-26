import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ProductsRootComponent } from './products.component';
import { SettingsRepository, ProductSpecsRepository, ProductModulesRepository, OptionsRepository, ProductOptionsRepository, ProductRelatedsRepository, CategoryModulesRepository, OptionValuesRepository, ProductLatestRepository, ProductBestsellersRepository, ReviewsRepository, ProductVariantsRepository, FlashsalesRepository, ProductIncombosRepository } from './services';
import { CategoriesComponent } from './categories/categories.component';
import { CategoryFormComponent } from './categories/form/form.component';
import { CategoryDescComponent } from './categories/desc/desc.component';
import { CategoryPropertiesComponent } from './categories/properties/properties.component';
import { CategoryPropertyFormComponent } from './categories/properties/form/form.component';
import { CategoryOptionsComponent } from './categories/options/options.component';
import { CategoryOptionFormComponent } from './categories/options/form/form.component';
import { CategoryModulesComponent } from './categories/modules/modules.component';
import { CategoryModuleDescComponent } from './categories/modules/desc/desc.component';
import { CategoryModuleFormComponent } from './categories/modules/form/form.component';
import { ManufacturersComponent } from './manufacturers/manufacturers.component';
import { ManufacturerFormComponent } from './manufacturers/form/form.component';
import { ProductResolve } from './products/product.resolve';
import { ProductsComponent } from './products/products.component';
import { ProductFormComponent } from './products/form/form.component';
import { ProductDescComponent } from './products/desc/desc.component';
import { ProductDetailComponent } from './products/detail/detail.component';
import { ImagesComponent } from './products/images/images.component';
import { ImageFormComponent } from './products/images/form/form.component';
import { ProductOptionsComponent } from './products/options/options.component';
import { ProductOptionFormComponent } from './products/options/form/form.component';
import { ProductModulesComponent } from './products/modules/modules.component';
import { ProductModuleFormComponent } from './products/modules/form/form.component';
import { ProductModuleDescComponent } from './products/modules/desc/desc.component';
import { ProductVariantsComponent } from './products/variants/variants.component';
import { ProductVariantFormComponent } from './products/variants/form/form.component';
import { ProductVariantEditFormComponent } from './products/variants/edit-form/edit-form.component';
import { SettingsComponent } from './settings/settings.component';
import { SettingFormComponent } from './settings/form/form.component';
import { ProductQuantityFormComponent } from './products/form/quantity-form.component';
import { ProductSpecsComponent } from './products/specs/specs.component';
import { ProductSpecDescComponent } from './products/specs/desc/desc.component';
import { ProductSpecFormComponent } from './products/specs/form/form.component';
import { OptionsComponent } from './options/options.component';
import { OptionDescComponent } from './options/desc/desc.component';
import { OptionFormComponent } from './options/form/form.component';
import { OptionValuesComponent } from './options/values/values.component';
import { OptionValueFormComponent } from './options/values/form/form.component';
import { OptionValueDescComponent } from './options/values/desc/desc.component';
import { ManufacturersRepository, ProductsRepository } from './shared/services';
import { ProductRelatedsComponent } from './products/relateds/relateds.component';
import { ProductRelatedFormComponent } from './products/relateds/form/form.component';
import { SpecialsComponent } from './products/specials/specials.component';
import { SpecialFormComponent } from './products/specials/form/form.component';
import { IncludedProductsComponent } from './included-products/included-products.component';
import { IncludedProductFormComponent } from './included-products/form/form.component';
import { IncludedProductDescComponent } from './included-products/desc/desc.component';
import { GiftProductsComponent } from './gift-products/gift-products.component';
import { GiftProductFormComponent } from './gift-products/form/form.component';
import { GiftProductDescComponent } from './gift-products/desc/desc.component';
import { GiftSetsComponent } from './gift-sets/gift-sets.component';
import { GiftSetFormComponent } from './gift-sets/form/form.component';
import { LatestProductsComponent } from './latest-products/latest-products.component';
import { LatestProductFormComponent } from './latest-products/form/form.component';
import { BestsellerProductsComponent } from './bestseller-products/bestseller-products.component';
import { BestsellerProductFormComponent } from './bestseller-products/form/form.component';
import { ReviewsComponent } from './reviews/reviews.component';
import { ReviewFormComponent } from './reviews/form/form.component';
import { ReviewImagesComponent } from './reviews/images/images.component';
import { ReviewImageFormComponent } from './reviews/images/form/form.component';
import { GiftOrdersComponent } from './gift-orders/gift-orders.component';
import { GiftOrderFormComponent } from './gift-orders/form/form.component';
import { FlashsaleFormComponent } from './flashsale/form/form.component';
import { FlashsalesComponent } from './flashsale/flashsales.component';
import { FlashsaleValuesComponent } from './flashsale/values/values.component';
import { FlashsaleValueFormComponent } from './flashsale/values/form/form.component';
import { ProductIncombosComponent } from './products/incombos/incombos.component';
import { ProductIncomboFormComponent } from './products/incombos/form/form.component';

const routes: Routes = [{
  path: '',
  component: ProductsRootComponent,
  children: [
    {path: 'products', component: ProductsComponent},
    {path: 'products/:id', component: ProductDetailComponent, resolve: {info: ProductResolve}},
    {path: 'included-products', component: IncludedProductsComponent},
    {path: 'gift-products', component: GiftProductsComponent},
    {path: 'gift-sets', component: GiftSetsComponent},
    {path: 'gift-orders', component: GiftOrdersComponent},
    {path: 'latest-products', component: LatestProductsComponent},
    {path: 'bestseller-products', component: BestsellerProductsComponent},
    {path: 'manufacturers', component: ManufacturersComponent},
    {path: 'categories', component: CategoriesComponent},
    {path: 'reviews', component: ReviewsComponent},
    {path: 'options', component: OptionsComponent},
    {path: 'flashsales', component: FlashsalesComponent},
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
  CategoryDescComponent,
  CategoryPropertiesComponent,
  CategoryPropertyFormComponent,
  CategoryOptionsComponent,
  CategoryOptionFormComponent,
  CategoryModulesComponent,
  CategoryModuleFormComponent,
  CategoryModuleDescComponent,
  ManufacturersComponent,
  ManufacturerFormComponent,
  ProductsComponent,
  ProductFormComponent,
  ProductQuantityFormComponent,
  ProductDescComponent,
  ProductDetailComponent,
  ImagesComponent,
  ImageFormComponent,
  ProductOptionsComponent,
  ProductOptionFormComponent,
  ProductRelatedsComponent,
  ProductRelatedFormComponent,
  ProductSpecsComponent,
  ProductSpecFormComponent,
  ProductSpecDescComponent,
  ProductModulesComponent,
  ProductModuleFormComponent,
  ProductModuleDescComponent,
  ProductVariantsComponent,
  ProductVariantFormComponent,
  ProductVariantEditFormComponent,
  OptionsComponent,
  OptionFormComponent,
  OptionDescComponent,
  OptionValuesComponent,
  OptionValueFormComponent,
  OptionValueDescComponent,
  FlashsalesComponent,
  FlashsaleFormComponent,
  FlashsaleValuesComponent,
  FlashsaleValueFormComponent,
  SettingsComponent,
  SettingFormComponent,
  SpecialsComponent,
  SpecialFormComponent,
  IncludedProductsComponent,
  IncludedProductFormComponent,
  IncludedProductDescComponent,
  GiftProductsComponent,
  GiftProductFormComponent,
  GiftProductDescComponent,
  GiftSetsComponent,
  GiftSetFormComponent,
  GiftOrdersComponent,
  GiftOrderFormComponent,
  LatestProductsComponent,
  LatestProductFormComponent,
  BestsellerProductsComponent,
  BestsellerProductFormComponent,
  ReviewsComponent,
  ReviewFormComponent,
  ReviewImagesComponent,
  ReviewImageFormComponent,
  ProductIncombosComponent,
  ProductIncomboFormComponent,
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
