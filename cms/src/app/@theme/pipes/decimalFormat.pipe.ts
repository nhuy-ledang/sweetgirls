import { Pipe, PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';

@Pipe({name: 'decimalFormat'})
export class DecimalFormatPipe implements PipeTransform {
  transform(value: any, code_iso?: string, digits?: number) {
    if (value === null || value === '' || value === '.') {
      return '0';
    }
    const minFraction = digits ? digits : 2;
    const maxFraction = digits ? digits : minFraction;
    let locale = 'en-US';
    if (code_iso) {
      code_iso = code_iso.toLowerCase().trim();
      // Dong (₫)
      if (code_iso === 'vnd') {
        locale = 'vi-VN';
        value = Math.round(value);
      } else if (code_iso === 'eur') { // France // Euro (€)
        locale = 'fr-FR';
      } else if (code_iso === 'brl' || code_iso === 'idr' || code_iso === 'try') { // Turkish // Real (R$)/ Rupiah (Rp)/ Lira (TL)
        locale = 'tr-TR';
      }
    }
    const r = new DecimalPipe(locale).transform(value, `1.${minFraction}-${maxFraction}`);
    /*if (locale === 'fr-FR') {
      return r.replace(/,/g, '.');
    } else {
      return r;
    }*/
    return r;
  }
}
