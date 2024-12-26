import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { CategoriesRepository, CategoryOptionsRepository, CategoryPropertiesRepository, GiftOrdersRepository, GiftProductsRepository, GiftSetsRepository, IncludedProductsRepository, ManufacturersRepository, ProductsRepository, StatRepository } from './services';
import { InputProductComponent } from './components';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    BsDropdownModule.forRoot(),
  ],
  exports: [
    InputProductComponent,
  ],
  declarations: [
    InputProductComponent,
  ],
  providers: [
    ManufacturersRepository,
    CategoriesRepository,
    CategoryPropertiesRepository,
    CategoryOptionsRepository,
    ProductsRepository,
    IncludedProductsRepository,
    GiftProductsRepository,
    GiftSetsRepository,
    GiftOrdersRepository,
    StatRepository,
  ],
})
export class PdSharedModule {
}
