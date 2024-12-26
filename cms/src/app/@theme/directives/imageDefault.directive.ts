import { Directive, ElementRef, Input } from '@angular/core';

@Directive({
  selector: '[ngxDefault]',
  host: {
    '[src]': 'checkPath(src)',
    '(error)': 'onError()',
  },
})

export class ImageDefaultDirective {
  @Input() src: string;
  @Input('ngxDefault') def: string;

  private loaded: boolean = false;

  constructor(private el: ElementRef) {
  }

  onError() {
    if (!this.loaded) {
      this.el.nativeElement.src = this.def ? this.def : 'assets/images/no-image.jpg';
      this.loaded = true;
    }
  }

  checkPath(src) {
    return src ? src : (this.def ? this.def : 'assets/images/no-image.jpg');
  }
}
