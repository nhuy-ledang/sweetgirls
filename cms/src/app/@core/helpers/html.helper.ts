export class HtmlHelper {

  /**
   * Create Style Tag
   *
   * @param elementRef
   * @returns {HTMLStyleElement}
   */

  /*createStyle(elementRef: ElementRef): HTMLStyleElement {
    const style = document.createElement('style');
    style.type = 'text/css';

    jQuery(elementRef.nativeElement).prepend(style);

    return style;
  }*/

  /***
   * Add Style
   * @param style
   * @param cssString
   * @param isOnly
   * @returns {HTMLStyleElement}
   */
  appendChildStyle(style: HTMLStyleElement, cssString: string, isOnly: boolean): HTMLStyleElement {
    if (isOnly) {
      style.innerText = '';
    }
    style.appendChild(document.createTextNode(cssString));

    return style;
  }

  /***
   * Scroll to element
   *
   * @param elementName
   * @param parent
   */
  /*doScroll(elementName: any, parent?: any): void {
    let elements;
    if (parent) {
      elements = jQuery(elementName, parent);
    } else {
      elements = jQuery(elementName);
    }
    if (elements.length > 0) {
      if (elementName === '.form-group.has-error') {
        if (elements[0].querySelector('.textarea-description')) {
          elements[0].querySelector('.textarea-description').click();
        } else if (elements[0].querySelector('google-autocomplete')) {
          elements.find('input').focus();
        } else if (elements[0].querySelector('.inputPrice')) {
          elements[0].querySelector('.inputPrice').click();
        } else {
          elements.find('input, textarea, select')
            .not('input[type=hidden],input[type=button],input[type=submit],input[type=reset],input[type=image],button')
            .filter(':enabled:visible:first')
            .focus();
        }
      } else {
        elements.find('input, textarea, select')
          .not('input[type=hidden],input[type=button],input[type=submit],input[type=reset],input[type=image],button')
          .filter(':enabled:visible:first')
          .focus();
      }
      elements[0].scrollIntoView();
    }
  }*/

  /***
   * Scroll to error element
   *
   * @param elementName
   */
  /*doScrollError(parent?: any): void {
    setTimeout(() => {
      return this.doScroll('.form-group.has-error', parent);
    }, 200);
  }*/
}
