import { AbstractControl } from '@angular/forms';

export class PasswordValidator {
  public static validate(c: AbstractControl) {
    // let regexp = CPATTERN.PASSWORD;

    /*return regexp.test(c.value) ? null : {
      validatePassword: {
        valid: false
      }
    };*/
    if (c.value) {
      return (c.value.indexOf(' ') == -1) ? null : {
        validatePassword: {
          valid: false
        }
      }
    } else {
      return null;
    }
  }
}
