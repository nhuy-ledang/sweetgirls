import { Directive, ElementRef, Input } from '@angular/core';

@Directive({
  selector: '[ngxFileExtension]',
})

export class FileExtensionDirective {

  protected init(fileUrl): void {
    if (fileUrl) {
      const arr = fileUrl.split('.');
      const ex = arr[arr.length - 1];
      if (ex) {
        let className = '';
        if (['png', 'jpg', 'jpeg'].indexOf(ex) !== -1) {
          className = 'ic_file_att';
        } else if (['doc', 'docx'].indexOf(ex) !== -1) {
          className = 'ic_word';
        } else if (['xls', 'xlsx'].indexOf(ex) !== -1) {
          className = 'ic_excel';
        } else if (['pdf'].indexOf(ex) !== -1) {
          className = 'ic_pdf';
        } else {
          className = 'ic_download';
        }
        this.el.nativeElement.innerHTML = '<span class="' + className + '"></span>';
        console.log(fileUrl);
      }
    }
  }

  @Input() set ngxFileExtension(fileUrl: string) {
    this.init(fileUrl);
  }

  constructor(private el: ElementRef) {
  }
}
