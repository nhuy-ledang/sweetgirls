import { Pipe, PipeTransform } from '@angular/core';

@Pipe({name: 'ngxRangeNumber'})
export class RangeNumberPipe implements PipeTransform {
  transform(input: number[], start: number, end: number): number[] {
    for (let i = start; i <= end; i++) input.push(i);
    return input;
  }
}
