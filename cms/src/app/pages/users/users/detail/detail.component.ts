import { AfterViewInit, Component, ElementRef, OnDestroy, OnInit, Renderer2, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { ConfirmComponent } from '../../../../@theme/modals';
import { AppList } from '../../../../app.base';
import { UsersRepository } from '../../shared/services';
import { User } from '../../shared/entities';

@Component({
  selector: 'ngx-user-detail',
  templateUrl: './detail.component.html',
})
export class UserDetailComponent extends AppList implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild(ConfirmComponent) confirm: ConfirmComponent;
  @ViewChild('viewport') viewport: ElementRef;
  info: User = null;
  isCollapsed: boolean = false;
  sectionId: string;

  constructor(router: Router, security: Security, state: GlobalState, repository: UsersRepository,
              private _renderer2: Renderer2, private _el: ElementRef,
              protected _route: ActivatedRoute) {
    super(router, security, state, repository);
    this.info = this._route.snapshot.data['info'];
    console.log(this.info);
  }

  ngOnInit(): void {

  }

  ngOnDestroy(): void {
    super.destroy();
  }

  ngAfterViewInit() {
    // setTimeout(() => this.getData(), 200);
    /*const container = this._el.nativeElement.closest('.scrollable-container');
    if (container) this._renderer2.listen(container, 'scroll', (event) => {
      let sectionId: string;
      const children = this.viewport.nativeElement.children;
      const scrollTop = event.target.scrollTop;
      const parentOffset = event.target.offsetTop;
      for (let i = 0; i < children.length; i++) {
        const element = children[i];
        if ((element.offsetTop - parentOffset) <= scrollTop) {
          sectionId = element.id;
        }
      }
      if (sectionId !== this.sectionId) {
        this.sectionId = sectionId;
        console.log(sectionId);
      }
    });*/
  }

  create() {

  }

  edit(item) {

  }

  remove(item) {
    this.confirm.show({title: 'Xác nhận', message: 'Bạn có chắc chắn muốn xóa mục này?', type: 'delete', confirmText: 'Đồng ý', cancelText: 'Không', data: {type: 'remove', info: item}});
  }

  back() {
    this._router.navigateByUrl('pages/users');
  }

  /*scrollTo(sectionId: string) {
    document.querySelector('#' + sectionId).scrollIntoView();
  }*/

  onConfirm(data: any) {
    if (data.type === 'remove') {
      this.removeItem(data.info);
    }
  }
}
