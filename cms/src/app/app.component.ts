import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { Spinner } from './@core/services';
import { AlertComponent, FilemanagerComponent } from './@theme/modals';
import { GlobalState } from './@core/utils';

@Component({
  selector: 'ngx-app',
  template: '<router-outlet></router-outlet><ngx-modal-filemanager style="position: relative;z-index: 99999;"></ngx-modal-filemanager><ngx-modal-alert #alertModal></ngx-modal-alert>',
})
export class AppComponent implements OnInit, OnDestroy, AfterViewInit {
  @ViewChild('alertModal') alert: AlertComponent;
  @ViewChild(FilemanagerComponent) filemanager: FilemanagerComponent;

  constructor(private _spinner: Spinner, private _state: GlobalState) {
    this._state.subscribe('modal.alert', (data: {title: string, errors: any[], type?: string}) => this.alert.show(_.extend({message: data.errors}, data)));
    this._state.subscribe('modal.success', (data: {title: string, message: string}) => this.alert.show(_.extend({type: 'success'}, data)));
    /*this._state.subscribe('messenger:init', (data: { from: User, to: User }) => {
      this.fromUser = data.from;
      this.toUser = data.to;
    });
    this._state.subscribe('messenger:destroy', () => {
      this.fromUser = null;
      this.toUser = null;
    });*/
    this._state.subscribe('modal.filemanager', (callback?: Function) => this.filemanager.show(callback));
  }

  ngOnInit(): void {
  }

  ngAfterViewInit(): void {
    this._spinner.clear();
  }

  ngOnDestroy(): void {
    this._state.unsubscribe('modal.alert');
    this._state.unsubscribe('modal.success');
    this._state.unsubscribe('modal.filemanager');
  }
}
