import { Component, Input, Output, EventEmitter, ViewChild, ElementRef } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { BsDropdownDirective } from 'ngx-bootstrap/dropdown';
import { ProductsRepository } from '../../services';

// <ngx-input-product [formControl]="controls.product_id" [(selected)]="curSelected" (ngModelChange)="onProductChange()" (onSelected)="onProductSelected($event)"></ngx-input-product>
@Component({
  selector: 'ngx-input-product',
  styleUrls: ['./input-product.component.scss'],
  templateUrl: './input-product.component.html',
  providers: [
    {provide: NG_VALUE_ACCESSOR, multi: true, useExisting: InputProductComponent},
  ],
})

export class InputProductComponent implements ControlValueAccessor {
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

  setDisabledState(isDisabled: boolean): void {
    this.readOnly = isDisabled;
  }

  @ViewChild('dropdown') dropdown: BsDropdownDirective;
  @ViewChild('inputElement') inputElement: ElementRef;
  curSelected: {id: number|'', name: string} = {id: 0, name: ''};
  @Input() readOnly: boolean;

  @Input() set selected(item: {id: 0, name: ''}) {
    if (item && _.isObject(item)) {
      this.curSelected.id = item.id;
      this.curSelected.name = item.name;
    }
  }

  @Output() selectedChange: EventEmitter<{id: ''|number, name: string}> = new EventEmitter<{id: ''|number, name: string}>();
  @Output() onSelected: EventEmitter<any> = new EventEmitter();
  data: {loading?: boolean, items: any[], paging: number, page: number, pageSize: number, sort: string, order: string, data: any} = {
    loading: false, items: [], paging: 0, page: 1, pageSize: 25, sort: 'name', order: 'asc', data: {q: ''},
  };
  tempCache: any = {};

  constructor(private _element: ElementRef, private _api: ProductsRepository) {
  }

  // Get data table
  protected getData(): void {
    const key = '|q=' + this.data.data.q;
    if (this.tempCache[key] && this.tempCache[key].length) {
      this.data.items = this.tempCache[key];
    } else {
      this.data.loading = true;
      this._api.search(this.data, false).then((res: any) => {
          this.data.items = res.data;
          this.data.loading = false;
          this.tempCache[key] = this.data.items;
        }, (errors) => {
          console.log(errors);
          this.data.loading = false;
        },
      );
    }
  }

  select(item: any): void {
    this.dropdown.isOpen = false;
    if (this.curSelected.id !== item.id) {
      this.curSelected.id = item.id;
      this.curSelected.name = item.name;
      this.onSelected.emit(item);
      this.selectedChange.emit(this.curSelected);
      this.writeValue(this.curSelected.id);
    }
  }

  unselect(): void {
    this.curSelected.id = '';
    this.curSelected.name = '';
    this.onSelected.emit(null);
    this.selectedChange.emit(this.curSelected);
    this.writeValue(this.curSelected.id);
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
