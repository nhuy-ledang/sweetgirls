import {Directive, HostListener, Input} from '@angular/core';
import {NgControl} from '@angular/forms';

@Directive({
  selector: '[ngxPriceKeyPress]',
})

export class PriceKeyPressDirective {
  @Input() priceKeyPress: boolean = true;

  constructor(private control: NgControl) {
  }

  @HostListener('keypress', ['$event'])
  onKeyPress(e: any) {
    const event = e || window.event;
    if (event && this.priceKeyPress !== false) {
      let charCode = (event.which) ? event.which : event.keyCode;

      // Decimal Point
      if (charCode === 46 && event.key === '.') {
        charCode = 110;
      }

      // Not: Delete + Backspace + Left/Up/Right/Down
      if (!(charCode === 46 || charCode === 8 || (37 <= charCode && charCode <= 40))) {
        // Number + Decimal Point
        if ((48 <= charCode && charCode <= 57) || (charCode === 110 && event.target.value.indexOf('.') === -1)) {
          return true;
        } else {
          return false;
        }
      }
    }
  }

  @HostListener('input', ['$event'])
  onInput(e: any) {
    if (e.target.value && e.target.value.length > 9 && e.target.value.slice(-1) !== '.') {
      // Total length is 12
      const values = e.target.value.split('.');
      let value;
      if (values.length > 1) {
        value = `${values[0].slice(0, 9)}.${values[1].slice(0, 2)}`;
      } else {
        value = `${values[0].slice(0, 9)}`;
      }
      e.target.value = value;
      if (this.control.control) {
        this.control.control.setValue(e.target.value, {
          // onlySelf: true,
          // emitEvent: true,
          emitModelToViewChange: true,
          emitViewToModelChange: true,
        });
      }
    }
  }
}
