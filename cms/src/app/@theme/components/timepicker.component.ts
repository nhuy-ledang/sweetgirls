import { Component, Input } from '@angular/core';
import { AbstractControl, ControlValueAccessor, NG_VALIDATORS, NG_VALUE_ACCESSOR, ValidationErrors, Validator } from '@angular/forms';

@Component({
  selector: 'ngx-timepicker',
  template: `
    <table>
      <tbody>
      <tr>
        <td class="form-group"><select style="width:65px" class="form-control" [(ngModel)]="params.hour" (change)="onChangeHour()">
          <option value="">--</option>
          <option *ngFor="let item of []|ngxRangeNumber:0:23" [value]="item">{{ item|pad:2 }}</option>
        </select></td>
        <td>&nbsp;:&nbsp;</td>
        <td class="form-group"><select style="width:65px" class="form-control" [(ngModel)]="params.minute" (change)="onChangeMinute()">
          <option value="">--</option>
          <option *ngFor="let item of minutes" [value]="item">{{ item|pad:2 }}</option>
        </select></td>
      </tr>
      </tbody>
    </table>`,
  providers: [
    {provide: NG_VALUE_ACCESSOR, multi: true, useExisting: TimepickerComponent},
    {provide: NG_VALIDATORS, multi: true, useExisting: TimepickerComponent},
  ],
})
export class TimepickerComponent implements ControlValueAccessor, Validator {
  @Input() minuteStep: number = 5;
  onChange: any = () => {
  }
  onTouched: any = () => {
  }

  minutes: number[] = [];
  params: {year?: number, hour: string, minute: string} = {hour: '', minute: ''};

  // This is the updated value that the class accesses
  val: Date = null;

  // This value is updated by programmatic changes
  set value(val) {
    if (val !== undefined && this.val !== val) {
      this.val = val;
      this.onChange(val);
      // this.onTouched(val);
      if (this.val && this.val instanceof Date) {
        this.params.year = this.val.getFullYear();
        this.params.hour = String(this.val.getHours());
        let m = this.val.getMinutes();
        while (this.minuteStep > 0 && m % this.minuteStep !== 0) m--;
        this.params.minute = String(m);
      }
    }
  }

  constructor() {
    this.minutes = [];
    for (let i = 0; i < 60; i = i + this.minuteStep) {
      this.minutes.push(i);
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
    if (this.params.hour && this.params.minute) {
      const d = this.val ? new Date(this.val) : new Date();
      d.setHours(parseInt(this.params.hour, 0), parseInt(this.params.minute, 0));
      this.value = d;
    } else {
      this.value = null;
    }
  }

  onChangeHour(): void {
    this.update();
  }

  onChangeMinute(): void {
    this.update();
  }
}
