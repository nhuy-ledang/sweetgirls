import {Component, OnDestroy} from '@angular/core';

@Component({
  selector: 'ngx-forgot',
  templateUrl: './forgot.component.html',
})

export class ForgotComponent implements OnDestroy {
  constructor() {
    console.log('ForgotComponent');
  }

  ngOnDestroy() {
  }
}
