import { Component } from '@angular/core';

@Component({
  selector: 'ngx-footer',
  styleUrls: ['./footer.component.scss'],
  template: `
    <span class="created-by">
      Created with ♥ by <b><a href="https://tedfast.vn/" target="_blank">TEDFAST</a></b>
    </span>
    <div class="socials">
      <a href="https://www.facebook.com/motila.vn" target="_blank" class="ion ion-social-facebook"></a>
    </div>
  `,
})
export class FooterComponent {
}
