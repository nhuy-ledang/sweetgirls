import { Pipe, PipeTransform } from '@angular/core';

@Pipe({name: 'firstLetter'})
export class FirstLetterPipe implements PipeTransform {
  transform(input: string): any {
    input = input ? input.trim() : '';
    const string = input ? input : 'a';
    return string.charAt(0).toUpperCase();
  }
}
