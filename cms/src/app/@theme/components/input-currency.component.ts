import { Component, Input, Output, EventEmitter, ViewChild, ElementRef, OnInit } from '@angular/core';
import { AbstractControl } from '@angular/forms';

@Component({
  selector: 'ngx-input-currency',
  template: `
    <ng-container *ngIf="isRender">
      <span (click)="onEnable()" [ngStyle]="{'display': (!isInput ? '' : 'none')}" class="form-control" [innerText]="frmControl.value|currencyFormat"></span>
      <input #inputElement type="number" class="form-control" ngxPriceKeyPress="true" [formControl]="frmControl" (change)="onChange()" (blur)="onDisable()" (keyup)="onKeyUp($event)" [ngStyle]="{'display': (isInput ? '' : 'none')}">
    </ng-container>`,
})

export class InputCurrencyComponent implements OnInit {
  @Input() frmControl: AbstractControl;
  @Output() change = new EventEmitter();

  @ViewChild('inputElement') inputElement: ElementRef;

  isInput: boolean = false;
  isRender = false;

  constructor() {
  }

  ngOnInit(): void {
    this.isRender = true;
  }

  onChange(): void {
    this.change.emit(this.frmControl);
  }

  onEnable(): void {
    this.isInput = true;
    setTimeout(() => {
      this.inputElement.nativeElement.focus();
    }, 200);
  }

  onDisable(): void {
    this.isInput = false;
  }

  onKeyUp(event): void {
    if (event.keyCode === 13) { // If Enter Key
      this.onDisable();
    }
  }
}
