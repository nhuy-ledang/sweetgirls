import { Pipe, PipeTransform } from '@angular/core';

@Pipe({name: 'pad'})
export class PadPipe implements PipeTransform {
  transform(value: number, digit: number) {
    let text = value.toString();
    while (text.length < digit) {
      text = '0' + text;
    }

    return text;
  }
}
