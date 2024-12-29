import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Subject } from 'rxjs';
import { TranslateService } from '@ngx-translate/core';
import { GlobalState } from '../../../../@core/utils';
import { Security } from '../../../../@core/security';
import { StorageService } from '../../../../@core/services';
import { NotifiesRepository } from '../../../../@core/repositories';
import { SidebarService } from '../../../../@core/utils';

@Component({
  selector: 'ngx-header',
  styleUrls: ['./header.component.scss'],
  templateUrl: './header.component.html',
})
export class HeaderComponent implements OnInit, OnDestroy {
  private destroy$: Subject<void> = new Subject<void>();
  private timer;
  compacted = false;
  user: any;
  alerts: any[] = [];
  alertCount: number = 0;

  constructor(protected _sidebarService: SidebarService,
              protected _route: ActivatedRoute,
              protected _storage: StorageService,
              protected _state: GlobalState,
              protected _security: Security,
              protected _notifies: NotifiesRepository, private _translate: TranslateService) {
    this._translate.addLangs(['en', 'vi']);
  }

  protected getAlerts(): void {
    if (!this.user) return;
    this._notifies.getAlerts({paging: 0, page: 1, pageSize: 25, sort: 'id', order: ''}).then(
      (res: any) => {
        if (typeof res.alert !== 'undefined') this.alertCount = res.alert;
        _.forEach(res.data, (item: any) => {
          this.alerts.push(item);
        });
      },
      (errors) => {
        console.log(errors);
      },
    );
  }

  protected setUser(user: any): void {
    if (!this.user || (this.user && this.user.id !== user.id)) {
      this.user = user;
    }
  }

  ngOnInit(): void {
    this.setUser(this._route.snapshot.data['auth']);
    this._state.subscribe('security.isLogged', (user: any) => {
      this.setUser(user);
    });

    this._sidebarService.onToggle().subscribe((data: {compact: boolean, tag: string}) => {
      this.compacted = data.compact;
    });
    /*setTimeout(() => {
      this.getAlerts();
    }, 1000);*/
    /*this.timer = setInterval(() => {
      this.getAlerts();
    }, 5000);*/
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
    if (this.timer) {
      clearInterval(this.timer);
    }
  }

  toggleSidebar(): boolean {
    this.compacted = !this.compacted;
    this._sidebarService.toggle(this.compacted, 'menu-sidebar');

    return this.compacted;
  }

  changPass(): void {
    this._state.notifyDataChanged('dlgPassword:show', {user: this.user});
  }

  logout() {
    this._state.notifyDataChanged('messenger:destroy', {});
    this._security.logout();
  }

  alertClick(item: any) {
    // Mark read
    if (!item.is_read) {
      this.alertCount--;
      item.is_read = !item.is_read;
      this._notifies.markAlerts({ids: item.id}).then((res: any) => {
        if (typeof res.alert !== 'undefined') this.alertCount = res.alert;
      }, (errors) => console.log(errors));
    }
  }

  markReadAll() {
    this.alertCount = 0;
    _.forEach(this.alerts, (item: any) => {
      item.is_read = true;
    });
    this._notifies.markAlerts({ids: ''}).then((res: any) => {
      if (typeof res.alert !== 'undefined') this.alertCount = res.alert;
    }, (errors) => console.log(errors));
  }

  setLang(lang: 'vi'|'en'): void {
    this._translate.setDefaultLang(lang);
  }
}
