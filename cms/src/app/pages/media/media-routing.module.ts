import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MediaRootComponent } from './media.component';
import { SitemapsRepository } from './services';
import { FilemanagerComponent } from './filemanager/filemanager.component';
import { SitemapsComponent } from './sitemaps/sitemaps.component';

const routes: Routes = [{
  path: '',
  component: MediaRootComponent,
  children: [
    {path: 'filemanager', component: FilemanagerComponent},
    {path: 'sitemaps', component: SitemapsComponent},
    {path: '', redirectTo: 'filemanager', pathMatch: 'full'},
  ],
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})

export class MediaRoutingModule {
}

export const routedComponents = [
  MediaRootComponent,
  FilemanagerComponent,
  SitemapsComponent,
];

export const providerComponents = [
  SitemapsRepository,
];
