import { OnDestroy, ElementRef, Directive, Renderer2, Input, AfterViewInit, Inject, PLATFORM_ID, NgZone } from '@angular/core';
import { isPlatformBrowser, LocationStrategy } from '@angular/common';
import { AbstractControl, ControlValueAccessor, NG_VALIDATORS, NG_VALUE_ACCESSOR, ValidationErrors, Validator } from '@angular/forms';
import { GlobalState } from '../../@core/utils';
import { environment } from '../../../environments/environment';

// <textarea ngxEditor name="description" [formControl]="controls.description" (ngModelChange)="onChangeEditor()" class="form-control"></textarea>
@Directive({
  selector: '[ngxEditor]',
  providers: [
    {provide: NG_VALUE_ACCESSOR, multi: true, useExisting: EditorDirective},
    {provide: NG_VALIDATORS, multi: true, useExisting: EditorDirective},
  ],
})
export class EditorDirective implements AfterViewInit, OnDestroy, ControlValueAccessor, Validator {
  onChange: any = () => {
  }
  onTouched: any = () => {
  }

  // This is the updated value that the class accesses
  val: string = '';

  // This value is updated by programmatic changes
  set value(val) {
    if (this.val !== val) {
      // console.log(val);
      this.val = val;
      if (this.editor && !this.isInput) this.editor.setContent(this.val ? this.val : '');
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

  // Method that performs synchronous validation against the provided control.
  validate(control: AbstractControl): ValidationErrors|null {
    // const value = control.value;
    return null;
  }

  private editor: any;
  private editorElement: any;
  private isInput: boolean = false;
  @Input() opt: any = {};

  constructor(@Inject(PLATFORM_ID) private platformId: Object, private zone: NgZone, private host: ElementRef, private _renderer2: Renderer2, private locationStrategy: LocationStrategy, private _state: GlobalState) {
  }

  private createEditorElement() {
    // Use Angular's Renderer2 to create the div element
    const element = this._renderer2.createElement('div');
    // Set the id of the div
    // this._renderer2.setProperty(element, 'id', 'recaptcha-container');
    // Append the created div to the body element
    this._renderer2.appendChild(this.host.nativeElement.parentNode, element);
    // this.host.nativeElement.parentNode.insertAdjacentHTML('beforeend', '<div class="two">two</div>');

    return element;
  }

  private update(): void {
    if (this.editor) {
      this.isInput = true;
      const val = this.editor.getContent();
      // this.val = val;
      this.value = val;
      // this.onChange(val);
      setTimeout(() => {
        this.isInput = false;
      }, 1000);
    }
  }

  // Run the function only in the browser
  browserOnly(f: () => void) {
    if (isPlatformBrowser(this.platformId)) {
      this.zone.runOutsideAngular(() => {
        f();
      });
    }
  }

  ngAfterViewInit(): void {
    this.browserOnly(() => {
      const urls = `${environment.API_URL}`.split('/api/');
      const url = `${urls[0]}/api/editor_template`;
      this.editorElement = this.createEditorElement();
      this._renderer2.setStyle(this.host.nativeElement, 'display', 'none');
      const opt = _.extend({
        toolbar1: 'formatselect | fontsizeselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat',
        fontsize_formats: '12px 13px 15px 16px 18px 20px 24px 28px 36px 48px',
        content_style: 'body {font-size: 14px}',
        height: '320',
      }, this.opt);
      setTimeout(() => tinymce.init(_.extend(opt, {
        target: this.editorElement,
        plugins: 'code fullscreen image imagetools link lists media paste preview table hr pagebreak nonbreaking toc textcolor colorpicker template',
        templates: url,
        image_advtab: true,
        skin_url: `${this.locationStrategy.getBaseHref()}assets/skins/lightgray`,
        template_popup_width: 1000,
        table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tablecellprops tablemergecells tablesplitcells',
        // menubar: false,
        // toolbar: false,
        // statusbar: false,
        setup: (editor) => {
          editor.on('init', () => {
            // console.log('The Editor has initialized.');
            editor.setContent(this.val ? this.val : '');
            this.editor = editor;
          });
          editor.on('keyup', () => {
            this.update();
          });
          /*editor.on('keypress', () => {
            console.log('keypress');
          });
          editor.on('keydown', () => {
            console.log('keydown');
          });*/
          editor.on('change', () => {
            this.update();
          });
          /*editor.on('ExecCommand', (e) => {
            console.log('The ' + e.command + ' command was fired.');
          });*/
          // setTimeout(() => editor.setContent(this.val ? this.val : ''));
        },
        file_picker_callback: (callback, value, meta) => {
          this._state.notifyDataChanged('modal.filemanager', (img: any) => {
            // console.log(meta);
            // callback(img.src, {alt: img.alt ? img.alt : '', width: img.width ? img.width : '', height: img.height ? img.height : ''});
            callback(img.src);
          });
        },
      })), 500);
    });
  }

  ngOnDestroy(): void {
    // Clean up chart when the component is removed
    this.browserOnly(() => {
      if (this.editor) tinymce.remove(this.editor);
    });
  }
}
