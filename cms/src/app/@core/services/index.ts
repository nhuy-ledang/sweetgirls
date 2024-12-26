import { DocumentRef, WindowRef } from './browser-globals';
import { Api } from './api';
import { Dialog } from './dialog';
import { Dom } from './dom';
import { Http } from './http';
import { Restore } from './restore';
import { Script } from './script';
import { Spinner } from './spinner';
import { StorageService } from './storage-service';
import { CookieVar } from './cookie-var';
import { Utils } from './utils';

export {
  DocumentRef, WindowRef,
  Api,
  Dialog, Dom,
  Http,
  Restore,
  Script,
  Spinner,
  StorageService, CookieVar,
  Utils,
};

export const CORE_SERVICES = [
  DocumentRef, WindowRef,
  Api,
  Dialog, Dom,
  Http,
  Restore,
  Script,
  Spinner,
  StorageService, CookieVar,
  Utils,
];
