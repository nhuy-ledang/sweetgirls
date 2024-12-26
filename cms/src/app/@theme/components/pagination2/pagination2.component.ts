import {Component, Input, Output, EventEmitter} from '@angular/core';

@Component({
  selector: 'ngx-pagination2',
  templateUrl: './pagination2.html',
})
export class Pagination2Component {
  @Input() page: number = 1;
  @Input() pageSize: number = 25;
  @Input() pageSizeList: number[] = [10, 25, 50, 100];
  @Input() totalItems: number = 100;
  @Input() maxSize: number = 10;
  @Output() change: EventEmitter<{page: number, itemsPerPage: number}> = new EventEmitter();
  @Output() pageSizeChange: EventEmitter<number> = new EventEmitter();

  options = {
    minimumResultsForSearch: Infinity,
    dropdownCssClass: 'custom-select-sm',
    dropdownAutoWidth: true,
    selectionCssClass: 'custom-select-paging',
    width: true,
  };

  pageChanged(event: {page: number, itemsPerPage: number}): void {
    this.page = event.page;
    this.change.emit(event);
  }

  pageSizeChanged(pageSize: string): void {
    this.page = 1;
    this.pageSize = parseInt(pageSize, 0);
    this.pageSizeChange.emit(this.pageSize);
  }
}
