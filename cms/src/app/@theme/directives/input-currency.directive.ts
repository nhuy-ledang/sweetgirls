import { AfterViewInit, Directive, ElementRef, Inject, NgZone, OnDestroy, PLATFORM_ID, Renderer2 } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { CurrencyFormatPipe } from '../pipes';

// <input ngxCurrency type="number" [(ngModel)]="model.pretax" (ngModelChange)="onChangeModel()" class="form-control">
@Directive({
  selector: 'input[ngxCurrency]',
  providers: [
    {provide: NG_VALUE_ACCESSOR, multi: true, useExisting: InputCurrencyDirective},
  ],
})
export class InputCurrencyDirective implements AfterViewInit, OnDestroy, ControlValueAccessor {
  private _input: any;
  onChange: any = () => {
  }
  onTouched: any = () => {
  }

  // This is the updated value that the class accesses
  val: number;

  // This value is updated by programmatic changes
  set value(val) {
    if (val !== undefined && this.val !== val) {
      this.val = val;
      this.onChange(val);
      // this.onTouched(val);
      this.setInputValue();
    }
  }

  // Writes a new value to the element.
  writeValue(value: any): void {
    this.value = value;
  }

  // Registers a callback function that is called when the control's value changes in the UI.
  registerOnChange(onChange: any) {
    this.onChange = onChange;
  }

  // Registers a callback function that is called by the forms API on initialization to update the form model on blur.
  registerOnTouched(onTouched: any) {
    this.onTouched = onTouched;
  }

  constructor(@Inject(PLATFORM_ID) private platformId: Object, private zone: NgZone, private _el: ElementRef, private _renderer2: Renderer2) {
    // console.log(this._el.nativeElement);
  }

  // Total length is 15
  private getDecimalValue(val: string): string {
    if (!val) return val;
    const values = val.split('.');
    let digits: string = values[0];
    if (digits.length > 12) digits = digits.slice(0, 12);
    let fraction: string = values.length > 1 ? values[1] : '';
    if (fraction.length > 2) fraction = fraction.slice(0, 2);

    return new CurrencyFormatPipe().transform(digits) + (fraction && fraction !== '00' ? `.${fraction}` : '');
  }

  private setInputValue(): void {
    if (this._input) {
      const val = this.getDecimalValue(String(this.val ? this.val : '0'));
      // this._renderer2.setAttribute(this._input, 'value', val);
      jQuery(this._input).val(val);
    }
  }

  private createInput(): any {
    // Use Angular's Renderer2 to create the input element
    const input = this._renderer2.createElement('input');
    // Set the class of the input
    this._renderer2.setAttribute(input, 'class', 'form-control');
    // Append the created div to the body element
    this._renderer2.appendChild(this._el.nativeElement.parentNode, input);
    // this._el.nativeElement.parentNode.insertAdjacentHTML('beforeend', '<div class="two">two</div>');
    this._renderer2.listen(input, 'change', (e) => {
      const value: number = parseFloat(e.target.value ? e.target.value.replace(/[^0-9.]*/g, '') : '');
      this.writeValue(value ? value : 0);
    });
    this._renderer2.listen(input, 'keypress', (e) => {
      let charCode = (e.which) ? e.which : e.keyCode;
      // Decimal Point
      if (charCode === 46 && e.key === '.') charCode = 110;
      // Not: Delete + Backspace + Left/Up/Right/Down
      if (!(charCode === 46 || charCode === 8 || (37 <= charCode && charCode <= 40))) {
        // Number + Decimal Point
        if ((48 <= charCode && charCode <= 57) || (charCode === 110 && e.target.value.indexOf('.') === -1)) {
          return true;
        } else {
          return false;
        }
      }
    });
    this._renderer2.listen(input, 'input', (e) => {
      const val = e.target.value ? e.target.value.replace(/[^0-9.]*/g, '') : '';
      if (!val || val && val.slice(-1) === '.') return;
      e.target.value = this.getDecimalValue(val);
    });
    // Disable host
    this._renderer2.setStyle(this._el.nativeElement, 'display', 'none');

    return input;
  }

  // Run the function only in the browser
  browserOnly(f: () => void) {
    if (isPlatformBrowser(this.platformId)) {
      this.zone.runOutsideAngular(() => {
        f();
      });
    }
  }

  ngAfterViewInit(): void {
    this.browserOnly(() => {
      // Create element
      this._input = this.createInput();
      this.setInputValue();
      // Init code more
    });
  }

  ngOnDestroy(): void {
    // Clean up chart when the component is removed
    this.browserOnly(() => {
      // if (this.root) this.root.dispose();
    });
  }
}
