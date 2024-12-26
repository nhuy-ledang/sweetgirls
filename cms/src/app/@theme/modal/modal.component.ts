import { Component, ViewEncapsulation, ElementRef, Input, OnInit, OnDestroy } from '@angular/core';

import { ModalService } from './modal.service';

@Component({
  selector: 'ngx-modal',
  templateUrl: 'modal.component.html',
  styleUrls: ['modal.component.scss'],
  encapsulation: ViewEncapsulation.None,
})
export class ModalComponent implements OnInit, OnDestroy {
  public id: string;
  @Input() size: string = 'sm modal-dialog-centered';

  constructor(private _modalService: ModalService, private _el: ElementRef) {
  }

  ngOnInit(): void {
    /*// ensure id attribute exists
    if (!this.id) {
      console.error('modal must have an id');
      return;
    }*/

    // move element to bottom of page (just before </body>) so it can be displayed above everything else
    document.body.appendChild(this._el.nativeElement);

    // close modal on background click
    this._el.nativeElement.addEventListener('click', el => {
      if (el.target.className.includes('ngx-modal')) this.close();
    });

    // add self (this modal instance) to the modal service so it's accessible from controllers
    this._modalService.add(this);
  }

  // remove self from modal service when component is destroyed
  ngOnDestroy(): void {
    this._modalService.remove(this.id);
    this._el.nativeElement.remove();
  }

  // open modal
  open(): void {
    this._el.nativeElement.style.display = 'block';
    document.body.classList.add('ngx-modal-open');
  }

  // close modal
  close(): void {
    this._el.nativeElement.style.display = 'none';
    document.body.classList.remove('ngx-modal-open');
  }
}
