import { Component, Input, Output, ViewChild, ElementRef, EventEmitter, HostListener, Renderer2, OnInit, AfterViewInit, OnDestroy } from '@angular/core';
import { Client, MessengerService, XMPP_EVENTS, XMPP_MESSAGE_BODY_TYPES, XMPP_MESSAGE_CHATSTATES, XMPP_MESSAGE_TYPES, XMPPImageInterface, XMPPMessage } from './messenger.service';
import { User } from '../entities';
import { MediasRepository } from '../../../../@core/repositories';
import { UUID } from '../../../../@core/helpers';

@Component({
  selector: 'ngx-messenger',
  template: `
    <div #containerElem class="messengerbox" [ngClass]="{'connected': connected, 'disconnected': !connected}">
      <div class="msgheader">CHAT <a class="close" (click)="close()"><span class="ic_close"></span></a></div>
      <div #bodyElem class="msgbody">
        <ul>
          <li *ngFor="let m of messageList; let i = index;">
            <div *ngIf="m.body.channel && (!messageList[i-1] || (messageList[i-1] && messageList[i-1].body.channel != m.body.channel))" class="msgchannel">{{m.body.channel}}</div>
            <span class="{{ m.type }}" [ngClass]="{'pos_left': !m.owner, 'pos_right': m.owner, 'link': m.isLink }">
              <span *ngIf="m.type == TYPES.TEXT">
                <span *ngIf="!m.isLink" [innerHTML]="formatText(m.content)"></span>
                <ng-container *ngIf="m.isLink"><a href="{{ m.content }}" target="_blank">{{ m.content }}</a></ng-container>
              </span>
              <span *ngIf="m.type == TYPES.IMAGE" class="msgimages len{{m.files.length}}"><span *ngFor="let f of m.files"><img [src]="f.raw_url" ngxDefault="assets/images/no-image.jpg" class="img-fluid"></span></span>
              <span *ngIf="m.type == TYPES.EMOJI"><img [src]="m.content" class="img-fluid"></span>
            </span>
          </li>
        </ul>
        <ngx-typing *ngIf="typing"></ngx-typing>
        <div class="msgpreview" *ngIf="files.length > 0">
          <div *ngFor="let m of files">
            <div class="loading-indicator"></div>
          </div>
        </div>
      </div>
      <div #footerElem class="msgfooter">
        <span (click)="selector()" class="pointer"><i class="ic_file_att"></i></span>
        <textarea #inputElement [(ngModel)]="message"
                  (keyup)="onKeyUp($event)"
                  (keypress)="onKeyPress($event)"
                  (input)="onInput($event)"
                  placeholder="Type something to send"></textarea>
        <span *ngIf="!disabled" (click)="sendMessage()" class="pointer"><i class="ic_send"></i></span>
        <span *ngIf="disabled" class="pointer"><i class="ic_send disabled"></i></span>
      </div>
      <input #fileUpload type="file" multiple hidden="true" accept="image/*" (change)="upload($event)">
    </div>
  `,
})

export class MessengerComponent implements OnInit, OnDestroy, AfterViewInit {
  TYPES = XMPP_MESSAGE_BODY_TYPES;

  @Output() onPreSend = new EventEmitter<any>();
  @Output() onSent = new EventEmitter<string>();
  @Output() onReceived = new EventEmitter<string>();
  @Output() onUpload = new EventEmitter<string>();

  @ViewChild('containerElem') containerElem: ElementRef;
  @ViewChild('bodyElem') bodyElem: ElementRef;
  @ViewChild('footerElem') footerElem: ElementRef;
  @ViewChild('inputElement') inputElement: ElementRef;

  private client: Client = null;
  connected: boolean = false;
  message: string = '';
  messageList: Array<XMPPMessage> = [];
  typing: boolean = false;
  private typingTimer: any = null;

  files: Array<File> = [];
  disabled: boolean = true;

  @ViewChild('fileUpload') fileUpload: ElementRef;

  private fromUser: User = null;
  private toUser: User = null;

  @Input() set data(d: { from: User, to: User }) {
    if (d && d.from && d.to) {
      const fromChanged = !!this.fromUser && this.fromUser.id !== d.from.id;
      const toChanged = !!this.toUser && this.toUser.id !== d.to.id;
      this.fromUser = d.from;
      this.toUser = d.to;
      // Check myself
      if (String(this.fromUser.id) === String(this.toUser.id)) {
        this.close();
      } else if (this.client) {
        this._renderer2.setStyle(this.containerElem.nativeElement, 'display', '');
        if (fromChanged) {
          this.client.destroy();
          this.init();
        } else if (toChanged) {
          this.client.addUser(String(this.toUser.id));
          this.getMessages();
        }
      }
    } else {
      this.close();
      if (this.client) {
        this.client.destroy();
      }
    }
  }

  constructor(private _renderer2: Renderer2,
              private elementRef: ElementRef,
              private _messenger: MessengerService,
              private _medias: MediasRepository) {
  }

  private scrollToBottom(): void {
    setTimeout(() => {
      if (this.bodyElem.nativeElement.scrollHeight) {
        this.bodyElem.nativeElement.scrollTop = this.bodyElem.nativeElement.scrollHeight;
      }
    }, 200);
  }

  private preSendMessage(): void {
    this.onPreSend.emit();
  }

  private resize(): void {
    // window['huy'] = this.elementRef.nativeElement;
    /*setTimeout(() => {
      // Container
      const top = this.elementRef.nativeElement.offsetTop; // ? this.elementRef.nativeElement.offsetTop : 150;
      if (top) {
        this._renderer2.setStyle(this.containerElem.nativeElement, 'height', `calc(100vh - ${top}px)`);
      }

      // Body
      const height = this.footerElem.nativeElement.offsetHeight; // ? this.footerElem.nativeElement.offsetHeight : 55;
      if (height) {
        this._renderer2.setStyle(this.bodyElem.nativeElement, 'height', `calc(100% - ${height}px)`);
      }
    }, 500);*/
  }

  private getMessages() {
    this.messageList = this.client.getLogs(String(this.toUser.id));
    return this.scrollToBottom();
  }

  private appendLogs(data: any) {
    if ((data.type === XMPP_MESSAGE_TYPES.NORMAL && !data.chatState) || (data.type === XMPP_MESSAGE_TYPES.CHAT && data.chatState === XMPP_MESSAGE_CHATSTATES.ACTIVE)) {
      const mine: boolean = this.client.appendLogs(String(this.toUser.id), data);
      if (mine) {
        const lastLog = this.client.getLastLog(String(this.toUser.id));
        this.onSent.emit(lastLog.content);
        return this.getMessages();
      }
    }
  }

  private fetchLogs(data: any) {
    if (typeof data.mamItem === 'object') {
      const mine: boolean = this.client.appendLogs(String(this.toUser.id), data.mamItem.forwarded.message);
      if (mine) {
        return this.getMessages();
      }
    }
    if (typeof data.mamResult === 'object') {
      return this.scrollToBottom();
    }
  }

  private init(): void {
    if (!this.fromUser || !this.toUser || String(this.fromUser.id) === String(this.toUser.id)) {
      this._renderer2.setStyle(this.containerElem.nativeElement, 'display', 'none');
      return;
    }
    this.client = this._messenger.login(this.fromUser.id).addListener((name: string, data: any) => {
      if (name === XMPP_EVENTS.CONNECTED) {
        this.connected = true;
        return;
      } else if (name === XMPP_EVENTS.AUTH_FAILED) {
        this.connected = false;
        return;
      } else if (name === XMPP_EVENTS.SESSION_STARTED) { // Add Friend
        this.client.addUser(String(this.toUser.id));
      } else if (name === XMPP_EVENTS.IQ) {
        if (data.lastActivity && data.from.local !== String(this.fromUser.id)) { // Is online
          this.client.setOnline(data);
        }
      } else if (name === XMPP_EVENTS.PRESENCE) {
        if (data.from.local !== String(this.fromUser.id)) { // Change online status
          this.client.changePresence(data);
        }
        // this.client.updateAvailableByPresence(String(this.toUser.id), data);
      } else if (name === XMPP_EVENTS.CHAT) {

        return this.scrollToBottom();
      } else if (name === XMPP_EVENTS.CHAT_STATE) {
        if (data.chatState === XMPP_MESSAGE_CHATSTATES.COMPOSING) {
          this.typing = this.client.hasTyping(data);
        } else {
          this.typing = false;
        }
        return;
      } else if (name === XMPP_EVENTS.MESSAGE_ERROR) { // Error message
      } else if (name === XMPP_EVENTS.MESSAGE) { // Received message
        // Fetch histories
        if (typeof data.mamItem === 'object' || typeof data.mamResult === 'object') {
          return this.fetchLogs(data);
        }
        // Received message
        if (data.body) {
          this.appendLogs(data);
        }
      }
    });
  }

  ngOnInit(): void {
    this.init();
  }

  ngAfterViewInit(): void {
    this._renderer2.listen(this.elementRef.nativeElement, 'resize', () => {
      this.resize();
    });
    this._renderer2.listen(this.footerElem.nativeElement, 'resize', () => {
      this.resize();
    });
    this.resize();
  }

  ngOnDestroy(): void {
    if (this.client) {
      if (this.typingTimer) {
        clearTimeout(this.typingTimer);
        this.client.sendTyping(String(this.toUser.id), false);
        this.typingTimer = null;
      }
      this.client.destroy();
    }
  }

  close(): void {
    if (this.client && this.toUser) {
      this.client.removeUser(String(this.toUser.id));
    }
    this._renderer2.setStyle(this.containerElem.nativeElement, 'display', 'none');
    // this.elementRef.nativeElement.remove();
  }

  formatText(content: string): string {
    return content.replace(/(?:\r\n|\r|\n)/g, '<br>');
  }

  onKeyUp(e): void {
    // console.log(String(this.toUser.id));
    if (e.keyCode === 13) {
      if (e.ctrlKey || e.shiftKey) {
        if (e.ctrlKey) {
          this.message += `\n`;
          setTimeout(() => {
            this.inputElement.nativeElement.scrollTop = this.inputElement.nativeElement.scrollHeight;
            this.inputElement.nativeElement.focus();
          });
        }
        return;
      } else if (!this.disabled) {
        return this.sendMessage();
      }
      return;
    }

    this.message = _.trimStart(this.message);

    if (!this.message.replace(/ /gi, '')) {
      this.disabled = true;
    } else {
      this.disabled = false;
    }
  }

  onKeyPress(e: any): boolean {
    const charCode = (e.which) ? e.which : e.keyCode;

    return true;
  }

  onInput(e: any): void {
    if (!this.typingTimer) {
      this.client.sendTyping(String(this.toUser.id), true);
      this.typingTimer = setTimeout(() => {
        this.client.sendTyping(String(this.toUser.id), false);
        this.typingTimer = null;
      }, 1000);
    } else {
      clearTimeout(this.typingTimer);
      this.typingTimer = setTimeout(() => {
        this.client.sendTyping(String(this.toUser.id), false);
        this.typingTimer = null;
      }, 1000);
    }
  }

  sendMessage(): void {
    const message = this.message;
    const type = XMPP_MESSAGE_BODY_TYPES.TEXT;
    if (!message.replace(/ /gi, '')) {
      console.log('Empty');
    } else {
      this.disabled = true;
      this.preSendMessage();
      setTimeout(() => this.client.sendMessage(String(this.toUser.id), message, type), 100);
    }

    this.message = '';
  }

  // Upload files
  selector(): void {
    this.files = [];
    this.fileUpload.nativeElement.value = '';
    this.fileUpload.nativeElement.click();
  }

  upload($event): void {
    const files = this.fileUpload.nativeElement.files;
    if (files.length > 3) {
      alert('You can not send more than 3 pictures!');
    }
    if (files.length) {
      this._medias.chatUpload(files).then((res: any[]) => {
        const images: string[] = [];
        _.forEach(res, (item: any) => {
          const image: XMPPImageInterface = {
            id: UUID.UUID(),
            file_id: item.id,
            thumb_url: item.thumb_url,
            large_url: item.large_url,
            raw_url: item.raw_url,
            filename: item.filename,
          };
          images.push(JSON.stringify(image));
        });
        setTimeout(() => this.client.sendMessage(String(this.toUser.id), images.join(';'), XMPP_MESSAGE_BODY_TYPES.IMAGE), 100);
      });
    }
  }

  @HostListener('dragenter', ['$event'])
  onDragenter($event) {
    $event.stopPropagation();
    $event.preventDefault();
  }

  @HostListener('dragleave', ['$event'])
  onDragleave($event) {
    $event.stopPropagation();
    $event.preventDefault();
  }

  @HostListener('dragover', ['$event'])
  onDragover($event) {
    $event.stopPropagation();
    $event.preventDefault();
  }

  @HostListener('drop', ['$event'])
  onDrop($event) {
    $event.stopPropagation();
    $event.preventDefault();

    const transfers = $event.dataTransfer.files;
    if (transfers.length > 0) {
      // const arrayFiles = [];
      // for(let i = 0; i < transfers.length; i++) {
      //   const file = transfers[i];
      //   if(checkFileSize(file, $scope.maxSize) && checkFileType(file, $scope.fileTypes)) {
      //     arrayFiles.push(transfers[i]);
      //   }
      // }
      // Upload code
      // upload(arrayFiles);
    }
  }

  @HostListener('window:resize', ['$event'])
  onWindowResize($event) {
    this.resize();
  }

  @HostListener('resize', ['$event'])
  onResize($event) {
    this.resize();
  }
}
