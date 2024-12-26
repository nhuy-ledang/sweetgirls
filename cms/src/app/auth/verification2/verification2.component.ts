import {Component, OnDestroy} from '@angular/core';

@Component({
  selector: 'ngx-verification2',
  templateUrl: './verification2.component.html',
})

export class Verification2Component implements OnDestroy {

  constructor() {
    console.log('Verification2Component');
  }

  ngOnDestroy() {
  }
}
