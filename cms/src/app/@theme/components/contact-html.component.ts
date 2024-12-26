import { Component, Input } from '@angular/core';

@Component({
  selector: 'ngx-contact-html',
  template: `<span *ngIf="contact" class="d-flex">
    <span><span class="avatar avatar-sm avatar-circle float-left"><img class="avatar-img" [src]="contact.avatar_url"></span></span>
    <span class="ml-2">
      <span class="d-block font-weight-bold" [innerText]="contact.display?contact.display:contact.fullname"></span>
      <span class="d-block fs-6 text-primary" [innerText]="contact.contact_title?contact.contact_title:(contact.position?contact.position:'')"></span>
    </span>
  </span>`,
})
export class ContactHtmlComponent {
  @Input() contact: any;
}
