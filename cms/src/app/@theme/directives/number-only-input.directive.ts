import { Directive, ElementRef, HostListener } from '@angular/core';

@Directive({
  selector: 'input[ngxNumberOnly]',
})
export class NumberOnlyInputDirective {
  constructor(private _el: ElementRef) {
  }

  @HostListener('input', ['$event']) onInputChange(event) {
    const value = this._el.nativeElement.value;
    this._el.nativeElement.value = value.replace(/[^0-9]*/g, '');
    if (value !== this._el.nativeElement.value) {
      event.stopPropagation();
    }
  }
}
