import { Pipe, PipeTransform } from '@angular/core';
import { Helper } from '../../@core/helpers';

@Pipe({name: 'byte2Size'})
export class Byte2SizePipe implements PipeTransform {
  transform(input: number): string {
    return Helper.bytesToSize(input);
  }
}
