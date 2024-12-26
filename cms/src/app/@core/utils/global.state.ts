import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { share } from 'rxjs/operators';

@Injectable()
export class GlobalState {
  private data$ = new Subject<Object>();
  private dataStream$ = this.data$.asObservable();

  private _subscriptions: Map<string, Array<Function>> = new Map<string, Array<Function>>();

  constructor() {
    this.dataStream$.subscribe((data) => this._onEvent(data));
  }

  notifyDataChanged(event: string, value?: any): void {
    const current = this.data$[event];
    if (current !== value) {
      this.data$[event] = value;
      this.data$.next({event: event, data: this.data$[event]});
    }
  }

  subscribe(event: string, callback: Function): void {
    const subscribers = this._subscriptions.get(event) || [];
    subscribers.push(callback);
    this._subscriptions.set(event, subscribers);
  }

  unsubscribe(event: string): void {
    this._subscriptions.delete(event);
  }

  private _onEvent(data: any): void {
    const subscribers = this._subscriptions.get(data['event']) || [];
    subscribers.forEach((callback) => {
      callback.call(null, data['data']);
    });
  }

  /**
   * Subscribe to events
   *
   * @returns Observable<{ event: string, data: any }>
   */
  onEvent() {
    return this.data$.pipe(share());
  }
}
