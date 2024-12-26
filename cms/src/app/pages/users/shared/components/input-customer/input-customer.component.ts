import { Component, Input, ViewChild, ElementRef, Output, EventEmitter } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { BsDropdownDirective } from 'ngx-bootstrap/dropdown';
import { UsersRepository } from '../../services';

// <ngx-input-customer [formControl]="controls.user_id" [selected]="userSelected" (ngModelChange)="onChangeCustomer()"></ngx-input-customer>
@Component({
  selector: 'ngx-input-customer',
  styleUrls: ['./input-customer.component.scss'],
  templateUrl: './input-customer.component.html',
  providers: [
    {provide: NG_VALUE_ACCESSOR, multi: true, useExisting: InputCustomerComponent},
  ],
})

export class InputCustomerComponent implements ControlValueAccessor {
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

  @ViewChild('dropdown') dropdown: BsDropdownDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  curSelected: {user_id: ''|number, display: string} = {user_id: '', display: ''};
  @Input() readOnly: boolean;

  @Input() set selected(item: {user_id: ''|number, display: string}) {
    if (item && _.isObject(item)) {
      this.curSelected.user_id = item.user_id;
      this.curSelected.display = item.display;
    }
  }

  @Output() onSelected: EventEmitter<any> = new EventEmitter();

  data: {loading?: boolean, items: any[], paging: number, page: number, pageSize: number, sort: string, order: string, data: any} = {
    loading: false,
    items: [],
    paging: 0,
    page: 1,
    pageSize: 25,
    sort: 'first_name',
    order: 'asc',
    data: {
      fields: 'id,first_name,last_name,email,phone_number',
      q: '',
    },
  };

  constructor(private _element: ElementRef, private _user: UsersRepository) {
  }

  // Get data table
  protected getData(): void {
    this.data.loading = true;
    this._user.get(this.data, false).then((res: any) => {
        this.data.items = res.data;
        this.data.loading = false;
      }, (errors) => {
        console.log(errors);
        this.data.loading = false;
      },
    );
  }

  select(item: any): void {
    this.dropdown.isOpen = false;
    if (this.curSelected.user_id !== item.id) {
      this.curSelected.user_id = item.id;
      this.curSelected.display = item.display;
      this.writeValue(this.curSelected.user_id);
      this.onSelected.emit(item);
    }
  }

  // <editor-fold desc="Events">
  private timer: any;

  onFilter(): void {
    if (this.timer) {
      clearTimeout(this.timer);
      this.timer = undefined;
    }
    this.timer = setTimeout(() => {
      this.data.page = 1;
      this.getData();
    }, 800);
  }

  // </editor-fold>

  onShown(): void {
    setTimeout(() => this.inputElement.nativeElement.focus());
  }
}
