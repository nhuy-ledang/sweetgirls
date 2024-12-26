import { ModuleWithProviders, NgModule, Optional, SkipSelf } from '@angular/core';
import { CommonModule, DatePipe } from '@angular/common';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { CookieModule } from 'ngx-cookie';
import { throwIfAlreadyLoaded } from './module-import-guard';
import { CORE_UTILS } from './utils';
import { CORE_SERVICES } from './services';
import { CORE_REPOSITORIES } from './repositories';
import { CORE_SECURITIES } from './security';
import { ReqInterceptor } from './req.interceptor';
import { ResInterceptor } from './res.interceptor';

@NgModule({
  imports: [
    CommonModule,
    CookieModule.forRoot(),
    HttpClientModule,
  ],
  exports: [],
  providers: [
    {provide: HTTP_INTERCEPTORS, useClass: ReqInterceptor, multi: true},
    {provide: HTTP_INTERCEPTORS, useClass: ResInterceptor, multi: true},
  ],
  declarations: [],
})
export class CoreModule {
  constructor(@Optional() @SkipSelf() parentModule: CoreModule) {
    throwIfAlreadyLoaded(parentModule, 'CoreModule');
  }

  static forRoot(): ModuleWithProviders<CoreModule> {
    return {
      ngModule: CoreModule,
      providers: [
        DatePipe,
        ...CORE_UTILS,
        ...CORE_REPOSITORIES,
        ...CORE_SECURITIES,
        ...CORE_SERVICES,
      ],
    };
  }
}
