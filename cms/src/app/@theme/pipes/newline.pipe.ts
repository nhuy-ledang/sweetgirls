import { Pipe, PipeTransform } from '@angular/core';

@Pipe({name: 'newline'})
export class NewlinePipe implements PipeTransform {
  transform(value: string, args: string[]): string {
    return !value ? value : value.replace(/(?:\r\n|\r|\n|\\n|\\r)/g, '<br/>');
  }
}
