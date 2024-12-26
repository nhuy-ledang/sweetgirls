import { Pipe, PipeTransform } from '@angular/core';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

@Pipe({
  name: 'safeHtml',
})
export class SafeHtmlPipe implements PipeTransform {
  constructor(private sanitizer: DomSanitizer) {
  }

  transform(value: string, newline: boolean): SafeHtml {
    return this.sanitizer.bypassSecurityTrustHtml(!!newline ? (!value ? value : value.replace(/(?:\r\n|\r|\n|\\n|\\r)/g, '<br/>')) : value);
  }
}
