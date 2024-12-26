import { Component, OnDestroy, AfterViewInit, Output, EventEmitter, ElementRef, Input, OnChanges, SimpleChanges } from '@angular/core';
import { LocationStrategy } from '@angular/common';
import { GlobalState } from '../../../@core/utils';

@Component({
  selector: 'ngx-tiny-mce',
  template: '',
})
export class TinyMCEComponent implements OnDestroy, AfterViewInit, OnChanges {
  @Output() editorKeyup = new EventEmitter<any>();

  private editor: any;
  private val: string;

  @Input() set value(val) {
    this.val = val;
    this.change();
  }

  constructor(private host: ElementRef, private locationStrategy: LocationStrategy, private _state: GlobalState) {
  }

  private change(): void {
    if (this.editor) {
      this.editor.setContent(this.val ? this.val : '');
    }
  }

  ngAfterViewInit(): void {
    setTimeout(() => tinymce.init({
      target: this.host.nativeElement,
      plugins: 'code fullscreen image imagetools link lists media paste preview table hr pagebreak nonbreaking toc textcolor colorpicker template',
      // toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat | link image',
      toolbar1: 'formatselect | fontsizeselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | numlist bullist indent removeformat',
      // image_advtab: true,
      skin_url: `${this.locationStrategy.getBaseHref()}assets/skins/lightgray`,
      setup: editor => {
        editor.on('init', () => {
          // console.log('The Editor has initialized.');
          editor.setContent(this.val ? this.val : '');
          this.editor = editor;
        });
        editor.on('keyup', () => {
          this.editorKeyup.emit(editor.getContent());
        });
        editor.on('change', () => {
          this.editorKeyup.emit(editor.getContent());
        });
      },
      fontsize_formats: '12px 13px 15px 16px 18px 20px 24px 28px 36px 48px',
      content_style: 'body {font-size: 14px}',
      table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tablecellprops tablemergecells tablesplitcells',
      height: '320',
      /*file_picker_callback: (callback, value, meta) => {
        this._state.notifyDataChanged('modal.filemanager', (img: any) => {
          console.log(meta);
          // callback(img.src, {alt: img.alt ? img.alt : '', width: img.width ? img.width : '', height: img.height ? img.height : ''});
          callback(img.src);
        });
      },*/
    }), 500);
  }

  ngOnChanges(changes: SimpleChanges): void {
    this.change();
  }

  ngOnDestroy(): void {
    if (this.editor) tinymce.remove(this.editor);
  }
}
