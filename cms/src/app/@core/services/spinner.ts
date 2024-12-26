import { Injectable } from '@angular/core';

@Injectable()
export class Spinner {
  protected _selector: string = 'nb-global-spinner';
  protected _element: HTMLElement;

  constructor() {
    this._element = document.getElementById(this._selector);
  }

  load(opacity: string = '0.7'): void {
    this._element.style['opacity'] = opacity;
    this._element.style['display'] = 'block';
  }

  clear(delay: number = 0): void {
    this._element.style['opacity'] = '1';
    setTimeout(() => {
      this._element.style['display'] = 'none';
    }, delay);
  }
}
