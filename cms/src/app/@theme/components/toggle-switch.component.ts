import { Component } from '@angular/core';
import { AbstractControl, ControlValueAccessor, NG_VALIDATORS, NG_VALUE_ACCESSOR, ValidationErrors, Validator } from '@angular/forms';

@Component({
  selector: 'ngx-toggle-switch',
  template: `<label class="toggle-switch toggle-switch-sm">
    <input type="checkbox" class="toggle-switch-input" [(ngModel)]="model" (change)="onChangeModel()">
    <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
  </label>`,
  providers: [
    {provide: NG_VALUE_ACCESSOR, multi: true, useExisting: ToggleSwitchComponent},
    {provide: NG_VALIDATORS, multi: true, useExisting: ToggleSwitchComponent},
  ],
})
export class ToggleSwitchComponent implements ControlValueAccessor, Validator {
  onChange: any = () => {
  }
  onTouched: any = () => {
  }

  model: boolean = false;

  // This is the updated value that the class accesses
  val: boolean;

  // This value is updated by programmatic changes
  set value(val) {
    if (val !== undefined && this.val !== val) {
      this.val = val;
      this.onChange(val);
      // this.onTouched(val);
      this.model = this.val;
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

  // Method that performs synchronous validation against the provided control.
  validate(control: AbstractControl): ValidationErrors|null {
    // const value = control.value;
    return null;
  }

  private update(): void {
    this.value = this.model;
  }

  onChangeModel(): void {
    this.update();
  }
}
