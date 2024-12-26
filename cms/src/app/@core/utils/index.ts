import { AnalyticsService } from './analytics.service';
import { GlobalState } from './global.state';
import { SeoService } from './seo.service';
import { SidebarService } from './sidebar.service';

export {
  GlobalState,
  SidebarService,
};

export const CORE_UTILS = [
  GlobalState,
  SidebarService,
];
