import { Component, EventEmitter, Input, OnChanges, OnDestroy, OnInit, Output, SimpleChanges } from '@angular/core';
import { ActivatedRoute, NavigationEnd, Router } from '@angular/router';

@Component({
  selector: 'ngx-minibar',
  styleUrls: ['./minibar.component.scss'],
  templateUrl: './minibar.component.html',
})
export class MinibarComponent {
  @Output() toggle = new EventEmitter();

  constructor() {
  }

  mini: { status: 0 | 1 | 2, tab: string } = {status: 0, tab: ''};

  onMini(tabActive: string): void {
    if (this.mini.status === 0) this.mini.status = 1;
    this.mini.tab = tabActive;
    if (this.mini.status === 1) {
      this.toggle.emit('mini');
    }
  }

  extendMini(): void {
    this.mini.status = 2;

    this.toggle.emit('extend');
  }

  closeMini(): void {
    this.mini.status = 0;
    this.toggle.emit('close');
  }
}
