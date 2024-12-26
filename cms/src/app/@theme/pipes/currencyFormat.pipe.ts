import { Pipe, PipeTransform } from '@angular/core';
import { CurrencyPipe } from '@angular/common';

@Pipe({name: 'currencyFormat'})
export class CurrencyFormatPipe implements PipeTransform {
  transform(value: any, currencyCode?: string, display?: 'code'|'symbol'|'symbol-narrow'|string|boolean, digitsInfo?: string, locale?: string) {
    if (!value) return 0;
    if (!locale) locale = 'en-US';

    return new CurrencyPipe(locale).transform(value, currencyCode ? currencyCode : '', display ? display : '', digitsInfo ? digitsInfo : '1.0-0');
  }
}
