import { Component, ViewChild, Input, Output, EventEmitter, ElementRef, OnInit, Renderer2, OnChanges } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ImageHelper } from '../../../@core/helpers/image.helper';
import { GlobalState } from '../../../@core/utils';

@Component({
  selector: 'ngx-picture-uploader',
  templateUrl: './picture-uploader.html',
})
export class PictureUploaderComponent implements OnInit, OnChanges {
  thumb_url: any = '';
  aspect_ratio: string = '16by9';

  @Input() set options(options: {thumb_url: string, aspect_ratio: '16by9'|'1by1'|string}) {
    if (options) {
      this.thumb_url = options.thumb_url ? options.thumb_url : '';
      this.aspect_ratio = options.aspect_ratio ? options.aspect_ratio : '16by9';
    }
  }

  @Output() onSelected: EventEmitter<any> = new EventEmitter();
  @Output() onDeleted: EventEmitter<any> = new EventEmitter();

  @ViewChild('fileUpload') protected _fileUpload: ElementRef;

  constructor(private _state: GlobalState, private _sanitizer: DomSanitizer, private elementRef: ElementRef, private renderer: Renderer2) {
    // this.renderer.setStyle(this.elementRef.nativeElement, 'background-url:', 'url()');
  }

  ngOnInit(): void {
  }

  ngOnChanges(changes: any): void {
  }

  protected _change(file: File): void {
    const reader = new FileReader();
    reader.addEventListener('load', (event: Event) => {
      // this.thumb_url = (<any>event.target).result;
      this.thumb_url = this._sanitizer.bypassSecurityTrustResourceUrl((<any>event.target).result);
    }, false);
    reader.readAsDataURL(file);
    if (file.type !== 'image/svg+xml') {
      ImageHelper.resizeImage(file).then((res) => this.onSelected.emit(res));
    } else {
      this.onSelected.emit(file);
    }
  }

  selector(): void {
    this._fileUpload.nativeElement.value = '';
    this._fileUpload.nativeElement.click();
  }

  open(): void {
    this._state.notifyDataChanged('modal.filemanager', (item: {type: 'image'|string, subtype: string, src: string, thumb_url: string, path: string, alt?: string, width?: number, height?: number}) => {
      console.log(item);
      if (item.type === 'image') {
        this.thumb_url = item.thumb_url;
        this.onSelected.emit(_.extend(_.cloneDeep(item), {'type': 'select'}));
      }
    });
  }

  remove(): void {
    this.thumb_url = '';
    this.onDeleted.emit('');
  }

  onFiles(): void {
    const files = this._fileUpload.nativeElement.files;
    if (files.length) {
      const file = files[0];
      this._change(file);
    }
  }
}
