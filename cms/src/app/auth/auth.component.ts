import {AfterViewInit, Component} from '@angular/core';
// import {NbSpinnerService} from "@nebular/theme";

@Component({
  selector: 'ngx-auth',
  template: `
    <router-outlet></router-outlet>
  `,
})

export class AuthComponent implements AfterViewInit {
  constructor(/*public _spinner: NbSpinnerService*/) {
    console.log('AuthComponent');
  }

  ngAfterViewInit() {
    console.log('ngAfterViewInit');
    // this._spinner.clear();
  }
}
