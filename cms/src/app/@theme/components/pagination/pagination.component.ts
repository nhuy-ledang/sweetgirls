import {Component, Input, Output, EventEmitter, OnInit, OnChanges} from '@angular/core';

@Component({
  selector: 'ngx-pagination',
  template: `
    <div class="btn-group-pagination" *ngIf="totalItems">
      <span class="result"><span [textContent]="(page-1)*pageSize+1">1</span>-<span [textContent]="totalItems<((page-1)*pageSize+pageSize)?totalItems:((page-1)*pageSize+pageSize)">20</span> of <span [textContent]="totalItems">100</span></span>
      <button type="button" class="btn btn-light btn-icon" (click)="previous()" [disabled]="previousDisabled"><span class="ic ic_angle_left"></span></button>
      <button type="button" class="btn btn-light btn-icon" (click)="next()" [disabled]="nextDisabled"><span class="ic ic_angle_right"></span></button>
    </div>`,
})
export class PaginationComponent implements OnInit, OnChanges {
  @Input() page: number = 1;
  @Input() pageSize: number = 25;
  @Input() pageSizeList: number[] = [10, 25, 50, 100];
  @Input() totalItems: number = 100;
  @Input() maxSize: number = 10;
  @Output() change: EventEmitter<{page: number, itemsPerPage: number}> = new EventEmitter();
  @Output() pageSizeChange: EventEmitter<number> = new EventEmitter();

  previousDisabled: boolean = false;
  nextDisabled: boolean = false;

  private checkAction(): void {
    this.previousDisabled = this.page <= 1;
    this.nextDisabled = (this.page >= Math.ceil(this.totalItems / this.pageSize));
  }

  previous(): void {
    if (this.page > 1) {
      --this.page;
      setTimeout(() => this.change.emit({page: this.page, itemsPerPage: this.pageSize}));
    }
    this.checkAction();
  }

  next(): void {
    if (this.page < Math.ceil(this.totalItems / this.pageSize)) {
      ++this.page;
      setTimeout(() => this.change.emit({page: this.page, itemsPerPage: this.pageSize}));
    }
    this.checkAction();
  }

  ngOnInit(): void {
    this.checkAction();
  }

  ngOnChanges(changes: any): void {
    this.checkAction();
  }
}
