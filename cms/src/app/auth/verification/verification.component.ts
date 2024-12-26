import {Component, OnDestroy} from '@angular/core';

@Component({
  selector: 'ngx-verification',
  templateUrl: './verification.component.html',
})

export class VerificationComponent implements OnDestroy {
  constructor() {
    console.log('VerificationComponent');
  }

  ngOnDestroy() {
  }
}
