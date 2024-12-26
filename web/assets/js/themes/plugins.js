/**
 * Countdown
 * 
 * downCount
 * 
 * Sonny T. <hi@sonnyt.com>, sonnyt.com
 */
(function(e){e.fn.downCount=function(t,n){function o(){var e=new Date(r.date),t=s();var o=e-t;if(o<0){clearInterval(u);if(n&&typeof n==="function")n();return}var a=1e3,f=a*60,l=f*60,c=l*24;var h=Math.floor(o/c),p=Math.floor(o%c/l),d=Math.floor(o%l/f),v=Math.floor(o%f/a);h=String(h).length>=2?h:"0"+h;p=String(p).length>=2?p:"0"+p;d=String(d).length>=2?d:"0"+d;v=String(v).length>=2?v:"0"+v;var m=h===1?"day":"days",g=p===1?"hour":"hours",y=d===1?"minute":"minutes",b=v===1?"second":"seconds";i.find(".days").text(h);i.find(".hours").text(p);i.find(".minutes").text(d);i.find(".seconds").text(v);i.find(".days_ref").text(m);i.find(".hours_ref").text(g);i.find(".minutes_ref").text(y);i.find(".seconds_ref").text(b)}var r=e.extend({date:null,offset:null},t);if(!r.date){e.error("Date is not defined.")}if(!Date.parse(r.date)){e.error("Incorrect date format, it should look like this, 12/24/2012 12:00:00.")}var i=this;var s=function(){var e=new Date;var t=e.getTime()+e.getTimezoneOffset()*6e4;var n=new Date(t+36e5*r.offset);return n};var u=setInterval(o,1e3)}})(jQuery);

/**
 * imagesLoaded
 * required for: Isotope
 * 
 * 4.1.0 | http://imagesloaded.desandro.com/
 */
(function(t,e){"function"==typeof define&&define.amd?define("ev-emitter/ev-emitter",e):"object"==typeof module&&module.exports?module.exports=e():t.EvEmitter=e()}(this,function(){function t(){}var e=t.prototype;return e.on=function(t,e){if(t&&e){var i=this._events=this._events||{},n=i[t]=i[t]||[];return-1==n.indexOf(e)&&n.push(e),this}},e.once=function(t,e){if(t&&e){this.on(t,e);var i=this._onceEvents=this._onceEvents||{},n=i[t]=i[t]||[];return n[e]=!0,this}},e.off=function(t,e){var i=this._events&&this._events[t];if(i&&i.length){var n=i.indexOf(e);return-1!=n&&i.splice(n,1),this}},e.emitEvent=function(t,e){var i=this._events&&this._events[t];if(i&&i.length){var n=0,o=i[n];e=e||[];for(var r=this._onceEvents&&this._onceEvents[t];o;){var s=r&&r[o];s&&(this.off(t,o),delete r[o]),o.apply(this,e),n+=s?0:1,o=i[n]}return this}},t}),function(t,e){"use strict";"function"==typeof define&&define.amd?define(["ev-emitter/ev-emitter"],function(i){return e(t,i)}):"object"==typeof module&&module.exports?module.exports=e(t,require("ev-emitter")):t.imagesLoaded=e(t,t.EvEmitter)}(window,function(t,e){function i(t,e){for(var i in e)t[i]=e[i];return t}function n(t){var e=[];if(Array.isArray(t))e=t;else if("number"==typeof t.length)for(var i=0;i<t.length;i++)e.push(t[i]);else e.push(t);return e}function o(t,e,r){return this instanceof o?("string"==typeof t&&(t=document.querySelectorAll(t)),this.elements=n(t),this.options=i({},this.options),"function"==typeof e?r=e:i(this.options,e),r&&this.on("always",r),this.getImages(),h&&(this.jqDeferred=new h.Deferred),void setTimeout(function(){this.check()}.bind(this))):new o(t,e,r)}function r(t){this.img=t}function s(t,e){this.url=t,this.element=e,this.img=new Image}var h=t.jQuery,a=t.console;o.prototype=Object.create(e.prototype),o.prototype.options={},o.prototype.getImages=function(){this.images=[],this.elements.forEach(this.addElementImages,this)},o.prototype.addElementImages=function(t){"IMG"==t.nodeName&&this.addImage(t),this.options.background===!0&&this.addElementBackgroundImages(t);var e=t.nodeType;if(e&&d[e]){for(var i=t.querySelectorAll("img"),n=0;n<i.length;n++){var o=i[n];this.addImage(o)}if("string"==typeof this.options.background){var r=t.querySelectorAll(this.options.background);for(n=0;n<r.length;n++){var s=r[n];this.addElementBackgroundImages(s)}}}};var d={1:!0,9:!0,11:!0};return o.prototype.addElementBackgroundImages=function(t){var e=getComputedStyle(t);if(e)for(var i=/url\((['"])?(.*?)\1\)/gi,n=i.exec(e.backgroundImage);null!==n;){var o=n&&n[2];o&&this.addBackground(o,t),n=i.exec(e.backgroundImage)}},o.prototype.addImage=function(t){var e=new r(t);this.images.push(e)},o.prototype.addBackground=function(t,e){var i=new s(t,e);this.images.push(i)},o.prototype.check=function(){function t(t,i,n){setTimeout(function(){e.progress(t,i,n)})}var e=this;return this.progressedCount=0,this.hasAnyBroken=!1,this.images.length?void this.images.forEach(function(e){e.once("progress",t),e.check()}):void this.complete()},o.prototype.progress=function(t,e,i){this.progressedCount++,this.hasAnyBroken=this.hasAnyBroken||!t.isLoaded,this.emitEvent("progress",[this,t,e]),this.jqDeferred&&this.jqDeferred.notify&&this.jqDeferred.notify(this,t),this.progressedCount==this.images.length&&this.complete(),this.options.debug&&a&&a.log("progress: "+i,t,e)},o.prototype.complete=function(){var t=this.hasAnyBroken?"fail":"done";if(this.isComplete=!0,this.emitEvent(t,[this]),this.emitEvent("always",[this]),this.jqDeferred){var e=this.hasAnyBroken?"reject":"resolve";this.jqDeferred[e](this)}},r.prototype=Object.create(e.prototype),r.prototype.check=function(){var t=this.getIsImageComplete();return t?void this.confirm(0!==this.img.naturalWidth,"naturalWidth"):(this.proxyImage=new Image,this.proxyImage.addEventListener("load",this),this.proxyImage.addEventListener("error",this),this.img.addEventListener("load",this),this.img.addEventListener("error",this),void(this.proxyImage.src=this.img.src))},r.prototype.getIsImageComplete=function(){return this.img.complete&&void 0!==this.img.naturalWidth},r.prototype.confirm=function(t,e){this.isLoaded=t,this.emitEvent("progress",[this,this.img,e])},r.prototype.handleEvent=function(t){var e="on"+t.type;this[e]&&this[e](t)},r.prototype.onload=function(){this.confirm(!0,"onload"),this.unbindEvents()},r.prototype.onerror=function(){this.confirm(!1,"onerror"),this.unbindEvents()},r.prototype.unbindEvents=function(){this.proxyImage.removeEventListener("load",this),this.proxyImage.removeEventListener("error",this),this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype=Object.create(r.prototype),s.prototype.check=function(){this.img.addEventListener("load",this),this.img.addEventListener("error",this),this.img.src=this.url;var t=this.getIsImageComplete();t&&(this.confirm(0!==this.img.naturalWidth,"naturalWidth"),this.unbindEvents())},s.prototype.unbindEvents=function(){this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype.confirm=function(t,e){this.isLoaded=t,this.emitEvent("progress",[this,this.element,e])},o.makeJQueryPlugin=function(e){e=e||t.jQuery,e&&(h=e,h.fn.imagesLoaded=function(t,e){var i=new o(this,t,e);return i.jqDeferred.promise(h(this))})},o.makeJQueryPlugin(),o}));

/**
 * Magnific Popup
 * 
 * 1.1.0 | Dmitry Semenov | MIT | http://dimsemenov.com/plugins/magnific-popup/
 */
(function(a){typeof define=="function"&&define.amd?define(["jquery"],a):typeof exports=="object"?a(require("jquery")):a(window.jQuery||window.Zepto)})(function(a){var b="Close",c="BeforeClose",d="AfterClose",e="BeforeAppend",f="MarkupParse",g="Open",h="Change",i="mfp",j="."+i,k="mfp-ready",l="mfp-removing",m="mfp-prevent-close",n,o=function(){},p=!!window.jQuery,q,r=a(window),s,t,u,v,w=function(a,b){n.ev.on(i+a+j,b)},x=function(b,c,d,e){var f=document.createElement("div");return f.className="mfp-"+b,d&&(f.innerHTML=d),e?c&&c.appendChild(f):(f=a(f),c&&f.appendTo(c)),f},y=function(b,c){n.ev.triggerHandler(i+b,c),n.st.callbacks&&(b=b.charAt(0).toLowerCase()+b.slice(1),n.st.callbacks[b]&&n.st.callbacks[b].apply(n,a.isArray(c)?c:[c]))},z=function(b){if(b!==v||!n.currTemplate.closeBtn)n.currTemplate.closeBtn=a(n.st.closeMarkup.replace("%title%",n.st.tClose)),v=b;return n.currTemplate.closeBtn},A=function(){a.magnificPopup.instance||(n=new o,n.init(),a.magnificPopup.instance=n)},B=function(){var a=document.createElement("p").style,b=["ms","O","Moz","Webkit"];if(a.transition!==undefined)return!0;while(b.length)if(b.pop()+"Transition"in a)return!0;return!1};o.prototype={constructor:o,init:function(){var b=navigator.appVersion;n.isLowIE=n.isIE8=document.all&&!document.addEventListener,n.isAndroid=/android/gi.test(b),n.isIOS=/iphone|ipad|ipod/gi.test(b),n.supportsTransition=B(),n.probablyMobile=n.isAndroid||n.isIOS||/(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent),s=a(document),n.popupsCache={}},open:function(b){var c;if(b.isObj===!1){n.items=b.items.toArray(),n.index=0;var d=b.items,e;for(c=0;c<d.length;c++){e=d[c],e.parsed&&(e=e.el[0]);if(e===b.el[0]){n.index=c;break}}}else n.items=a.isArray(b.items)?b.items:[b.items],n.index=b.index||0;if(n.isOpen){n.updateItemHTML();return}n.types=[],u="",b.mainEl&&b.mainEl.length?n.ev=b.mainEl.eq(0):n.ev=s,b.key?(n.popupsCache[b.key]||(n.popupsCache[b.key]={}),n.currTemplate=n.popupsCache[b.key]):n.currTemplate={},n.st=a.extend(!0,{},a.magnificPopup.defaults,b),n.fixedContentPos=n.st.fixedContentPos==="auto"?!n.probablyMobile:n.st.fixedContentPos,n.st.modal&&(n.st.closeOnContentClick=!1,n.st.closeOnBgClick=!1,n.st.showCloseBtn=!1,n.st.enableEscapeKey=!1),n.bgOverlay||(n.bgOverlay=x("bg").on("click"+j,function(){n.close()}),n.wrap=x("wrap").attr("tabindex",-1).on("click"+j,function(a){n._checkIfClose(a.target)&&n.close()}),n.container=x("container",n.wrap)),n.contentContainer=x("content"),n.st.preloader&&(n.preloader=x("preloader",n.container,n.st.tLoading));var h=a.magnificPopup.modules;for(c=0;c<h.length;c++){var i=h[c];i=i.charAt(0).toUpperCase()+i.slice(1),n["init"+i].call(n)}y("BeforeOpen"),n.st.showCloseBtn&&(n.st.closeBtnInside?(w(f,function(a,b,c,d){c.close_replaceWith=z(d.type)}),u+=" mfp-close-btn-in"):n.wrap.append(z())),n.st.alignTop&&(u+=" mfp-align-top"),n.fixedContentPos?n.wrap.css({overflow:n.st.overflowY,overflowX:"hidden",overflowY:n.st.overflowY}):n.wrap.css({top:r.scrollTop(),position:"absolute"}),(n.st.fixedBgPos===!1||n.st.fixedBgPos==="auto"&&!n.fixedContentPos)&&n.bgOverlay.css({height:s.height(),position:"absolute"}),n.st.enableEscapeKey&&s.on("keyup"+j,function(a){a.keyCode===27&&n.close()}),r.on("resize"+j,function(){n.updateSize()}),n.st.closeOnContentClick||(u+=" mfp-auto-cursor"),u&&n.wrap.addClass(u);var l=n.wH=r.height(),m={};if(n.fixedContentPos&&n._hasScrollBar(l)){var o=n._getScrollbarSize();o&&(m.marginRight=o)}n.fixedContentPos&&(n.isIE7?a("body, html").css("overflow","hidden"):m.overflow="hidden");var p=n.st.mainClass;return n.isIE7&&(p+=" mfp-ie7"),p&&n._addClassToMFP(p),n.updateItemHTML(),y("BuildControls"),a("html").css(m),n.bgOverlay.add(n.wrap).prependTo(n.st.prependTo||a(document.body)),n._lastFocusedEl=document.activeElement,setTimeout(function(){n.content?(n._addClassToMFP(k),n._setFocus()):n.bgOverlay.addClass(k),s.on("focusin"+j,n._onFocusIn)},16),n.isOpen=!0,n.updateSize(l),y(g),b},close:function(){if(!n.isOpen)return;y(c),n.isOpen=!1,n.st.removalDelay&&!n.isLowIE&&n.supportsTransition?(n._addClassToMFP(l),setTimeout(function(){n._close()},n.st.removalDelay)):n._close()},_close:function(){y(b);var c=l+" "+k+" ";n.bgOverlay.detach(),n.wrap.detach(),n.container.empty(),n.st.mainClass&&(c+=n.st.mainClass+" "),n._removeClassFromMFP(c);if(n.fixedContentPos){var e={marginRight:""};n.isIE7?a("body, html").css("overflow",""):e.overflow="",a("html").css(e)}s.off("keyup"+j+" focusin"+j),n.ev.off(j),n.wrap.attr("class","mfp-wrap").removeAttr("style"),n.bgOverlay.attr("class","mfp-bg"),n.container.attr("class","mfp-container"),n.st.showCloseBtn&&(!n.st.closeBtnInside||n.currTemplate[n.currItem.type]===!0)&&n.currTemplate.closeBtn&&n.currTemplate.closeBtn.detach(),n.st.autoFocusLast&&n._lastFocusedEl&&a(n._lastFocusedEl).focus(),n.currItem=null,n.content=null,n.currTemplate=null,n.prevHeight=0,y(d)},updateSize:function(a){if(n.isIOS){var b=document.documentElement.clientWidth/window.innerWidth,c=window.innerHeight*b;n.wrap.css("height",c),n.wH=c}else n.wH=a||r.height();n.fixedContentPos||n.wrap.css("height",n.wH),y("Resize")},updateItemHTML:function(){var b=n.items[n.index];n.contentContainer.detach(),n.content&&n.content.detach(),b.parsed||(b=n.parseEl(n.index));var c=b.type;y("BeforeChange",[n.currItem?n.currItem.type:"",c]),n.currItem=b;if(!n.currTemplate[c]){var d=n.st[c]?n.st[c].markup:!1;y("FirstMarkupParse",d),d?n.currTemplate[c]=a(d):n.currTemplate[c]=!0}t&&t!==b.type&&n.container.removeClass("mfp-"+t+"-holder");var e=n["get"+c.charAt(0).toUpperCase()+c.slice(1)](b,n.currTemplate[c]);n.appendContent(e,c),b.preloaded=!0,y(h,b),t=b.type,n.container.prepend(n.contentContainer),y("AfterChange")},appendContent:function(a,b){n.content=a,a?n.st.showCloseBtn&&n.st.closeBtnInside&&n.currTemplate[b]===!0?n.content.find(".mfp-close").length||n.content.append(z()):n.content=a:n.content="",y(e),n.container.addClass("mfp-"+b+"-holder"),n.contentContainer.append(n.content)},parseEl:function(b){var c=n.items[b],d;c.tagName?c={el:a(c)}:(d=c.type,c={data:c,src:c.src});if(c.el){var e=n.types;for(var f=0;f<e.length;f++)if(c.el.hasClass("mfp-"+e[f])){d=e[f];break}c.src=c.el.attr("data-mfp-src"),c.src||(c.src=c.el.attr("href"))}return c.type=d||n.st.type||"inline",c.index=b,c.parsed=!0,n.items[b]=c,y("ElementParse",c),n.items[b]},addGroup:function(a,b){var c=function(c){c.mfpEl=this,n._openClick(c,a,b)};b||(b={});var d="click.magnificPopup";b.mainEl=a,b.items?(b.isObj=!0,a.off(d).on(d,c)):(b.isObj=!1,b.delegate?a.off(d).on(d,b.delegate,c):(b.items=a,a.off(d).on(d,c)))},_openClick:function(b,c,d){var e=d.midClick!==undefined?d.midClick:a.magnificPopup.defaults.midClick;if(!e&&(b.which===2||b.ctrlKey||b.metaKey||b.altKey||b.shiftKey))return;var f=d.disableOn!==undefined?d.disableOn:a.magnificPopup.defaults.disableOn;if(f)if(a.isFunction(f)){if(!f.call(n))return!0}else if(r.width()<f)return!0;b.type&&(b.preventDefault(),n.isOpen&&b.stopPropagation()),d.el=a(b.mfpEl),d.delegate&&(d.items=c.find(d.delegate)),n.open(d)},updateStatus:function(a,b){if(n.preloader){q!==a&&n.container.removeClass("mfp-s-"+q),!b&&a==="loading"&&(b=n.st.tLoading);var c={status:a,text:b};y("UpdateStatus",c),a=c.status,b=c.text,n.preloader.html(b),n.preloader.find("a").on("click",function(a){a.stopImmediatePropagation()}),n.container.addClass("mfp-s-"+a),q=a}},_checkIfClose:function(b){if(a(b).hasClass(m))return;var c=n.st.closeOnContentClick,d=n.st.closeOnBgClick;if(c&&d)return!0;if(!n.content||a(b).hasClass("mfp-close")||n.preloader&&b===n.preloader[0])return!0;if(b!==n.content[0]&&!a.contains(n.content[0],b)){if(d&&a.contains(document,b))return!0}else if(c)return!0;return!1},_addClassToMFP:function(a){n.bgOverlay.addClass(a),n.wrap.addClass(a)},_removeClassFromMFP:function(a){this.bgOverlay.removeClass(a),n.wrap.removeClass(a)},_hasScrollBar:function(a){return(n.isIE7?s.height():document.body.scrollHeight)>(a||r.height())},_setFocus:function(){(n.st.focus?n.content.find(n.st.focus).eq(0):n.wrap).focus()},_onFocusIn:function(b){if(b.target!==n.wrap[0]&&!a.contains(n.wrap[0],b.target))return n._setFocus(),!1},_parseMarkup:function(b,c,d){var e;d.data&&(c=a.extend(d.data,c)),y(f,[b,c,d]),a.each(c,function(c,d){if(d===undefined||d===!1)return!0;e=c.split("_");if(e.length>1){var f=b.find(j+"-"+e[0]);if(f.length>0){var g=e[1];g==="replaceWith"?f[0]!==d[0]&&f.replaceWith(d):g==="img"?f.is("img")?f.attr("src",d):f.replaceWith(a("<img>").attr("src",d).attr("class",f.attr("class"))):f.attr(e[1],d)}}else b.find(j+"-"+c).html(d)})},_getScrollbarSize:function(){if(n.scrollbarSize===undefined){var a=document.createElement("div");a.style.cssText="width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;",document.body.appendChild(a),n.scrollbarSize=a.offsetWidth-a.clientWidth,document.body.removeChild(a)}return n.scrollbarSize}},a.magnificPopup={instance:null,proto:o.prototype,modules:[],open:function(b,c){return A(),b?b=a.extend(!0,{},b):b={},b.isObj=!0,b.index=c||0,this.instance.open(b)},close:function(){return a.magnificPopup.instance&&a.magnificPopup.instance.close()},registerModule:function(b,c){c.options&&(a.magnificPopup.defaults[b]=c.options),a.extend(this.proto,c.proto),this.modules.push(b)},defaults:{disableOn:0,key:null,midClick:!1,mainClass:"",preloader:!0,focus:"",closeOnContentClick:!1,closeOnBgClick:!0,closeBtnInside:!0,showCloseBtn:!0,enableEscapeKey:!0,modal:!1,alignTop:!1,removalDelay:0,prependTo:null,fixedContentPos:"auto",fixedBgPos:"auto",overflowY:"auto",closeMarkup:'<button title="%title%" type="button" class="mfp-close">&#215;</button>',tClose:"Close (Esc)",tLoading:"Loading...",autoFocusLast:!0}},a.fn.magnificPopup=function(b){A();var c=a(this);if(typeof b=="string")if(b==="open"){var d,e=p?c.data("magnificPopup"):c[0].magnificPopup,f=parseInt(arguments[1],10)||0;e.items?d=e.items[f]:(d=c,e.delegate&&(d=d.find(e.delegate)),d=d.eq(f)),n._openClick({mfpEl:d},c,e)}else n.isOpen&&n[b].apply(n,Array.prototype.slice.call(arguments,1));else b=a.extend(!0,{},b),p?c.data("magnificPopup",b):c[0].magnificPopup=b,n.addGroup(c,b);return c};var C="inline",D,E,F,G=function(){F&&(E.after(F.addClass(D)).detach(),F=null)};a.magnificPopup.registerModule(C,{options:{hiddenClass:"hide",markup:"",tNotFound:"Content not found"},proto:{initInline:function(){n.types.push(C),w(b+"."+C,function(){G()})},getInline:function(b,c){G();if(b.src){var d=n.st.inline,e=a(b.src);if(e.length){var f=e[0].parentNode;f&&f.tagName&&(E||(D=d.hiddenClass,E=x(D),D="mfp-"+D),F=e.after(E).detach().removeClass(D)),n.updateStatus("ready")}else n.updateStatus("error",d.tNotFound),e=a("<div>");return b.inlineElement=e,e}return n.updateStatus("ready"),n._parseMarkup(c,{},b),c}}});var H="ajax",I,J=function(){I&&a(document.body).removeClass(I)},K=function(){J(),n.req&&n.req.abort()};a.magnificPopup.registerModule(H,{options:{settings:null,cursor:"mfp-ajax-cur",tError:'<a href="%url%">The content</a> could not be loaded.'},proto:{initAjax:function(){n.types.push(H),I=n.st.ajax.cursor,w(b+"."+H,K),w("BeforeChange."+H,K)},getAjax:function(b){I&&a(document.body).addClass(I),n.updateStatus("loading");var c=a.extend({url:b.src,success:function(c,d,e){var f={data:c,xhr:e};y("ParseAjax",f),n.appendContent(a(f.data),H),b.finished=!0,J(),n._setFocus(),setTimeout(function(){n.wrap.addClass(k)},16),n.updateStatus("ready"),y("AjaxContentAdded")},error:function(){J(),b.finished=b.loadError=!0,n.updateStatus("error",n.st.ajax.tError.replace("%url%",b.src))}},n.st.ajax.settings);return n.req=a.ajax(c),""}}});var L,M=function(b){if(b.data&&b.data.title!==undefined)return b.data.title;var c=n.st.image.titleSrc;if(c){if(a.isFunction(c))return c.call(n,b);if(b.el)return b.el.attr(c)||""}return""};a.magnificPopup.registerModule("image",{options:{markup:'<div class="mfp-figure"><div class="mfp-close"></div><figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar"><div class="mfp-title"></div><div class="mfp-counter"></div></div></figcaption></figure></div>',cursor:"mfp-zoom-out-cur",titleSrc:"title",verticalFit:!0,tError:'<a href="%url%">The image</a> could not be loaded.'},proto:{initImage:function(){var c=n.st.image,d=".image";n.types.push("image"),w(g+d,function(){n.currItem.type==="image"&&c.cursor&&a(document.body).addClass(c.cursor)}),w(b+d,function(){c.cursor&&a(document.body).removeClass(c.cursor),r.off("resize"+j)}),w("Resize"+d,n.resizeImage),n.isLowIE&&w("AfterChange",n.resizeImage)},resizeImage:function(){var a=n.currItem;if(!a||!a.img)return;if(n.st.image.verticalFit){var b=0;n.isLowIE&&(b=parseInt(a.img.css("padding-top"),10)+parseInt(a.img.css("padding-bottom"),10)),a.img.css("max-height",n.wH-b)}},_onImageHasSize:function(a){a.img&&(a.hasSize=!0,L&&clearInterval(L),a.isCheckingImgSize=!1,y("ImageHasSize",a),a.imgHidden&&(n.content&&n.content.removeClass("mfp-loading"),a.imgHidden=!1))},findImageSize:function(a){var b=0,c=a.img[0],d=function(e){L&&clearInterval(L),L=setInterval(function(){if(c.naturalWidth>0){n._onImageHasSize(a);return}b>200&&clearInterval(L),b++,b===3?d(10):b===40?d(50):b===100&&d(500)},e)};d(1)},getImage:function(b,c){var d=0,e=function(){b&&(b.img[0].complete?(b.img.off(".mfploader"),b===n.currItem&&(n._onImageHasSize(b),n.updateStatus("ready")),b.hasSize=!0,b.loaded=!0,y("ImageLoadComplete")):(d++,d<200?setTimeout(e,100):f()))},f=function(){b&&(b.img.off(".mfploader"),b===n.currItem&&(n._onImageHasSize(b),n.updateStatus("error",g.tError.replace("%url%",b.src))),b.hasSize=!0,b.loaded=!0,b.loadError=!0)},g=n.st.image,h=c.find(".mfp-img");if(h.length){var i=document.createElement("img");i.className="mfp-img",b.el&&b.el.find("img").length&&(i.alt=b.el.find("img").attr("alt")),b.img=a(i).on("load.mfploader",e).on("error.mfploader",f),i.src=b.src,h.is("img")&&(b.img=b.img.clone()),i=b.img[0],i.naturalWidth>0?b.hasSize=!0:i.width||(b.hasSize=!1)}return n._parseMarkup(c,{title:M(b),img_replaceWith:b.img},b),n.resizeImage(),b.hasSize?(L&&clearInterval(L),b.loadError?(c.addClass("mfp-loading"),n.updateStatus("error",g.tError.replace("%url%",b.src))):(c.removeClass("mfp-loading"),n.updateStatus("ready")),c):(n.updateStatus("loading"),b.loading=!0,b.hasSize||(b.imgHidden=!0,c.addClass("mfp-loading"),n.findImageSize(b)),c)}}});var N,O=function(){return N===undefined&&(N=document.createElement("p").style.MozTransform!==undefined),N};a.magnificPopup.registerModule("zoom",{options:{enabled:!1,easing:"ease-in-out",duration:300,opener:function(a){return a.is("img")?a:a.find("img")}},proto:{initZoom:function(){var a=n.st.zoom,d=".zoom",e;if(!a.enabled||!n.supportsTransition)return;var f=a.duration,g=function(b){var c=b.clone().removeAttr("style").removeAttr("class").addClass("mfp-animated-image"),d="all "+a.duration/1e3+"s "+a.easing,e={position:"fixed",zIndex:9999,left:0,top:0,"-webkit-backface-visibility":"hidden"},f="transition";return e["-webkit-"+f]=e["-moz-"+f]=e["-o-"+f]=e[f]=d,c.css(e),c},h=function(){n.content.css("visibility","visible")},i,j;w("BuildControls"+d,function(){if(n._allowZoom()){clearTimeout(i),n.content.css("visibility","hidden"),e=n._getItemToZoom();if(!e){h();return}j=g(e),j.css(n._getOffset()),n.wrap.append(j),i=setTimeout(function(){j.css(n._getOffset(!0)),i=setTimeout(function(){h(),setTimeout(function(){j.remove(),e=j=null,y("ZoomAnimationEnded")},16)},f)},16)}}),w(c+d,function(){if(n._allowZoom()){clearTimeout(i),n.st.removalDelay=f;if(!e){e=n._getItemToZoom();if(!e)return;j=g(e)}j.css(n._getOffset(!0)),n.wrap.append(j),n.content.css("visibility","hidden"),setTimeout(function(){j.css(n._getOffset())},16)}}),w(b+d,function(){n._allowZoom()&&(h(),j&&j.remove(),e=null)})},_allowZoom:function(){return n.currItem.type==="image"},_getItemToZoom:function(){return n.currItem.hasSize?n.currItem.img:!1},_getOffset:function(b){var c;b?c=n.currItem.img:c=n.st.zoom.opener(n.currItem.el||n.currItem);var d=c.offset(),e=parseInt(c.css("padding-top"),10),f=parseInt(c.css("padding-bottom"),10);d.top-=a(window).scrollTop()-e;var g={width:c.width(),height:(p?c.innerHeight():c[0].offsetHeight)-f-e};return O()?g["-moz-transform"]=g.transform="translate("+d.left+"px,"+d.top+"px)":(g.left=d.left,g.top=d.top),g}}});var P="iframe",Q="//about:blank",R=function(a){if(n.currTemplate[P]){var b=n.currTemplate[P].find("iframe");b.length&&(a||(b[0].src=Q),n.isIE8&&b.css("display",a?"block":"none"))}};a.magnificPopup.registerModule(P,{options:{markup:'<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe></div>',srcAction:"iframe_src",patterns:{youtube:{index:"youtube.com",id:"v=",src:"//www.youtube.com/embed/%id%?autoplay=1"},vimeo:{index:"vimeo.com/",id:"/",src:"//player.vimeo.com/video/%id%?autoplay=1"},gmaps:{index:"//maps.google.",src:"%id%&output=embed"}}},proto:{initIframe:function(){n.types.push(P),w("BeforeChange",function(a,b,c){b!==c&&(b===P?R():c===P&&R(!0))}),w(b+"."+P,function(){R()})},getIframe:function(b,c){var d=b.src,e=n.st.iframe;a.each(e.patterns,function(){if(d.indexOf(this.index)>-1)return this.id&&(typeof this.id=="string"?d=d.substr(d.lastIndexOf(this.id)+this.id.length,d.length):d=this.id.call(this,d)),d=this.src.replace("%id%",d),!1});var f={};return e.srcAction&&(f[e.srcAction]=d),n._parseMarkup(c,f,b),n.updateStatus("ready"),c}}});var S=function(a){var b=n.items.length;return a>b-1?a-b:a<0?b+a:a},T=function(a,b,c){return a.replace(/%curr%/gi,b+1).replace(/%total%/gi,c)};a.magnificPopup.registerModule("gallery",{options:{enabled:!1,arrowMarkup:'<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',preload:[0,2],navigateByImgClick:!0,arrows:!0,tPrev:"Previous (Left arrow key)",tNext:"Next (Right arrow key)",tCounter:"%curr% of %total%"},proto:{initGallery:function(){var c=n.st.gallery,d=".mfp-gallery";n.direction=!0;if(!c||!c.enabled)return!1;u+=" mfp-gallery",w(g+d,function(){c.navigateByImgClick&&n.wrap.on("click"+d,".mfp-img",function(){if(n.items.length>1)return n.next(),!1}),s.on("keydown"+d,function(a){a.keyCode===37?n.prev():a.keyCode===39&&n.next()})}),w("UpdateStatus"+d,function(a,b){b.text&&(b.text=T(b.text,n.currItem.index,n.items.length))}),w(f+d,function(a,b,d,e){var f=n.items.length;d.counter=f>1?T(c.tCounter,e.index,f):""}),w("BuildControls"+d,function(){if(n.items.length>1&&c.arrows&&!n.arrowLeft){var b=c.arrowMarkup,d=n.arrowLeft=a(b.replace(/%title%/gi,c.tPrev).replace(/%dir%/gi,"left")).addClass(m),e=n.arrowRight=a(b.replace(/%title%/gi,c.tNext).replace(/%dir%/gi,"right")).addClass(m);d.click(function(){n.prev()}),e.click(function(){n.next()}),n.container.append(d.add(e))}}),w(h+d,function(){n._preloadTimeout&&clearTimeout(n._preloadTimeout),n._preloadTimeout=setTimeout(function(){n.preloadNearbyImages(),n._preloadTimeout=null},16)}),w(b+d,function(){s.off(d),n.wrap.off("click"+d),n.arrowRight=n.arrowLeft=null})},next:function(){n.direction=!0,n.index=S(n.index+1),n.updateItemHTML()},prev:function(){n.direction=!1,n.index=S(n.index-1),n.updateItemHTML()},goTo:function(a){n.direction=a>=n.index,n.index=a,n.updateItemHTML()},preloadNearbyImages:function(){var a=n.st.gallery.preload,b=Math.min(a[0],n.items.length),c=Math.min(a[1],n.items.length),d;for(d=1;d<=(n.direction?c:b);d++)n._preloadItem(n.index+d);for(d=1;d<=(n.direction?b:c);d++)n._preloadItem(n.index-d)},_preloadItem:function(b){b=S(b);if(n.items[b].preloaded)return;var c=n.items[b];c.parsed||(c=n.parseEl(b)),y("LazyLoad",c),c.type==="image"&&(c.img=a('<img class="mfp-img" />').on("load.mfploader",function(){c.hasSize=!0}).on("error.mfploader",function(){c.hasSize=!0,c.loadError=!0,y("LazyLoadError",c)}).attr("src",c.src)),c.preloaded=!0}}});var U="retina";a.magnificPopup.registerModule(U,{options:{replaceSrc:function(a){return a.src.replace(/\.\w+$/,function(a){return"@2x"+a})},ratio:1},proto:{initRetina:function(){if(window.devicePixelRatio>1){var a=n.st.retina,b=a.ratio;b=isNaN(b)?b():b,b>1&&(w("ImageHasSize."+U,function(a,c){c.img.css({"max-width":c.img[0].naturalWidth/b,width:"100%"})}),w("ElementParse."+U,function(c,d){d.src=a.replaceSrc(d,b)}))}}}}),A()})


/**
 * Scroll
 * 
 * Nice Scroll
 * 
 * 3.6.6 | InuYaksa | 2015 MIT | http://nicescroll.areaaperta.com
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?module.exports=e(require("jquery")):e(jQuery)}(function(e){"use strict";function o(){var e=document.getElementsByTagName("script"),o=e.length?e[e.length-1].src.split("?")[0]:"";return o.split("/").length>0?o.split("/").slice(0,-1).join("/")+"/":""}function t(e,o,t){for(var r=0;r<o.length;r++)t(e,o[r])}var r=!1,i=!1,n=0,s=2e3,l=0,a=e,c=["webkit","ms","moz","o"],d=window.requestAnimationFrame||!1,u=window.cancelAnimationFrame||!1;if(!d)for(var h in c){var p=c[h];d||(d=window[p+"RequestAnimationFrame"]),u||(u=window[p+"CancelAnimationFrame"]||window[p+"CancelRequestAnimationFrame"])}var m=window.MutationObserver||window.WebKitMutationObserver||!1,f={zindex:"auto",cursoropacitymin:0,cursoropacitymax:1,cursorcolor:"#424242",cursorwidth:"5px",cursorborder:"1px solid #fff",cursorborderradius:"5px",scrollspeed:60,mousescrollstep:24,touchbehavior:!1,hwacceleration:!0,usetransition:!0,boxzoom:!1,dblclickzoom:!0,gesturezoom:!0,grabcursorenabled:!0,autohidemode:!0,background:"",iframeautoresize:!0,cursorminheight:32,preservenativescrolling:!0,railoffset:!1,railhoffset:!1,bouncescroll:!0,spacebarenabled:!0,railpadding:{top:0,right:0,left:0,bottom:0},disableoutline:!0,horizrailenabled:!0,railalign:"right",railvalign:"bottom",enabletranslate3d:!0,enablemousewheel:!0,enablekeyboard:!0,smoothscroll:!0,sensitiverail:!0,enablemouselockapi:!0,cursorfixedheight:!1,directionlockdeadzone:6,hidecursordelay:400,nativeparentscrolling:!0,enablescrollonselection:!0,overflowx:!0,overflowy:!0,cursordragspeed:.3,rtlmode:"auto",cursordragontouch:!1,oneaxismousemode:"auto",scriptpath:o(),preventmultitouchscrolling:!0},g=!1,w=function(){function e(){var e=["-webkit-grab","-moz-grab","grab"];(n.ischrome&&!n.ischrome22||n.isie)&&(e=[]);for(var o=0;o<e.length;o++){var r=e[o];if(t.cursor=r,t.cursor==r)return r}return"url(//mail.google.com/mail/images/2/openhand.cur),n-resize"}if(g)return g;var o=document.createElement("DIV"),t=o.style,r=navigator.userAgent,i=navigator.platform,n={};n.haspointerlock="pointerLockElement"in document||"webkitPointerLockElement"in document||"mozPointerLockElement"in document,n.isopera="opera"in window,n.isopera12=n.isopera&&"getUserMedia"in navigator,n.isoperamini="[object OperaMini]"===Object.prototype.toString.call(window.operamini),n.isie="all"in document&&"attachEvent"in o&&!n.isopera,n.isieold=n.isie&&!("msInterpolationMode"in t),n.isie7=n.isie&&!n.isieold&&(!("documentMode"in document)||7==document.documentMode),n.isie8=n.isie&&"documentMode"in document&&8==document.documentMode,n.isie9=n.isie&&"performance"in window&&document.documentMode>=9,n.isie10=n.isie&&"performance"in window&&10==document.documentMode,n.isie11="msRequestFullscreen"in o&&document.documentMode>=11,n.isieedge=navigator.userAgent.match(/Edge\/12\./),n.isie9mobile=/iemobile.9/i.test(r),n.isie9mobile&&(n.isie9=!1),n.isie7mobile=!n.isie9mobile&&n.isie7&&/iemobile/i.test(r),n.ismozilla="MozAppearance"in t,n.iswebkit="WebkitAppearance"in t,n.ischrome="chrome"in window,n.ischrome22=n.ischrome&&n.haspointerlock,n.ischrome26=n.ischrome&&"transition"in t,n.cantouch="ontouchstart"in document.documentElement||"ontouchstart"in window,n.hasmstouch=window.MSPointerEvent||!1,n.hasw3ctouch=(window.PointerEvent||!1)&&(navigator.MaxTouchPoints>0||navigator.msMaxTouchPoints>0),n.ismac=/^mac$/i.test(i),n.isios=n.cantouch&&/iphone|ipad|ipod/i.test(i),n.isios4=n.isios&&!("seal"in Object),n.isios7=n.isios&&"webkitHidden"in document,n.isandroid=/android/i.test(r),n.haseventlistener="addEventListener"in o,n.trstyle=!1,n.hastransform=!1,n.hastranslate3d=!1,n.transitionstyle=!1,n.hastransition=!1,n.transitionend=!1;var s,l=["transform","msTransform","webkitTransform","MozTransform","OTransform"];for(s=0;s<l.length;s++)if("undefined"!=typeof t[l[s]]){n.trstyle=l[s];break}n.hastransform=!!n.trstyle,n.hastransform&&(t[n.trstyle]="translate3d(1px,2px,3px)",n.hastranslate3d=/translate3d/.test(t[n.trstyle])),n.transitionstyle=!1,n.prefixstyle="",n.transitionend=!1,l=["transition","webkitTransition","msTransition","MozTransition","OTransition","OTransition","KhtmlTransition"];var a=["","-webkit-","-ms-","-moz-","-o-","-o","-khtml-"],c=["transitionend","webkitTransitionEnd","msTransitionEnd","transitionend","otransitionend","oTransitionEnd","KhtmlTransitionEnd"];for(s=0;s<l.length;s++)if(l[s]in t){n.transitionstyle=l[s],n.prefixstyle=a[s],n.transitionend=c[s];break}return n.ischrome26&&(n.prefixstyle=a[1]),n.hastransition=n.transitionstyle,n.cursorgrabvalue=e(),n.hasmousecapture="setCapture"in o,n.hasMutationObserver=m!==!1,o=null,g=n,n},v=function(e,o){function t(){var e=v.doc.css(x.trstyle);return e&&"matrix"==e.substr(0,6)?e.replace(/^.*\((.*)\)$/g,"$1").replace(/px/g,"").split(/, +/):!1}function c(){var e=v.win;if("zIndex"in e)return e.zIndex();for(;e.length>0;){if(9==e[0].nodeType)return!1;var o=e.css("zIndex");if(!isNaN(o)&&0!=o)return parseInt(o);e=e.parent()}return!1}function h(e,o,t){var r=e.css(o),i=parseFloat(r);if(isNaN(i)){i=k[r]||0;var n=3==i?t?v.win.outerHeight()-v.win.innerHeight():v.win.outerWidth()-v.win.innerWidth():1;return v.isie8&&i&&(i+=1),n?i:0}return i}function p(e,o,t,r){v._bind(e,o,function(r){var r=r?r:window.event,i={original:r,target:r.target||r.srcElement,type:"wheel",deltaMode:"MozMousePixelScroll"==r.type?0:1,deltaX:0,deltaZ:0,preventDefault:function(){return r.preventDefault?r.preventDefault():r.returnValue=!1,!1},stopImmediatePropagation:function(){r.stopImmediatePropagation?r.stopImmediatePropagation():r.cancelBubble=!0}};return"mousewheel"==o?(i.deltaY=-1/40*r.wheelDelta,r.wheelDeltaX&&(i.deltaX=-1/40*r.wheelDeltaX)):i.deltaY=r.detail,t.call(e,i)},r)}function g(e,o,t){var r,i;if(0==e.deltaMode?(r=-Math.floor(e.deltaX*(v.opt.mousescrollstep/54)),i=-Math.floor(e.deltaY*(v.opt.mousescrollstep/54))):1==e.deltaMode&&(r=-Math.floor(e.deltaX*v.opt.mousescrollstep),i=-Math.floor(e.deltaY*v.opt.mousescrollstep)),o&&v.opt.oneaxismousemode&&0==r&&i&&(r=i,i=0,t)){var n=0>r?v.getScrollLeft()>=v.page.maxw:v.getScrollLeft()<=0;n&&(i=r,r=0)}if(r&&(v.scrollmom&&v.scrollmom.stop(),v.lastdeltax+=r,v.debounced("mousewheelx",function(){var e=v.lastdeltax;v.lastdeltax=0,v.rail.drag||v.doScrollLeftBy(e)},15)),i){if(v.opt.nativeparentscrolling&&t&&!v.ispage&&!v.zoomactive)if(0>i){if(v.getScrollTop()>=v.page.maxh)return!0}else if(v.getScrollTop()<=0)return!0;v.scrollmom&&v.scrollmom.stop(),v.lastdeltay+=i,v.debounced("mousewheely",function(){var e=v.lastdeltay;v.lastdeltay=0,v.rail.drag||v.doScrollBy(e)},15)}return e.stopImmediatePropagation(),e.preventDefault()}var v=this;if(this.version="3.6.6",this.name="nicescroll",this.me=o,this.opt={doc:a("body"),win:!1},a.extend(this.opt,f),this.opt.snapbackspeed=80,e)for(var y in v.opt)"undefined"!=typeof e[y]&&(v.opt[y]=e[y]);this.doc=v.opt.doc,this.iddoc=this.doc&&this.doc[0]?this.doc[0].id||"":"",this.ispage=/^BODY|HTML/.test(v.opt.win?v.opt.win[0].nodeName:this.doc[0].nodeName),this.haswrapper=v.opt.win!==!1,this.win=v.opt.win||(this.ispage?a(window):this.doc),this.docscroll=this.ispage&&!this.haswrapper?a(window):this.win,this.body=a("body"),this.viewport=!1,this.isfixed=!1,this.iframe=!1,this.isiframe="IFRAME"==this.doc[0].nodeName&&"IFRAME"==this.win[0].nodeName,this.istextarea="TEXTAREA"==this.win[0].nodeName,this.forcescreen=!1,this.canshowonmouseevent="scroll"!=v.opt.autohidemode,this.onmousedown=!1,this.onmouseup=!1,this.onmousemove=!1,this.onmousewheel=!1,this.onkeypress=!1,this.ongesturezoom=!1,this.onclick=!1,this.onscrollstart=!1,this.onscrollend=!1,this.onscrollcancel=!1,this.onzoomin=!1,this.onzoomout=!1,this.view=!1,this.page=!1,this.scroll={x:0,y:0},this.scrollratio={x:0,y:0},this.cursorheight=20,this.scrollvaluemax=0,this.isrtlmode="auto"==this.opt.rtlmode?"rtl"==(this.win[0]==window?this.body:this.win).css("direction"):this.opt.rtlmode===!0,this.scrollrunning=!1,this.scrollmom=!1,this.observer=!1,this.observerremover=!1,this.observerbody=!1;do this.id="ascrail"+s++;while(document.getElementById(this.id));this.rail=!1,this.cursor=!1,this.cursorfreezed=!1,this.selectiondrag=!1,this.zoom=!1,this.zoomactive=!1,this.hasfocus=!1,this.hasmousefocus=!1,this.visibility=!0,this.railslocked=!1,this.locked=!1,this.hidden=!1,this.cursoractive=!0,this.wheelprevented=!1,this.overflowx=v.opt.overflowx,this.overflowy=v.opt.overflowy,this.nativescrollingarea=!1,this.checkarea=0,this.events=[],this.saved={},this.delaylist={},this.synclist={},this.lastdeltax=0,this.lastdeltay=0,this.detected=w();var x=a.extend({},this.detected);this.canhwscroll=x.hastransform&&v.opt.hwacceleration,this.ishwscroll=this.canhwscroll&&v.haswrapper,this.hasreversehr=this.isrtlmode&&!x.iswebkit,this.istouchcapable=!1,!x.cantouch||x.isios||x.isandroid||!x.iswebkit&&!x.ismozilla||(this.istouchcapable=!0,x.cantouch=!1),v.opt.enablemouselockapi||(x.hasmousecapture=!1,x.haspointerlock=!1),this.debounced=function(e,o,t){var r=v.delaylist[e];v.delaylist[e]=o,r||(v.debouncedelayed=setTimeout(function(){if(v){var o=v.delaylist[e];v.delaylist[e]=!1,o.call(v)}},t))};var S=!1;this.synched=function(e,o){function t(){S||(d(function(){S=!1;for(var e in v.synclist){var o=v.synclist[e];o&&o.call(v),v.synclist[e]=!1}}),S=!0)}return v.synclist[e]=o,t(),e},this.unsynched=function(e){v.synclist[e]&&(v.synclist[e]=!1)},this.css=function(e,o){for(var t in o)v.saved.css.push([e,t,e.css(t)]),e.css(t,o[t])},this.scrollTop=function(e){return"undefined"==typeof e?v.getScrollTop():v.setScrollTop(e)},this.scrollLeft=function(e){return"undefined"==typeof e?v.getScrollLeft():v.setScrollLeft(e)};var z=function(e,o,t,r,i,n,s){this.st=e,this.ed=o,this.spd=t,this.p1=r||0,this.p2=i||1,this.p3=n||0,this.p4=s||1,this.ts=(new Date).getTime(),this.df=this.ed-this.st};if(z.prototype={B2:function(e){return 3*e*e*(1-e)},B3:function(e){return 3*e*(1-e)*(1-e)},B4:function(e){return(1-e)*(1-e)*(1-e)},getNow:function(){var e=(new Date).getTime(),o=1-(e-this.ts)/this.spd,t=this.B2(o)+this.B3(o)+this.B4(o);return 0>o?this.ed:this.st+Math.round(this.df*t)},update:function(e,o){return this.st=this.getNow(),this.ed=e,this.spd=o,this.ts=(new Date).getTime(),this.df=this.ed-this.st,this}},this.ishwscroll){this.doc.translate={x:0,y:0,tx:"0px",ty:"0px"},x.hastranslate3d&&x.isios&&this.doc.css("-webkit-backface-visibility","hidden"),this.getScrollTop=function(e){if(!e){var o=t();if(o)return 16==o.length?-o[13]:-o[5];if(v.timerscroll&&v.timerscroll.bz)return v.timerscroll.bz.getNow()}return v.doc.translate.y},this.getScrollLeft=function(e){if(!e){var o=t();if(o)return 16==o.length?-o[12]:-o[4];if(v.timerscroll&&v.timerscroll.bh)return v.timerscroll.bh.getNow()}return v.doc.translate.x},this.notifyScrollEvent=function(e){var o=document.createEvent("UIEvents");o.initUIEvent("scroll",!1,!0,window,1),o.niceevent=!0,e.dispatchEvent(o)};var T=this.isrtlmode?1:-1;x.hastranslate3d&&v.opt.enabletranslate3d?(this.setScrollTop=function(e,o){v.doc.translate.y=e,v.doc.translate.ty=-1*e+"px",v.doc.css(x.trstyle,"translate3d("+v.doc.translate.tx+","+v.doc.translate.ty+",0px)"),o||v.notifyScrollEvent(v.win[0])},this.setScrollLeft=function(e,o){v.doc.translate.x=e,v.doc.translate.tx=e*T+"px",v.doc.css(x.trstyle,"translate3d("+v.doc.translate.tx+","+v.doc.translate.ty+",0px)"),o||v.notifyScrollEvent(v.win[0])}):(this.setScrollTop=function(e,o){v.doc.translate.y=e,v.doc.translate.ty=-1*e+"px",v.doc.css(x.trstyle,"translate("+v.doc.translate.tx+","+v.doc.translate.ty+")"),o||v.notifyScrollEvent(v.win[0])},this.setScrollLeft=function(e,o){v.doc.translate.x=e,v.doc.translate.tx=e*T+"px",v.doc.css(x.trstyle,"translate("+v.doc.translate.tx+","+v.doc.translate.ty+")"),o||v.notifyScrollEvent(v.win[0])})}else this.getScrollTop=function(){return v.docscroll.scrollTop()},this.setScrollTop=function(e){return setTimeout(function(){v.docscroll.scrollTop(e)},1)},this.getScrollLeft=function(){return v.detected.ismozilla&&v.isrtlmode?Math.abs(v.docscroll.scrollLeft()):v.docscroll.scrollLeft()},this.setScrollLeft=function(e){return setTimeout(function(){v.docscroll.scrollLeft(v.detected.ismozilla&&v.isrtlmode?-e:e)},1)};this.getTarget=function(e){return e?e.target?e.target:e.srcElement?e.srcElement:!1:!1},this.hasParent=function(e,o){if(!e)return!1;for(var t=e.target||e.srcElement||e||!1;t&&t.id!=o;)t=t.parentNode||!1;return t!==!1};var k={thin:1,medium:3,thick:5};this.getDocumentScrollOffset=function(){return{top:window.pageYOffset||document.documentElement.scrollTop,left:window.pageXOffset||document.documentElement.scrollLeft}},this.getOffset=function(){if(v.isfixed){var e=v.win.offset(),o=v.getDocumentScrollOffset();return e.top-=o.top,e.left-=o.left,e}var t=v.win.offset();if(!v.viewport)return t;var r=v.viewport.offset();return{top:t.top-r.top,left:t.left-r.left}},this.updateScrollBar=function(e){if(v.ishwscroll)v.rail.css({height:v.win.innerHeight()-(v.opt.railpadding.top+v.opt.railpadding.bottom)}),v.railh&&v.railh.css({width:v.win.innerWidth()-(v.opt.railpadding.left+v.opt.railpadding.right)});else{var o=v.getOffset(),t={top:o.top,left:o.left-(v.opt.railpadding.left+v.opt.railpadding.right)};t.top+=h(v.win,"border-top-width",!0),t.left+=v.rail.align?v.win.outerWidth()-h(v.win,"border-right-width")-v.rail.width:h(v.win,"border-left-width");var r=v.opt.railoffset;if(r&&(r.top&&(t.top+=r.top),r.left&&(t.left+=r.left)),v.railslocked||v.rail.css({top:t.top,left:t.left,height:(e?e.h:v.win.innerHeight())-(v.opt.railpadding.top+v.opt.railpadding.bottom)}),v.zoom&&v.zoom.css({top:t.top+1,left:1==v.rail.align?t.left-20:t.left+v.rail.width+4}),v.railh&&!v.railslocked){var t={top:o.top,left:o.left},r=v.opt.railhoffset;r&&(r.top&&(t.top+=r.top),r.left&&(t.left+=r.left));var i=v.railh.align?t.top+h(v.win,"border-top-width",!0)+v.win.innerHeight()-v.railh.height:t.top+h(v.win,"border-top-width",!0),n=t.left+h(v.win,"border-left-width");v.railh.css({top:i-(v.opt.railpadding.top+v.opt.railpadding.bottom),left:n,width:v.railh.width})}}},this.doRailClick=function(e,o,t){var r,i,n,s;v.railslocked||(v.cancelEvent(e),o?(r=t?v.doScrollLeft:v.doScrollTop,n=t?(e.pageX-v.railh.offset().left-v.cursorwidth/2)*v.scrollratio.x:(e.pageY-v.rail.offset().top-v.cursorheight/2)*v.scrollratio.y,r(n)):(r=t?v.doScrollLeftBy:v.doScrollBy,n=t?v.scroll.x:v.scroll.y,s=t?e.pageX-v.railh.offset().left:e.pageY-v.rail.offset().top,i=t?v.view.w:v.view.h,r(n>=s?i:-i)))},v.hasanimationframe=d,v.hascancelanimationframe=u,v.hasanimationframe?v.hascancelanimationframe||(u=function(){v.cancelAnimationFrame=!0}):(d=function(e){return setTimeout(e,15-Math.floor(+new Date/1e3)%16)},u=clearInterval),this.init=function(){if(v.saved.css=[],x.isie7mobile)return!0;if(x.isoperamini)return!0;if(x.hasmstouch&&v.css(v.ispage?a("html"):v.win,{"-ms-touch-action":"none"}),v.zindex="auto",v.ispage||"auto"!=v.opt.zindex?v.zindex=v.opt.zindex:v.zindex=c()||"auto",v.ispage||"auto"==v.zindex||v.zindex>l&&(l=v.zindex),v.isie&&0==v.zindex&&"auto"==v.opt.zindex&&(v.zindex="auto"),!v.ispage||!x.cantouch&&!x.isieold&&!x.isie9mobile){var e=v.docscroll;v.ispage&&(e=v.haswrapper?v.win:v.doc),x.isie9mobile||v.css(e,{"overflow-y":"hidden"}),v.ispage&&x.isie7&&("BODY"==v.doc[0].nodeName?v.css(a("html"),{"overflow-y":"hidden"}):"HTML"==v.doc[0].nodeName&&v.css(a("body"),{"overflow-y":"hidden"})),!x.isios||v.ispage||v.haswrapper||v.css(a("body"),{"-webkit-overflow-scrolling":"touch"});var o=a(document.createElement("div"));o.css({position:"relative",top:0,"float":"right",width:v.opt.cursorwidth,height:"0px","background-color":v.opt.cursorcolor,border:v.opt.cursorborder,"background-clip":"padding-box","-webkit-border-radius":v.opt.cursorborderradius,"-moz-border-radius":v.opt.cursorborderradius,"border-radius":v.opt.cursorborderradius}),o.hborder=parseFloat(o.outerHeight()-o.innerHeight()),o.addClass("nicescroll-cursors"),v.cursor=o;var t=a(document.createElement("div"));t.attr("id",v.id),t.addClass("nicescroll-rails nicescroll-rails-vr");var s,d,u=["left","right","top","bottom"];for(var h in u)d=u[h],s=v.opt.railpadding[d],s?t.css("padding-"+d,s+"px"):v.opt.railpadding[d]=0;t.append(o),t.width=Math.max(parseFloat(v.opt.cursorwidth),o.outerWidth()),t.css({width:t.width+"px",zIndex:v.zindex,background:v.opt.background,cursor:"default"}),t.visibility=!0,t.scrollable=!0,t.align="left"==v.opt.railalign?0:1,v.rail=t,v.rail.drag=!1;var p=!1;!v.opt.boxzoom||v.ispage||x.isieold||(p=document.createElement("div"),v.bind(p,"click",v.doZoom),v.bind(p,"mouseenter",function(){v.zoom.css("opacity",v.opt.cursoropacitymax)}),v.bind(p,"mouseleave",function(){v.zoom.css("opacity",v.opt.cursoropacitymin)}),v.zoom=a(p),v.zoom.css({cursor:"pointer","z-index":v.zindex,backgroundImage:"url("+v.opt.scriptpath+"zoomico.png)",height:18,width:18,backgroundPosition:"0px 0px"}),v.opt.dblclickzoom&&v.bind(v.win,"dblclick",v.doZoom),x.cantouch&&v.opt.gesturezoom&&(v.ongesturezoom=function(e){return e.scale>1.5&&v.doZoomIn(e),e.scale<.8&&v.doZoomOut(e),v.cancelEvent(e)},v.bind(v.win,"gestureend",v.ongesturezoom))),v.railh=!1;var f;if(v.opt.horizrailenabled){v.css(e,{"overflow-x":"hidden"});var o=a(document.createElement("div"));o.css({position:"absolute",top:0,height:v.opt.cursorwidth,width:"0px","background-color":v.opt.cursorcolor,border:v.opt.cursorborder,"background-clip":"padding-box","-webkit-border-radius":v.opt.cursorborderradius,"-moz-border-radius":v.opt.cursorborderradius,"border-radius":v.opt.cursorborderradius}),x.isieold&&o.css({overflow:"hidden"}),o.wborder=parseFloat(o.outerWidth()-o.innerWidth()),o.addClass("nicescroll-cursors"),v.cursorh=o,f=a(document.createElement("div")),f.attr("id",v.id+"-hr"),f.addClass("nicescroll-rails nicescroll-rails-hr"),f.height=Math.max(parseFloat(v.opt.cursorwidth),o.outerHeight()),f.css({height:f.height+"px",zIndex:v.zindex,background:v.opt.background}),f.append(o),f.visibility=!0,f.scrollable=!0,f.align="top"==v.opt.railvalign?0:1,v.railh=f,v.railh.drag=!1}if(v.ispage)t.css({position:"fixed",top:"0px",height:"100%"}),t.align?t.css({right:"0px"}):t.css({left:"0px"}),v.body.append(t),v.railh&&(f.css({position:"fixed",left:"0px",width:"100%"}),f.align?f.css({bottom:"0px"}):f.css({top:"0px"}),v.body.append(f));else{if(v.ishwscroll){"static"==v.win.css("position")&&v.css(v.win,{position:"relative"});var g="HTML"==v.win[0].nodeName?v.body:v.win;a(g).scrollTop(0).scrollLeft(0),v.zoom&&(v.zoom.css({position:"absolute",top:1,right:0,"margin-right":t.width+4}),g.append(v.zoom)),t.css({position:"absolute",top:0}),t.align?t.css({right:0}):t.css({left:0}),g.append(t),f&&(f.css({position:"absolute",left:0,bottom:0}),f.align?f.css({bottom:0}):f.css({top:0}),g.append(f))}else{v.isfixed="fixed"==v.win.css("position");var w=v.isfixed?"fixed":"absolute";v.isfixed||(v.viewport=v.getViewport(v.win[0])),v.viewport&&(v.body=v.viewport,0==/fixed|absolute/.test(v.viewport.css("position"))&&v.css(v.viewport,{position:"relative"})),t.css({position:w}),v.zoom&&v.zoom.css({position:w}),v.updateScrollBar(),v.body.append(t),v.zoom&&v.body.append(v.zoom),v.railh&&(f.css({position:w}),v.body.append(f))}x.isios&&v.css(v.win,{"-webkit-tap-highlight-color":"rgba(0,0,0,0)","-webkit-touch-callout":"none"}),x.isie&&v.opt.disableoutline&&v.win.attr("hideFocus","true"),x.iswebkit&&v.opt.disableoutline&&v.win.css({outline:"none"})}if(v.opt.autohidemode===!1?(v.autohidedom=!1,v.rail.css({opacity:v.opt.cursoropacitymax}),v.railh&&v.railh.css({opacity:v.opt.cursoropacitymax})):v.opt.autohidemode===!0||"leave"===v.opt.autohidemode?(v.autohidedom=a().add(v.rail),x.isie8&&(v.autohidedom=v.autohidedom.add(v.cursor)),v.railh&&(v.autohidedom=v.autohidedom.add(v.railh)),v.railh&&x.isie8&&(v.autohidedom=v.autohidedom.add(v.cursorh))):"scroll"==v.opt.autohidemode?(v.autohidedom=a().add(v.rail),v.railh&&(v.autohidedom=v.autohidedom.add(v.railh))):"cursor"==v.opt.autohidemode?(v.autohidedom=a().add(v.cursor),v.railh&&(v.autohidedom=v.autohidedom.add(v.cursorh))):"hidden"==v.opt.autohidemode&&(v.autohidedom=!1,v.hide(),v.railslocked=!1),x.isie9mobile){v.scrollmom=new b(v),v.onmangotouch=function(){var e=v.getScrollTop(),o=v.getScrollLeft();if(e==v.scrollmom.lastscrolly&&o==v.scrollmom.lastscrollx)return!0;var t=e-v.mangotouch.sy,r=o-v.mangotouch.sx,i=Math.round(Math.sqrt(Math.pow(r,2)+Math.pow(t,2)));if(0!=i){var n=0>t?-1:1,s=0>r?-1:1,l=+new Date;if(v.mangotouch.lazy&&clearTimeout(v.mangotouch.lazy),l-v.mangotouch.tm>80||v.mangotouch.dry!=n||v.mangotouch.drx!=s)v.scrollmom.stop(),v.scrollmom.reset(o,e),v.mangotouch.sy=e,v.mangotouch.ly=e,v.mangotouch.sx=o,v.mangotouch.lx=o,v.mangotouch.dry=n,v.mangotouch.drx=s,v.mangotouch.tm=l;else{v.scrollmom.stop(),v.scrollmom.update(v.mangotouch.sx-r,v.mangotouch.sy-t),v.mangotouch.tm=l;var a=Math.max(Math.abs(v.mangotouch.ly-e),Math.abs(v.mangotouch.lx-o));v.mangotouch.ly=e,v.mangotouch.lx=o,a>2&&(v.mangotouch.lazy=setTimeout(function(){v.mangotouch.lazy=!1,v.mangotouch.dry=0,v.mangotouch.drx=0,v.mangotouch.tm=0,v.scrollmom.doMomentum(30)},100))}}};var y=v.getScrollTop(),S=v.getScrollLeft();v.mangotouch={sy:y,ly:y,dry:0,sx:S,lx:S,drx:0,lazy:!1,tm:0},v.bind(v.docscroll,"scroll",v.onmangotouch)}else{if(x.cantouch||v.istouchcapable||v.opt.touchbehavior||x.hasmstouch){v.scrollmom=new b(v),v.ontouchstart=function(e){if(e.pointerType&&2!=e.pointerType&&"touch"!=e.pointerType)return!1;if(v.hasmoving=!1,!v.railslocked){var o;if(x.hasmstouch)for(o=e.target?e.target:!1;o;){var t=a(o).getNiceScroll();if(t.length>0&&t[0].me==v.me)break;if(t.length>0)return!1;if("DIV"==o.nodeName&&o.id==v.id)break;o=o.parentNode?o.parentNode:!1}if(v.cancelScroll(),o=v.getTarget(e)){var r=/INPUT/i.test(o.nodeName)&&/range/i.test(o.type);if(r)return v.stopPropagation(e)}if(!("clientX"in e)&&"changedTouches"in e&&(e.clientX=e.changedTouches[0].clientX,e.clientY=e.changedTouches[0].clientY),v.forcescreen){var i=e;e={original:e.original?e.original:e},e.clientX=i.screenX,e.clientY=i.screenY}if(v.rail.drag={x:e.clientX,y:e.clientY,sx:v.scroll.x,sy:v.scroll.y,st:v.getScrollTop(),sl:v.getScrollLeft(),pt:2,dl:!1},v.ispage||!v.opt.directionlockdeadzone)v.rail.drag.dl="f";else{var n={w:a(window).width(),h:a(window).height()},s={w:Math.max(document.body.scrollWidth,document.documentElement.scrollWidth),h:Math.max(document.body.scrollHeight,document.documentElement.scrollHeight)},l=Math.max(0,s.h-n.h),c=Math.max(0,s.w-n.w);!v.rail.scrollable&&v.railh.scrollable?v.rail.drag.ck=l>0?"v":!1:v.rail.scrollable&&!v.railh.scrollable?v.rail.drag.ck=c>0?"h":!1:v.rail.drag.ck=!1,v.rail.drag.ck||(v.rail.drag.dl="f")}if(v.opt.touchbehavior&&v.isiframe&&x.isie){var d=v.win.position();v.rail.drag.x+=d.left,v.rail.drag.y+=d.top}if(v.hasmoving=!1,v.lastmouseup=!1,v.scrollmom.reset(e.clientX,e.clientY),!x.cantouch&&!this.istouchcapable&&!e.pointerType){var u=o?/INPUT|SELECT|TEXTAREA/i.test(o.nodeName):!1;if(!u)return!v.ispage&&x.hasmousecapture&&o.setCapture(),v.opt.touchbehavior?(o.onclick&&!o._onclick&&(o._onclick=o.onclick,o.onclick=function(e){return v.hasmoving?!1:void o._onclick.call(this,e)}),v.cancelEvent(e)):v.stopPropagation(e);/SUBMIT|CANCEL|BUTTON/i.test(a(o).attr("type"))&&(pc={tg:o,click:!1},v.preventclick=pc)}}},v.ontouchend=function(e){if(!v.rail.drag)return!0;if(2==v.rail.drag.pt){if(e.pointerType&&2!=e.pointerType&&"touch"!=e.pointerType)return!1;if(v.scrollmom.doMomentum(),v.rail.drag=!1,v.hasmoving&&(v.lastmouseup=!0,v.hideCursor(),x.hasmousecapture&&document.releaseCapture(),!x.cantouch))return v.cancelEvent(e)}else if(1==v.rail.drag.pt)return v.onmouseup(e)};var z=v.opt.touchbehavior&&v.isiframe&&!x.hasmousecapture;v.ontouchmove=function(e,o){if(!v.rail.drag)return!1;if(e.targetTouches&&v.opt.preventmultitouchscrolling&&e.targetTouches.length>1)return!1;if(e.pointerType&&2!=e.pointerType&&"touch"!=e.pointerType)return!1;if(2==v.rail.drag.pt){if(x.cantouch&&x.isios&&"undefined"==typeof e.original)return!0;v.hasmoving=!0,v.preventclick&&!v.preventclick.click&&(v.preventclick.click=v.preventclick.tg.onclick||!1,v.preventclick.tg.onclick=v.onpreventclick);var t=a.extend({original:e},e);if(e=t,"changedTouches"in e&&(e.clientX=e.changedTouches[0].clientX,e.clientY=e.changedTouches[0].clientY),v.forcescreen){var r=e;e={original:e.original?e.original:e},e.clientX=r.screenX,e.clientY=r.screenY}var i,n;if(n=i=0,z&&!o){var s=v.win.position();n=-s.left,i=-s.top}var l=e.clientY+i,c=l-v.rail.drag.y,d=e.clientX+n,u=d-v.rail.drag.x,h=v.rail.drag.st-c;v.ishwscroll&&v.opt.bouncescroll?0>h?h=Math.round(h/2):h>v.page.maxh&&(h=v.page.maxh+Math.round((h-v.page.maxh)/2)):(0>h&&(h=0,l=0),h>v.page.maxh&&(h=v.page.maxh,l=0));var p;v.railh&&v.railh.scrollable&&(p=v.isrtlmode?u-v.rail.drag.sl:v.rail.drag.sl-u,v.ishwscroll&&v.opt.bouncescroll?0>p?p=Math.round(p/2):p>v.page.maxw&&(p=v.page.maxw+Math.round((p-v.page.maxw)/2)):(0>p&&(p=0,d=0),p>v.page.maxw&&(p=v.page.maxw,d=0)));var m=!1;if(v.rail.drag.dl)m=!0,"v"==v.rail.drag.dl?p=v.rail.drag.sl:"h"==v.rail.drag.dl&&(h=v.rail.drag.st);else{var f=Math.abs(c),g=Math.abs(u),w=v.opt.directionlockdeadzone;if("v"==v.rail.drag.ck){if(f>w&&.3*f>=g)return v.rail.drag=!1,!0;g>w&&(v.rail.drag.dl="f",a("body").scrollTop(a("body").scrollTop()))}else if("h"==v.rail.drag.ck){if(g>w&&.3*g>=f)return v.rail.drag=!1,!0;f>w&&(v.rail.drag.dl="f",a("body").scrollLeft(a("body").scrollLeft()))}}if(v.synched("touchmove",function(){v.rail.drag&&2==v.rail.drag.pt&&(v.prepareTransition&&v.prepareTransition(0),v.rail.scrollable&&v.setScrollTop(h),v.scrollmom.update(d,l),v.railh&&v.railh.scrollable?(v.setScrollLeft(p),v.showCursor(h,p)):v.showCursor(h),x.isie10&&document.selection.clear())}),x.ischrome&&v.istouchcapable&&(m=!1),m)return v.cancelEvent(e)}else if(1==v.rail.drag.pt)return v.onmousemove(e)}}if(v.onmousedown=function(e,o){if(!v.rail.drag||1==v.rail.drag.pt){if(v.railslocked)return v.cancelEvent(e);v.cancelScroll(),v.rail.drag={x:e.clientX,y:e.clientY,sx:v.scroll.x,sy:v.scroll.y,pt:1,hr:!!o};var t=v.getTarget(e);return!v.ispage&&x.hasmousecapture&&t.setCapture(),v.isiframe&&!x.hasmousecapture&&(v.saved.csspointerevents=v.doc.css("pointer-events"),v.css(v.doc,{"pointer-events":"none"})),v.hasmoving=!1,v.cancelEvent(e)}},v.onmouseup=function(e){return v.rail.drag?1!=v.rail.drag.pt?!0:(x.hasmousecapture&&document.releaseCapture(),v.isiframe&&!x.hasmousecapture&&v.doc.css("pointer-events",v.saved.csspointerevents),v.rail.drag=!1,v.hasmoving&&v.triggerScrollEnd(),v.cancelEvent(e)):void 0},v.onmousemove=function(e){if(v.rail.drag){if(1!=v.rail.drag.pt)return;if(x.ischrome&&0==e.which)return v.onmouseup(e);if(v.cursorfreezed=!0,v.hasmoving=!0,v.rail.drag.hr){v.scroll.x=v.rail.drag.sx+(e.clientX-v.rail.drag.x),v.scroll.x<0&&(v.scroll.x=0);var o=v.scrollvaluemaxw;v.scroll.x>o&&(v.scroll.x=o)}else{v.scroll.y=v.rail.drag.sy+(e.clientY-v.rail.drag.y),v.scroll.y<0&&(v.scroll.y=0);var t=v.scrollvaluemax;v.scroll.y>t&&(v.scroll.y=t)}return v.synched("mousemove",function(){v.rail.drag&&1==v.rail.drag.pt&&(v.showCursor(),v.rail.drag.hr?v.hasreversehr?v.doScrollLeft(v.scrollvaluemaxw-Math.round(v.scroll.x*v.scrollratio.x),v.opt.cursordragspeed):v.doScrollLeft(Math.round(v.scroll.x*v.scrollratio.x),v.opt.cursordragspeed):v.doScrollTop(Math.round(v.scroll.y*v.scrollratio.y),v.opt.cursordragspeed))}),v.cancelEvent(e)}v.checkarea=0},x.cantouch||v.opt.touchbehavior)v.onpreventclick=function(e){return v.preventclick?(v.preventclick.tg.onclick=v.preventclick.click,v.preventclick=!1,v.cancelEvent(e)):void 0},v.bind(v.win,"mousedown",v.ontouchstart),v.onclick=x.isios?!1:function(e){return v.lastmouseup?(v.lastmouseup=!1,v.cancelEvent(e)):!0},v.opt.grabcursorenabled&&x.cursorgrabvalue&&(v.css(v.ispage?v.doc:v.win,{cursor:x.cursorgrabvalue}),v.css(v.rail,{cursor:x.cursorgrabvalue}));else{var T=function(e){if(v.selectiondrag){if(e){var o=v.win.outerHeight(),t=e.pageY-v.selectiondrag.top;t>0&&o>t&&(t=0),t>=o&&(t-=o),v.selectiondrag.df=t}if(0!=v.selectiondrag.df){var r=2*-Math.floor(v.selectiondrag.df/6);v.doScrollBy(r),v.debounced("doselectionscroll",function(){T()},50)}}};"getSelection"in document?v.hasTextSelected=function(){return document.getSelection().rangeCount>0}:"selection"in document?v.hasTextSelected=function(){return"None"!=document.selection.type}:v.hasTextSelected=function(){return!1},v.onselectionstart=function(e){v.ispage||(v.selectiondrag=v.win.offset())},v.onselectionend=function(e){v.selectiondrag=!1},v.onselectiondrag=function(e){v.selectiondrag&&v.hasTextSelected()&&v.debounced("selectionscroll",function(){T(e)},250)}}x.hasw3ctouch?(v.css(v.rail,{"touch-action":"none"}),v.css(v.cursor,{"touch-action":"none"}),v.bind(v.win,"pointerdown",v.ontouchstart),v.bind(document,"pointerup",v.ontouchend),v.bind(document,"pointermove",v.ontouchmove)):x.hasmstouch?(v.css(v.rail,{"-ms-touch-action":"none"}),v.css(v.cursor,{"-ms-touch-action":"none"}),v.bind(v.win,"MSPointerDown",v.ontouchstart),v.bind(document,"MSPointerUp",v.ontouchend),v.bind(document,"MSPointerMove",v.ontouchmove),v.bind(v.cursor,"MSGestureHold",function(e){e.preventDefault()}),v.bind(v.cursor,"contextmenu",function(e){e.preventDefault()})):this.istouchcapable&&(v.bind(v.win,"touchstart",v.ontouchstart),v.bind(document,"touchend",v.ontouchend),v.bind(document,"touchcancel",v.ontouchend),v.bind(document,"touchmove",v.ontouchmove)),(v.opt.cursordragontouch||!x.cantouch&&!v.opt.touchbehavior)&&(v.rail.css({cursor:"default"}),v.railh&&v.railh.css({cursor:"default"}),v.jqbind(v.rail,"mouseenter",function(){return v.ispage||v.win.is(":visible")?(v.canshowonmouseevent&&v.showCursor(),void(v.rail.active=!0)):!1}),v.jqbind(v.rail,"mouseleave",function(){v.rail.active=!1,v.rail.drag||v.hideCursor()}),v.opt.sensitiverail&&(v.bind(v.rail,"click",function(e){v.doRailClick(e,!1,!1)}),v.bind(v.rail,"dblclick",function(e){v.doRailClick(e,!0,!1)}),v.bind(v.cursor,"click",function(e){v.cancelEvent(e)}),v.bind(v.cursor,"dblclick",function(e){v.cancelEvent(e)})),v.railh&&(v.jqbind(v.railh,"mouseenter",function(){return v.ispage||v.win.is(":visible")?(v.canshowonmouseevent&&v.showCursor(),void(v.rail.active=!0)):!1}),v.jqbind(v.railh,"mouseleave",function(){v.rail.active=!1,v.rail.drag||v.hideCursor()}),v.opt.sensitiverail&&(v.bind(v.railh,"click",function(e){v.doRailClick(e,!1,!0)}),v.bind(v.railh,"dblclick",function(e){v.doRailClick(e,!0,!0)}),v.bind(v.cursorh,"click",function(e){v.cancelEvent(e)}),v.bind(v.cursorh,"dblclick",function(e){v.cancelEvent(e)})))),x.cantouch||v.opt.touchbehavior?(v.bind(x.hasmousecapture?v.win:document,"mouseup",v.ontouchend),v.bind(document,"mousemove",v.ontouchmove),v.onclick&&v.bind(document,"click",v.onclick),v.opt.cursordragontouch&&(v.bind(v.cursor,"mousedown",v.onmousedown),v.bind(v.cursor,"mouseup",v.onmouseup),v.cursorh&&v.bind(v.cursorh,"mousedown",function(e){v.onmousedown(e,!0)}),v.cursorh&&v.bind(v.cursorh,"mouseup",v.onmouseup))):(v.bind(x.hasmousecapture?v.win:document,"mouseup",v.onmouseup),v.bind(document,"mousemove",v.onmousemove),v.onclick&&v.bind(document,"click",v.onclick),v.bind(v.cursor,"mousedown",v.onmousedown),v.bind(v.cursor,"mouseup",v.onmouseup),v.railh&&(v.bind(v.cursorh,"mousedown",function(e){v.onmousedown(e,!0)}),v.bind(v.cursorh,"mouseup",v.onmouseup)),!v.ispage&&v.opt.enablescrollonselection&&(v.bind(v.win[0],"mousedown",v.onselectionstart),v.bind(document,"mouseup",v.onselectionend),v.bind(v.cursor,"mouseup",v.onselectionend),v.cursorh&&v.bind(v.cursorh,"mouseup",v.onselectionend),v.bind(document,"mousemove",v.onselectiondrag)),v.zoom&&(v.jqbind(v.zoom,"mouseenter",function(){v.canshowonmouseevent&&v.showCursor(),v.rail.active=!0}),v.jqbind(v.zoom,"mouseleave",function(){v.rail.active=!1,v.rail.drag||v.hideCursor()}))),v.opt.enablemousewheel&&(v.isiframe||v.bind(x.isie&&v.ispage?document:v.win,"mousewheel",v.onmousewheel),v.bind(v.rail,"mousewheel",v.onmousewheel),v.railh&&v.bind(v.railh,"mousewheel",v.onmousewheelhr)),v.ispage||x.cantouch||/HTML|^BODY/.test(v.win[0].nodeName)||(v.win.attr("tabindex")||v.win.attr({tabindex:n++}),v.jqbind(v.win,"focus",function(e){r=v.getTarget(e).id||!0,v.hasfocus=!0,v.canshowonmouseevent&&v.noticeCursor()}),
v.jqbind(v.win,"blur",function(e){r=!1,v.hasfocus=!1}),v.jqbind(v.win,"mouseenter",function(e){i=v.getTarget(e).id||!0,v.hasmousefocus=!0,v.canshowonmouseevent&&v.noticeCursor()}),v.jqbind(v.win,"mouseleave",function(){i=!1,v.hasmousefocus=!1,v.rail.drag||v.hideCursor()}))}if(v.onkeypress=function(e){if(v.railslocked&&0==v.page.maxh)return!0;e=e?e:window.e;var o=v.getTarget(e);if(o&&/INPUT|TEXTAREA|SELECT|OPTION/.test(o.nodeName)){var t=o.getAttribute("type")||o.type||!1;if(!t||!/submit|button|cancel/i.tp)return!0}if(a(o).attr("contenteditable"))return!0;if(v.hasfocus||v.hasmousefocus&&!r||v.ispage&&!r&&!i){var n=e.keyCode;if(v.railslocked&&27!=n)return v.cancelEvent(e);var s=e.ctrlKey||!1,l=e.shiftKey||!1,c=!1;switch(n){case 38:case 63233:v.doScrollBy(72),c=!0;break;case 40:case 63235:v.doScrollBy(-72),c=!0;break;case 37:case 63232:v.railh&&(s?v.doScrollLeft(0):v.doScrollLeftBy(72),c=!0);break;case 39:case 63234:v.railh&&(s?v.doScrollLeft(v.page.maxw):v.doScrollLeftBy(-72),c=!0);break;case 33:case 63276:v.doScrollBy(v.view.h),c=!0;break;case 34:case 63277:v.doScrollBy(-v.view.h),c=!0;break;case 36:case 63273:v.railh&&s?v.doScrollPos(0,0):v.doScrollTo(0),c=!0;break;case 35:case 63275:v.railh&&s?v.doScrollPos(v.page.maxw,v.page.maxh):v.doScrollTo(v.page.maxh),c=!0;break;case 32:v.opt.spacebarenabled&&(l?v.doScrollBy(v.view.h):v.doScrollBy(-v.view.h),c=!0);break;case 27:v.zoomactive&&(v.doZoom(),c=!0)}if(c)return v.cancelEvent(e)}},v.opt.enablekeyboard&&v.bind(document,x.isopera&&!x.isopera12?"keypress":"keydown",v.onkeypress),v.bind(document,"keydown",function(e){var o=e.ctrlKey||!1;o&&(v.wheelprevented=!0)}),v.bind(document,"keyup",function(e){var o=e.ctrlKey||!1;o||(v.wheelprevented=!1)}),v.bind(window,"blur",function(e){v.wheelprevented=!1}),v.bind(window,"resize",v.lazyResize),v.bind(window,"orientationchange",v.lazyResize),v.bind(window,"load",v.lazyResize),x.ischrome&&!v.ispage&&!v.haswrapper){var k=v.win.attr("style"),M=parseFloat(v.win.css("width"))+1;v.win.css("width",M),v.synched("chromefix",function(){v.win.attr("style",k)})}v.onAttributeChange=function(e){v.lazyResize(v.isieold?250:30)},m!==!1&&(v.observerbody=new m(function(e){return e.forEach(function(e){return"attributes"==e.type?a("body").hasClass("modal-open")&&!a.contains(a(".modal-dialog")[0],v.doc[0])?v.hide():v.show():void 0}),document.body.scrollHeight!=v.page.maxh?v.lazyResize(30):void 0}),v.observerbody.observe(document.body,{childList:!0,subtree:!0,characterData:!1,attributes:!0,attributeFilter:["class"]})),v.ispage||v.haswrapper||(m!==!1?(v.observer=new m(function(e){e.forEach(v.onAttributeChange)}),v.observer.observe(v.win[0],{childList:!0,characterData:!1,attributes:!0,subtree:!1}),v.observerremover=new m(function(e){e.forEach(function(e){if(e.removedNodes.length>0)for(var o in e.removedNodes)if(v&&e.removedNodes[o]==v.win[0])return v.remove()})}),v.observerremover.observe(v.win[0].parentNode,{childList:!0,characterData:!1,attributes:!1,subtree:!1})):(v.bind(v.win,x.isie&&!x.isie9?"propertychange":"DOMAttrModified",v.onAttributeChange),x.isie9&&v.win[0].attachEvent("onpropertychange",v.onAttributeChange),v.bind(v.win,"DOMNodeRemoved",function(e){e.target==v.win[0]&&v.remove()}))),!v.ispage&&v.opt.boxzoom&&v.bind(window,"resize",v.resizeZoom),v.istextarea&&(v.bind(v.win,"keydown",v.lazyResize),v.bind(v.win,"mouseup",v.lazyResize)),v.lazyResize(30)}if("IFRAME"==this.doc[0].nodeName){var E=function(){v.iframexd=!1;var e;try{e="contentDocument"in this?this.contentDocument:this.contentWindow.document;e.domain}catch(o){v.iframexd=!0,e=!1}if(v.iframexd)return"console"in window&&console.log("NiceScroll error: policy restriced iframe"),!0;if(v.forcescreen=!0,v.isiframe&&(v.iframe={doc:a(e),html:v.doc.contents().find("html")[0],body:v.doc.contents().find("body")[0]},v.getContentSize=function(){return{w:Math.max(v.iframe.html.scrollWidth,v.iframe.body.scrollWidth),h:Math.max(v.iframe.html.scrollHeight,v.iframe.body.scrollHeight)}},v.docscroll=a(v.iframe.body)),!x.isios&&v.opt.iframeautoresize&&!v.isiframe){v.win.scrollTop(0),v.doc.height("");var t=Math.max(e.getElementsByTagName("html")[0].scrollHeight,e.body.scrollHeight);v.doc.height(t)}v.lazyResize(30),x.isie7&&v.css(a(v.iframe.html),{"overflow-y":"hidden"}),v.css(a(v.iframe.body),{"overflow-y":"hidden"}),x.isios&&v.haswrapper&&v.css(a(e.body),{"-webkit-transform":"translate3d(0,0,0)"}),"contentWindow"in this?v.bind(this.contentWindow,"scroll",v.onscroll):v.bind(e,"scroll",v.onscroll),v.opt.enablemousewheel&&v.bind(e,"mousewheel",v.onmousewheel),v.opt.enablekeyboard&&v.bind(e,x.isopera?"keypress":"keydown",v.onkeypress),(x.cantouch||v.opt.touchbehavior)&&(v.bind(e,"mousedown",v.ontouchstart),v.bind(e,"mousemove",function(e){return v.ontouchmove(e,!0)}),v.opt.grabcursorenabled&&x.cursorgrabvalue&&v.css(a(e.body),{cursor:x.cursorgrabvalue})),v.bind(e,"mouseup",v.ontouchend),v.zoom&&(v.opt.dblclickzoom&&v.bind(e,"dblclick",v.doZoom),v.ongesturezoom&&v.bind(e,"gestureend",v.ongesturezoom))};this.doc[0].readyState&&"complete"==this.doc[0].readyState&&setTimeout(function(){E.call(v.doc[0],!1)},500),v.bind(this.doc,"load",E)}},this.showCursor=function(e,o){if(v.cursortimeout&&(clearTimeout(v.cursortimeout),v.cursortimeout=0),v.rail){if(v.autohidedom&&(v.autohidedom.stop().css({opacity:v.opt.cursoropacitymax}),v.cursoractive=!0),v.rail.drag&&1==v.rail.drag.pt||("undefined"!=typeof e&&e!==!1&&(v.scroll.y=Math.round(1*e/v.scrollratio.y)),"undefined"!=typeof o&&(v.scroll.x=Math.round(1*o/v.scrollratio.x))),v.cursor.css({height:v.cursorheight,top:v.scroll.y}),v.cursorh){var t=v.hasreversehr?v.scrollvaluemaxw-v.scroll.x:v.scroll.x;!v.rail.align&&v.rail.visibility?v.cursorh.css({width:v.cursorwidth,left:t+v.rail.width}):v.cursorh.css({width:v.cursorwidth,left:t}),v.cursoractive=!0}v.zoom&&v.zoom.stop().css({opacity:v.opt.cursoropacitymax})}},this.hideCursor=function(e){v.cursortimeout||v.rail&&v.autohidedom&&(v.hasmousefocus&&"leave"==v.opt.autohidemode||(v.cursortimeout=setTimeout(function(){v.rail.active&&v.showonmouseevent||(v.autohidedom.stop().animate({opacity:v.opt.cursoropacitymin}),v.zoom&&v.zoom.stop().animate({opacity:v.opt.cursoropacitymin}),v.cursoractive=!1),v.cursortimeout=0},e||v.opt.hidecursordelay)))},this.noticeCursor=function(e,o,t){v.showCursor(o,t),v.rail.active||v.hideCursor(e)},this.getContentSize=v.ispage?function(){return{w:Math.max(document.body.scrollWidth,document.documentElement.scrollWidth),h:Math.max(document.body.scrollHeight,document.documentElement.scrollHeight)}}:v.haswrapper?function(){return{w:v.doc.outerWidth()+parseInt(v.win.css("paddingLeft"))+parseInt(v.win.css("paddingRight")),h:v.doc.outerHeight()+parseInt(v.win.css("paddingTop"))+parseInt(v.win.css("paddingBottom"))}}:function(){return{w:v.docscroll[0].scrollWidth,h:v.docscroll[0].scrollHeight}},this.onResize=function(e,o){if(!v||!v.win)return!1;if(!v.haswrapper&&!v.ispage){if("none"==v.win.css("display"))return v.visibility&&v.hideRail().hideRailHr(),!1;v.hidden||v.visibility||v.showRail().showRailHr()}var t=v.page.maxh,r=v.page.maxw,i={h:v.view.h,w:v.view.w};if(v.view={w:v.ispage?v.win.width():parseInt(v.win[0].clientWidth),h:v.ispage?v.win.height():parseInt(v.win[0].clientHeight)},v.page=o?o:v.getContentSize(),v.page.maxh=Math.max(0,v.page.h-v.view.h),v.page.maxw=Math.max(0,v.page.w-v.view.w),v.page.maxh==t&&v.page.maxw==r&&v.view.w==i.w&&v.view.h==i.h){if(v.ispage)return v;var n=v.win.offset();if(v.lastposition){var s=v.lastposition;if(s.top==n.top&&s.left==n.left)return v}v.lastposition=n}if(0==v.page.maxh?(v.hideRail(),v.scrollvaluemax=0,v.scroll.y=0,v.scrollratio.y=0,v.cursorheight=0,v.setScrollTop(0),v.rail&&(v.rail.scrollable=!1)):(v.page.maxh-=v.opt.railpadding.top+v.opt.railpadding.bottom,v.rail.scrollable=!0),0==v.page.maxw?(v.hideRailHr(),v.scrollvaluemaxw=0,v.scroll.x=0,v.scrollratio.x=0,v.cursorwidth=0,v.setScrollLeft(0),v.railh&&(v.railh.scrollable=!1)):(v.page.maxw-=v.opt.railpadding.left+v.opt.railpadding.right,v.railh&&(v.railh.scrollable=v.opt.horizrailenabled)),v.railslocked=v.locked||0==v.page.maxh&&0==v.page.maxw,v.railslocked)return v.ispage||v.updateScrollBar(v.view),!1;v.hidden||v.visibility?!v.railh||v.hidden||v.railh.visibility||v.showRailHr():v.showRail().showRailHr(),v.istextarea&&v.win.css("resize")&&"none"!=v.win.css("resize")&&(v.view.h-=20),v.cursorheight=Math.min(v.view.h,Math.round(v.view.h*(v.view.h/v.page.h))),v.cursorheight=v.opt.cursorfixedheight?v.opt.cursorfixedheight:Math.max(v.opt.cursorminheight,v.cursorheight),v.cursorwidth=Math.min(v.view.w,Math.round(v.view.w*(v.view.w/v.page.w))),v.cursorwidth=v.opt.cursorfixedheight?v.opt.cursorfixedheight:Math.max(v.opt.cursorminheight,v.cursorwidth),v.scrollvaluemax=v.view.h-v.cursorheight-v.cursor.hborder-(v.opt.railpadding.top+v.opt.railpadding.bottom),v.railh&&(v.railh.width=v.page.maxh>0?v.view.w-v.rail.width:v.view.w,v.scrollvaluemaxw=v.railh.width-v.cursorwidth-v.cursorh.wborder-(v.opt.railpadding.left+v.opt.railpadding.right)),v.ispage||v.updateScrollBar(v.view),v.scrollratio={x:v.page.maxw/v.scrollvaluemaxw,y:v.page.maxh/v.scrollvaluemax};var l=v.getScrollTop();return l>v.page.maxh?v.doScrollTop(v.page.maxh):(v.scroll.y=Math.round(v.getScrollTop()*(1/v.scrollratio.y)),v.scroll.x=Math.round(v.getScrollLeft()*(1/v.scrollratio.x)),v.cursoractive&&v.noticeCursor()),v.scroll.y&&0==v.getScrollTop()&&v.doScrollTo(Math.floor(v.scroll.y*v.scrollratio.y)),v},this.resize=v.onResize,this.lazyResize=function(e){return e=isNaN(e)?30:e,v.debounced("resize",v.resize,e),v},this.jqbind=function(e,o,t){v.events.push({e:e,n:o,f:t,q:!0}),a(e).bind(o,t)},this.bind=function(e,o,t,r){var i="jquery"in e?e[0]:e;if("mousewheel"==o)if("onwheel"in v.win)v._bind(i,"wheel",t,r||!1);else{var n="undefined"!=typeof document.onmousewheel?"mousewheel":"DOMMouseScroll";p(i,n,t,r||!1),"DOMMouseScroll"==n&&p(i,"MozMousePixelScroll",t,r||!1)}else if(i.addEventListener){if(x.cantouch&&/mouseup|mousedown|mousemove/.test(o)){var s="mousedown"==o?"touchstart":"mouseup"==o?"touchend":"touchmove";v._bind(i,s,function(e){if(e.touches){if(e.touches.length<2){var o=e.touches.length?e.touches[0]:e;o.original=e,t.call(this,o)}}else if(e.changedTouches){var o=e.changedTouches[0];o.original=e,t.call(this,o)}},r||!1)}v._bind(i,o,t,r||!1),x.cantouch&&"mouseup"==o&&v._bind(i,"touchcancel",t,r||!1)}else v._bind(i,o,function(e){return e=e||window.event||!1,e&&e.srcElement&&(e.target=e.srcElement),"pageY"in e||(e.pageX=e.clientX+document.documentElement.scrollLeft,e.pageY=e.clientY+document.documentElement.scrollTop),t.call(i,e)===!1||r===!1?v.cancelEvent(e):!0})},x.haseventlistener?(this._bind=function(e,o,t,r){v.events.push({e:e,n:o,f:t,b:r,q:!1}),e.addEventListener(o,t,r||!1)},this.cancelEvent=function(e){if(!e)return!1;var e=e.original?e.original:e;return e.preventDefault(),e.stopPropagation(),e.preventManipulation&&e.preventManipulation(),!1},this.stopPropagation=function(e){if(!e)return!1;var e=e.original?e.original:e;return e.stopPropagation(),!1},this._unbind=function(e,o,t,r){e.removeEventListener(o,t,r)}):(this._bind=function(e,o,t,r){v.events.push({e:e,n:o,f:t,b:r,q:!1}),e.attachEvent?e.attachEvent("on"+o,t):e["on"+o]=t},this.cancelEvent=function(e){var e=window.event||!1;return e?(e.cancelBubble=!0,e.cancel=!0,e.returnValue=!1,!1):!1},this.stopPropagation=function(e){var e=window.event||!1;return e?(e.cancelBubble=!0,!1):!1},this._unbind=function(e,o,t,r){e.detachEvent?e.detachEvent("on"+o,t):e["on"+o]=!1}),this.unbindAll=function(){for(var e=0;e<v.events.length;e++){var o=v.events[e];o.q?o.e.unbind(o.n,o.f):v._unbind(o.e,o.n,o.f,o.b)}},this.showRail=function(){return 0==v.page.maxh||!v.ispage&&"none"==v.win.css("display")||(v.visibility=!0,v.rail.visibility=!0,v.rail.css("display","block")),v},this.showRailHr=function(){return v.railh?(0==v.page.maxw||!v.ispage&&"none"==v.win.css("display")||(v.railh.visibility=!0,v.railh.css("display","block")),v):v},this.hideRail=function(){return v.visibility=!1,v.rail.visibility=!1,v.rail.css("display","none"),v},this.hideRailHr=function(){return v.railh?(v.railh.visibility=!1,v.railh.css("display","none"),v):v},this.show=function(){return v.hidden=!1,v.railslocked=!1,v.showRail().showRailHr()},this.hide=function(){return v.hidden=!0,v.railslocked=!0,v.hideRail().hideRailHr()},this.toggle=function(){return v.hidden?v.show():v.hide()},this.remove=function(){v.stop(),v.cursortimeout&&clearTimeout(v.cursortimeout),v.debouncedelayed&&clearTimeout(v.debouncedelayed),v.doZoomOut(),v.unbindAll(),x.isie9&&v.win[0].detachEvent("onpropertychange",v.onAttributeChange),v.observer!==!1&&v.observer.disconnect(),v.observerremover!==!1&&v.observerremover.disconnect(),v.observerbody!==!1&&v.observerbody.disconnect(),v.events=null,v.cursor&&v.cursor.remove(),v.cursorh&&v.cursorh.remove(),v.rail&&v.rail.remove(),v.railh&&v.railh.remove(),v.zoom&&v.zoom.remove();for(var e=0;e<v.saved.css.length;e++){var o=v.saved.css[e];o[0].css(o[1],"undefined"==typeof o[2]?"":o[2])}v.saved=!1,v.me.data("__nicescroll","");var t=a.nicescroll;t.each(function(e){if(this&&this.id===v.id){delete t[e];for(var o=++e;o<t.length;o++,e++)t[e]=t[o];t.length--,t.length&&delete t[t.length]}});for(var r in v)v[r]=null,delete v[r];v=null},this.scrollstart=function(e){return this.onscrollstart=e,v},this.scrollend=function(e){return this.onscrollend=e,v},this.scrollcancel=function(e){return this.onscrollcancel=e,v},this.zoomin=function(e){return this.onzoomin=e,v},this.zoomout=function(e){return this.onzoomout=e,v},this.isScrollable=function(e){var o=e.target?e.target:e;if("OPTION"==o.nodeName)return!0;for(;o&&1==o.nodeType&&!/^BODY|HTML/.test(o.nodeName);){var t=a(o),r=t.css("overflowY")||t.css("overflowX")||t.css("overflow")||"";if(/scroll|auto/.test(r))return o.clientHeight!=o.scrollHeight;o=o.parentNode?o.parentNode:!1}return!1},this.getViewport=function(e){for(var o=e&&e.parentNode?e.parentNode:!1;o&&1==o.nodeType&&!/^BODY|HTML/.test(o.nodeName);){var t=a(o);if(/fixed|absolute/.test(t.css("position")))return t;var r=t.css("overflowY")||t.css("overflowX")||t.css("overflow")||"";if(/scroll|auto/.test(r)&&o.clientHeight!=o.scrollHeight)return t;if(t.getNiceScroll().length>0)return t;o=o.parentNode?o.parentNode:!1}return!1},this.triggerScrollEnd=function(){if(v.onscrollend){var e=v.getScrollLeft(),o=v.getScrollTop(),t={type:"scrollend",current:{x:e,y:o},end:{x:e,y:o}};v.onscrollend.call(v,t)}},this.onmousewheel=function(e){if(!v.wheelprevented){if(v.railslocked)return v.debounced("checkunlock",v.resize,250),!0;if(v.rail.drag)return v.cancelEvent(e);if("auto"==v.opt.oneaxismousemode&&0!=e.deltaX&&(v.opt.oneaxismousemode=!1),v.opt.oneaxismousemode&&0==e.deltaX&&!v.rail.scrollable)return v.railh&&v.railh.scrollable?v.onmousewheelhr(e):!0;var o=+new Date,t=!1;if(v.opt.preservenativescrolling&&v.checkarea+600<o&&(v.nativescrollingarea=v.isScrollable(e),t=!0),v.checkarea=o,v.nativescrollingarea)return!0;var r=g(e,!1,t);return r&&(v.checkarea=0),r}},this.onmousewheelhr=function(e){if(!v.wheelprevented){if(v.railslocked||!v.railh.scrollable)return!0;if(v.rail.drag)return v.cancelEvent(e);var o=+new Date,t=!1;return v.opt.preservenativescrolling&&v.checkarea+600<o&&(v.nativescrollingarea=v.isScrollable(e),t=!0),v.checkarea=o,v.nativescrollingarea?!0:v.railslocked?v.cancelEvent(e):g(e,!0,t)}},this.stop=function(){return v.cancelScroll(),v.scrollmon&&v.scrollmon.stop(),v.cursorfreezed=!1,v.scroll.y=Math.round(v.getScrollTop()*(1/v.scrollratio.y)),v.noticeCursor(),v},this.getTransitionSpeed=function(e){var o=Math.round(10*v.opt.scrollspeed),t=Math.min(o,Math.round(e/20*v.opt.scrollspeed));return t>20?t:0},v.opt.smoothscroll?v.ishwscroll&&x.hastransition&&v.opt.usetransition&&v.opt.smoothscroll?(this.prepareTransition=function(e,o){var t=o?e>20?e:0:v.getTransitionSpeed(e),r=t?x.prefixstyle+"transform "+t+"ms ease-out":"";return v.lasttransitionstyle&&v.lasttransitionstyle==r||(v.lasttransitionstyle=r,v.doc.css(x.transitionstyle,r)),t},this.doScrollLeft=function(e,o){var t=v.scrollrunning?v.newscrolly:v.getScrollTop();v.doScrollPos(e,t,o)},this.doScrollTop=function(e,o){var t=v.scrollrunning?v.newscrollx:v.getScrollLeft();v.doScrollPos(t,e,o)},this.doScrollPos=function(e,o,t){var r=v.getScrollTop(),i=v.getScrollLeft();return((v.newscrolly-r)*(o-r)<0||(v.newscrollx-i)*(e-i)<0)&&v.cancelScroll(),0==v.opt.bouncescroll&&(0>o?o=0:o>v.page.maxh&&(o=v.page.maxh),0>e?e=0:e>v.page.maxw&&(e=v.page.maxw)),v.scrollrunning&&e==v.newscrollx&&o==v.newscrolly?!1:(v.newscrolly=o,v.newscrollx=e,v.newscrollspeed=t||!1,v.timer?!1:void(v.timer=setTimeout(function(){var t=v.getScrollTop(),r=v.getScrollLeft(),i={};i.x=e-r,i.y=o-t,i.px=r,i.py=t;var n=Math.round(Math.sqrt(Math.pow(i.x,2)+Math.pow(i.y,2))),s=v.newscrollspeed&&v.newscrollspeed>1?v.newscrollspeed:v.getTransitionSpeed(n);if(v.newscrollspeed&&v.newscrollspeed<=1&&(s*=v.newscrollspeed),v.prepareTransition(s,!0),v.timerscroll&&v.timerscroll.tm&&clearInterval(v.timerscroll.tm),s>0){if(!v.scrollrunning&&v.onscrollstart){var l={type:"scrollstart",current:{x:r,y:t},request:{x:e,y:o},end:{x:v.newscrollx,y:v.newscrolly},speed:s};v.onscrollstart.call(v,l)}x.transitionend?v.scrollendtrapped||(v.scrollendtrapped=!0,v.bind(v.doc,x.transitionend,v.onScrollTransitionEnd,!1)):(v.scrollendtrapped&&clearTimeout(v.scrollendtrapped),v.scrollendtrapped=setTimeout(v.onScrollTransitionEnd,s));var a=t,c=r;v.timerscroll={bz:new z(a,v.newscrolly,s,0,0,.58,1),bh:new z(c,v.newscrollx,s,0,0,.58,1)},v.cursorfreezed||(v.timerscroll.tm=setInterval(function(){v.showCursor(v.getScrollTop(),v.getScrollLeft())},60))}v.synched("doScroll-set",function(){v.timer=0,v.scrollendtrapped&&(v.scrollrunning=!0),v.setScrollTop(v.newscrolly),v.setScrollLeft(v.newscrollx),v.scrollendtrapped||v.onScrollTransitionEnd()})},50)))},this.cancelScroll=function(){if(!v.scrollendtrapped)return!0;var e=v.getScrollTop(),o=v.getScrollLeft();return v.scrollrunning=!1,x.transitionend||clearTimeout(x.transitionend),v.scrollendtrapped=!1,v._unbind(v.doc[0],x.transitionend,v.onScrollTransitionEnd),v.prepareTransition(0),v.setScrollTop(e),v.railh&&v.setScrollLeft(o),v.timerscroll&&v.timerscroll.tm&&clearInterval(v.timerscroll.tm),v.timerscroll=!1,v.cursorfreezed=!1,v.showCursor(e,o),v},this.onScrollTransitionEnd=function(){v.scrollendtrapped&&v._unbind(v.doc[0],x.transitionend,v.onScrollTransitionEnd),v.scrollendtrapped=!1,v.prepareTransition(0),v.timerscroll&&v.timerscroll.tm&&clearInterval(v.timerscroll.tm),v.timerscroll=!1;var e=v.getScrollTop(),o=v.getScrollLeft();return v.setScrollTop(e),v.railh&&v.setScrollLeft(o),v.noticeCursor(!1,e,o),v.cursorfreezed=!1,0>e?e=0:e>v.page.maxh&&(e=v.page.maxh),0>o?o=0:o>v.page.maxw&&(o=v.page.maxw),e!=v.newscrolly||o!=v.newscrollx?v.doScrollPos(o,e,v.opt.snapbackspeed):(v.onscrollend&&v.scrollrunning&&v.triggerScrollEnd(),void(v.scrollrunning=!1))}):(this.doScrollLeft=function(e,o){var t=v.scrollrunning?v.newscrolly:v.getScrollTop();v.doScrollPos(e,t,o)},this.doScrollTop=function(e,o){var t=v.scrollrunning?v.newscrollx:v.getScrollLeft();v.doScrollPos(t,e,o)},this.doScrollPos=function(e,o,t){function r(){if(v.cancelAnimationFrame)return!0;if(v.scrollrunning=!0,h=1-h)return v.timer=d(r)||1;var e,o,t=0,i=o=v.getScrollTop();if(v.dst.ay){i=v.bzscroll?v.dst.py+v.bzscroll.getNow()*v.dst.ay:v.newscrolly;var n=i-o;(0>n&&i<v.newscrolly||n>0&&i>v.newscrolly)&&(i=v.newscrolly),v.setScrollTop(i),i==v.newscrolly&&(t=1)}else t=1;var s=e=v.getScrollLeft();if(v.dst.ax){s=v.bzscroll?v.dst.px+v.bzscroll.getNow()*v.dst.ax:v.newscrollx;var n=s-e;(0>n&&s<v.newscrollx||n>0&&s>v.newscrollx)&&(s=v.newscrollx),v.setScrollLeft(s),s==v.newscrollx&&(t+=1)}else t+=1;2==t?(v.timer=0,v.cursorfreezed=!1,v.bzscroll=!1,v.scrollrunning=!1,0>i?i=0:i>v.page.maxh&&(i=v.page.maxh),0>s?s=0:s>v.page.maxw&&(s=v.page.maxw),s!=v.newscrollx||i!=v.newscrolly?v.doScrollPos(s,i):v.onscrollend&&v.triggerScrollEnd()):v.timer=d(r)||1}var o="undefined"==typeof o||o===!1?v.getScrollTop(!0):o;if(v.timer&&v.newscrolly==o&&v.newscrollx==e)return!0;v.timer&&u(v.timer),v.timer=0;var i=v.getScrollTop(),n=v.getScrollLeft();((v.newscrolly-i)*(o-i)<0||(v.newscrollx-n)*(e-n)<0)&&v.cancelScroll(),v.newscrolly=o,v.newscrollx=e,v.bouncescroll&&v.rail.visibility||(v.newscrolly<0?v.newscrolly=0:v.newscrolly>v.page.maxh&&(v.newscrolly=v.page.maxh)),v.bouncescroll&&v.railh.visibility||(v.newscrollx<0?v.newscrollx=0:v.newscrollx>v.page.maxw&&(v.newscrollx=v.page.maxw)),v.dst={},v.dst.x=e-n,v.dst.y=o-i,v.dst.px=n,v.dst.py=i;var s=Math.round(Math.sqrt(Math.pow(v.dst.x,2)+Math.pow(v.dst.y,2)));v.dst.ax=v.dst.x/s,v.dst.ay=v.dst.y/s;var l=0,a=s;0==v.dst.x?(l=i,a=o,v.dst.ay=1,v.dst.py=0):0==v.dst.y&&(l=n,a=e,v.dst.ax=1,v.dst.px=0);var c=v.getTransitionSpeed(s);if(t&&1>=t&&(c*=t),c>0?v.bzscroll=v.bzscroll?v.bzscroll.update(a,c):new z(l,a,c,0,1,0,1):v.bzscroll=!1,!v.timer){(i==v.page.maxh&&o>=v.page.maxh||n==v.page.maxw&&e>=v.page.maxw)&&v.checkContentSize();var h=1;if(v.cancelAnimationFrame=!1,v.timer=1,v.onscrollstart&&!v.scrollrunning){var p={type:"scrollstart",current:{x:n,y:i},request:{x:e,y:o},end:{x:v.newscrollx,y:v.newscrolly},speed:c};v.onscrollstart.call(v,p)}r(),(i==v.page.maxh&&o>=i||n==v.page.maxw&&e>=n)&&v.checkContentSize(),v.noticeCursor()}},this.cancelScroll=function(){return v.timer&&u(v.timer),v.timer=0,v.bzscroll=!1,v.scrollrunning=!1,v}):(this.doScrollLeft=function(e,o){var t=v.getScrollTop();v.doScrollPos(e,t,o)},this.doScrollTop=function(e,o){var t=v.getScrollLeft();v.doScrollPos(t,e,o)},this.doScrollPos=function(e,o,t){var r=e>v.page.maxw?v.page.maxw:e;0>r&&(r=0);var i=o>v.page.maxh?v.page.maxh:o;0>i&&(i=0),v.synched("scroll",function(){v.setScrollTop(i),v.setScrollLeft(r)})},this.cancelScroll=function(){}),this.doScrollBy=function(e,o){var t=0;if(o)t=Math.floor((v.scroll.y-e)*v.scrollratio.y);else{var r=v.timer?v.newscrolly:v.getScrollTop(!0);t=r-e}if(v.bouncescroll){var i=Math.round(v.view.h/2);-i>t?t=-i:t>v.page.maxh+i&&(t=v.page.maxh+i)}v.cursorfreezed=!1;var n=v.getScrollTop(!0);return 0>t&&0>=n?v.noticeCursor():t>v.page.maxh&&n>=v.page.maxh?(v.checkContentSize(),v.noticeCursor()):void v.doScrollTop(t)},this.doScrollLeftBy=function(e,o){var t=0;if(o)t=Math.floor((v.scroll.x-e)*v.scrollratio.x);else{var r=v.timer?v.newscrollx:v.getScrollLeft(!0);t=r-e}if(v.bouncescroll){var i=Math.round(v.view.w/2);-i>t?t=-i:t>v.page.maxw+i&&(t=v.page.maxw+i)}v.cursorfreezed=!1;var n=v.getScrollLeft(!0);return 0>t&&0>=n?v.noticeCursor():t>v.page.maxw&&n>=v.page.maxw?v.noticeCursor():void v.doScrollLeft(t)},this.doScrollTo=function(e,o){var t=o?Math.round(e*v.scrollratio.y):e;0>t?t=0:t>v.page.maxh&&(t=v.page.maxh),v.cursorfreezed=!1,v.doScrollTop(e)},this.checkContentSize=function(){var e=v.getContentSize();(e.h!=v.page.h||e.w!=v.page.w)&&v.resize(!1,e)},v.onscroll=function(e){v.rail.drag||v.cursorfreezed||v.synched("scroll",function(){v.scroll.y=Math.round(v.getScrollTop()*(1/v.scrollratio.y)),v.railh&&(v.scroll.x=Math.round(v.getScrollLeft()*(1/v.scrollratio.x))),v.noticeCursor()})},v.bind(v.docscroll,"scroll",v.onscroll),this.doZoomIn=function(e){if(!v.zoomactive){v.zoomactive=!0,v.zoomrestore={style:{}};var o=["position","top","left","zIndex","backgroundColor","marginTop","marginBottom","marginLeft","marginRight"],t=v.win[0].style;for(var r in o){var i=o[r];v.zoomrestore.style[i]="undefined"!=typeof t[i]?t[i]:""}v.zoomrestore.style.width=v.win.css("width"),v.zoomrestore.style.height=v.win.css("height"),v.zoomrestore.padding={w:v.win.outerWidth()-v.win.width(),h:v.win.outerHeight()-v.win.height()},x.isios4&&(v.zoomrestore.scrollTop=a(window).scrollTop(),a(window).scrollTop(0)),v.win.css({position:x.isios4?"absolute":"fixed",top:0,left:0,"z-index":l+100,margin:"0px"});var n=v.win.css("backgroundColor");return(""==n||/transparent|rgba\(0, 0, 0, 0\)|rgba\(0,0,0,0\)/.test(n))&&v.win.css("backgroundColor","#fff"),v.rail.css({"z-index":l+101}),v.zoom.css({"z-index":l+102}),v.zoom.css("backgroundPosition","0px -18px"),v.resizeZoom(),v.onzoomin&&v.onzoomin.call(v),v.cancelEvent(e)}},this.doZoomOut=function(e){return v.zoomactive?(v.zoomactive=!1,v.win.css("margin",""),v.win.css(v.zoomrestore.style),x.isios4&&a(window).scrollTop(v.zoomrestore.scrollTop),v.rail.css({"z-index":v.zindex}),v.zoom.css({"z-index":v.zindex}),v.zoomrestore=!1,v.zoom.css("backgroundPosition","0px 0px"),v.onResize(),v.onzoomout&&v.onzoomout.call(v),v.cancelEvent(e)):void 0},this.doZoom=function(e){return v.zoomactive?v.doZoomOut(e):v.doZoomIn(e)},this.resizeZoom=function(){if(v.zoomactive){var e=v.getScrollTop();v.win.css({width:a(window).width()-v.zoomrestore.padding.w+"px",height:a(window).height()-v.zoomrestore.padding.h+"px"}),v.onResize(),v.setScrollTop(Math.min(v.page.maxh,e))}},this.init(),a.nicescroll.push(this)},b=function(e){var o=this;this.nc=e,this.lastx=0,this.lasty=0,this.speedx=0,this.speedy=0,this.lasttime=0,this.steptime=0,this.snapx=!1,this.snapy=!1,this.demulx=0,this.demuly=0,this.lastscrollx=-1,this.lastscrolly=-1,this.chkx=0,this.chky=0,this.timer=0,this.time=function(){return+new Date},this.reset=function(e,t){o.stop();var r=o.time();o.steptime=0,o.lasttime=r,o.speedx=0,o.speedy=0,o.lastx=e,o.lasty=t,o.lastscrollx=-1,o.lastscrolly=-1},this.update=function(e,t){var r=o.time();o.steptime=r-o.lasttime,o.lasttime=r;var i=t-o.lasty,n=e-o.lastx,s=o.nc.getScrollTop(),l=o.nc.getScrollLeft(),a=s+i,c=l+n;o.snapx=0>c||c>o.nc.page.maxw,o.snapy=0>a||a>o.nc.page.maxh,o.speedx=n,o.speedy=i,o.lastx=e,o.lasty=t},this.stop=function(){o.nc.unsynched("domomentum2d"),o.timer&&clearTimeout(o.timer),o.timer=0,o.lastscrollx=-1,o.lastscrolly=-1},this.doSnapy=function(e,t){var r=!1;0>t?(t=0,r=!0):t>o.nc.page.maxh&&(t=o.nc.page.maxh,r=!0),0>e?(e=0,r=!0):e>o.nc.page.maxw&&(e=o.nc.page.maxw,r=!0),r?o.nc.doScrollPos(e,t,o.nc.opt.snapbackspeed):o.nc.triggerScrollEnd()},this.doMomentum=function(e){var t=o.time(),r=e?t+e:o.lasttime,i=o.nc.getScrollLeft(),n=o.nc.getScrollTop(),s=o.nc.page.maxh,l=o.nc.page.maxw;o.speedx=l>0?Math.min(60,o.speedx):0,o.speedy=s>0?Math.min(60,o.speedy):0;var a=r&&60>=t-r;(0>n||n>s||0>i||i>l)&&(a=!1);var c=o.speedy&&a?o.speedy:!1,d=o.speedx&&a?o.speedx:!1;if(c||d){var u=Math.max(16,o.steptime);if(u>50){var h=u/50;o.speedx*=h,o.speedy*=h,u=50}o.demulxy=0,o.lastscrollx=o.nc.getScrollLeft(),o.chkx=o.lastscrollx,o.lastscrolly=o.nc.getScrollTop(),o.chky=o.lastscrolly;var p=o.lastscrollx,m=o.lastscrolly,f=function(){var e=o.time()-t>600?.04:.02;o.speedx&&(p=Math.floor(o.lastscrollx-o.speedx*(1-o.demulxy)),o.lastscrollx=p,(0>p||p>l)&&(e=.1)),o.speedy&&(m=Math.floor(o.lastscrolly-o.speedy*(1-o.demulxy)),o.lastscrolly=m,(0>m||m>s)&&(e=.1)),o.demulxy=Math.min(1,o.demulxy+e),o.nc.synched("domomentum2d",function(){if(o.speedx){var e=o.nc.getScrollLeft();e!=o.chkx&&o.stop(),o.chkx=p,o.nc.setScrollLeft(p)}if(o.speedy){var t=o.nc.getScrollTop();t!=o.chky&&o.stop(),o.chky=m,o.nc.setScrollTop(m)}o.timer||(o.nc.hideCursor(),o.doSnapy(p,m))}),o.demulxy<1?o.timer=setTimeout(f,u):(o.stop(),o.nc.hideCursor(),o.doSnapy(p,m))};f()}else o.doSnapy(o.nc.getScrollLeft(),o.nc.getScrollTop())}},y=e.fn.scrollTop;e.cssHooks.pageYOffset={get:function(e,o,t){var r=a.data(e,"__nicescroll")||!1;return r&&r.ishwscroll?r.getScrollTop():y.call(e)},set:function(e,o){var t=a.data(e,"__nicescroll")||!1;return t&&t.ishwscroll?t.setScrollTop(parseInt(o)):y.call(e,o),this}},e.fn.scrollTop=function(e){if("undefined"==typeof e){var o=this[0]?a.data(this[0],"__nicescroll")||!1:!1;return o&&o.ishwscroll?o.getScrollTop():y.call(this)}return this.each(function(){var o=a.data(this,"__nicescroll")||!1;o&&o.ishwscroll?o.setScrollTop(parseInt(e)):y.call(a(this),e)})};var x=e.fn.scrollLeft;a.cssHooks.pageXOffset={get:function(e,o,t){var r=a.data(e,"__nicescroll")||!1;return r&&r.ishwscroll?r.getScrollLeft():x.call(e)},set:function(e,o){var t=a.data(e,"__nicescroll")||!1;return t&&t.ishwscroll?t.setScrollLeft(parseInt(o)):x.call(e,o),this}},e.fn.scrollLeft=function(e){if("undefined"==typeof e){var o=this[0]?a.data(this[0],"__nicescroll")||!1:!1;return o&&o.ishwscroll?o.getScrollLeft():x.call(this)}return this.each(function(){var o=a.data(this,"__nicescroll")||!1;o&&o.ishwscroll?o.setScrollLeft(parseInt(e)):x.call(a(this),e)})};var S=function(e){var o=this;if(this.length=0,this.name="nicescrollarray",this.each=function(e){for(var t=0,r=0;t<o.length;t++)e.call(o[t],r++);return o},this.push=function(e){o[o.length]=e,o.length++},this.eq=function(e){return o[e]},e)for(var t=0;t<e.length;t++){var r=a.data(e[t],"__nicescroll")||!1;r&&(this[this.length]=r,this.length++)}return this};t(S.prototype,["show","hide","toggle","onResize","resize","remove","stop","doScrollPos"],function(e,o){e[o]=function(){var e=arguments;return this.each(function(){this[o].apply(this,e)})}}),e.fn.getNiceScroll=function(e){if("undefined"==typeof e)return new S(this);var o=this[e]&&a.data(this[e],"__nicescroll")||!1;return o},e.extend(e.expr[":"],{nicescroll:function(e){return a.data(e,"__nicescroll")?!0:!1}}),a.fn.niceScroll=function(e,o){"undefined"==typeof o&&("object"!=typeof e||"jquery"in e||(o=e,e=!1)),o=a.extend({},o);var t=new S;"undefined"==typeof o&&(o={}),e&&(o.doc=a(e),o.win=a(this));var r=!("doc"in o);return r||"win"in o||(o.win=a(this)),this.each(function(){var e=a(this).data("__nicescroll")||!1;e||(o.doc=r?a(this):o.doc,e=new v(o,a(this)),a(this).data("__nicescroll",e)),t.push(e)}),1==t.length?t[0]:t},window.NiceScroll={getjQuery:function(){return e}},a.nicescroll||(a.nicescroll=new S,a.nicescroll.options=f)});


/**
 * Waypoints
 * required for: Chart, Progress, Skills
 * 
 * 4.0.1 | Caleb Troughton | https://github.com/imakewebthings/waypoints | Licensed under the MIT license.
 */
!function(){"use strict";function t(o){if(!o)throw new Error("No options passed to Waypoint constructor");if(!o.element)throw new Error("No element option passed to Waypoint constructor");if(!o.handler)throw new Error("No handler option passed to Waypoint constructor");this.key="waypoint-"+e,this.options=t.Adapter.extend({},t.defaults,o),this.element=this.options.element,this.adapter=new t.Adapter(this.element),this.callback=o.handler,this.axis=this.options.horizontal?"horizontal":"vertical",this.enabled=this.options.enabled,this.triggerPoint=null,this.group=t.Group.findOrCreate({name:this.options.group,axis:this.axis}),this.context=t.Context.findOrCreateByElement(this.options.context),t.offsetAliases[this.options.offset]&&(this.options.offset=t.offsetAliases[this.options.offset]),this.group.add(this),this.context.add(this),i[this.key]=this,e+=1}var e=0,i={};t.prototype.queueTrigger=function(t){this.group.queueTrigger(this,t)},t.prototype.trigger=function(t){this.enabled&&this.callback&&this.callback.apply(this,t)},t.prototype.destroy=function(){this.context.remove(this),this.group.remove(this),delete i[this.key]},t.prototype.disable=function(){return this.enabled=!1,this},t.prototype.enable=function(){return this.context.refresh(),this.enabled=!0,this},t.prototype.next=function(){return this.group.next(this)},t.prototype.previous=function(){return this.group.previous(this)},t.invokeAll=function(t){var e=[];for(var o in i)e.push(i[o]);for(var n=0,r=e.length;r>n;n++)e[n][t]()},t.destroyAll=function(){t.invokeAll("destroy")},t.disableAll=function(){t.invokeAll("disable")},t.enableAll=function(){t.Context.refreshAll();for(var e in i)i[e].enabled=!0;return this},t.refreshAll=function(){t.Context.refreshAll()},t.viewportHeight=function(){return window.innerHeight||document.documentElement.clientHeight},t.viewportWidth=function(){return document.documentElement.clientWidth},t.adapters=[],t.defaults={context:window,continuous:!0,enabled:!0,group:"default",horizontal:!1,offset:0},t.offsetAliases={"bottom-in-view":function(){return this.context.innerHeight()-this.adapter.outerHeight()},"right-in-view":function(){return this.context.innerWidth()-this.adapter.outerWidth()}},window.Waypoint=t}(),function(){"use strict";function t(t){window.setTimeout(t,1e3/60)}function e(t){this.element=t,this.Adapter=n.Adapter,this.adapter=new this.Adapter(t),this.key="waypoint-context-"+i,this.didScroll=!1,this.didResize=!1,this.oldScroll={x:this.adapter.scrollLeft(),y:this.adapter.scrollTop()},this.waypoints={vertical:{},horizontal:{}},t.waypointContextKey=this.key,o[t.waypointContextKey]=this,i+=1,n.windowContext||(n.windowContext=!0,n.windowContext=new e(window)),this.createThrottledScrollHandler(),this.createThrottledResizeHandler()}var i=0,o={},n=window.Waypoint,r=window.onload;e.prototype.add=function(t){var e=t.options.horizontal?"horizontal":"vertical";this.waypoints[e][t.key]=t,this.refresh()},e.prototype.checkEmpty=function(){var t=this.Adapter.isEmptyObject(this.waypoints.horizontal),e=this.Adapter.isEmptyObject(this.waypoints.vertical),i=this.element==this.element.window;t&&e&&!i&&(this.adapter.off(".waypoints"),delete o[this.key])},e.prototype.createThrottledResizeHandler=function(){function t(){e.handleResize(),e.didResize=!1}var e=this;this.adapter.on("resize.waypoints",function(){e.didResize||(e.didResize=!0,n.requestAnimationFrame(t))})},e.prototype.createThrottledScrollHandler=function(){function t(){e.handleScroll(),e.didScroll=!1}var e=this;this.adapter.on("scroll.waypoints",function(){(!e.didScroll||n.isTouch)&&(e.didScroll=!0,n.requestAnimationFrame(t))})},e.prototype.handleResize=function(){n.Context.refreshAll()},e.prototype.handleScroll=function(){var t={},e={horizontal:{newScroll:this.adapter.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.adapter.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};for(var i in e){var o=e[i],n=o.newScroll>o.oldScroll,r=n?o.forward:o.backward;for(var s in this.waypoints[i]){var a=this.waypoints[i][s];if(null!==a.triggerPoint){var l=o.oldScroll<a.triggerPoint,h=o.newScroll>=a.triggerPoint,p=l&&h,u=!l&&!h;(p||u)&&(a.queueTrigger(r),t[a.group.id]=a.group)}}}for(var c in t)t[c].flushTriggers();this.oldScroll={x:e.horizontal.newScroll,y:e.vertical.newScroll}},e.prototype.innerHeight=function(){return this.element==this.element.window?n.viewportHeight():this.adapter.innerHeight()},e.prototype.remove=function(t){delete this.waypoints[t.axis][t.key],this.checkEmpty()},e.prototype.innerWidth=function(){return this.element==this.element.window?n.viewportWidth():this.adapter.innerWidth()},e.prototype.destroy=function(){var t=[];for(var e in this.waypoints)for(var i in this.waypoints[e])t.push(this.waypoints[e][i]);for(var o=0,n=t.length;n>o;o++)t[o].destroy()},e.prototype.refresh=function(){var t,e=this.element==this.element.window,i=e?void 0:this.adapter.offset(),o={};this.handleScroll(),t={horizontal:{contextOffset:e?0:i.left,contextScroll:e?0:this.oldScroll.x,contextDimension:this.innerWidth(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:e?0:i.top,contextScroll:e?0:this.oldScroll.y,contextDimension:this.innerHeight(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};for(var r in t){var s=t[r];for(var a in this.waypoints[r]){var l,h,p,u,c,d=this.waypoints[r][a],f=d.options.offset,w=d.triggerPoint,y=0,g=null==w;d.element!==d.element.window&&(y=d.adapter.offset()[s.offsetProp]),"function"==typeof f?f=f.apply(d):"string"==typeof f&&(f=parseFloat(f),d.options.offset.indexOf("%")>-1&&(f=Math.ceil(s.contextDimension*f/100))),l=s.contextScroll-s.contextOffset,d.triggerPoint=Math.floor(y+l-f),h=w<s.oldScroll,p=d.triggerPoint>=s.oldScroll,u=h&&p,c=!h&&!p,!g&&u?(d.queueTrigger(s.backward),o[d.group.id]=d.group):!g&&c?(d.queueTrigger(s.forward),o[d.group.id]=d.group):g&&s.oldScroll>=d.triggerPoint&&(d.queueTrigger(s.forward),o[d.group.id]=d.group)}}return n.requestAnimationFrame(function(){for(var t in o)o[t].flushTriggers()}),this},e.findOrCreateByElement=function(t){return e.findByElement(t)||new e(t)},e.refreshAll=function(){for(var t in o)o[t].refresh()},e.findByElement=function(t){return o[t.waypointContextKey]},window.onload=function(){r&&r(),e.refreshAll()},n.requestAnimationFrame=function(e){var i=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||t;i.call(window,e)},n.Context=e}(),function(){"use strict";function t(t,e){return t.triggerPoint-e.triggerPoint}function e(t,e){return e.triggerPoint-t.triggerPoint}function i(t){this.name=t.name,this.axis=t.axis,this.id=this.name+"-"+this.axis,this.waypoints=[],this.clearTriggerQueues(),o[this.axis][this.name]=this}var o={vertical:{},horizontal:{}},n=window.Waypoint;i.prototype.add=function(t){this.waypoints.push(t)},i.prototype.clearTriggerQueues=function(){this.triggerQueues={up:[],down:[],left:[],right:[]}},i.prototype.flushTriggers=function(){for(var i in this.triggerQueues){var o=this.triggerQueues[i],n="up"===i||"left"===i;o.sort(n?e:t);for(var r=0,s=o.length;s>r;r+=1){var a=o[r];(a.options.continuous||r===o.length-1)&&a.trigger([i])}}this.clearTriggerQueues()},i.prototype.next=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints),o=i===this.waypoints.length-1;return o?null:this.waypoints[i+1]},i.prototype.previous=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints);return i?this.waypoints[i-1]:null},i.prototype.queueTrigger=function(t,e){this.triggerQueues[e].push(t)},i.prototype.remove=function(t){var e=n.Adapter.inArray(t,this.waypoints);e>-1&&this.waypoints.splice(e,1)},i.prototype.first=function(){return this.waypoints[0]},i.prototype.last=function(){return this.waypoints[this.waypoints.length-1]},i.findOrCreate=function(t){return o[t.axis][t.name]||new i(t)},n.Group=i}(),function(){"use strict";function t(t){this.$element=e(t)}var e=window.jQuery,i=window.Waypoint;e.each(["innerHeight","innerWidth","off","offset","on","outerHeight","outerWidth","scrollLeft","scrollTop"],function(e,i){t.prototype[i]=function(){var t=Array.prototype.slice.call(arguments);return this.$element[i].apply(this.$element,t)}}),e.each(["extend","inArray","isEmptyObject"],function(i,o){t[o]=e[o]}),i.adapters.push({name:"jquery",Adapter:t}),i.Adapter=t}(),function(){"use strict";function t(t){return function(){var i=[],o=arguments[0];return t.isFunction(arguments[0])&&(o=t.extend({},arguments[1]),o.handler=arguments[0]),this.each(function(){var n=t.extend({},o,{element:this});"string"==typeof n.context&&(n.context=t(this).closest(n.context)[0]),i.push(new e(n))}),i}}var e=window.Waypoint;window.jQuery&&(window.jQuery.fn.waypoint=t(window.jQuery)),window.Zepto&&(window.Zepto.fn.waypoint=t(window.Zepto))}();


/*
 Anime
 2017 Julian Garnier
 Released under the MIT license
*/
var $jscomp$this=this;
(function(u,r){"function"===typeof define&&define.amd?define([],r):"object"===typeof module&&module.exports?module.exports=r():u.anime=r()})(this,function(){function u(a){if(!g.col(a))try{return document.querySelectorAll(a)}catch(b){}}function r(a){return a.reduce(function(a,c){return a.concat(g.arr(c)?r(c):c)},[])}function v(a){if(g.arr(a))return a;g.str(a)&&(a=u(a)||a);return a instanceof NodeList||a instanceof HTMLCollection?[].slice.call(a):[a]}function E(a,b){return a.some(function(a){return a===b})}
function z(a){var b={},c;for(c in a)b[c]=a[c];return b}function F(a,b){var c=z(a),d;for(d in a)c[d]=b.hasOwnProperty(d)?b[d]:a[d];return c}function A(a,b){var c=z(a),d;for(d in b)c[d]=g.und(a[d])?b[d]:a[d];return c}function R(a){a=a.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i,function(a,b,c,h){return b+b+c+c+h+h});var b=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(a);a=parseInt(b[1],16);var c=parseInt(b[2],16),b=parseInt(b[3],16);return"rgb("+a+","+c+","+b+")"}function S(a){function b(a,b,c){0>
c&&(c+=1);1<c&&--c;return c<1/6?a+6*(b-a)*c:.5>c?b:c<2/3?a+(b-a)*(2/3-c)*6:a}var c=/hsl\((\d+),\s*([\d.]+)%,\s*([\d.]+)%\)/g.exec(a);a=parseInt(c[1])/360;var d=parseInt(c[2])/100,c=parseInt(c[3])/100;if(0==d)d=c=a=c;else{var e=.5>c?c*(1+d):c+d-c*d,k=2*c-e,d=b(k,e,a+1/3),c=b(k,e,a);a=b(k,e,a-1/3)}return"rgb("+255*d+","+255*c+","+255*a+")"}function w(a){if(a=/([\+\-]?[0-9#\.]+)(%|px|pt|em|rem|in|cm|mm|ex|pc|vw|vh|deg|rad|turn)?/.exec(a))return a[2]}function T(a){if(-1<a.indexOf("translate"))return"px";
if(-1<a.indexOf("rotate")||-1<a.indexOf("skew"))return"deg"}function G(a,b){return g.fnc(a)?a(b.target,b.id,b.total):a}function B(a,b){if(b in a.style)return getComputedStyle(a).getPropertyValue(b.replace(/([a-z])([A-Z])/g,"$1-$2").toLowerCase())||"0"}function H(a,b){if(g.dom(a)&&E(U,b))return"transform";if(g.dom(a)&&(a.getAttribute(b)||g.svg(a)&&a[b]))return"attribute";if(g.dom(a)&&"transform"!==b&&B(a,b))return"css";if(null!=a[b])return"object"}function V(a,b){var c=T(b),c=-1<b.indexOf("scale")?
1:0+c;a=a.style.transform;if(!a)return c;for(var d=[],e=[],k=[],h=/(\w+)\((.+?)\)/g;d=h.exec(a);)e.push(d[1]),k.push(d[2]);a=k.filter(function(a,c){return e[c]===b});return a.length?a[0]:c}function I(a,b){switch(H(a,b)){case "transform":return V(a,b);case "css":return B(a,b);case "attribute":return a.getAttribute(b)}return a[b]||0}function J(a,b){var c=/^(\*=|\+=|-=)/.exec(a);if(!c)return a;b=parseFloat(b);a=parseFloat(a.replace(c[0],""));switch(c[0][0]){case "+":return b+a;case "-":return b-a;case "*":return b*
a}}function C(a){return g.obj(a)&&a.hasOwnProperty("totalLength")}function W(a,b){function c(c){c=void 0===c?0:c;return a.el.getPointAtLength(1<=b+c?b+c:0)}var d=c(),e=c(-1),k=c(1);switch(a.property){case "x":return d.x;case "y":return d.y;case "angle":return 180*Math.atan2(k.y-e.y,k.x-e.x)/Math.PI}}function K(a,b){var c=/-?\d*\.?\d+/g;a=C(a)?a.totalLength:a;if(g.col(a))b=g.rgb(a)?a:g.hex(a)?R(a):g.hsl(a)?S(a):void 0;else{var d=w(a);a=d?a.substr(0,a.length-d.length):a;b=b?a+b:a}b+="";return{original:b,
numbers:b.match(c)?b.match(c).map(Number):[0],strings:b.split(c)}}function X(a,b){return b.reduce(function(b,d,e){return b+a[e-1]+d})}function L(a){return(a?r(g.arr(a)?a.map(v):v(a)):[]).filter(function(a,c,d){return d.indexOf(a)===c})}function Y(a){var b=L(a);return b.map(function(a,d){return{target:a,id:d,total:b.length}})}function Z(a,b){var c=z(b);if(g.arr(a)){var d=a.length;2!==d||g.obj(a[0])?g.fnc(b.duration)||(c.duration=b.duration/d):a={value:a}}return v(a).map(function(a,c){c=c?0:b.delay;
a=g.obj(a)&&!C(a)?a:{value:a};g.und(a.delay)&&(a.delay=c);return a}).map(function(a){return A(a,c)})}function aa(a,b){var c={},d;for(d in a){var e=G(a[d],b);g.arr(e)&&(e=e.map(function(a){return G(a,b)}),1===e.length&&(e=e[0]));c[d]=e}c.duration=parseFloat(c.duration);c.delay=parseFloat(c.delay);return c}function ba(a){return g.arr(a)?x.apply(this,a):M[a]}function ca(a,b){var c;return a.tweens.map(function(d){d=aa(d,b);var e=d.value,k=I(b.target,a.name),h=c?c.to.original:k,h=g.arr(e)?e[0]:h,n=J(g.arr(e)?
e[1]:e,h),k=w(n)||w(h)||w(k);d.isPath=C(e);d.from=K(h,k);d.to=K(n,k);d.start=c?c.end:a.offset;d.end=d.start+d.delay+d.duration;d.easing=ba(d.easing);d.elasticity=(1E3-Math.min(Math.max(d.elasticity,1),999))/1E3;g.col(d.from.original)&&(d.round=1);return c=d})}function da(a,b){return r(a.map(function(a){return b.map(function(b){var c=H(a.target,b.name);if(c){var d=ca(b,a);b={type:c,property:b.name,animatable:a,tweens:d,duration:d[d.length-1].end,delay:d[0].delay}}else b=void 0;return b})})).filter(function(a){return!g.und(a)})}
function N(a,b,c){var d="delay"===a?Math.min:Math.max;return b.length?d.apply(Math,b.map(function(b){return b[a]})):c[a]}function ea(a){var b=F(fa,a),c=F(ga,a),d=Y(a.targets),e=[],g=A(b,c),h;for(h in a)g.hasOwnProperty(h)||"targets"===h||e.push({name:h,offset:g.offset,tweens:Z(a[h],c)});a=da(d,e);return A(b,{animatables:d,animations:a,duration:N("duration",a,c),delay:N("delay",a,c)})}function m(a){function b(){return window.Promise&&new Promise(function(a){return P=a})}function c(a){return f.reversed?
f.duration-a:a}function d(a){for(var b=0,c={},d=f.animations,e={};b<d.length;){var g=d[b],h=g.animatable,n=g.tweens;e.tween=n.filter(function(b){return a<b.end})[0]||n[n.length-1];e.isPath$0=e.tween.isPath;e.round=e.tween.round;e.eased=e.tween.easing(Math.min(Math.max(a-e.tween.start-e.tween.delay,0),e.tween.duration)/e.tween.duration,e.tween.elasticity);n=X(e.tween.to.numbers.map(function(a){return function(b,c){c=a.isPath$0?0:a.tween.from.numbers[c];b=c+a.eased*(b-c);a.isPath$0&&(b=W(a.tween.value,
b));a.round&&(b=Math.round(b*a.round)/a.round);return b}}(e)),e.tween.to.strings);ha[g.type](h.target,g.property,n,c,h.id);g.currentValue=n;b++;e={isPath$0:e.isPath$0,tween:e.tween,eased:e.eased,round:e.round}}if(c)for(var k in c)D||(D=B(document.body,"transform")?"transform":"-webkit-transform"),f.animatables[k].target.style[D]=c[k].join(" ");f.currentTime=a;f.progress=a/f.duration*100}function e(a){if(f[a])f[a](f)}function g(){f.remaining&&!0!==f.remaining&&f.remaining--}function h(a){var h=f.duration,
k=f.offset,m=f.delay,O=f.currentTime,p=f.reversed,q=c(a),q=Math.min(Math.max(q,0),h);q>k&&q<h?(d(q),!f.began&&q>=m&&(f.began=!0,e("begin")),e("run")):(q<=k&&0!==O&&(d(0),p&&g()),q>=h&&O!==h&&(d(h),p||g()));a>=h&&(f.remaining?(t=n,"alternate"===f.direction&&(f.reversed=!f.reversed)):(f.pause(),P(),Q=b(),f.completed||(f.completed=!0,e("complete"))),l=0);if(f.children)for(a=f.children,h=0;h<a.length;h++)a[h].seek(q);e("update")}a=void 0===a?{}:a;var n,t,l=0,P=null,Q=b(),f=ea(a);f.reset=function(){var a=
f.direction,b=f.loop;f.currentTime=0;f.progress=0;f.paused=!0;f.began=!1;f.completed=!1;f.reversed="reverse"===a;f.remaining="alternate"===a&&1===b?2:b};f.tick=function(a){n=a;t||(t=n);h((l+n-t)*m.speed)};f.seek=function(a){h(c(a))};f.pause=function(){var a=p.indexOf(f);-1<a&&p.splice(a,1);f.paused=!0};f.play=function(){f.paused&&(f.paused=!1,t=0,l=f.completed?0:c(f.currentTime),p.push(f),y||ia())};f.reverse=function(){f.reversed=!f.reversed;t=0;l=c(f.currentTime)};f.restart=function(){f.pause();
f.reset();f.play()};f.finished=Q;f.reset();f.autoplay&&f.play();return f}var fa={update:void 0,begin:void 0,run:void 0,complete:void 0,loop:1,direction:"normal",autoplay:!0,offset:0},ga={duration:1E3,delay:0,easing:"easeOutElastic",elasticity:500,round:0},U="translateX translateY translateZ rotate rotateX rotateY rotateZ scale scaleX scaleY scaleZ skewX skewY".split(" "),D,g={arr:function(a){return Array.isArray(a)},obj:function(a){return-1<Object.prototype.toString.call(a).indexOf("Object")},svg:function(a){return a instanceof
SVGElement},dom:function(a){return a.nodeType||g.svg(a)},str:function(a){return"string"===typeof a},fnc:function(a){return"function"===typeof a},und:function(a){return"undefined"===typeof a},hex:function(a){return/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(a)},rgb:function(a){return/^rgb/.test(a)},hsl:function(a){return/^hsl/.test(a)},col:function(a){return g.hex(a)||g.rgb(a)||g.hsl(a)}},x=function(){function a(a,c,d){return(((1-3*d+3*c)*a+(3*d-6*c))*a+3*c)*a}return function(b,c,d,e){if(0<=b&&1>=b&&
0<=d&&1>=d){var g=new Float32Array(11);if(b!==c||d!==e)for(var h=0;11>h;++h)g[h]=a(.1*h,b,d);return function(h){if(b===c&&d===e)return h;if(0===h)return 0;if(1===h)return 1;for(var k=0,l=1;10!==l&&g[l]<=h;++l)k+=.1;--l;var l=k+(h-g[l])/(g[l+1]-g[l])*.1,n=3*(1-3*d+3*b)*l*l+2*(3*d-6*b)*l+3*b;if(.001<=n){for(k=0;4>k;++k){n=3*(1-3*d+3*b)*l*l+2*(3*d-6*b)*l+3*b;if(0===n)break;var m=a(l,b,d)-h,l=l-m/n}h=l}else if(0===n)h=l;else{var l=k,k=k+.1,f=0;do m=l+(k-l)/2,n=a(m,b,d)-h,0<n?k=m:l=m;while(1e-7<Math.abs(n)&&
10>++f);h=m}return a(h,c,e)}}}}(),M=function(){function a(a,b){return 0===a||1===a?a:-Math.pow(2,10*(a-1))*Math.sin(2*(a-1-b/(2*Math.PI)*Math.asin(1))*Math.PI/b)}var b="Quad Cubic Quart Quint Sine Expo Circ Back Elastic".split(" "),c={In:[[.55,.085,.68,.53],[.55,.055,.675,.19],[.895,.03,.685,.22],[.755,.05,.855,.06],[.47,0,.745,.715],[.95,.05,.795,.035],[.6,.04,.98,.335],[.6,-.28,.735,.045],a],Out:[[.25,.46,.45,.94],[.215,.61,.355,1],[.165,.84,.44,1],[.23,1,.32,1],[.39,.575,.565,1],[.19,1,.22,1],
[.075,.82,.165,1],[.175,.885,.32,1.275],function(b,c){return 1-a(1-b,c)}],InOut:[[.455,.03,.515,.955],[.645,.045,.355,1],[.77,0,.175,1],[.86,0,.07,1],[.445,.05,.55,.95],[1,0,0,1],[.785,.135,.15,.86],[.68,-.55,.265,1.55],function(b,c){return.5>b?a(2*b,c)/2:1-a(-2*b+2,c)/2}]},d={linear:x(.25,.25,.75,.75)},e={},k;for(k in c)e.type=k,c[e.type].forEach(function(a){return function(c,e){d["ease"+a.type+b[e]]=g.fnc(c)?c:x.apply($jscomp$this,c)}}(e)),e={type:e.type};return d}(),ha={css:function(a,b,c){return a.style[b]=
c},attribute:function(a,b,c){return a.setAttribute(b,c)},object:function(a,b,c){return a[b]=c},transform:function(a,b,c,d,e){d[e]||(d[e]=[]);d[e].push(b+"("+c+")")}},p=[],y=0,ia=function(){function a(){y=requestAnimationFrame(b)}function b(b){var c=p.length;if(c){for(var e=0;e<c;)p[e]&&p[e].tick(b),e++;a()}else cancelAnimationFrame(y),y=0}return a}();m.version="2.0.1";m.speed=1;m.running=p;m.remove=function(a){a=L(a);for(var b=p.length-1;0<=b;b--)for(var c=p[b],d=c.animations,e=d.length-1;0<=e;e--)E(a,
d[e].animatable.target)&&(d.splice(e,1),d.length||c.pause())};m.getValue=I;m.path=function(a,b){var c=g.str(a)?u(a)[0]:a,d=b||100;return function(a){return{el:c,property:a,totalLength:c.getTotalLength()*(d/100)}}};m.setDashoffset=function(a){var b=a.getTotalLength();a.setAttribute("stroke-dasharray",b);return b};m.bezier=x;m.easings=M;m.timeline=function(a){var b=m(a);b.duration=0;b.children=[];b.add=function(a){v(a).forEach(function(a){var c=a.offset,d=b.duration;a.autoplay=!1;a.offset=g.und(c)?
d:J(c,d);a=m(a);a.duration>d&&(b.duration=a.duration);b.children.push(a)});return b};return b};m.random=function(a,b){return Math.floor(Math.random()*(b-a+1))+a};return m});

// Scroll MOnitor
!function(t,e){"object"==typeof exports&&"object"==typeof module?module.exports=e():"function"==typeof define&&define.amd?define("scrollMonitor",[],e):"object"==typeof exports?exports.scrollMonitor=e():t.scrollMonitor=e()}(this,function(){return function(t){function e(o){if(i[o])return i[o].exports;var s=i[o]={exports:{},id:o,loaded:!1};return t[o].call(s.exports,s,s.exports,e),s.loaded=!0,s.exports}var i={};return e.m=t,e.c=i,e.p="",e(0)}([function(t,e,i){"use strict";var o=i(1),s=o.isInBrowser,n=i(2),r=new n(s?document.body:null);r.setStateFromDOM(null),r.listenToDOM(),s&&(window.scrollMonitor=r),t.exports=r},function(t,e){"use strict";e.VISIBILITYCHANGE="visibilityChange",e.ENTERVIEWPORT="enterViewport",e.FULLYENTERVIEWPORT="fullyEnterViewport",e.EXITVIEWPORT="exitViewport",e.PARTIALLYEXITVIEWPORT="partiallyExitViewport",e.LOCATIONCHANGE="locationChange",e.STATECHANGE="stateChange",e.eventTypes=[e.VISIBILITYCHANGE,e.ENTERVIEWPORT,e.FULLYENTERVIEWPORT,e.EXITVIEWPORT,e.PARTIALLYEXITVIEWPORT,e.LOCATIONCHANGE,e.STATECHANGE],e.isOnServer="undefined"==typeof window,e.isInBrowser=!e.isOnServer,e.defaultOffsets={top:0,bottom:0}},function(t,e,i){"use strict";function o(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function s(t){return c?0:t===document.body?window.innerHeight||document.documentElement.clientHeight:t.clientHeight}function n(t){return c?0:t===document.body?Math.max(document.body.scrollHeight,document.documentElement.scrollHeight,document.body.offsetHeight,document.documentElement.offsetHeight,document.documentElement.clientHeight):t.scrollHeight}function r(t){return c?0:t===document.body?window.pageYOffset||document.documentElement&&document.documentElement.scrollTop||document.body.scrollTop:t.scrollTop}var h=i(1),c=h.isOnServer,a=h.isInBrowser,l=h.eventTypes,p=i(3),w=function(){function t(e,i){function h(){if(a.viewportTop=r(e),a.viewportBottom=a.viewportTop+a.viewportHeight,a.documentHeight=n(e),a.documentHeight!==p){for(w=a.watchers.length;w--;)a.watchers[w].recalculateLocation();p=a.documentHeight}}function c(){for(u=a.watchers.length;u--;)a.watchers[u].update();for(u=a.watchers.length;u--;)a.watchers[u].triggerCallbacks()}o(this,t);var a=this;this.item=e,this.watchers=[],this.viewportTop=null,this.viewportBottom=null,this.documentHeight=n(e),this.viewportHeight=s(e),this.DOMListener=function(){t.prototype.DOMListener.apply(a,arguments)},this.eventTypes=l,i&&(this.containerWatcher=i.create(e));var p,w,u;this.update=function(){h(),c()},this.recalculateLocations=function(){this.documentHeight=0,this.update()}}return t.prototype.listenToDOM=function(){a&&(window.addEventListener?(this.item===document.body?window.addEventListener("scroll",this.DOMListener):this.item.addEventListener("scroll",this.DOMListener),window.addEventListener("resize",this.DOMListener)):(this.item===document.body?window.attachEvent("onscroll",this.DOMListener):this.item.attachEvent("onscroll",this.DOMListener),window.attachEvent("onresize",this.DOMListener)),this.destroy=function(){window.addEventListener?(this.item===document.body?(window.removeEventListener("scroll",this.DOMListener),this.containerWatcher.destroy()):this.item.removeEventListener("scroll",this.DOMListener),window.removeEventListener("resize",this.DOMListener)):(this.item===document.body?(window.detachEvent("onscroll",this.DOMListener),this.containerWatcher.destroy()):this.item.detachEvent("onscroll",this.DOMListener),window.detachEvent("onresize",this.DOMListener))})},t.prototype.destroy=function(){},t.prototype.DOMListener=function(t){this.setStateFromDOM(t)},t.prototype.setStateFromDOM=function(t){var e=r(this.item),i=s(this.item),o=n(this.item);this.setState(e,i,o,t)},t.prototype.setState=function(t,e,i,o){var s=e!==this.viewportHeight||i!==this.contentHeight;if(this.latestEvent=o,this.viewportTop=t,this.viewportHeight=e,this.viewportBottom=t+e,this.contentHeight=i,s)for(var n=this.watchers.length;n--;)this.watchers[n].recalculateLocation();this.updateAndTriggerWatchers(o)},t.prototype.updateAndTriggerWatchers=function(t){for(var e=this.watchers.length;e--;)this.watchers[e].update();for(e=this.watchers.length;e--;)this.watchers[e].triggerCallbacks(t)},t.prototype.createCustomContainer=function(){return new t},t.prototype.createContainer=function(e){"string"==typeof e?e=document.querySelector(e):e&&e.length>0&&(e=e[0]);var i=new t(e,this);return i.setStateFromDOM(),i.listenToDOM(),i},t.prototype.create=function(t,e){"string"==typeof t?t=document.querySelector(t):t&&t.length>0&&(t=t[0]);var i=new p(this,t,e);return this.watchers.push(i),i},t.prototype.beget=function(t,e){return this.create(t,e)},t}();t.exports=w},function(t,e,i){"use strict";function o(t,e,i){function o(t,e){if(0!==t.length)for(E=t.length;E--;)T=t[E],T.callback.call(s,e,s),T.isOne&&t.splice(E,1)}var s=this;this.watchItem=e,this.container=t,i?i===+i?this.offsets={top:i,bottom:i}:this.offsets={top:i.top||u.top,bottom:i.bottom||u.bottom}:this.offsets=u,this.callbacks={};for(var d=0,f=w.length;d<f;d++)s.callbacks[w[d]]=[];this.locked=!1;var m,v,b,I,E,T;this.triggerCallbacks=function(t){switch(this.isInViewport&&!m&&o(this.callbacks[r],t),this.isFullyInViewport&&!v&&o(this.callbacks[h],t),this.isAboveViewport!==b&&this.isBelowViewport!==I&&(o(this.callbacks[n],t),v||this.isFullyInViewport||(o(this.callbacks[h],t),o(this.callbacks[a],t)),m||this.isInViewport||(o(this.callbacks[r],t),o(this.callbacks[c],t))),!this.isFullyInViewport&&v&&o(this.callbacks[a],t),!this.isInViewport&&m&&o(this.callbacks[c],t),this.isInViewport!==m&&o(this.callbacks[n],t),!0){case m!==this.isInViewport:case v!==this.isFullyInViewport:case b!==this.isAboveViewport:case I!==this.isBelowViewport:o(this.callbacks[p],t)}m=this.isInViewport,v=this.isFullyInViewport,b=this.isAboveViewport,I=this.isBelowViewport},this.recalculateLocation=function(){if(!this.locked){var t=this.top,e=this.bottom;if(this.watchItem.nodeName){var i=this.watchItem.style.display;"none"===i&&(this.watchItem.style.display="");for(var s=0,n=this.container;n.containerWatcher;)s+=n.containerWatcher.top-n.containerWatcher.container.viewportTop,n=n.containerWatcher.container;var r=this.watchItem.getBoundingClientRect();this.top=r.top+this.container.viewportTop-s,this.bottom=r.bottom+this.container.viewportTop-s,"none"===i&&(this.watchItem.style.display=i)}else this.watchItem===+this.watchItem?this.watchItem>0?this.top=this.bottom=this.watchItem:this.top=this.bottom=this.container.documentHeight-this.watchItem:(this.top=this.watchItem.top,this.bottom=this.watchItem.bottom);this.top-=this.offsets.top,this.bottom+=this.offsets.bottom,this.height=this.bottom-this.top,void 0===t&&void 0===e||this.top===t&&this.bottom===e||o(this.callbacks[l],null)}},this.recalculateLocation(),this.update(),m=this.isInViewport,v=this.isFullyInViewport,b=this.isAboveViewport,I=this.isBelowViewport}var s=i(1),n=s.VISIBILITYCHANGE,r=s.ENTERVIEWPORT,h=s.FULLYENTERVIEWPORT,c=s.EXITVIEWPORT,a=s.PARTIALLYEXITVIEWPORT,l=s.LOCATIONCHANGE,p=s.STATECHANGE,w=s.eventTypes,u=s.defaultOffsets;o.prototype={on:function(t,e,i){switch(!0){case t===n&&!this.isInViewport&&this.isAboveViewport:case t===r&&this.isInViewport:case t===h&&this.isFullyInViewport:case t===c&&this.isAboveViewport&&!this.isInViewport:case t===a&&this.isInViewport&&this.isAboveViewport:if(e.call(this,this.container.latestEvent,this),i)return}if(!this.callbacks[t])throw new Error("Tried to add a scroll monitor listener of type "+t+". Your options are: "+w.join(", "));this.callbacks[t].push({callback:e,isOne:i||!1})},off:function(t,e){if(!this.callbacks[t])throw new Error("Tried to remove a scroll monitor listener of type "+t+". Your options are: "+w.join(", "));for(var i,o=0;i=this.callbacks[t][o];o++)if(i.callback===e){this.callbacks[t].splice(o,1);break}},one:function(t,e){this.on(t,e,!0)},recalculateSize:function(){this.height=this.watchItem.offsetHeight+this.offsets.top+this.offsets.bottom,this.bottom=this.top+this.height},update:function(){this.isAboveViewport=this.top<this.container.viewportTop,this.isBelowViewport=this.bottom>this.container.viewportBottom,this.isInViewport=this.top<this.container.viewportBottom&&this.bottom>this.container.viewportTop,this.isFullyInViewport=this.top>=this.container.viewportTop&&this.bottom<=this.container.viewportBottom||this.isAboveViewport&&this.isBelowViewport},destroy:function(){var t=this.container.watchers.indexOf(this),e=this;this.container.watchers.splice(t,1);for(var i=0,o=w.length;i<o;i++)e.callbacks[w[i]].length=0},lock:function(){this.locked=!0},unlock:function(){this.locked=!1}};for(var d=function(t){return function(e,i){this.on.call(this,t,e,i)}},f=0,m=w.length;f<m;f++){var v=w[f];o.prototype[v]=d(v)}t.exports=o}])});


/*
        : Slick
 Version: 1.6.0
  Author: Ken Wheeler
 Website: http://kenwheeler.github.io
    Docs: http://kenwheeler.github.io/slick
    Repo: http://github.com/kenwheeler/slick
  Issues: http://github.com/kenwheeler/slick/issues

 */
!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):"undefined"!=typeof exports?module.exports=a(require("jquery")):a(jQuery)}(function(a){"use strict";var b=window.Slick||{};b=function(){function c(c,d){var f,e=this;e.defaults={accessibility:!0,adaptiveHeight:!1,appendArrows:a(c),appendDots:a(c),arrows:!0,asNavFor:null,prevArrow:'<button type="button" data-role="none" class="slick-prev" aria-label="Previous" tabindex="0" role="button">Previous</button>',nextArrow:'<button type="button" data-role="none" class="slick-next" aria-label="Next" tabindex="0" role="button">Next</button>',autoplay:!1,autoplaySpeed:3e3,centerMode:!1,centerPadding:"50px",cssEase:"ease",customPaging:function(b,c){return a('<button type="button" data-role="none" role="button" tabindex="0" />').text(c+1)},dots:!1,dotsClass:"slick-dots",draggable:!0,easing:"linear",edgeFriction:.35,fade:!1,focusOnSelect:!1,infinite:!0,initialSlide:0,lazyLoad:"ondemand",mobileFirst:!1,pauseOnHover:!0,pauseOnFocus:!0,pauseOnDotsHover:!1,respondTo:"window",responsive:null,rows:1,rtl:!1,slide:"",slidesPerRow:1,slidesToShow:1,slidesToScroll:1,speed:500,swipe:!0,swipeToSlide:!1,touchMove:!0,touchThreshold:5,useCSS:!0,useTransform:!0,variableWidth:!1,vertical:!1,verticalSwiping:!1,waitForAnimate:!0,zIndex:1e3},e.initials={animating:!1,dragging:!1,autoPlayTimer:null,currentDirection:0,currentLeft:null,currentSlide:0,direction:1,$dots:null,listWidth:null,listHeight:null,loadIndex:0,$nextArrow:null,$prevArrow:null,slideCount:null,slideWidth:null,$slideTrack:null,$slides:null,sliding:!1,slideOffset:0,swipeLeft:null,$list:null,touchObject:{},transformsEnabled:!1,unslicked:!1},a.extend(e,e.initials),e.activeBreakpoint=null,e.animType=null,e.animProp=null,e.breakpoints=[],e.breakpointSettings=[],e.cssTransitions=!1,e.focussed=!1,e.interrupted=!1,e.hidden="hidden",e.paused=!0,e.positionProp=null,e.respondTo=null,e.rowCount=1,e.shouldClick=!0,e.$slider=a(c),e.$slidesCache=null,e.transformType=null,e.transitionType=null,e.visibilityChange="visibilitychange",e.windowWidth=0,e.windowTimer=null,f=a(c).data("slick")||{},e.options=a.extend({},e.defaults,d,f),e.currentSlide=e.options.initialSlide,e.originalSettings=e.options,"undefined"!=typeof document.mozHidden?(e.hidden="mozHidden",e.visibilityChange="mozvisibilitychange"):"undefined"!=typeof document.webkitHidden&&(e.hidden="webkitHidden",e.visibilityChange="webkitvisibilitychange"),e.autoPlay=a.proxy(e.autoPlay,e),e.autoPlayClear=a.proxy(e.autoPlayClear,e),e.autoPlayIterator=a.proxy(e.autoPlayIterator,e),e.changeSlide=a.proxy(e.changeSlide,e),e.clickHandler=a.proxy(e.clickHandler,e),e.selectHandler=a.proxy(e.selectHandler,e),e.setPosition=a.proxy(e.setPosition,e),e.swipeHandler=a.proxy(e.swipeHandler,e),e.dragHandler=a.proxy(e.dragHandler,e),e.keyHandler=a.proxy(e.keyHandler,e),e.instanceUid=b++,e.htmlExpr=/^(?:\s*(<[\w\W]+>)[^>]*)$/,e.registerBreakpoints(),e.init(!0)}var b=0;return c}(),b.prototype.activateADA=function(){var a=this;a.$slideTrack.find(".slick-active").attr({"aria-hidden":"false"}).find("a, input, button, select").attr({tabindex:"0"})},b.prototype.addSlide=b.prototype.slickAdd=function(b,c,d){var e=this;if("boolean"==typeof c)d=c,c=null;else if(0>c||c>=e.slideCount)return!1;e.unload(),"number"==typeof c?0===c&&0===e.$slides.length?a(b).appendTo(e.$slideTrack):d?a(b).insertBefore(e.$slides.eq(c)):a(b).insertAfter(e.$slides.eq(c)):d===!0?a(b).prependTo(e.$slideTrack):a(b).appendTo(e.$slideTrack),e.$slides=e.$slideTrack.children(this.options.slide),e.$slideTrack.children(this.options.slide).detach(),e.$slideTrack.append(e.$slides),e.$slides.each(function(b,c){a(c).attr("data-slick-index",b)}),e.$slidesCache=e.$slides,e.reinit()},b.prototype.animateHeight=function(){var a=this;if(1===a.options.slidesToShow&&a.options.adaptiveHeight===!0&&a.options.vertical===!1){var b=a.$slides.eq(a.currentSlide).outerHeight(!0);a.$list.animate({height:b},a.options.speed)}},b.prototype.animateSlide=function(b,c){var d={},e=this;e.animateHeight(),e.options.rtl===!0&&e.options.vertical===!1&&(b=-b),e.transformsEnabled===!1?e.options.vertical===!1?e.$slideTrack.animate({left:b},e.options.speed,e.options.easing,c):e.$slideTrack.animate({top:b},e.options.speed,e.options.easing,c):e.cssTransitions===!1?(e.options.rtl===!0&&(e.currentLeft=-e.currentLeft),a({animStart:e.currentLeft}).animate({animStart:b},{duration:e.options.speed,easing:e.options.easing,step:function(a){a=Math.ceil(a),e.options.vertical===!1?(d[e.animType]="translate("+a+"px, 0px)",e.$slideTrack.css(d)):(d[e.animType]="translate(0px,"+a+"px)",e.$slideTrack.css(d))},complete:function(){c&&c.call()}})):(e.applyTransition(),b=Math.ceil(b),e.options.vertical===!1?d[e.animType]="translate3d("+b+"px, 0px, 0px)":d[e.animType]="translate3d(0px,"+b+"px, 0px)",e.$slideTrack.css(d),c&&setTimeout(function(){e.disableTransition(),c.call()},e.options.speed))},b.prototype.getNavTarget=function(){var b=this,c=b.options.asNavFor;return c&&null!==c&&(c=a(c).not(b.$slider)),c},b.prototype.asNavFor=function(b){var c=this,d=c.getNavTarget();null!==d&&"object"==typeof d&&d.each(function(){var c=a(this).slick("getSlick");c.unslicked||c.slideHandler(b,!0)})},b.prototype.applyTransition=function(a){var b=this,c={};b.options.fade===!1?c[b.transitionType]=b.transformType+" "+b.options.speed+"ms "+b.options.cssEase:c[b.transitionType]="opacity "+b.options.speed+"ms "+b.options.cssEase,b.options.fade===!1?b.$slideTrack.css(c):b.$slides.eq(a).css(c)},b.prototype.autoPlay=function(){var a=this;a.autoPlayClear(),a.slideCount>a.options.slidesToShow&&(a.autoPlayTimer=setInterval(a.autoPlayIterator,a.options.autoplaySpeed))},b.prototype.autoPlayClear=function(){var a=this;a.autoPlayTimer&&clearInterval(a.autoPlayTimer)},b.prototype.autoPlayIterator=function(){var a=this,b=a.currentSlide+a.options.slidesToScroll;a.paused||a.interrupted||a.focussed||(a.options.infinite===!1&&(1===a.direction&&a.currentSlide+1===a.slideCount-1?a.direction=0:0===a.direction&&(b=a.currentSlide-a.options.slidesToScroll,a.currentSlide-1===0&&(a.direction=1))),a.slideHandler(b))},b.prototype.buildArrows=function(){var b=this;b.options.arrows===!0&&(b.$prevArrow=a(b.options.prevArrow).addClass("slick-arrow"),b.$nextArrow=a(b.options.nextArrow).addClass("slick-arrow"),b.slideCount>b.options.slidesToShow?(b.$prevArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"),b.$nextArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"),b.htmlExpr.test(b.options.prevArrow)&&b.$prevArrow.prependTo(b.options.appendArrows),b.htmlExpr.test(b.options.nextArrow)&&b.$nextArrow.appendTo(b.options.appendArrows),b.options.infinite!==!0&&b.$prevArrow.addClass("slick-disabled").attr("aria-disabled","true")):b.$prevArrow.add(b.$nextArrow).addClass("slick-hidden").attr({"aria-disabled":"true",tabindex:"-1"}))},b.prototype.buildDots=function(){var c,d,b=this;if(b.options.dots===!0&&b.slideCount>b.options.slidesToShow){for(b.$slider.addClass("slick-dotted"),d=a("<ul />").addClass(b.options.dotsClass),c=0;c<=b.getDotCount();c+=1)d.append(a("<li />").append(b.options.customPaging.call(this,b,c)));b.$dots=d.appendTo(b.options.appendDots),b.$dots.find("li").first().addClass("slick-active").attr("aria-hidden","false")}},b.prototype.buildOut=function(){var b=this;b.$slides=b.$slider.children(b.options.slide+":not(.slick-cloned)").addClass("slick-slide"),b.slideCount=b.$slides.length,b.$slides.each(function(b,c){a(c).attr("data-slick-index",b).data("originalStyling",a(c).attr("style")||"")}),b.$slider.addClass("slick-slider"),b.$slideTrack=0===b.slideCount?a('<div class="slick-track"/>').appendTo(b.$slider):b.$slides.wrapAll('<div class="slick-track"/>').parent(),b.$list=b.$slideTrack.wrap('<div aria-live="polite" class="slick-list"/>').parent(),b.$slideTrack.css("opacity",0),(b.options.centerMode===!0||b.options.swipeToSlide===!0)&&(b.options.slidesToScroll=1),a("img[data-lazy]",b.$slider).not("[src]").addClass("slick-loading"),b.setupInfinite(),b.buildArrows(),b.buildDots(),b.updateDots(),b.setSlideClasses("number"==typeof b.currentSlide?b.currentSlide:0),b.options.draggable===!0&&b.$list.addClass("draggable")},b.prototype.buildRows=function(){var b,c,d,e,f,g,h,a=this;if(e=document.createDocumentFragment(),g=a.$slider.children(),a.options.rows>1){for(h=a.options.slidesPerRow*a.options.rows,f=Math.ceil(g.length/h),b=0;f>b;b++){var i=document.createElement("div");for(c=0;c<a.options.rows;c++){var j=document.createElement("div");for(d=0;d<a.options.slidesPerRow;d++){var k=b*h+(c*a.options.slidesPerRow+d);g.get(k)&&j.appendChild(g.get(k))}i.appendChild(j)}e.appendChild(i)}a.$slider.empty().append(e),a.$slider.children().children().children().css({width:100/a.options.slidesPerRow+"%",display:"inline-block"})}},b.prototype.checkResponsive=function(b,c){var e,f,g,d=this,h=!1,i=d.$slider.width(),j=window.innerWidth||a(window).width();if("window"===d.respondTo?g=j:"slider"===d.respondTo?g=i:"min"===d.respondTo&&(g=Math.min(j,i)),d.options.responsive&&d.options.responsive.length&&null!==d.options.responsive){f=null;for(e in d.breakpoints)d.breakpoints.hasOwnProperty(e)&&(d.originalSettings.mobileFirst===!1?g<d.breakpoints[e]&&(f=d.breakpoints[e]):g>d.breakpoints[e]&&(f=d.breakpoints[e]));null!==f?null!==d.activeBreakpoint?(f!==d.activeBreakpoint||c)&&(d.activeBreakpoint=f,"unslick"===d.breakpointSettings[f]?d.unslick(f):(d.options=a.extend({},d.originalSettings,d.breakpointSettings[f]),b===!0&&(d.currentSlide=d.options.initialSlide),d.refresh(b)),h=f):(d.activeBreakpoint=f,"unslick"===d.breakpointSettings[f]?d.unslick(f):(d.options=a.extend({},d.originalSettings,d.breakpointSettings[f]),b===!0&&(d.currentSlide=d.options.initialSlide),d.refresh(b)),h=f):null!==d.activeBreakpoint&&(d.activeBreakpoint=null,d.options=d.originalSettings,b===!0&&(d.currentSlide=d.options.initialSlide),d.refresh(b),h=f),b||h===!1||d.$slider.trigger("breakpoint",[d,h])}},b.prototype.changeSlide=function(b,c){var f,g,h,d=this,e=a(b.currentTarget);switch(e.is("a")&&b.preventDefault(),e.is("li")||(e=e.closest("li")),h=d.slideCount%d.options.slidesToScroll!==0,f=h?0:(d.slideCount-d.currentSlide)%d.options.slidesToScroll,b.data.message){case"previous":g=0===f?d.options.slidesToScroll:d.options.slidesToShow-f,d.slideCount>d.options.slidesToShow&&d.slideHandler(d.currentSlide-g,!1,c);break;case"next":g=0===f?d.options.slidesToScroll:f,d.slideCount>d.options.slidesToShow&&d.slideHandler(d.currentSlide+g,!1,c);break;case"index":var i=0===b.data.index?0:b.data.index||e.index()*d.options.slidesToScroll;d.slideHandler(d.checkNavigable(i),!1,c),e.children().trigger("focus");break;default:return}},b.prototype.checkNavigable=function(a){var c,d,b=this;if(c=b.getNavigableIndexes(),d=0,a>c[c.length-1])a=c[c.length-1];else for(var e in c){if(a<c[e]){a=d;break}d=c[e]}return a},b.prototype.cleanUpEvents=function(){var b=this;b.options.dots&&null!==b.$dots&&a("li",b.$dots).off("click.slick",b.changeSlide).off("mouseenter.slick",a.proxy(b.interrupt,b,!0)).off("mouseleave.slick",a.proxy(b.interrupt,b,!1)),b.$slider.off("focus.slick blur.slick"),b.options.arrows===!0&&b.slideCount>b.options.slidesToShow&&(b.$prevArrow&&b.$prevArrow.off("click.slick",b.changeSlide),b.$nextArrow&&b.$nextArrow.off("click.slick",b.changeSlide)),b.$list.off("touchstart.slick mousedown.slick",b.swipeHandler),b.$list.off("touchmove.slick mousemove.slick",b.swipeHandler),b.$list.off("touchend.slick mouseup.slick",b.swipeHandler),b.$list.off("touchcancel.slick mouseleave.slick",b.swipeHandler),b.$list.off("click.slick",b.clickHandler),a(document).off(b.visibilityChange,b.visibility),b.cleanUpSlideEvents(),b.options.accessibility===!0&&b.$list.off("keydown.slick",b.keyHandler),b.options.focusOnSelect===!0&&a(b.$slideTrack).children().off("click.slick",b.selectHandler),a(window).off("orientationchange.slick.slick-"+b.instanceUid,b.orientationChange),a(window).off("resize.slick.slick-"+b.instanceUid,b.resize),a("[draggable!=true]",b.$slideTrack).off("dragstart",b.preventDefault),a(window).off("load.slick.slick-"+b.instanceUid,b.setPosition),a(document).off("ready.slick.slick-"+b.instanceUid,b.setPosition)},b.prototype.cleanUpSlideEvents=function(){var b=this;b.$list.off("mouseenter.slick",a.proxy(b.interrupt,b,!0)),b.$list.off("mouseleave.slick",a.proxy(b.interrupt,b,!1))},b.prototype.cleanUpRows=function(){var b,a=this;a.options.rows>1&&(b=a.$slides.children().children(),b.removeAttr("style"),a.$slider.empty().append(b))},b.prototype.clickHandler=function(a){var b=this;b.shouldClick===!1&&(a.stopImmediatePropagation(),a.stopPropagation(),a.preventDefault())},b.prototype.destroy=function(b){var c=this;c.autoPlayClear(),c.touchObject={},c.cleanUpEvents(),a(".slick-cloned",c.$slider).detach(),c.$dots&&c.$dots.remove(),c.$prevArrow&&c.$prevArrow.length&&(c.$prevArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display",""),c.htmlExpr.test(c.options.prevArrow)&&c.$prevArrow.remove()),c.$nextArrow&&c.$nextArrow.length&&(c.$nextArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display",""),c.htmlExpr.test(c.options.nextArrow)&&c.$nextArrow.remove()),c.$slides&&(c.$slides.removeClass("slick-slide slick-active slick-center slick-visible slick-current").removeAttr("aria-hidden").removeAttr("data-slick-index").each(function(){a(this).attr("style",a(this).data("originalStyling"))}),c.$slideTrack.children(this.options.slide).detach(),c.$slideTrack.detach(),c.$list.detach(),c.$slider.append(c.$slides)),c.cleanUpRows(),c.$slider.removeClass("slick-slider"),c.$slider.removeClass("slick-initialized"),c.$slider.removeClass("slick-dotted"),c.unslicked=!0,b||c.$slider.trigger("destroy",[c])},b.prototype.disableTransition=function(a){var b=this,c={};c[b.transitionType]="",b.options.fade===!1?b.$slideTrack.css(c):b.$slides.eq(a).css(c)},b.prototype.fadeSlide=function(a,b){var c=this;c.cssTransitions===!1?(c.$slides.eq(a).css({zIndex:c.options.zIndex}),c.$slides.eq(a).animate({opacity:1},c.options.speed,c.options.easing,b)):(c.applyTransition(a),c.$slides.eq(a).css({opacity:1,zIndex:c.options.zIndex}),b&&setTimeout(function(){c.disableTransition(a),b.call()},c.options.speed))},b.prototype.fadeSlideOut=function(a){var b=this;b.cssTransitions===!1?b.$slides.eq(a).animate({opacity:0,zIndex:b.options.zIndex-2},b.options.speed,b.options.easing):(b.applyTransition(a),b.$slides.eq(a).css({opacity:0,zIndex:b.options.zIndex-2}))},b.prototype.filterSlides=b.prototype.slickFilter=function(a){var b=this;null!==a&&(b.$slidesCache=b.$slides,b.unload(),b.$slideTrack.children(this.options.slide).detach(),b.$slidesCache.filter(a).appendTo(b.$slideTrack),b.reinit())},b.prototype.focusHandler=function(){var b=this;b.$slider.off("focus.slick blur.slick").on("focus.slick blur.slick","*:not(.slick-arrow)",function(c){c.stopImmediatePropagation();var d=a(this);setTimeout(function(){b.options.pauseOnFocus&&(b.focussed=d.is(":focus"),b.autoPlay())},0)})},b.prototype.getCurrent=b.prototype.slickCurrentSlide=function(){var a=this;return a.currentSlide},b.prototype.getDotCount=function(){var a=this,b=0,c=0,d=0;if(a.options.infinite===!0)for(;b<a.slideCount;)++d,b=c+a.options.slidesToScroll,c+=a.options.slidesToScroll<=a.options.slidesToShow?a.options.slidesToScroll:a.options.slidesToShow;else if(a.options.centerMode===!0)d=a.slideCount;else if(a.options.asNavFor)for(;b<a.slideCount;)++d,b=c+a.options.slidesToScroll,c+=a.options.slidesToScroll<=a.options.slidesToShow?a.options.slidesToScroll:a.options.slidesToShow;else d=1+Math.ceil((a.slideCount-a.options.slidesToShow)/a.options.slidesToScroll);return d-1},b.prototype.getLeft=function(a){var c,d,f,b=this,e=0;return b.slideOffset=0,d=b.$slides.first().outerHeight(!0),b.options.infinite===!0?(b.slideCount>b.options.slidesToShow&&(b.slideOffset=b.slideWidth*b.options.slidesToShow*-1,e=d*b.options.slidesToShow*-1),b.slideCount%b.options.slidesToScroll!==0&&a+b.options.slidesToScroll>b.slideCount&&b.slideCount>b.options.slidesToShow&&(a>b.slideCount?(b.slideOffset=(b.options.slidesToShow-(a-b.slideCount))*b.slideWidth*-1,e=(b.options.slidesToShow-(a-b.slideCount))*d*-1):(b.slideOffset=b.slideCount%b.options.slidesToScroll*b.slideWidth*-1,e=b.slideCount%b.options.slidesToScroll*d*-1))):a+b.options.slidesToShow>b.slideCount&&(b.slideOffset=(a+b.options.slidesToShow-b.slideCount)*b.slideWidth,e=(a+b.options.slidesToShow-b.slideCount)*d),b.slideCount<=b.options.slidesToShow&&(b.slideOffset=0,e=0),b.options.centerMode===!0&&b.options.infinite===!0?b.slideOffset+=b.slideWidth*Math.floor(b.options.slidesToShow/2)-b.slideWidth:b.options.centerMode===!0&&(b.slideOffset=0,b.slideOffset+=b.slideWidth*Math.floor(b.options.slidesToShow/2)),c=b.options.vertical===!1?a*b.slideWidth*-1+b.slideOffset:a*d*-1+e,b.options.variableWidth===!0&&(f=b.slideCount<=b.options.slidesToShow||b.options.infinite===!1?b.$slideTrack.children(".slick-slide").eq(a):b.$slideTrack.children(".slick-slide").eq(a+b.options.slidesToShow),c=b.options.rtl===!0?f[0]?-1*(b.$slideTrack.width()-f[0].offsetLeft-f.width()):0:f[0]?-1*f[0].offsetLeft:0,b.options.centerMode===!0&&(f=b.slideCount<=b.options.slidesToShow||b.options.infinite===!1?b.$slideTrack.children(".slick-slide").eq(a):b.$slideTrack.children(".slick-slide").eq(a+b.options.slidesToShow+1),c=b.options.rtl===!0?f[0]?-1*(b.$slideTrack.width()-f[0].offsetLeft-f.width()):0:f[0]?-1*f[0].offsetLeft:0,c+=(b.$list.width()-f.outerWidth())/2)),c},b.prototype.getOption=b.prototype.slickGetOption=function(a){var b=this;return b.options[a]},b.prototype.getNavigableIndexes=function(){var e,a=this,b=0,c=0,d=[];for(a.options.infinite===!1?e=a.slideCount:(b=-1*a.options.slidesToScroll,c=-1*a.options.slidesToScroll,e=2*a.slideCount);e>b;)d.push(b),b=c+a.options.slidesToScroll,c+=a.options.slidesToScroll<=a.options.slidesToShow?a.options.slidesToScroll:a.options.slidesToShow;return d},b.prototype.getSlick=function(){return this},b.prototype.getSlideCount=function(){var c,d,e,b=this;return e=b.options.centerMode===!0?b.slideWidth*Math.floor(b.options.slidesToShow/2):0,b.options.swipeToSlide===!0?(b.$slideTrack.find(".slick-slide").each(function(c,f){return f.offsetLeft-e+a(f).outerWidth()/2>-1*b.swipeLeft?(d=f,!1):void 0}),c=Math.abs(a(d).attr("data-slick-index")-b.currentSlide)||1):b.options.slidesToScroll},b.prototype.goTo=b.prototype.slickGoTo=function(a,b){var c=this;c.changeSlide({data:{message:"index",index:parseInt(a)}},b)},b.prototype.init=function(b){var c=this;a(c.$slider).hasClass("slick-initialized")||(a(c.$slider).addClass("slick-initialized"),c.buildRows(),c.buildOut(),c.setProps(),c.startLoad(),c.loadSlider(),c.initializeEvents(),c.updateArrows(),c.updateDots(),c.checkResponsive(!0),c.focusHandler()),b&&c.$slider.trigger("init",[c]),c.options.accessibility===!0&&c.initADA(),c.options.autoplay&&(c.paused=!1,c.autoPlay())},b.prototype.initADA=function(){var b=this;b.$slides.add(b.$slideTrack.find(".slick-cloned")).attr({"aria-hidden":"true",tabindex:"-1"}).find("a, input, button, select").attr({tabindex:"-1"}),b.$slideTrack.attr("role","listbox"),b.$slides.not(b.$slideTrack.find(".slick-cloned")).each(function(c){a(this).attr({role:"option","aria-describedby":"slick-slide"+b.instanceUid+c})}),null!==b.$dots&&b.$dots.attr("role","tablist").find("li").each(function(c){a(this).attr({role:"presentation","aria-selected":"false","aria-controls":"navigation"+b.instanceUid+c,id:"slick-slide"+b.instanceUid+c})}).first().attr("aria-selected","true").end().find("button").attr("role","button").end().closest("div").attr("role","toolbar"),b.activateADA()},b.prototype.initArrowEvents=function(){var a=this;a.options.arrows===!0&&a.slideCount>a.options.slidesToShow&&(a.$prevArrow.off("click.slick").on("click.slick",{message:"previous"},a.changeSlide),a.$nextArrow.off("click.slick").on("click.slick",{message:"next"},a.changeSlide))},b.prototype.initDotEvents=function(){var b=this;b.options.dots===!0&&b.slideCount>b.options.slidesToShow&&a("li",b.$dots).on("click.slick",{message:"index"},b.changeSlide),b.options.dots===!0&&b.options.pauseOnDotsHover===!0&&a("li",b.$dots).on("mouseenter.slick",a.proxy(b.interrupt,b,!0)).on("mouseleave.slick",a.proxy(b.interrupt,b,!1))},b.prototype.initSlideEvents=function(){var b=this;b.options.pauseOnHover&&(b.$list.on("mouseenter.slick",a.proxy(b.interrupt,b,!0)),b.$list.on("mouseleave.slick",a.proxy(b.interrupt,b,!1)))},b.prototype.initializeEvents=function(){var b=this;b.initArrowEvents(),b.initDotEvents(),b.initSlideEvents(),b.$list.on("touchstart.slick mousedown.slick",{action:"start"},b.swipeHandler),b.$list.on("touchmove.slick mousemove.slick",{action:"move"},b.swipeHandler),b.$list.on("touchend.slick mouseup.slick",{action:"end"},b.swipeHandler),b.$list.on("touchcancel.slick mouseleave.slick",{action:"end"},b.swipeHandler),b.$list.on("click.slick",b.clickHandler),a(document).on(b.visibilityChange,a.proxy(b.visibility,b)),b.options.accessibility===!0&&b.$list.on("keydown.slick",b.keyHandler),b.options.focusOnSelect===!0&&a(b.$slideTrack).children().on("click.slick",b.selectHandler),a(window).on("orientationchange.slick.slick-"+b.instanceUid,a.proxy(b.orientationChange,b)),a(window).on("resize.slick.slick-"+b.instanceUid,a.proxy(b.resize,b)),a("[draggable!=true]",b.$slideTrack).on("dragstart",b.preventDefault),a(window).on("load.slick.slick-"+b.instanceUid,b.setPosition),a(document).on("ready.slick.slick-"+b.instanceUid,b.setPosition)},b.prototype.initUI=function(){var a=this;a.options.arrows===!0&&a.slideCount>a.options.slidesToShow&&(a.$prevArrow.show(),a.$nextArrow.show()),a.options.dots===!0&&a.slideCount>a.options.slidesToShow&&a.$dots.show()},b.prototype.keyHandler=function(a){var b=this;a.target.tagName.match("TEXTAREA|INPUT|SELECT")||(37===a.keyCode&&b.options.accessibility===!0?b.changeSlide({data:{message:b.options.rtl===!0?"next":"previous"}}):39===a.keyCode&&b.options.accessibility===!0&&b.changeSlide({data:{message:b.options.rtl===!0?"previous":"next"}}))},b.prototype.lazyLoad=function(){function g(c){a("img[data-lazy]",c).each(function(){var c=a(this),d=a(this).attr("data-lazy"),e=document.createElement("img");e.onload=function(){c.animate({opacity:0},100,function(){c.attr("src",d).animate({opacity:1},200,function(){c.removeAttr("data-lazy").removeClass("slick-loading")}),b.$slider.trigger("lazyLoaded",[b,c,d])})},e.onerror=function(){c.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"),b.$slider.trigger("lazyLoadError",[b,c,d])},e.src=d})}var c,d,e,f,b=this;b.options.centerMode===!0?b.options.infinite===!0?(e=b.currentSlide+(b.options.slidesToShow/2+1),f=e+b.options.slidesToShow+2):(e=Math.max(0,b.currentSlide-(b.options.slidesToShow/2+1)),f=2+(b.options.slidesToShow/2+1)+b.currentSlide):(e=b.options.infinite?b.options.slidesToShow+b.currentSlide:b.currentSlide,f=Math.ceil(e+b.options.slidesToShow),b.options.fade===!0&&(e>0&&e--,f<=b.slideCount&&f++)),c=b.$slider.find(".slick-slide").slice(e,f),g(c),b.slideCount<=b.options.slidesToShow?(d=b.$slider.find(".slick-slide"),g(d)):b.currentSlide>=b.slideCount-b.options.slidesToShow?(d=b.$slider.find(".slick-cloned").slice(0,b.options.slidesToShow),g(d)):0===b.currentSlide&&(d=b.$slider.find(".slick-cloned").slice(-1*b.options.slidesToShow),g(d))},b.prototype.loadSlider=function(){var a=this;a.setPosition(),a.$slideTrack.css({opacity:1}),a.$slider.removeClass("slick-loading"),a.initUI(),"progressive"===a.options.lazyLoad&&a.progressiveLazyLoad()},b.prototype.next=b.prototype.slickNext=function(){var a=this;a.changeSlide({data:{message:"next"}})},b.prototype.orientationChange=function(){var a=this;a.checkResponsive(),a.setPosition()},b.prototype.pause=b.prototype.slickPause=function(){var a=this;a.autoPlayClear(),a.paused=!0},b.prototype.play=b.prototype.slickPlay=function(){var a=this;a.autoPlay(),a.options.autoplay=!0,a.paused=!1,a.focussed=!1,a.interrupted=!1},b.prototype.postSlide=function(a){var b=this;b.unslicked||(b.$slider.trigger("afterChange",[b,a]),b.animating=!1,b.setPosition(),b.swipeLeft=null,b.options.autoplay&&b.autoPlay(),b.options.accessibility===!0&&b.initADA())},b.prototype.prev=b.prototype.slickPrev=function(){var a=this;a.changeSlide({data:{message:"previous"}})},b.prototype.preventDefault=function(a){a.preventDefault()},b.prototype.progressiveLazyLoad=function(b){b=b||1;var e,f,g,c=this,d=a("img[data-lazy]",c.$slider);d.length?(e=d.first(),f=e.attr("data-lazy"),g=document.createElement("img"),g.onload=function(){e.attr("src",f).removeAttr("data-lazy").removeClass("slick-loading"),c.options.adaptiveHeight===!0&&c.setPosition(),c.$slider.trigger("lazyLoaded",[c,e,f]),c.progressiveLazyLoad()},g.onerror=function(){3>b?setTimeout(function(){c.progressiveLazyLoad(b+1)},500):(e.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"),c.$slider.trigger("lazyLoadError",[c,e,f]),c.progressiveLazyLoad())},g.src=f):c.$slider.trigger("allImagesLoaded",[c])},b.prototype.refresh=function(b){var d,e,c=this;e=c.slideCount-c.options.slidesToShow,!c.options.infinite&&c.currentSlide>e&&(c.currentSlide=e),c.slideCount<=c.options.slidesToShow&&(c.currentSlide=0),d=c.currentSlide,c.destroy(!0),a.extend(c,c.initials,{currentSlide:d}),c.init(),b||c.changeSlide({data:{message:"index",index:d}},!1)},b.prototype.registerBreakpoints=function(){var c,d,e,b=this,f=b.options.responsive||null;if("array"===a.type(f)&&f.length){b.respondTo=b.options.respondTo||"window";for(c in f)if(e=b.breakpoints.length-1,d=f[c].breakpoint,f.hasOwnProperty(c)){for(;e>=0;)b.breakpoints[e]&&b.breakpoints[e]===d&&b.breakpoints.splice(e,1),e--;b.breakpoints.push(d),b.breakpointSettings[d]=f[c].settings}b.breakpoints.sort(function(a,c){return b.options.mobileFirst?a-c:c-a})}},b.prototype.reinit=function(){var b=this;b.$slides=b.$slideTrack.children(b.options.slide).addClass("slick-slide"),b.slideCount=b.$slides.length,b.currentSlide>=b.slideCount&&0!==b.currentSlide&&(b.currentSlide=b.currentSlide-b.options.slidesToScroll),b.slideCount<=b.options.slidesToShow&&(b.currentSlide=0),b.registerBreakpoints(),b.setProps(),b.setupInfinite(),b.buildArrows(),b.updateArrows(),b.initArrowEvents(),b.buildDots(),b.updateDots(),b.initDotEvents(),b.cleanUpSlideEvents(),b.initSlideEvents(),b.checkResponsive(!1,!0),b.options.focusOnSelect===!0&&a(b.$slideTrack).children().on("click.slick",b.selectHandler),b.setSlideClasses("number"==typeof b.currentSlide?b.currentSlide:0),b.setPosition(),b.focusHandler(),b.paused=!b.options.autoplay,b.autoPlay(),b.$slider.trigger("reInit",[b])},b.prototype.resize=function(){var b=this;a(window).width()!==b.windowWidth&&(clearTimeout(b.windowDelay),b.windowDelay=window.setTimeout(function(){b.windowWidth=a(window).width(),b.checkResponsive(),b.unslicked||b.setPosition()},50))},b.prototype.removeSlide=b.prototype.slickRemove=function(a,b,c){var d=this;return"boolean"==typeof a?(b=a,a=b===!0?0:d.slideCount-1):a=b===!0?--a:a,d.slideCount<1||0>a||a>d.slideCount-1?!1:(d.unload(),c===!0?d.$slideTrack.children().remove():d.$slideTrack.children(this.options.slide).eq(a).remove(),d.$slides=d.$slideTrack.children(this.options.slide),d.$slideTrack.children(this.options.slide).detach(),d.$slideTrack.append(d.$slides),d.$slidesCache=d.$slides,void d.reinit())},b.prototype.setCSS=function(a){var d,e,b=this,c={};b.options.rtl===!0&&(a=-a),d="left"==b.positionProp?Math.ceil(a)+"px":"0px",e="top"==b.positionProp?Math.ceil(a)+"px":"0px",c[b.positionProp]=a,b.transformsEnabled===!1?b.$slideTrack.css(c):(c={},b.cssTransitions===!1?(c[b.animType]="translate("+d+", "+e+")",b.$slideTrack.css(c)):(c[b.animType]="translate3d("+d+", "+e+", 0px)",b.$slideTrack.css(c)))},b.prototype.setDimensions=function(){var a=this;a.options.vertical===!1?a.options.centerMode===!0&&a.$list.css({padding:"0px "+a.options.centerPadding}):(a.$list.height(a.$slides.first().outerHeight(!0)*a.options.slidesToShow),a.options.centerMode===!0&&a.$list.css({padding:a.options.centerPadding+" 0px"})),a.listWidth=a.$list.width(),a.listHeight=a.$list.height(),a.options.vertical===!1&&a.options.variableWidth===!1?(a.slideWidth=Math.ceil(a.listWidth/a.options.slidesToShow),a.$slideTrack.width(Math.ceil(a.slideWidth*a.$slideTrack.children(".slick-slide").length))):a.options.variableWidth===!0?a.$slideTrack.width(5e3*a.slideCount):(a.slideWidth=Math.ceil(a.listWidth),a.$slideTrack.height(Math.ceil(a.$slides.first().outerHeight(!0)*a.$slideTrack.children(".slick-slide").length)));var b=a.$slides.first().outerWidth(!0)-a.$slides.first().width();a.options.variableWidth===!1&&a.$slideTrack.children(".slick-slide").width(a.slideWidth-b)},b.prototype.setFade=function(){var c,b=this;b.$slides.each(function(d,e){c=b.slideWidth*d*-1,b.options.rtl===!0?a(e).css({position:"relative",right:c,top:0,zIndex:b.options.zIndex-2,opacity:0}):a(e).css({position:"relative",left:c,top:0,zIndex:b.options.zIndex-2,opacity:0})}),b.$slides.eq(b.currentSlide).css({zIndex:b.options.zIndex-1,opacity:1})},b.prototype.setHeight=function(){var a=this;if(1===a.options.slidesToShow&&a.options.adaptiveHeight===!0&&a.options.vertical===!1){var b=a.$slides.eq(a.currentSlide).outerHeight(!0);a.$list.css("height",b)}},b.prototype.setOption=b.prototype.slickSetOption=function(){var c,d,e,f,h,b=this,g=!1;if("object"===a.type(arguments[0])?(e=arguments[0],g=arguments[1],h="multiple"):"string"===a.type(arguments[0])&&(e=arguments[0],f=arguments[1],g=arguments[2],"responsive"===arguments[0]&&"array"===a.type(arguments[1])?h="responsive":"undefined"!=typeof arguments[1]&&(h="single")),"single"===h)b.options[e]=f;else if("multiple"===h)a.each(e,function(a,c){b.options[a]=c});else if("responsive"===h)for(d in f)if("array"!==a.type(b.options.responsive))b.options.responsive=[f[d]];else{for(c=b.options.responsive.length-1;c>=0;)b.options.responsive[c].breakpoint===f[d].breakpoint&&b.options.responsive.splice(c,1),c--;b.options.responsive.push(f[d])}g&&(b.unload(),b.reinit())},b.prototype.setPosition=function(){var a=this;a.setDimensions(),a.setHeight(),a.options.fade===!1?a.setCSS(a.getLeft(a.currentSlide)):a.setFade(),a.$slider.trigger("setPosition",[a])},b.prototype.setProps=function(){var a=this,b=document.body.style;a.positionProp=a.options.vertical===!0?"top":"left","top"===a.positionProp?a.$slider.addClass("slick-vertical"):a.$slider.removeClass("slick-vertical"),(void 0!==b.WebkitTransition||void 0!==b.MozTransition||void 0!==b.msTransition)&&a.options.useCSS===!0&&(a.cssTransitions=!0),a.options.fade&&("number"==typeof a.options.zIndex?a.options.zIndex<3&&(a.options.zIndex=3):a.options.zIndex=a.defaults.zIndex),void 0!==b.OTransform&&(a.animType="OTransform",a.transformType="-o-transform",a.transitionType="OTransition",void 0===b.perspectiveProperty&&void 0===b.webkitPerspective&&(a.animType=!1)),void 0!==b.MozTransform&&(a.animType="MozTransform",a.transformType="-moz-transform",a.transitionType="MozTransition",void 0===b.perspectiveProperty&&void 0===b.MozPerspective&&(a.animType=!1)),void 0!==b.webkitTransform&&(a.animType="webkitTransform",a.transformType="-webkit-transform",a.transitionType="webkitTransition",void 0===b.perspectiveProperty&&void 0===b.webkitPerspective&&(a.animType=!1)),void 0!==b.msTransform&&(a.animType="msTransform",a.transformType="-ms-transform",a.transitionType="msTransition",void 0===b.msTransform&&(a.animType=!1)),void 0!==b.transform&&a.animType!==!1&&(a.animType="transform",a.transformType="transform",a.transitionType="transition"),a.transformsEnabled=a.options.useTransform&&null!==a.animType&&a.animType!==!1},b.prototype.setSlideClasses=function(a){var c,d,e,f,b=this;d=b.$slider.find(".slick-slide").removeClass("slick-active slick-center slick-current").attr("aria-hidden","true"),b.$slides.eq(a).addClass("slick-current"),b.options.centerMode===!0?(c=Math.floor(b.options.slidesToShow/2),b.options.infinite===!0&&(a>=c&&a<=b.slideCount-1-c?b.$slides.slice(a-c,a+c+1).addClass("slick-active").attr("aria-hidden","false"):(e=b.options.slidesToShow+a,
d.slice(e-c+1,e+c+2).addClass("slick-active").attr("aria-hidden","false")),0===a?d.eq(d.length-1-b.options.slidesToShow).addClass("slick-center"):a===b.slideCount-1&&d.eq(b.options.slidesToShow).addClass("slick-center")),b.$slides.eq(a).addClass("slick-center")):a>=0&&a<=b.slideCount-b.options.slidesToShow?b.$slides.slice(a,a+b.options.slidesToShow).addClass("slick-active").attr("aria-hidden","false"):d.length<=b.options.slidesToShow?d.addClass("slick-active").attr("aria-hidden","false"):(f=b.slideCount%b.options.slidesToShow,e=b.options.infinite===!0?b.options.slidesToShow+a:a,b.options.slidesToShow==b.options.slidesToScroll&&b.slideCount-a<b.options.slidesToShow?d.slice(e-(b.options.slidesToShow-f),e+f).addClass("slick-active").attr("aria-hidden","false"):d.slice(e,e+b.options.slidesToShow).addClass("slick-active").attr("aria-hidden","false")),"ondemand"===b.options.lazyLoad&&b.lazyLoad()},b.prototype.setupInfinite=function(){var c,d,e,b=this;if(b.options.fade===!0&&(b.options.centerMode=!1),b.options.infinite===!0&&b.options.fade===!1&&(d=null,b.slideCount>b.options.slidesToShow)){for(e=b.options.centerMode===!0?b.options.slidesToShow+1:b.options.slidesToShow,c=b.slideCount;c>b.slideCount-e;c-=1)d=c-1,a(b.$slides[d]).clone(!0).attr("id","").attr("data-slick-index",d-b.slideCount).prependTo(b.$slideTrack).addClass("slick-cloned");for(c=0;e>c;c+=1)d=c,a(b.$slides[d]).clone(!0).attr("id","").attr("data-slick-index",d+b.slideCount).appendTo(b.$slideTrack).addClass("slick-cloned");b.$slideTrack.find(".slick-cloned").find("[id]").each(function(){a(this).attr("id","")})}},b.prototype.interrupt=function(a){var b=this;a||b.autoPlay(),b.interrupted=a},b.prototype.selectHandler=function(b){var c=this,d=a(b.target).is(".slick-slide")?a(b.target):a(b.target).parents(".slick-slide"),e=parseInt(d.attr("data-slick-index"));return e||(e=0),c.slideCount<=c.options.slidesToShow?(c.setSlideClasses(e),void c.asNavFor(e)):void c.slideHandler(e)},b.prototype.slideHandler=function(a,b,c){var d,e,f,g,j,h=null,i=this;return b=b||!1,i.animating===!0&&i.options.waitForAnimate===!0||i.options.fade===!0&&i.currentSlide===a||i.slideCount<=i.options.slidesToShow?void 0:(b===!1&&i.asNavFor(a),d=a,h=i.getLeft(d),g=i.getLeft(i.currentSlide),i.currentLeft=null===i.swipeLeft?g:i.swipeLeft,i.options.infinite===!1&&i.options.centerMode===!1&&(0>a||a>i.getDotCount()*i.options.slidesToScroll)?void(i.options.fade===!1&&(d=i.currentSlide,c!==!0?i.animateSlide(g,function(){i.postSlide(d)}):i.postSlide(d))):i.options.infinite===!1&&i.options.centerMode===!0&&(0>a||a>i.slideCount-i.options.slidesToScroll)?void(i.options.fade===!1&&(d=i.currentSlide,c!==!0?i.animateSlide(g,function(){i.postSlide(d)}):i.postSlide(d))):(i.options.autoplay&&clearInterval(i.autoPlayTimer),e=0>d?i.slideCount%i.options.slidesToScroll!==0?i.slideCount-i.slideCount%i.options.slidesToScroll:i.slideCount+d:d>=i.slideCount?i.slideCount%i.options.slidesToScroll!==0?0:d-i.slideCount:d,i.animating=!0,i.$slider.trigger("beforeChange",[i,i.currentSlide,e]),f=i.currentSlide,i.currentSlide=e,i.setSlideClasses(i.currentSlide),i.options.asNavFor&&(j=i.getNavTarget(),j=j.slick("getSlick"),j.slideCount<=j.options.slidesToShow&&j.setSlideClasses(i.currentSlide)),i.updateDots(),i.updateArrows(),i.options.fade===!0?(c!==!0?(i.fadeSlideOut(f),i.fadeSlide(e,function(){i.postSlide(e)})):i.postSlide(e),void i.animateHeight()):void(c!==!0?i.animateSlide(h,function(){i.postSlide(e)}):i.postSlide(e))))},b.prototype.startLoad=function(){var a=this;a.options.arrows===!0&&a.slideCount>a.options.slidesToShow&&(a.$prevArrow.hide(),a.$nextArrow.hide()),a.options.dots===!0&&a.slideCount>a.options.slidesToShow&&a.$dots.hide(),a.$slider.addClass("slick-loading")},b.prototype.swipeDirection=function(){var a,b,c,d,e=this;return a=e.touchObject.startX-e.touchObject.curX,b=e.touchObject.startY-e.touchObject.curY,c=Math.atan2(b,a),d=Math.round(180*c/Math.PI),0>d&&(d=360-Math.abs(d)),45>=d&&d>=0?e.options.rtl===!1?"left":"right":360>=d&&d>=315?e.options.rtl===!1?"left":"right":d>=135&&225>=d?e.options.rtl===!1?"right":"left":e.options.verticalSwiping===!0?d>=35&&135>=d?"down":"up":"vertical"},b.prototype.swipeEnd=function(a){var c,d,b=this;if(b.dragging=!1,b.interrupted=!1,b.shouldClick=b.touchObject.swipeLength>10?!1:!0,void 0===b.touchObject.curX)return!1;if(b.touchObject.edgeHit===!0&&b.$slider.trigger("edge",[b,b.swipeDirection()]),b.touchObject.swipeLength>=b.touchObject.minSwipe){switch(d=b.swipeDirection()){case"left":case"down":c=b.options.swipeToSlide?b.checkNavigable(b.currentSlide+b.getSlideCount()):b.currentSlide+b.getSlideCount(),b.currentDirection=0;break;case"right":case"up":c=b.options.swipeToSlide?b.checkNavigable(b.currentSlide-b.getSlideCount()):b.currentSlide-b.getSlideCount(),b.currentDirection=1}"vertical"!=d&&(b.slideHandler(c),b.touchObject={},b.$slider.trigger("swipe",[b,d]))}else b.touchObject.startX!==b.touchObject.curX&&(b.slideHandler(b.currentSlide),b.touchObject={})},b.prototype.swipeHandler=function(a){var b=this;if(!(b.options.swipe===!1||"ontouchend"in document&&b.options.swipe===!1||b.options.draggable===!1&&-1!==a.type.indexOf("mouse")))switch(b.touchObject.fingerCount=a.originalEvent&&void 0!==a.originalEvent.touches?a.originalEvent.touches.length:1,b.touchObject.minSwipe=b.listWidth/b.options.touchThreshold,b.options.verticalSwiping===!0&&(b.touchObject.minSwipe=b.listHeight/b.options.touchThreshold),a.data.action){case"start":b.swipeStart(a);break;case"move":b.swipeMove(a);break;case"end":b.swipeEnd(a)}},b.prototype.swipeMove=function(a){var d,e,f,g,h,b=this;return h=void 0!==a.originalEvent?a.originalEvent.touches:null,!b.dragging||h&&1!==h.length?!1:(d=b.getLeft(b.currentSlide),b.touchObject.curX=void 0!==h?h[0].pageX:a.clientX,b.touchObject.curY=void 0!==h?h[0].pageY:a.clientY,b.touchObject.swipeLength=Math.round(Math.sqrt(Math.pow(b.touchObject.curX-b.touchObject.startX,2))),b.options.verticalSwiping===!0&&(b.touchObject.swipeLength=Math.round(Math.sqrt(Math.pow(b.touchObject.curY-b.touchObject.startY,2)))),e=b.swipeDirection(),"vertical"!==e?(void 0!==a.originalEvent&&b.touchObject.swipeLength>4&&a.preventDefault(),g=(b.options.rtl===!1?1:-1)*(b.touchObject.curX>b.touchObject.startX?1:-1),b.options.verticalSwiping===!0&&(g=b.touchObject.curY>b.touchObject.startY?1:-1),f=b.touchObject.swipeLength,b.touchObject.edgeHit=!1,b.options.infinite===!1&&(0===b.currentSlide&&"right"===e||b.currentSlide>=b.getDotCount()&&"left"===e)&&(f=b.touchObject.swipeLength*b.options.edgeFriction,b.touchObject.edgeHit=!0),b.options.vertical===!1?b.swipeLeft=d+f*g:b.swipeLeft=d+f*(b.$list.height()/b.listWidth)*g,b.options.verticalSwiping===!0&&(b.swipeLeft=d+f*g),b.options.fade===!0||b.options.touchMove===!1?!1:b.animating===!0?(b.swipeLeft=null,!1):void b.setCSS(b.swipeLeft)):void 0)},b.prototype.swipeStart=function(a){var c,b=this;return b.interrupted=!0,1!==b.touchObject.fingerCount||b.slideCount<=b.options.slidesToShow?(b.touchObject={},!1):(void 0!==a.originalEvent&&void 0!==a.originalEvent.touches&&(c=a.originalEvent.touches[0]),b.touchObject.startX=b.touchObject.curX=void 0!==c?c.pageX:a.clientX,b.touchObject.startY=b.touchObject.curY=void 0!==c?c.pageY:a.clientY,void(b.dragging=!0))},b.prototype.unfilterSlides=b.prototype.slickUnfilter=function(){var a=this;null!==a.$slidesCache&&(a.unload(),a.$slideTrack.children(this.options.slide).detach(),a.$slidesCache.appendTo(a.$slideTrack),a.reinit())},b.prototype.unload=function(){var b=this;a(".slick-cloned",b.$slider).remove(),b.$dots&&b.$dots.remove(),b.$prevArrow&&b.htmlExpr.test(b.options.prevArrow)&&b.$prevArrow.remove(),b.$nextArrow&&b.htmlExpr.test(b.options.nextArrow)&&b.$nextArrow.remove(),b.$slides.removeClass("slick-slide slick-active slick-visible slick-current").attr("aria-hidden","true").css("width","")},b.prototype.unslick=function(a){var b=this;b.$slider.trigger("unslick",[b,a]),b.destroy()},b.prototype.updateArrows=function(){var b,a=this;b=Math.floor(a.options.slidesToShow/2),a.options.arrows===!0&&a.slideCount>a.options.slidesToShow&&!a.options.infinite&&(a.$prevArrow.removeClass("slick-disabled").attr("aria-disabled","false"),a.$nextArrow.removeClass("slick-disabled").attr("aria-disabled","false"),0===a.currentSlide?(a.$prevArrow.addClass("slick-disabled").attr("aria-disabled","true"),a.$nextArrow.removeClass("slick-disabled").attr("aria-disabled","false")):a.currentSlide>=a.slideCount-a.options.slidesToShow&&a.options.centerMode===!1?(a.$nextArrow.addClass("slick-disabled").attr("aria-disabled","true"),a.$prevArrow.removeClass("slick-disabled").attr("aria-disabled","false")):a.currentSlide>=a.slideCount-1&&a.options.centerMode===!0&&(a.$nextArrow.addClass("slick-disabled").attr("aria-disabled","true"),a.$prevArrow.removeClass("slick-disabled").attr("aria-disabled","false")))},b.prototype.updateDots=function(){var a=this;null!==a.$dots&&(a.$dots.find("li").removeClass("slick-active").attr("aria-hidden","true"),a.$dots.find("li").eq(Math.floor(a.currentSlide/a.options.slidesToScroll)).addClass("slick-active").attr("aria-hidden","false"))},b.prototype.visibility=function(){var a=this;a.options.autoplay&&(document[a.hidden]?a.interrupted=!0:a.interrupted=!1)},a.fn.slick=function(){var f,g,a=this,c=arguments[0],d=Array.prototype.slice.call(arguments,1),e=a.length;for(f=0;e>f;f++)if("object"==typeof c||"undefined"==typeof c?a[f].slick=new b(a[f],c):g=a[f].slick[c].apply(a[f].slick,d),"undefined"!=typeof g)return g;return a}});


/* Modernizr 2.6.2 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-backgroundsize-csstransforms3d-csstransitions-touch-shiv-cssclasses-prefixed-teststyles-testprop-testallprops-prefixes-domprefixes-load
 */
;window.Modernizr=function(a,b,c){function z(a){j.cssText=a}function A(a,b){return z(m.join(a+";")+(b||""))}function B(a,b){return typeof a===b}function C(a,b){return!!~(""+a).indexOf(b)}function D(a,b){for(var d in a){var e=a[d];if(!C(e,"-")&&j[e]!==c)return b=="pfx"?e:!0}return!1}function E(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:B(f,"function")?f.bind(d||b):f}return!1}function F(a,b,c){var d=a.charAt(0).toUpperCase()+a.slice(1),e=(a+" "+o.join(d+" ")+d).split(" ");return B(b,"string")||B(b,"undefined")?D(e,b):(e=(a+" "+p.join(d+" ")+d).split(" "),E(e,b,c))}var d="2.6.2",e={},f=!0,g=b.documentElement,h="modernizr",i=b.createElement(h),j=i.style,k,l={}.toString,m=" -webkit- -moz- -o- -ms- ".split(" "),n="Webkit Moz O ms",o=n.split(" "),p=n.toLowerCase().split(" "),q={},r={},s={},t=[],u=t.slice,v,w=function(a,c,d,e){var f,i,j,k,l=b.createElement("div"),m=b.body,n=m||b.createElement("body");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:h+(d+1),l.appendChild(j);return f=["&#173;",'<style id="s',h,'">',a,"</style>"].join(""),l.id=h,(m?l:n).innerHTML+=f,n.appendChild(l),m||(n.style.background="",n.style.overflow="hidden",k=g.style.overflow,g.style.overflow="hidden",g.appendChild(n)),i=c(l,a),m?l.parentNode.removeChild(l):(n.parentNode.removeChild(n),g.style.overflow=k),!!i},x={}.hasOwnProperty,y;!B(x,"undefined")&&!B(x.call,"undefined")?y=function(a,b){return x.call(a,b)}:y=function(a,b){return b in a&&B(a.constructor.prototype[b],"undefined")},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if(typeof c!="function")throw new TypeError;var d=u.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(u.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(u.call(arguments)))};return e}),q.touch=function(){var c;return"ontouchstart"in a||a.DocumentTouch&&b instanceof DocumentTouch?c=!0:w(["@media (",m.join("touch-enabled),("),h,")","{#modernizr{top:9px;position:absolute}}"].join(""),function(a){c=a.offsetTop===9}),c},q.backgroundsize=function(){return F("backgroundSize")},q.csstransforms3d=function(){var a=!!F("perspective");return a&&"webkitPerspective"in g.style&&w("@media (transform-3d),(-webkit-transform-3d){#modernizr{left:9px;position:absolute;height:3px;}}",function(b,c){a=b.offsetLeft===9&&b.offsetHeight===3}),a},q.csstransitions=function(){return F("transition")};for(var G in q)y(q,G)&&(v=G.toLowerCase(),e[v]=q[G](),t.push((e[v]?"":"no-")+v));return e.addTest=function(a,b){if(typeof a=="object")for(var d in a)y(a,d)&&e.addTest(d,a[d]);else{a=a.toLowerCase();if(e[a]!==c)return e;b=typeof b=="function"?b():b,typeof f!="undefined"&&f&&(g.className+=" "+(b?"":"no-")+a),e[a]=b}return e},z(""),i=k=null,function(a,b){function k(a,b){var c=a.createElement("p"),d=a.getElementsByTagName("head")[0]||a.documentElement;return c.innerHTML="x<style>"+b+"</style>",d.insertBefore(c.lastChild,d.firstChild)}function l(){var a=r.elements;return typeof a=="string"?a.split(" "):a}function m(a){var b=i[a[g]];return b||(b={},h++,a[g]=h,i[h]=b),b}function n(a,c,f){c||(c=b);if(j)return c.createElement(a);f||(f=m(c));var g;return f.cache[a]?g=f.cache[a].cloneNode():e.test(a)?g=(f.cache[a]=f.createElem(a)).cloneNode():g=f.createElem(a),g.canHaveChildren&&!d.test(a)?f.frag.appendChild(g):g}function o(a,c){a||(a=b);if(j)return a.createDocumentFragment();c=c||m(a);var d=c.frag.cloneNode(),e=0,f=l(),g=f.length;for(;e<g;e++)d.createElement(f[e]);return d}function p(a,b){b.cache||(b.cache={},b.createElem=a.createElement,b.createFrag=a.createDocumentFragment,b.frag=b.createFrag()),a.createElement=function(c){return r.shivMethods?n(c,a,b):b.createElem(c)},a.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+l().join().replace(/\w+/g,function(a){return b.createElem(a),b.frag.createElement(a),'c("'+a+'")'})+");return n}")(r,b.frag)}function q(a){a||(a=b);var c=m(a);return r.shivCSS&&!f&&!c.hasCSS&&(c.hasCSS=!!k(a,"article,aside,figcaption,figure,footer,header,hgroup,nav,section{display:block}mark{background:#FF0;color:#000}")),j||p(a,c),a}var c=a.html5||{},d=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,e=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,f,g="_html5shiv",h=0,i={},j;(function(){try{var a=b.createElement("a");a.innerHTML="<xyz></xyz>",f="hidden"in a,j=a.childNodes.length==1||function(){b.createElement("a");var a=b.createDocumentFragment();return typeof a.cloneNode=="undefined"||typeof a.createDocumentFragment=="undefined"||typeof a.createElement=="undefined"}()}catch(c){f=!0,j=!0}})();var r={elements:c.elements||"abbr article aside audio bdi canvas data datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video",shivCSS:c.shivCSS!==!1,supportsUnknownElements:j,shivMethods:c.shivMethods!==!1,type:"default",shivDocument:q,createElement:n,createDocumentFragment:o};a.html5=r,q(b)}(this,b),e._version=d,e._prefixes=m,e._domPrefixes=p,e._cssomPrefixes=o,e.testProp=function(a){return D([a])},e.testAllProps=F,e.testStyles=w,e.prefixed=function(a,b,c){return b?F(a,b,c):F(a,"pfx")},g.className=g.className.replace(/(^|\s)no-js(\s|$)/,"$1$2")+(f?" js "+t.join(" "):""),e}(this,this.document),function(a,b,c){function d(a){return"[object Function]"==o.call(a)}function e(a){return"string"==typeof a}function f(){}function g(a){return!a||"loaded"==a||"complete"==a||"uninitialized"==a}function h(){var a=p.shift();q=1,a?a.t?m(function(){("c"==a.t?B.injectCss:B.injectJs)(a.s,0,a.a,a.x,a.e,1)},0):(a(),h()):q=0}function i(a,c,d,e,f,i,j){function k(b){if(!o&&g(l.readyState)&&(u.r=o=1,!q&&h(),l.onload=l.onreadystatechange=null,b)){"img"!=a&&m(function(){t.removeChild(l)},50);for(var d in y[c])y[c].hasOwnProperty(d)&&y[c][d].onload()}}var j=j||B.errorTimeout,l=b.createElement(a),o=0,r=0,u={t:d,s:c,e:f,a:i,x:j};1===y[c]&&(r=1,y[c]=[]),"object"==a?l.data=c:(l.src=c,l.type=a),l.width=l.height="0",l.onerror=l.onload=l.onreadystatechange=function(){k.call(this,r)},p.splice(e,0,u),"img"!=a&&(r||2===y[c]?(t.insertBefore(l,s?null:n),m(k,j)):y[c].push(l))}function j(a,b,c,d,f){return q=0,b=b||"j",e(a)?i("c"==b?v:u,a,b,this.i++,c,d,f):(p.splice(this.i++,0,a),1==p.length&&h()),this}function k(){var a=B;return a.loader={load:j,i:0},a}var l=b.documentElement,m=a.setTimeout,n=b.getElementsByTagName("script")[0],o={}.toString,p=[],q=0,r="MozAppearance"in l.style,s=r&&!!b.createRange().compareNode,t=s?l:n.parentNode,l=a.opera&&"[object Opera]"==o.call(a.opera),l=!!b.attachEvent&&!l,u=r?"object":l?"script":"img",v=l?"script":u,w=Array.isArray||function(a){return"[object Array]"==o.call(a)},x=[],y={},z={timeout:function(a,b){return b.length&&(a.timeout=b[0]),a}},A,B;B=function(a){function b(a){var a=a.split("!"),b=x.length,c=a.pop(),d=a.length,c={url:c,origUrl:c,prefixes:a},e,f,g;for(f=0;f<d;f++)g=a[f].split("="),(e=z[g.shift()])&&(c=e(c,g));for(f=0;f<b;f++)c=x[f](c);return c}function g(a,e,f,g,h){var i=b(a),j=i.autoCallback;i.url.split(".").pop().split("?").shift(),i.bypass||(e&&(e=d(e)?e:e[a]||e[g]||e[a.split("/").pop().split("?")[0]]),i.instead?i.instead(a,e,f,g,h):(y[i.url]?i.noexec=!0:y[i.url]=1,f.load(i.url,i.forceCSS||!i.forceJS&&"css"==i.url.split(".").pop().split("?").shift()?"c":c,i.noexec,i.attrs,i.timeout),(d(e)||d(j))&&f.load(function(){k(),e&&e(i.origUrl,h,g),j&&j(i.origUrl,h,g),y[i.url]=2})))}function h(a,b){function c(a,c){if(a){if(e(a))c||(j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}),g(a,j,b,0,h);else if(Object(a)===a)for(n in m=function(){var b=0,c;for(c in a)a.hasOwnProperty(c)&&b++;return b}(),a)a.hasOwnProperty(n)&&(!c&&!--m&&(d(j)?j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}:j[n]=function(a){return function(){var b=[].slice.call(arguments);a&&a.apply(this,b),l()}}(k[n])),g(a[n],j,b,n,h))}else!c&&l()}var h=!!a.test,i=a.load||a.both,j=a.callback||f,k=j,l=a.complete||f,m,n;c(h?a.yep:a.nope,!!i),i&&c(i)}var i,j,l=this.yepnope.loader;if(e(a))g(a,0,l,0);else if(w(a))for(i=0;i<a.length;i++)j=a[i],e(j)?g(j,0,l,0):w(j)?B(j):Object(j)===j&&h(j,l);else Object(a)===a&&h(a,l)},B.addPrefix=function(a,b){z[a]=b},B.addFilter=function(a){x.push(a)},B.errorTimeout=1e4,null==b.readyState&&b.addEventListener&&(b.readyState="loading",b.addEventListener("DOMContentLoaded",A=function(){b.removeEventListener("DOMContentLoaded",A,0),b.readyState="complete"},0)),a.yepnope=k(),a.yepnope.executeStack=h,a.yepnope.injectJs=function(a,c,d,e,i,j){var k=b.createElement("script"),l,o,e=e||B.errorTimeout;k.src=a;for(o in d)k.setAttribute(o,d[o]);c=j?h:c||f,k.onreadystatechange=k.onload=function(){!l&&g(k.readyState)&&(l=1,c(),k.onload=k.onreadystatechange=null)},m(function(){l||(l=1,c(1))},e),i?k.onload():n.parentNode.insertBefore(k,n)},a.yepnope.injectCss=function(a,c,d,e,g,i){var e=b.createElement("link"),j,c=i?h:c||f;e.href=a,e.rel="stylesheet",e.type="text/css";for(j in d)e.setAttribute(j,d[j]);g||(n.parentNode.insertBefore(e,n),m(c,0))}}(this,document),Modernizr.load=function(){yepnope.apply(window,[].slice.call(arguments,0))};


/**
 * jquery.gridrotator.js v1.1.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2012, Codrops
 * http://www.codrops.com
 */
;( function( $, window, undefined ) {
	
	'use strict';

	/*
	* debouncedresize: special jQuery event that happens once after a window resize
	*
	* latest version and complete README available on Github:
	* https://github.com/louisremi/jquery-smartresize/blob/master/jquery.debouncedresize.js
	*
	* Copyright 2011 @louis_remi
	* Licensed under the MIT license.
	*/
	var $event = $.event,
	$special,
	resizeTimeout;

	$special = $event.special.debouncedresize = {
		setup: function() {
			$( this ).on( "resize", $special.handler );
		},
		teardown: function() {
			$( this ).off( "resize", $special.handler );
		},
		handler: function( event, execAsap ) {
			// Save the context
			var context = this,
				args = arguments,
				dispatch = function() {
					// set correct event type
					event.type = "debouncedresize";
					$event.dispatch.apply( context, args );
				};

			if ( resizeTimeout ) {
				clearTimeout( resizeTimeout );
			}

			execAsap ?
				dispatch() :
				resizeTimeout = setTimeout( dispatch, $special.threshold );
		},
		threshold: 100
	};

	// http://www.hardcode.nl/subcategory_1/article_317-array-shuffle-function
	Array.prototype.shuffle = function() {
		var i=this.length,p,t;
		while (i--) {
			p = Math.floor(Math.random()*i);
			t = this[i];
			this[i]=this[p];
			this[p]=t;
		}
		return this;
	};

	// HTML5 PageVisibility API
	// http://www.html5rocks.com/en/tutorials/pagevisibility/intro/
	// by Joe Marini (@joemarini)
	function getHiddenProp(){
		var prefixes = ['webkit','moz','ms','o'];

		// if 'hidden' is natively supported just return it
		if ('hidden' in document) return 'hidden';

		// otherwise loop over all the known prefixes until we find one
		for (var i = 0; i < prefixes.length; i++){
			if ((prefixes[i] + 'Hidden') in document) 
				return prefixes[i] + 'Hidden';
		}

		// otherwise it's not supported
		return null;
	}
	function isHidden() {
		var prop = getHiddenProp();
		if (!prop) return false;

		return document[prop];
	}

	function isEmpty( obj ) {
		return Object.keys(obj).length === 0;
	}

	// global
	var $window = $( window ),
		Modernizr = window.Modernizr;

	$.GridRotator = function( options, element ) {
		
		this.$el = $( element );
		if( Modernizr.backgroundsize ) {

			var self = this;
			this.$el.addClass( 'box-aboutTeam-loading' );
			this._init( options );

		}
		
	};

	// the options
	$.GridRotator.defaults = {
		// number of rows
		rows : 4,
		// number of columns 
		columns : 10,
		w1400 : { rows : 4, columns : 12 },
		w1200 : { rows : 3, columns : 10 },
		w1024 : { rows : 3, columns : 8 },
		w768 : {rows : 3,columns : 7 },
		w480 : {rows : 3,columns : 5 },
		w320 : {rows : 2,columns : 4 },
		w240 : {rows : 2,columns : 3 },
		// step: number of items that are replaced at the same time
		// random || [some number]
		// note: for performance issues, the number "can't" be > options.maxStep
		step : 'random',
		// change it as you wish..
		maxStep : 3,
		// prevent user to click the items
		preventClick : true,
		// animation type
		// showHide || fadeInOut || 
		// slideLeft || slideRight || slideTop || slideBottom || 
		// rotateBottom || rotateLeft || rotateRight || rotateTop || 
		// scale ||
		// rotate3d ||
		// rotateLeftScale || rotateRightScale || rotateTopScale || rotateBottomScale || 
		// random
		animType : 'random',
		// animation speed
		animSpeed : 800,
		// animation easings
		animEasingOut : 'linear',
		animEasingIn: 'linear',
		// the item(s) will be replaced every 3 seconds
		// note: for performance issues, the time "can't" be < 300 ms
		interval : 3000,
		// if false the animations will not start
		// use false if onhover is true for example
		slideshow : true,
		// if true the items will switch when hovered
		onhover : false,
		// ids of elements that shouldn't change
		nochange : []
	};

	$.GridRotator.prototype = {

		_init : function( options ) {
			
			// options
			this.options = $.extend( true, {}, $.GridRotator.defaults, options );
			// cache some elements + variables
			this._config();

		},
		_config : function() {

			var self = this,
				transEndEventNames = {
					'WebkitTransition' : 'webkitTransitionEnd',
					'MozTransition' : 'transitionend',
					'OTransition' : 'oTransitionEnd',
					'msTransition' : 'MSTransitionEnd',
					'transition' : 'transitionend'
				};

			// support CSS transitions and 3d transforms
			this.supportTransitions = Modernizr.csstransitions;
			this.supportTransforms3D = Modernizr.csstransforms3d;

			this.transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ] + '.gridrotator';

			// all animation types for the random option
			this.animTypes = this.supportTransforms3D ? [
				'fadeInOut',
				'slideLeft', 
				'slideRight', 
				'slideTop', 
				'slideBottom', 
				'rotateLeft', 
				'rotateRight', 
				'rotateTop', 
				'rotateBottom', 
				'scale', 
				'rotate3d', 
				'rotateLeftScale', 
				'rotateRightScale', 
				'rotateTopScale', 
				'rotateBottomScale' ] :
				[ 'fadeInOut', 'slideLeft', 'slideRight', 'slideTop', 'slideBottom' ];

			this.animType = this.options.animType;

			if( this.animType !== 'random' && !this.supportTransforms3D && $.inArray( this.animType, this.animTypes ) === -1 && this.animType !== 'showHide' ) {

				// fallback to 'fadeInOut' if user sets a type which is not supported
				this.animType = 'fadeInOut';

			}

			this.animTypesTotal	= this.animTypes.length;

			// the <ul> where the items are placed
			this.$list = this.$el.children( 'ul' );
			// remove images and add background-image to anchors
			// preload the images before
			var loaded = 0,
				$imgs = this.$list.find( 'img' ),
				count = $imgs.length;

			$imgs.each( function() {

				var $img = $( this ), src = $img.attr( 'src' );

				$( '<img/>' ).load( function() {

					++loaded;
					$img.parent().css( 'background-image', 'url(' + src + ')' );

					if( loaded === count ) {

						$imgs.remove();
						self.$el.removeClass( 'box-aboutTeam-loading' );
						// the items
						self.$items = self.$list.children( 'li' );
						// make a copy of the items
						self.$itemsCache = self.$items.clone();
						// total number of items
						self.itemsTotal = self.$items.length;
						// the items that will be out of the grid
						// actually the item's child (anchor element)
						self.outItems= [];
						self._layout( function() {
							self._initEvents();
						} );
						// replace [options.step] items after [options.interval] time
						// the items that go out are randomly chosen, while the ones that get in
						// follow a "First In First Out" logic
						self._start();

					}

				} ).attr( 'src', src )
				 
			} );

		},
		_layout : function( callback ) {

			var self = this;

			// sets the grid dimentions based on the container's width
			this._setGridDim();

			// reset
			this.$list.empty();
			this.$items = this.$itemsCache.clone().appendTo( this.$list );
			
			var $outItems = this.$items.filter( ':gt(' + ( this.showTotal - 1 ) + ')' ),
				$outAItems = $outItems.children( 'a' );

			this.outItems.length = 0;

			$outAItems.each( function( i ) {
				self.outItems.push( $( this ) );
			} );

			$outItems.remove();

				// container's width
			var containerWidth = ( document.defaultView ) ? parseInt( document.defaultView.getComputedStyle( this.$el.get( 0 ), null ).width ) : this.$el.width(),
				// item's width
				itemWidth = Math.floor( containerWidth / this.columns ),
				// calculate gap
				gapWidth = containerWidth - ( this.columns * Math.floor( itemWidth ) );

			for( var i = 0; i < this.rows; ++i ) {

				for( var j = 0; j < this.columns; ++j ) {

					var idx = this.columns * i + j,
						$item = this.$items.eq( idx );

					$item.css( {
						width : j < Math.floor( gapWidth ) ? itemWidth + 1 : itemWidth,
						height : itemWidth
					} );

					if( $.inArray( idx, this.options.nochange ) !== -1 ) {
						$item.addClass( 'ri-nochange' ).data( 'nochange', true );
					}

				}

			}

			if( this.options.preventClick ) {

				this.$items.children().css( 'cursor', 'default' ).on( 'click.gridrotator', false );

			}

			if( callback ) {
				callback.call();
			}

		},
		// set the grid rows and columns
		_setGridDim	 : function() {

			// container's width
			var c_w = this.$el.width();

			// we will choose the number of rows/columns according to the container's width and the values set in the plugin options 
			switch( true ) {
				case ( c_w < 240 ) : this.rows = this.options.w240.rows; this.columns = this.options.w240.columns; break;
				case ( c_w < 320 ) : this.rows = this.options.w320.rows; this.columns = this.options.w320.columns; break;
				case ( c_w < 480 ) : this.rows = this.options.w480.rows; this.columns = this.options.w480.columns; break;
				case ( c_w < 768 ) : this.rows = this.options.w768.rows; this.columns = this.options.w768.columns; break;
				case ( c_w < 1024 ) : this.rows = this.options.w1024.rows; this.columns = this.options.w1024.columns; break;
				case ( c_w < 1200 ) : this.rows = this.options.w1200.rows; this.columns = this.options.w1200.columns; break;
				case ( c_w < 1400 ) : this.rows = this.options.w1400.rows; this.columns = this.options.w1400.columns; break;
				default : this.rows = this.options.rows; this.columns = this.options.columns; break;
			}

			this.showTotal = this.rows * this.columns;

		},
		// init window resize event
		_initEvents : function() {

			var self = this;

			$window.on( 'debouncedresize.gridrotator', function() {
				self._layout();
			} );

			// use the property name to generate the prefixed event name
			var visProp = getHiddenProp();
			
			// HTML5 PageVisibility API
			// http://www.html5rocks.com/en/tutorials/pagevisibility/intro/
			// by Joe Marini (@joemarini)
			if (visProp) {

				var evtname = visProp.replace(/[H|h]idden/,'') + 'visibilitychange';
				document.addEventListener(evtname, function() { self._visChange(); } );

			}

			if( !Modernizr.touch && this.options.onhover ) {
				
				self.$items.on( 'mouseenter.gridrotator', function() {

					var $item = $( this );
					if( !$item.data( 'active' ) && !$item.data( 'hovered' ) && !$item.data( 'nochange' ) ) {
						$item.data( 'hovered', true );
						self._replace( $item );
					}

				} ).on( 'mouseleave.gridrotator', function() {

					$( this ).data( 'hovered', false );

				} );

			}

		},
		_visChange : function() {

			isHidden() ? clearTimeout( this.playtimeout ) : this._start();

		},
		// start rotating elements
		_start : function() {

			if( this.showTotal < this.itemsTotal && this.options.slideshow ) {
				this._showNext();
			}

		},
		// get which type of animation
		_getAnimType : function() {

			return this.animType === 'random' ? this.animTypes[ Math.floor( Math.random() * this.animTypesTotal ) ] : this.animType;

		},
		// get css properties for the transition effect
		_getAnimProperties : function( $out ) {

			var startInProp = {}, startOutProp = {}, endInProp = {}, endOutProp = {},
				animType = this._getAnimType(), speed, delay = 0;

			switch( animType ) {

				case 'showHide' :
					
					speed = 0;
					endOutProp.opacity = 0;
					break;

				case 'fadeInOut' :

					endOutProp.opacity = 0;
					break;

				case 'slideLeft' :
					
					startInProp.left = $out.width();
					endInProp.left = 0;
					endOutProp.left = -$out.width();
					break;

				case 'slideRight' :
					
					startInProp.left = -$out.width();
					endInProp.left = 0;
					endOutProp.left = $out.width();
					break;

				case 'slideTop' :
					
					startInProp.top = $out.height();
					endInProp.top = 0;
					endOutProp.top = -$out.height();
					break;

				case 'slideBottom' :
					
					startInProp.top = -$out.height();
					endInProp.top = 0;
					endOutProp.top = $out.height();
					break;

				case 'rotateLeft' :
					
					speed = this.options.animSpeed / 2;
					startInProp.transform = 'rotateY(90deg)';
					endInProp.transform = 'rotateY(0deg)';
					delay = speed;
					endOutProp.transform = 'rotateY(-90deg)';
					break;

				case 'rotateRight' :
					
					speed = this.options.animSpeed / 2;
					startInProp.transform = 'rotateY(-90deg)';
					endInProp.transform = 'rotateY(0deg)';
					delay = speed;
					endOutProp.transform = 'rotateY(90deg)';
					break;

				case 'rotateTop' :
					
					speed = this.options.animSpeed / 2;
					startInProp.transform= 'rotateX(90deg)';
					endInProp.transform = 'rotateX(0deg)';
					delay = speed;
					endOutProp.transform = 'rotateX(-90deg)';
					break;

				case 'rotateBottom' :
					
					speed = this.options.animSpeed / 2;
					startInProp.transform = 'rotateX(-90deg)';
					endInProp.transform = 'rotateX(0deg)';
					delay = speed;
					endOutProp.transform = 'rotateX(90deg)';
					break;

				case 'scale' :
					
					speed = this.options.animSpeed / 2;
					startInProp.transform = 'scale(0)';
					startOutProp.transform = 'scale(1)';
					endInProp.transform = 'scale(1)';
					delay = speed;
					endOutProp.transform = 'scale(0)';
					break;

				case 'rotateLeftScale' :
					
					startOutProp.transform = 'scale(1)';
					speed = this.options.animSpeed / 2;	
					startInProp.transform = 'scale(0.3) rotateY(90deg)';
					endInProp.transform = 'scale(1) rotateY(0deg)';
					delay = speed;
					endOutProp.transform = 'scale(0.3) rotateY(-90deg)';
					break;

				case 'rotateRightScale' :
					
					startOutProp.transform = 'scale(1)';
					speed = this.options.animSpeed / 2;
					startInProp.transform = 'scale(0.3) rotateY(-90deg)';
					endInProp.transform = 'scale(1) rotateY(0deg)';
					delay = speed;
					endOutProp.transform = 'scale(0.3) rotateY(90deg)';
					break;

				case 'rotateTopScale' :
					
					startOutProp.transform = 'scale(1)';
					speed = this.options.animSpeed / 2;
					startInProp.transform = 'scale(0.3) rotateX(90deg)';
					endInProp.transform = 'scale(1) rotateX(0deg)';
					delay = speed;
					endOutProp.transform = 'scale(0.3) rotateX(-90deg)';
					break;

				case 'rotateBottomScale' :
					
					startOutProp.transform = 'scale(1)';
					speed = this.options.animSpeed / 2;
					startInProp.transform = 'scale(0.3) rotateX(-90deg)';
					endInProp.transform = 'scale(1) rotateX(0deg)';
					delay = speed;
					endOutProp.transform = 'scale(0.3) rotateX(90deg)';
					break;

				case 'rotate3d' :
					
					speed = this.options.animSpeed / 2;
					startInProp.transform = 'rotate3d( 1, 1, 0, 90deg )';
					endInProp.transform = 'rotate3d( 1, 1, 0, 0deg )';
					delay = speed;
					endOutProp.transform = 'rotate3d( 1, 1, 0, -90deg )';
					break;

			}

			return {
				startInProp : startInProp,
				startOutProp : startOutProp,
				endInProp : endInProp,
				endOutProp : endOutProp,				
				delay : delay,
				animSpeed : speed != undefined ? speed : this.options.animSpeed
			};

		},
		// show next [option.step] elements
		_showNext : function( time ) {

			var self = this;

			clearTimeout( this.playtimeout );

			this.playtimeout = setTimeout( function() {

				var step = self.options.step, max= self.options.maxStep, min = 1;
				
				if( max > self.showTotal ) {
					max = self.showTotal;
				}

					// number of items to swith at this point of time
				var nmbOut	= step === 'random' ? Math.floor( Math.random() * max + min ) : Math.min( Math.abs( step ) , max ) ,
					// array with random indexes. These will be the indexes of the items we will replace
					randArr	= self._getRandom( nmbOut, self.showTotal );

				for( var i = 0; i < nmbOut; ++i ) {

					// element to go out
					var $out = self.$items.eq( randArr[ i ] );

					// if element is active, which means it is currently animating,
					// then we need to get different positions.. 
					if( $out.data( 'active' ) || $out.data( 'nochange' ) ) {

						// one of the items is active, call again..
						self._showNext( 1 );
						return false;

					}

					self._replace( $out );

				}

				// again and again..
				self._showNext();

			}, time || Math.max( Math.abs( this.options.interval ) , 300 ) );

		},
		_replace : function( $out ) {

			$out.data( 'active', true );

			var self = this,
				$outA = $out.children( 'a:last' ),
				newElProp = {
					width : $outA.width(),
					height : $outA.height()
				};

			// element stays active
			$out.data( 'active', true );

			// get the element (anchor) that will go in (first one inserted in this.outItems)
			var $inA = this.outItems.shift();

			// save element that went out
			this.outItems.push( $outA.clone().css( 'transition', 'none' ) );
			
			// prepend in element
			$inA.css( newElProp ).prependTo( $out );

			var animProp = this._getAnimProperties( $outA );

			$inA.css( animProp.startInProp );
			$outA.css( animProp.startOutProp );
			
			this._setTransition( $inA, 'all', animProp.animSpeed, animProp.delay, this.options.animEasingIn );
			this._setTransition( $outA, 'all', animProp.animSpeed, 0, this.options.animEasingOut );

			this._applyTransition( $inA, animProp.endInProp, animProp.animSpeed, function() {

				var $el = $( this ),
					t = animProp.animSpeed === self.options.animSpeed && isEmpty( animProp.endInProp ) ? animProp.animSpeed : 0;
					
				setTimeout( function() {
					
					if( self.supportTransitions ) {
						$el.off( self.transEndEventName );
					}
					
					$el.next().remove();
					$el.parent().data( 'active', false );

				}, t );

			}, animProp.animSpeed === 0 || isEmpty( animProp.endInProp ) );
			this._applyTransition( $outA, animProp.endOutProp, animProp.animSpeed );

		},
		_getRandom : function( cnt, limit ) {

			var randArray = [];

			for( var i = 0; i < limit; ++i ) {
				randArray.push( i )
			}
			
			return randArray.shuffle().slice( 0, cnt );

		},
		_setTransition : function( el, prop, speed, delay, easing ) {

			setTimeout( function() {
				el.css( 'transition', prop + ' ' + speed + 'ms ' + delay + 'ms ' + easing );
			}, 25 );

		},
		_applyTransition : function( el, styleCSS, speed, fncomplete, force ) {

			var self = this;
			setTimeout( function() {
				$.fn.applyStyle = self.supportTransitions ? $.fn.css : $.fn.animate;

				if( fncomplete && self.supportTransitions ) {

					el.on( self.transEndEventName, fncomplete );

					if( force ) {
						fncomplete.call( el );					
					}

				}

				fncomplete = fncomplete || function() { return false; };

				el.stop().applyStyle( styleCSS, $.extend( true, [], { duration : speed + 'ms', complete : fncomplete } ) );
			}, 25 );

		}

	};
	
	var logError = function( message ) {

		if ( window.console ) {

			window.console.error( message );
		
		}

	};
	
	$.fn.gridrotator = function( options ) {

		var instance = $.data( this, 'gridrotator' );
		
		if ( typeof options === 'string' ) {
			
			var args = Array.prototype.slice.call( arguments, 1 );
			
			this.each(function() {
			
				if ( !instance ) {

					logError( "cannot call methods on gridrotator prior to initialization; " +
					"attempted to call method '" + options + "'" );
					return;
				
				}
				
				if ( !$.isFunction( instance[options] ) || options.charAt(0) === "_" ) {

					logError( "no such method '" + options + "' for gridrotator instance" );
					return;
				
				}
				
				instance[ options ].apply( instance, args );
			
			});
		
		} 
		else {
		
			this.each(function() {
				
				if ( instance ) {

					instance._init();
				
				}
				else {

					instance = $.data( this, 'gridrotator', new $.GridRotator( options, this ) );
				
				}

			});
		
		}
		
		return instance;
		
	};
	
} )( jQuery, window );



/**
 * HideSeek jQuery plugin
 *
 * @copyright Copyright 2015, Dimitris Krestos
 * @license   Apache License, Version 2.0 (http://www.opensource.org/licenses/apache2.0.php)
 * @link      http://vdw.staytuned.gr
 * @version   v0.8.0
 *
 * Dependencies are include in minified versions at the bottom:
 * 1. Highlight v4 by Johann Burkard
 *
 */

  /* Sample html structure

  <input name="search" placeholder="Start typing here" type="text" data-list=".list">
  <ul class="list">
    <li>item 1</li>
    <li>...</li>
    <li><a href="#">item 2</a></li>
  </ul>

  or

  <input name="search" placeholder="Start typing here" type="text" data-list=".list">
  <div class="list">
    <span>item 1</span>
    <span>...</span>
    <span>item 2</span>
  </div>

  or any similar structure...

  */

!function(e){"use strict";e.fn.hideseek=function(t){var s={list:".hideseek-data",nodata:"",attribute:"text",matches:!1,highlight:!1,ignore:"",headers:"",navigation:!1,ignore_accents:!1,hidden_mode:!1,min_chars:1},t=e.extend(s,t);return this.each(function(){var s=e(this);s.opts=[],e.map(["list","nodata","attribute","matches","highlight","ignore","headers","navigation","ignore_accents","hidden_mode","min_chars"],function(e){s.opts[e]=s.data(e)||t[e]}),s.opts.headers&&(s.opts.ignore+=s.opts.ignore?", "+s.opts.headers:s.opts.headers);var i=e(s.opts.list);s.opts.navigation&&s.attr("autocomplete","off"),s.opts.hidden_mode&&i.children().hide(),s.keyup(function(t){function n(e){s.opts.highlight?e.removeHighlight().highlight(h).show():e.show()}function o(e){return e.children(".selected:visible")}function a(e){return o(e).prevAll(":visible:first")}function r(e){return o(e).nextAll(":visible:first")}if(-1==[38,40,13].indexOf(t.keyCode)&&(8!=t.keyCode?s.val().length>=s.opts.min_chars:!0)){var h=s.val().toLowerCase();i.children(s.opts.ignore.trim()?":not("+s.opts.ignore+")":"").removeClass("selected").each(function(){var t=("text"!=s.opts.attribute?e(this).attr(s.opts.attribute)||"":e(this).text()).toLowerCase(),i=-1==t.removeAccents(s.opts.ignore_accents).indexOf(h)||h===(s.opts.hidden_mode?"":!1);i?e(this).hide():(n(e(this)),s.opts.matches&&null!==h.match(new RegExp(Object.keys(s.opts.matches)[0]))&&(null!==t.match(new RegExp(Object.values(s.opts.matches)[0]))?n(e(this)):e(this).hide())),s.trigger("_after_each")}),s.opts.nodata&&(i.find(".no-results").remove(),i.children(':not([style*="display: none"])').length||(i.children().first().clone().removeHighlight().addClass("no-results").show().prependTo(s.opts.list).text(s.opts.nodata),s.trigger("_after_nodata"))),s.opts.headers&&e(s.opts.headers,i).each(function(){e(this).nextUntil(s.opts.headers).not('[style*="display: none"],'+s.opts.ignore).length?e(this).show():e(this).hide()}),s.trigger("_after")}s.opts.navigation&&(38==t.keyCode?o(i).length?(a(i).addClass("selected"),o(i).last().removeClass("selected")):i.children(":visible").last().addClass("selected"):40==t.keyCode?o(i).length?(r(i).addClass("selected"),o(i).first().removeClass("selected")):i.children(":visible").first().addClass("selected"):13==t.keyCode&&(o(i).find("a").length?document.location=o(i).find("a").attr("href"):s.val(o(i).text())))})})},e(document).ready(function(){e('[data-toggle="hideseek"]').hideseek()})}(jQuery);

/*

highlight v4

Highlights arbitrary terms.

<http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html>

MIT license.

Johann Burkard
<http://johannburkard.de>
<mailto:jb@eaio.com>

*/
jQuery.fn.highlight=function(t){function e(t,i){var n=0;if(3==t.nodeType){var a=t.data.removeAccents(true).toUpperCase().indexOf(i);if(a>=0){var s=document.createElement("mark");s.className="highlight";var r=t.splitText(a);r.splitText(i.length);var o=r.cloneNode(!0);s.appendChild(o),r.parentNode.replaceChild(s,r),n=1}}else if(1==t.nodeType&&t.childNodes&&!/(script|style)/i.test(t.tagName))for(var h=0;h<t.childNodes.length;++h)h+=e(t.childNodes[h],i);return n}return this.length&&t&&t.length?this.each(function(){e(this,t.toUpperCase())}):this},jQuery.fn.removeHighlight=function(){return this.find("mark.highlight").each(function(){with(this.parentNode.firstChild.nodeName,this.parentNode)replaceChild(this.firstChild,this),normalize()}).end()};

// Ignore accents
String.prototype.removeAccents=function(e){return e?this.replace(/[áàãâä]/gi,"a").replace(/[éè¨ê]/gi,"e").replace(/[íìïî]/gi,"i").replace(/[óòöôõ]/gi,"o").replace(/[úùüû]/gi,"u").replace(/[ç]/gi,"c").replace(/[ñ]/gi,"n"):this};


/*
 *
 * More info at [www.dropzonejs.com](http://www.dropzonejs.com)
 *
 * Copyright (c) 2012, Matias Meno
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

(function() {
  var Dropzone, Emitter, ExifRestore, camelize, contentLoaded, detectVerticalSquash, drawImageIOSFix, noop, without,
    slice = [].slice,
    extend1 = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  noop = function() {};

  Emitter = (function() {
    function Emitter() {}

    Emitter.prototype.addEventListener = Emitter.prototype.on;

    Emitter.prototype.on = function(event, fn) {
      this._callbacks = this._callbacks || {};
      if (!this._callbacks[event]) {
        this._callbacks[event] = [];
      }
      this._callbacks[event].push(fn);
      return this;
    };

    Emitter.prototype.emit = function() {
      var args, callback, callbacks, event, j, len;
      event = arguments[0], args = 2 <= arguments.length ? slice.call(arguments, 1) : [];
      this._callbacks = this._callbacks || {};
      callbacks = this._callbacks[event];
      if (callbacks) {
        for (j = 0, len = callbacks.length; j < len; j++) {
          callback = callbacks[j];
          callback.apply(this, args);
        }
      }
      return this;
    };

    Emitter.prototype.removeListener = Emitter.prototype.off;

    Emitter.prototype.removeAllListeners = Emitter.prototype.off;

    Emitter.prototype.removeEventListener = Emitter.prototype.off;

    Emitter.prototype.off = function(event, fn) {
      var callback, callbacks, i, j, len;
      if (!this._callbacks || arguments.length === 0) {
        this._callbacks = {};
        return this;
      }
      callbacks = this._callbacks[event];
      if (!callbacks) {
        return this;
      }
      if (arguments.length === 1) {
        delete this._callbacks[event];
        return this;
      }
      for (i = j = 0, len = callbacks.length; j < len; i = ++j) {
        callback = callbacks[i];
        if (callback === fn) {
          callbacks.splice(i, 1);
          break;
        }
      }
      return this;
    };

    return Emitter;

  })();

  Dropzone = (function(superClass) {
    var extend, resolveOption;

    extend1(Dropzone, superClass);

    Dropzone.prototype.Emitter = Emitter;


    /*
    This is a list of all available events you can register on a dropzone object.
    
    You can register an event handler like this:
    
        dropzone.on("dragEnter", function() { });
     */

    Dropzone.prototype.events = ["drop", "dragstart", "dragend", "dragenter", "dragover", "dragleave", "addedfile", "addedfiles", "removedfile", "thumbnail", "error", "errormultiple", "processing", "processingmultiple", "uploadprogress", "totaluploadprogress", "sending", "sendingmultiple", "success", "successmultiple", "canceled", "canceledmultiple", "complete", "completemultiple", "reset", "maxfilesexceeded", "maxfilesreached", "queuecomplete"];

    Dropzone.prototype.defaultOptions = {
      url: null,
      method: "post",
      withCredentials: false,
      timeout: 30000,
      parallelUploads: 2,
      uploadMultiple: false,
      maxFilesize: 256,
      paramName: "file",
      createImageThumbnails: true,
      maxThumbnailFilesize: 10,
      thumbnailWidth: 120,
      thumbnailHeight: 120,
      thumbnailMethod: 'crop',
      resizeWidth: null,
      resizeHeight: null,
      resizeMimeType: null,
      resizeQuality: 0.8,
      resizeMethod: 'contain',
      filesizeBase: 1000,
      maxFiles: null,
      params: {},
      headers: null,
      clickable: true,
      ignoreHiddenFiles: true,
      acceptedFiles: null,
      acceptedMimeTypes: null,
      autoProcessQueue: true,
      autoQueue: true,
      addRemoveLinks: false,
      previewsContainer: null,
      hiddenInputContainer: "body",
      capture: null,
      renameFilename: null,
      renameFile: null,
      forceFallback: false,
      dictDefaultMessage: "Drop files here to upload",
      dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
      dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",
      dictFileTooBig: "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
      dictInvalidFileType: "You can't upload files of this type.",
      dictResponseError: "Server responded with {{statusCode}} code.",
      dictCancelUpload: "Cancel upload",
      dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
      dictRemoveFile: "Remove file",
      dictRemoveFileConfirmation: null,
      dictMaxFilesExceeded: "You can not upload any more files.",
      dictFileSizeUnits: {
        tb: "TB",
        gb: "GB",
        mb: "MB",
        kb: "KB",
        b: "b"
      },
      init: function() {
        return noop;
      },
      accept: function(file, done) {
        return done();
      },
      fallback: function() {
        var child, j, len, messageElement, ref, span;
        this.element.className = this.element.className + " dz-browser-not-supported";
        ref = this.element.getElementsByTagName("div");
        for (j = 0, len = ref.length; j < len; j++) {
          child = ref[j];
          if (/(^| )dz-message($| )/.test(child.className)) {
            messageElement = child;
            child.className = "dz-message";
            continue;
          }
        }
        if (!messageElement) {
          messageElement = Dropzone.createElement("<div class=\"dz-message\"><span></span></div>");
          this.element.appendChild(messageElement);
        }
        span = messageElement.getElementsByTagName("span")[0];
        if (span) {
          if (span.textContent != null) {
            span.textContent = this.options.dictFallbackMessage;
          } else if (span.innerText != null) {
            span.innerText = this.options.dictFallbackMessage;
          }
        }
        return this.element.appendChild(this.getFallbackForm());
      },
      resize: function(file, width, height, resizeMethod) {
        var info, srcRatio, trgRatio;
        info = {
          srcX: 0,
          srcY: 0,
          srcWidth: file.width,
          srcHeight: file.height
        };
        srcRatio = file.width / file.height;
        if ((width == null) && (height == null)) {
          width = info.srcWidth;
          height = info.srcHeight;
        } else if (width == null) {
          width = height * srcRatio;
        } else if (height == null) {
          height = width / srcRatio;
        }
        width = Math.min(width, info.srcWidth);
        height = Math.min(height, info.srcHeight);
        trgRatio = width / height;
        if (info.srcWidth > width || info.srcHeight > height) {
          if (resizeMethod === 'crop') {
            if (srcRatio > trgRatio) {
              info.srcHeight = file.height;
              info.srcWidth = info.srcHeight * trgRatio;
            } else {
              info.srcWidth = file.width;
              info.srcHeight = info.srcWidth / trgRatio;
            }
          } else if (resizeMethod === 'contain') {
            if (srcRatio > trgRatio) {
              height = width / srcRatio;
            } else {
              width = height * srcRatio;
            }
          } else {
            throw new Error("Unknown resizeMethod '" + resizeMethod + "'");
          }
        }
        info.srcX = (file.width - info.srcWidth) / 2;
        info.srcY = (file.height - info.srcHeight) / 2;
        info.trgWidth = width;
        info.trgHeight = height;
        return info;
      },
      transformFile: function(file, done) {
        if ((this.options.resizeWidth || this.options.resizeHeight) && file.type.match(/image.*/)) {
          return this.resizeImage(file, this.options.resizeWidth, this.options.resizeHeight, this.options.resizeMethod, done);
        } else {
          return done(file);
        }
      },
      previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-image\"><img data-dz-thumbnail /></div>\n  <div class=\"dz-details\">\n    <div class=\"dz-size\"><span data-dz-size></span></div>\n    <div class=\"dz-filename\"><span data-dz-name></span></div>\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n  <div class=\"dz-success-mark\">\n    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:sketch=\"http://www.bohemiancoding.com/sketch/ns\">\n      <title>Check</title>\n      <defs></defs>\n      <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\" sketch:type=\"MSPage\">\n        <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\" id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\" fill-opacity=\"0.816519475\" fill=\"#FFFFFF\" sketch:type=\"MSShapeGroup\"></path>\n      </g>\n    </svg>\n  </div>\n  <div class=\"dz-error-mark\">\n    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:sketch=\"http://www.bohemiancoding.com/sketch/ns\">\n      <title>Error</title>\n      <defs></defs>\n      <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\" sketch:type=\"MSPage\">\n        <g id=\"Check-+-Oval-2\" sketch:type=\"MSLayerGroup\" stroke=\"#747474\" stroke-opacity=\"0.198794158\" fill=\"#FFFFFF\" fill-opacity=\"0.816519475\">\n          <path d=\"M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\" id=\"Oval-2\" sketch:type=\"MSShapeGroup\"></path>\n        </g>\n      </g>\n    </svg>\n  </div>\n</div>",

      /*
      Those functions register themselves to the events on init and handle all
      the user interface specific stuff. Overwriting them won't break the upload
      but can break the way it's displayed.
      You can overwrite them if you don't like the default behavior. If you just
      want to add an additional event handler, register it on the dropzone object
      and don't overwrite those options.
       */
      drop: function(e) {
        return this.element.classList.remove("dz-drag-hover");
      },
      dragstart: noop,
      dragend: function(e) {
        return this.element.classList.remove("dz-drag-hover");
      },
      dragenter: function(e) {
        return this.element.classList.add("dz-drag-hover");
      },
      dragover: function(e) {
        return this.element.classList.add("dz-drag-hover");
      },
      dragleave: function(e) {
        return this.element.classList.remove("dz-drag-hover");
      },
      paste: noop,
      reset: function() {
        return this.element.classList.remove("dz-started");
      },
      addedfile: function(file) {
        var j, k, l, len, len1, len2, node, ref, ref1, ref2, removeFileEvent, removeLink, results;
        if (this.element === this.previewsContainer) {
          this.element.classList.add("dz-started");
        }
        if (this.previewsContainer) {
          file.previewElement = Dropzone.createElement(this.options.previewTemplate.trim());
          file.previewTemplate = file.previewElement;
          this.previewsContainer.appendChild(file.previewElement);
          ref = file.previewElement.querySelectorAll("[data-dz-name]");
          for (j = 0, len = ref.length; j < len; j++) {
            node = ref[j];
            node.textContent = file.name;
          }
          ref1 = file.previewElement.querySelectorAll("[data-dz-size]");
          for (k = 0, len1 = ref1.length; k < len1; k++) {
            node = ref1[k];
            node.innerHTML = this.filesize(file.size);
          }
          if (this.options.addRemoveLinks) {
            file._removeLink = Dropzone.createElement("<a class=\"dz-remove\" href=\"javascript:undefined;\" data-dz-remove>" + this.options.dictRemoveFile + "</a>");
            file.previewElement.appendChild(file._removeLink);
          }
          removeFileEvent = (function(_this) {
            return function(e) {
              e.preventDefault();
              e.stopPropagation();
              if (file.status === Dropzone.UPLOADING) {
                return Dropzone.confirm(_this.options.dictCancelUploadConfirmation, function() {
                  return _this.removeFile(file);
                });
              } else {
                if (_this.options.dictRemoveFileConfirmation) {
                  return Dropzone.confirm(_this.options.dictRemoveFileConfirmation, function() {
                    return _this.removeFile(file);
                  });
                } else {
                  return _this.removeFile(file);
                }
              }
            };
          })(this);
          ref2 = file.previewElement.querySelectorAll("[data-dz-remove]");
          results = [];
          for (l = 0, len2 = ref2.length; l < len2; l++) {
            removeLink = ref2[l];
            results.push(removeLink.addEventListener("click", removeFileEvent));
          }
          return results;
        }
      },
      removedfile: function(file) {
        var ref;
        if (file.previewElement) {
          if ((ref = file.previewElement) != null) {
            ref.parentNode.removeChild(file.previewElement);
          }
        }
        return this._updateMaxFilesReachedClass();
      },
      thumbnail: function(file, dataUrl) {
        var j, len, ref, thumbnailElement;
        if (file.previewElement) {
          file.previewElement.classList.remove("dz-file-preview");
          ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
          for (j = 0, len = ref.length; j < len; j++) {
            thumbnailElement = ref[j];
            thumbnailElement.alt = file.name;
            thumbnailElement.src = dataUrl;
          }
          return setTimeout(((function(_this) {
            return function() {
              return file.previewElement.classList.add("dz-image-preview");
            };
          })(this)), 1);
        }
      },
      error: function(file, message) {
        var j, len, node, ref, results;
        if (file.previewElement) {
          file.previewElement.classList.add("dz-error");
          if (typeof message !== "String" && message.error) {
            message = message.error;
          }
          ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
          results = [];
          for (j = 0, len = ref.length; j < len; j++) {
            node = ref[j];
            results.push(node.textContent = message);
          }
          return results;
        }
      },
      errormultiple: noop,
      processing: function(file) {
        if (file.previewElement) {
          file.previewElement.classList.add("dz-processing");
          if (file._removeLink) {
            return file._removeLink.textContent = this.options.dictCancelUpload;
          }
        }
      },
      processingmultiple: noop,
      uploadprogress: function(file, progress, bytesSent) {
        var j, len, node, ref, results;
        if (file.previewElement) {
          ref = file.previewElement.querySelectorAll("[data-dz-uploadprogress]");
          results = [];
          for (j = 0, len = ref.length; j < len; j++) {
            node = ref[j];
            if (node.nodeName === 'PROGRESS') {
              results.push(node.value = progress);
            } else {
              results.push(node.style.width = progress + "%");
            }
          }
          return results;
        }
      },
      totaluploadprogress: noop,
      sending: noop,
      sendingmultiple: noop,
      success: function(file) {
        if (file.previewElement) {
          return file.previewElement.classList.add("dz-success");
        }
      },
      successmultiple: noop,
      canceled: function(file) {
        return this.emit("error", file, "Upload canceled.");
      },
      canceledmultiple: noop,
      complete: function(file) {
        if (file._removeLink) {
          file._removeLink.textContent = this.options.dictRemoveFile;
        }
        if (file.previewElement) {
          return file.previewElement.classList.add("dz-complete");
        }
      },
      completemultiple: noop,
      maxfilesexceeded: noop,
      maxfilesreached: noop,
      queuecomplete: noop,
      addedfiles: noop
    };

    extend = function() {
      var j, key, len, object, objects, target, val;
      target = arguments[0], objects = 2 <= arguments.length ? slice.call(arguments, 1) : [];
      for (j = 0, len = objects.length; j < len; j++) {
        object = objects[j];
        for (key in object) {
          val = object[key];
          target[key] = val;
        }
      }
      return target;
    };

    function Dropzone(element1, options) {
      var elementOptions, fallback, ref;
      this.element = element1;
      this.version = Dropzone.version;
      this.defaultOptions.previewTemplate = this.defaultOptions.previewTemplate.replace(/\n*/g, "");
      this.clickableElements = [];
      this.listeners = [];
      this.files = [];
      if (typeof this.element === "string") {
        this.element = document.querySelector(this.element);
      }
      if (!(this.element && (this.element.nodeType != null))) {
        throw new Error("Invalid dropzone element.");
      }
      if (this.element.dropzone) {
        throw new Error("Dropzone already attached.");
      }
      Dropzone.instances.push(this);
      this.element.dropzone = this;
      elementOptions = (ref = Dropzone.optionsForElement(this.element)) != null ? ref : {};
      this.options = extend({}, this.defaultOptions, elementOptions, options != null ? options : {});
      if (this.options.forceFallback || !Dropzone.isBrowserSupported()) {
        return this.options.fallback.call(this);
      }
      if (this.options.url == null) {
        this.options.url = this.element.getAttribute("action");
      }
      if (!this.options.url) {
        throw new Error("No URL provided.");
      }
      if (this.options.acceptedFiles && this.options.acceptedMimeTypes) {
        throw new Error("You can't provide both 'acceptedFiles' and 'acceptedMimeTypes'. 'acceptedMimeTypes' is deprecated.");
      }
      if (this.options.acceptedMimeTypes) {
        this.options.acceptedFiles = this.options.acceptedMimeTypes;
        delete this.options.acceptedMimeTypes;
      }
      if (this.options.renameFilename != null) {
        this.options.renameFile = (function(_this) {
          return function(file) {
            return _this.options.renameFilename.call(_this, file.name, file);
          };
        })(this);
      }
      this.options.method = this.options.method.toUpperCase();
      if ((fallback = this.getExistingFallback()) && fallback.parentNode) {
        fallback.parentNode.removeChild(fallback);
      }
      if (this.options.previewsContainer !== false) {
        if (this.options.previewsContainer) {
          this.previewsContainer = Dropzone.getElement(this.options.previewsContainer, "previewsContainer");
        } else {
          this.previewsContainer = this.element;
        }
      }
      if (this.options.clickable) {
        if (this.options.clickable === true) {
          this.clickableElements = [this.element];
        } else {
          this.clickableElements = Dropzone.getElements(this.options.clickable, "clickable");
        }
      }
      this.init();
    }

    Dropzone.prototype.getAcceptedFiles = function() {
      var file, j, len, ref, results;
      ref = this.files;
      results = [];
      for (j = 0, len = ref.length; j < len; j++) {
        file = ref[j];
        if (file.accepted) {
          results.push(file);
        }
      }
      return results;
    };

    Dropzone.prototype.getRejectedFiles = function() {
      var file, j, len, ref, results;
      ref = this.files;
      results = [];
      for (j = 0, len = ref.length; j < len; j++) {
        file = ref[j];
        if (!file.accepted) {
          results.push(file);
        }
      }
      return results;
    };

    Dropzone.prototype.getFilesWithStatus = function(status) {
      var file, j, len, ref, results;
      ref = this.files;
      results = [];
      for (j = 0, len = ref.length; j < len; j++) {
        file = ref[j];
        if (file.status === status) {
          results.push(file);
        }
      }
      return results;
    };

    Dropzone.prototype.getQueuedFiles = function() {
      return this.getFilesWithStatus(Dropzone.QUEUED);
    };

    Dropzone.prototype.getUploadingFiles = function() {
      return this.getFilesWithStatus(Dropzone.UPLOADING);
    };

    Dropzone.prototype.getAddedFiles = function() {
      return this.getFilesWithStatus(Dropzone.ADDED);
    };

    Dropzone.prototype.getActiveFiles = function() {
      var file, j, len, ref, results;
      ref = this.files;
      results = [];
      for (j = 0, len = ref.length; j < len; j++) {
        file = ref[j];
        if (file.status === Dropzone.UPLOADING || file.status === Dropzone.QUEUED) {
          results.push(file);
        }
      }
      return results;
    };

    Dropzone.prototype.init = function() {
      var eventName, j, len, noPropagation, ref, ref1, setupHiddenFileInput;
      if (this.element.tagName === "form") {
        this.element.setAttribute("enctype", "multipart/form-data");
      }
      if (this.element.classList.contains("dropzone") && !this.element.querySelector(".dz-message")) {
        this.element.appendChild(Dropzone.createElement("<div class=\"dz-default dz-message\"><span>" + this.options.dictDefaultMessage + "</span></div>"));
      }
      if (this.clickableElements.length) {
        setupHiddenFileInput = (function(_this) {
          return function() {
            if (_this.hiddenFileInput) {
              _this.hiddenFileInput.parentNode.removeChild(_this.hiddenFileInput);
            }
            _this.hiddenFileInput = document.createElement("input");
            _this.hiddenFileInput.setAttribute("type", "file");
            if ((_this.options.maxFiles == null) || _this.options.maxFiles > 1) {
              _this.hiddenFileInput.setAttribute("multiple", "multiple");
            }
            _this.hiddenFileInput.className = "dz-hidden-input";
            if (_this.options.acceptedFiles != null) {
              _this.hiddenFileInput.setAttribute("accept", _this.options.acceptedFiles);
            }
            if (_this.options.capture != null) {
              _this.hiddenFileInput.setAttribute("capture", _this.options.capture);
            }
            _this.hiddenFileInput.style.visibility = "hidden";
            _this.hiddenFileInput.style.position = "absolute";
            _this.hiddenFileInput.style.top = "0";
            _this.hiddenFileInput.style.left = "0";
            _this.hiddenFileInput.style.height = "0";
            _this.hiddenFileInput.style.width = "0";
            document.querySelector(_this.options.hiddenInputContainer).appendChild(_this.hiddenFileInput);
            return _this.hiddenFileInput.addEventListener("change", function() {
              var file, files, j, len;
              files = _this.hiddenFileInput.files;
              if (files.length) {
                for (j = 0, len = files.length; j < len; j++) {
                  file = files[j];
                  _this.addFile(file);
                }
              }
              _this.emit("addedfiles", files);
              return setupHiddenFileInput();
            });
          };
        })(this);
        setupHiddenFileInput();
      }
      this.URL = (ref = window.URL) != null ? ref : window.webkitURL;
      ref1 = this.events;
      for (j = 0, len = ref1.length; j < len; j++) {
        eventName = ref1[j];
        this.on(eventName, this.options[eventName]);
      }
      this.on("uploadprogress", (function(_this) {
        return function() {
          return _this.updateTotalUploadProgress();
        };
      })(this));
      this.on("removedfile", (function(_this) {
        return function() {
          return _this.updateTotalUploadProgress();
        };
      })(this));
      this.on("canceled", (function(_this) {
        return function(file) {
          return _this.emit("complete", file);
        };
      })(this));
      this.on("complete", (function(_this) {
        return function(file) {
          if (_this.getAddedFiles().length === 0 && _this.getUploadingFiles().length === 0 && _this.getQueuedFiles().length === 0) {
            return setTimeout((function() {
              return _this.emit("queuecomplete");
            }), 0);
          }
        };
      })(this));
      noPropagation = function(e) {
        e.stopPropagation();
        if (e.preventDefault) {
          return e.preventDefault();
        } else {
          return e.returnValue = false;
        }
      };
      this.listeners = [
        {
          element: this.element,
          events: {
            "dragstart": (function(_this) {
              return function(e) {
                return _this.emit("dragstart", e);
              };
            })(this),
            "dragenter": (function(_this) {
              return function(e) {
                noPropagation(e);
                return _this.emit("dragenter", e);
              };
            })(this),
            "dragover": (function(_this) {
              return function(e) {
                var efct;
                try {
                  efct = e.dataTransfer.effectAllowed;
                } catch (undefined) {}
                e.dataTransfer.dropEffect = 'move' === efct || 'linkMove' === efct ? 'move' : 'copy';
                noPropagation(e);
                return _this.emit("dragover", e);
              };
            })(this),
            "dragleave": (function(_this) {
              return function(e) {
                return _this.emit("dragleave", e);
              };
            })(this),
            "drop": (function(_this) {
              return function(e) {
                noPropagation(e);
                return _this.drop(e);
              };
            })(this),
            "dragend": (function(_this) {
              return function(e) {
                return _this.emit("dragend", e);
              };
            })(this)
          }
        }
      ];
      this.clickableElements.forEach((function(_this) {
        return function(clickableElement) {
          return _this.listeners.push({
            element: clickableElement,
            events: {
              "click": function(evt) {
                if ((clickableElement !== _this.element) || (evt.target === _this.element || Dropzone.elementInside(evt.target, _this.element.querySelector(".dz-message")))) {
                  _this.hiddenFileInput.click();
                }
                return true;
              }
            }
          });
        };
      })(this));
      this.enable();
      return this.options.init.call(this);
    };

    Dropzone.prototype.destroy = function() {
      var ref;
      this.disable();
      this.removeAllFiles(true);
      if ((ref = this.hiddenFileInput) != null ? ref.parentNode : void 0) {
        this.hiddenFileInput.parentNode.removeChild(this.hiddenFileInput);
        this.hiddenFileInput = null;
      }
      delete this.element.dropzone;
      return Dropzone.instances.splice(Dropzone.instances.indexOf(this), 1);
    };

    Dropzone.prototype.updateTotalUploadProgress = function() {
      var activeFiles, file, j, len, ref, totalBytes, totalBytesSent, totalUploadProgress;
      totalBytesSent = 0;
      totalBytes = 0;
      activeFiles = this.getActiveFiles();
      if (activeFiles.length) {
        ref = this.getActiveFiles();
        for (j = 0, len = ref.length; j < len; j++) {
          file = ref[j];
          totalBytesSent += file.upload.bytesSent;
          totalBytes += file.upload.total;
        }
        totalUploadProgress = 100 * totalBytesSent / totalBytes;
      } else {
        totalUploadProgress = 100;
      }
      return this.emit("totaluploadprogress", totalUploadProgress, totalBytes, totalBytesSent);
    };

    Dropzone.prototype._getParamName = function(n) {
      if (typeof this.options.paramName === "function") {
        return this.options.paramName(n);
      } else {
        return "" + this.options.paramName + (this.options.uploadMultiple ? "[" + n + "]" : "");
      }
    };

    Dropzone.prototype._renameFile = function(file) {
      if (typeof this.options.renameFile !== "function") {
        return file.name;
      }
      return this.options.renameFile(file);
    };

    Dropzone.prototype.getFallbackForm = function() {
      var existingFallback, fields, fieldsString, form;
      if (existingFallback = this.getExistingFallback()) {
        return existingFallback;
      }
      fieldsString = "<div class=\"dz-fallback\">";
      if (this.options.dictFallbackText) {
        fieldsString += "<p>" + this.options.dictFallbackText + "</p>";
      }
      fieldsString += "<input type=\"file\" name=\"" + (this._getParamName(0)) + "\" " + (this.options.uploadMultiple ? 'multiple="multiple"' : void 0) + " /><input type=\"submit\" value=\"Upload!\"></div>";
      fields = Dropzone.createElement(fieldsString);
      if (this.element.tagName !== "FORM") {
        form = Dropzone.createElement("<form action=\"" + this.options.url + "\" enctype=\"multipart/form-data\" method=\"" + this.options.method + "\"></form>");
        form.appendChild(fields);
      } else {
        this.element.setAttribute("enctype", "multipart/form-data");
        this.element.setAttribute("method", this.options.method);
      }
      return form != null ? form : fields;
    };

    Dropzone.prototype.getExistingFallback = function() {
      var fallback, getFallback, j, len, ref, tagName;
      getFallback = function(elements) {
        var el, j, len;
        for (j = 0, len = elements.length; j < len; j++) {
          el = elements[j];
          if (/(^| )fallback($| )/.test(el.className)) {
            return el;
          }
        }
      };
      ref = ["div", "form"];
      for (j = 0, len = ref.length; j < len; j++) {
        tagName = ref[j];
        if (fallback = getFallback(this.element.getElementsByTagName(tagName))) {
          return fallback;
        }
      }
    };

    Dropzone.prototype.setupEventListeners = function() {
      var elementListeners, event, j, len, listener, ref, results;
      ref = this.listeners;
      results = [];
      for (j = 0, len = ref.length; j < len; j++) {
        elementListeners = ref[j];
        results.push((function() {
          var ref1, results1;
          ref1 = elementListeners.events;
          results1 = [];
          for (event in ref1) {
            listener = ref1[event];
            results1.push(elementListeners.element.addEventListener(event, listener, false));
          }
          return results1;
        })());
      }
      return results;
    };

    Dropzone.prototype.removeEventListeners = function() {
      var elementListeners, event, j, len, listener, ref, results;
      ref = this.listeners;
      results = [];
      for (j = 0, len = ref.length; j < len; j++) {
        elementListeners = ref[j];
        results.push((function() {
          var ref1, results1;
          ref1 = elementListeners.events;
          results1 = [];
          for (event in ref1) {
            listener = ref1[event];
            results1.push(elementListeners.element.removeEventListener(event, listener, false));
          }
          return results1;
        })());
      }
      return results;
    };

    Dropzone.prototype.disable = function() {
      var file, j, len, ref, results;
      this.clickableElements.forEach(function(element) {
        return element.classList.remove("dz-clickable");
      });
      this.removeEventListeners();
      ref = this.files;
      results = [];
      for (j = 0, len = ref.length; j < len; j++) {
        file = ref[j];
        results.push(this.cancelUpload(file));
      }
      return results;
    };

    Dropzone.prototype.enable = function() {
      this.clickableElements.forEach(function(element) {
        return element.classList.add("dz-clickable");
      });
      return this.setupEventListeners();
    };

    Dropzone.prototype.filesize = function(size) {
      var cutoff, i, j, len, selectedSize, selectedUnit, unit, units;
      selectedSize = 0;
      selectedUnit = "b";
      if (size > 0) {
        units = ['tb', 'gb', 'mb', 'kb', 'b'];
        for (i = j = 0, len = units.length; j < len; i = ++j) {
          unit = units[i];
          cutoff = Math.pow(this.options.filesizeBase, 4 - i) / 10;
          if (size >= cutoff) {
            selectedSize = size / Math.pow(this.options.filesizeBase, 4 - i);
            selectedUnit = unit;
            break;
          }
        }
        selectedSize = Math.round(10 * selectedSize) / 10;
      }
      return "<strong>" + selectedSize + "</strong> " + this.options.dictFileSizeUnits[selectedUnit];
    };

    Dropzone.prototype._updateMaxFilesReachedClass = function() {
      if ((this.options.maxFiles != null) && this.getAcceptedFiles().length >= this.options.maxFiles) {
        if (this.getAcceptedFiles().length === this.options.maxFiles) {
          this.emit('maxfilesreached', this.files);
        }
        return this.element.classList.add("dz-max-files-reached");
      } else {
        return this.element.classList.remove("dz-max-files-reached");
      }
    };

    Dropzone.prototype.drop = function(e) {
      var files, items;
      if (!e.dataTransfer) {
        return;
      }
      this.emit("drop", e);
      files = e.dataTransfer.files;
      this.emit("addedfiles", files);
      if (files.length) {
        items = e.dataTransfer.items;
        if (items && items.length && (items[0].webkitGetAsEntry != null)) {
          this._addFilesFromItems(items);
        } else {
          this.handleFiles(files);
        }
      }
    };

    Dropzone.prototype.paste = function(e) {
      var items, ref;
      if ((e != null ? (ref = e.clipboardData) != null ? ref.items : void 0 : void 0) == null) {
        return;
      }
      this.emit("paste", e);
      items = e.clipboardData.items;
      if (items.length) {
        return this._addFilesFromItems(items);
      }
    };

    Dropzone.prototype.handleFiles = function(files) {
      var file, j, len, results;
      results = [];
      for (j = 0, len = files.length; j < len; j++) {
        file = files[j];
        results.push(this.addFile(file));
      }
      return results;
    };

    Dropzone.prototype._addFilesFromItems = function(items) {
      var entry, item, j, len, results;
      results = [];
      for (j = 0, len = items.length; j < len; j++) {
        item = items[j];
        if ((item.webkitGetAsEntry != null) && (entry = item.webkitGetAsEntry())) {
          if (entry.isFile) {
            results.push(this.addFile(item.getAsFile()));
          } else if (entry.isDirectory) {
            results.push(this._addFilesFromDirectory(entry, entry.name));
          } else {
            results.push(void 0);
          }
        } else if (item.getAsFile != null) {
          if ((item.kind == null) || item.kind === "file") {
            results.push(this.addFile(item.getAsFile()));
          } else {
            results.push(void 0);
          }
        } else {
          results.push(void 0);
        }
      }
      return results;
    };

    Dropzone.prototype._addFilesFromDirectory = function(directory, path) {
      var dirReader, errorHandler, readEntries;
      dirReader = directory.createReader();
      errorHandler = function(error) {
        return typeof console !== "undefined" && console !== null ? typeof console.log === "function" ? console.log(error) : void 0 : void 0;
      };
      readEntries = (function(_this) {
        return function() {
          return dirReader.readEntries(function(entries) {
            var entry, j, len;
            if (entries.length > 0) {
              for (j = 0, len = entries.length; j < len; j++) {
                entry = entries[j];
                if (entry.isFile) {
                  entry.file(function(file) {
                    if (_this.options.ignoreHiddenFiles && file.name.substring(0, 1) === '.') {
                      return;
                    }
                    file.fullPath = path + "/" + file.name;
                    return _this.addFile(file);
                  });
                } else if (entry.isDirectory) {
                  _this._addFilesFromDirectory(entry, path + "/" + entry.name);
                }
              }
              readEntries();
            }
            return null;
          }, errorHandler);
        };
      })(this);
      return readEntries();
    };

    Dropzone.prototype.accept = function(file, done) {
      if (file.size > this.options.maxFilesize * 1024 * 1024) {
        return done(this.options.dictFileTooBig.replace("{{filesize}}", Math.round(file.size / 1024 / 10.24) / 100).replace("{{maxFilesize}}", this.options.maxFilesize));
      } else if (!Dropzone.isValidFile(file, this.options.acceptedFiles)) {
        return done(this.options.dictInvalidFileType);
      } else if ((this.options.maxFiles != null) && this.getAcceptedFiles().length >= this.options.maxFiles) {
        done(this.options.dictMaxFilesExceeded.replace("{{maxFiles}}", this.options.maxFiles));
        return this.emit("maxfilesexceeded", file);
      } else {
        return this.options.accept.call(this, file, done);
      }
    };

    Dropzone.prototype.addFile = function(file) {
      file.upload = {
        progress: 0,
        total: file.size,
        bytesSent: 0,
        filename: this._renameFile(file)
      };
      this.files.push(file);
      file.status = Dropzone.ADDED;
      this.emit("addedfile", file);
      this._enqueueThumbnail(file);
      return this.accept(file, (function(_this) {
        return function(error) {
          if (error) {
            file.accepted = false;
            _this._errorProcessing([file], error);
          } else {
            file.accepted = true;
            if (_this.options.autoQueue) {
              _this.enqueueFile(file);
            }
          }
          return _this._updateMaxFilesReachedClass();
        };
      })(this));
    };

    Dropzone.prototype.enqueueFiles = function(files) {
      var file, j, len;
      for (j = 0, len = files.length; j < len; j++) {
        file = files[j];
        this.enqueueFile(file);
      }
      return null;
    };

    Dropzone.prototype.enqueueFile = function(file) {
      if (file.status === Dropzone.ADDED && file.accepted === true) {
        file.status = Dropzone.QUEUED;
        if (this.options.autoProcessQueue) {
          return setTimeout(((function(_this) {
            return function() {
              return _this.processQueue();
            };
          })(this)), 0);
        }
      } else {
        throw new Error("This file can't be queued because it has already been processed or was rejected.");
      }
    };

    Dropzone.prototype._thumbnailQueue = [];

    Dropzone.prototype._processingThumbnail = false;

    Dropzone.prototype._enqueueThumbnail = function(file) {
      if (this.options.createImageThumbnails && file.type.match(/image.*/) && file.size <= this.options.maxThumbnailFilesize * 1024 * 1024) {
        this._thumbnailQueue.push(file);
        return setTimeout(((function(_this) {
          return function() {
            return _this._processThumbnailQueue();
          };
        })(this)), 0);
      }
    };

    Dropzone.prototype._processThumbnailQueue = function() {
      var file;
      if (this._processingThumbnail || this._thumbnailQueue.length === 0) {
        return;
      }
      this._processingThumbnail = true;
      file = this._thumbnailQueue.shift();
      return this.createThumbnail(file, this.options.thumbnailWidth, this.options.thumbnailHeight, this.options.thumbnailMethod, true, (function(_this) {
        return function(dataUrl) {
          _this.emit("thumbnail", file, dataUrl);
          _this._processingThumbnail = false;
          return _this._processThumbnailQueue();
        };
      })(this));
    };

    Dropzone.prototype.removeFile = function(file) {
      if (file.status === Dropzone.UPLOADING) {
        this.cancelUpload(file);
      }
      this.files = without(this.files, file);
      this.emit("removedfile", file);
      if (this.files.length === 0) {
        return this.emit("reset");
      }
    };

    Dropzone.prototype.removeAllFiles = function(cancelIfNecessary) {
      var file, j, len, ref;
      if (cancelIfNecessary == null) {
        cancelIfNecessary = false;
      }
      ref = this.files.slice();
      for (j = 0, len = ref.length; j < len; j++) {
        file = ref[j];
        if (file.status !== Dropzone.UPLOADING || cancelIfNecessary) {
          this.removeFile(file);
        }
      }
      return null;
    };

    Dropzone.prototype.resizeImage = function(file, width, height, resizeMethod, callback) {
      return this.createThumbnail(file, width, height, resizeMethod, false, (function(_this) {
        return function(dataUrl, canvas) {
          var resizeMimeType, resizedDataURL;
          if (canvas === null) {
            return callback(file);
          } else {
            resizeMimeType = _this.options.resizeMimeType;
            if (resizeMimeType == null) {
              resizeMimeType = file.type;
            }
            resizedDataURL = canvas.toDataURL(resizeMimeType, _this.options.resizeQuality);
            if (resizeMimeType === 'image/jpeg' || resizeMimeType === 'image/jpg') {
              resizedDataURL = ExifRestore.restore(file.dataURL, resizedDataURL);
            }
            return callback(Dropzone.dataURItoBlob(resizedDataURL));
          }
        };
      })(this));
    };

    Dropzone.prototype.createThumbnail = function(file, width, height, resizeMethod, fixOrientation, callback) {
      var fileReader;
      fileReader = new FileReader;
      fileReader.onload = (function(_this) {
        return function() {
          file.dataURL = fileReader.result;
          if (file.type === "image/svg+xml") {
            if (callback != null) {
              callback(fileReader.result);
            }
            return;
          }
          return _this.createThumbnailFromUrl(file, width, height, resizeMethod, fixOrientation, callback);
        };
      })(this);
      return fileReader.readAsDataURL(file);
    };

    Dropzone.prototype.createThumbnailFromUrl = function(file, width, height, resizeMethod, fixOrientation, callback, crossOrigin) {
      var img;
      img = document.createElement("img");
      if (crossOrigin) {
        img.crossOrigin = crossOrigin;
      }
      img.onload = (function(_this) {
        return function() {
          var loadExif;
          loadExif = function(callback) {
            return callback(1);
          };
          if ((typeof EXIF !== "undefined" && EXIF !== null) && fixOrientation) {
            loadExif = function(callback) {
              return EXIF.getData(img, function() {
                return callback(EXIF.getTag(this, 'Orientation'));
              });
            };
          }
          return loadExif(function(orientation) {
            var canvas, ctx, ref, ref1, ref2, ref3, resizeInfo, thumbnail;
            file.width = img.width;
            file.height = img.height;
            resizeInfo = _this.options.resize.call(_this, file, width, height, resizeMethod);
            canvas = document.createElement("canvas");
            ctx = canvas.getContext("2d");
            canvas.width = resizeInfo.trgWidth;
            canvas.height = resizeInfo.trgHeight;
            if (orientation > 4) {
              canvas.width = resizeInfo.trgHeight;
              canvas.height = resizeInfo.trgWidth;
            }
            switch (orientation) {
              case 2:
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
                break;
              case 3:
                ctx.translate(canvas.width, canvas.height);
                ctx.rotate(Math.PI);
                break;
              case 4:
                ctx.translate(0, canvas.height);
                ctx.scale(1, -1);
                break;
              case 5:
                ctx.rotate(0.5 * Math.PI);
                ctx.scale(1, -1);
                break;
              case 6:
                ctx.rotate(0.5 * Math.PI);
                ctx.translate(0, -canvas.height);
                break;
              case 7:
                ctx.rotate(0.5 * Math.PI);
                ctx.translate(canvas.width, -canvas.height);
                ctx.scale(-1, 1);
                break;
              case 8:
                ctx.rotate(-0.5 * Math.PI);
                ctx.translate(-canvas.width, 0);
            }
            drawImageIOSFix(ctx, img, (ref = resizeInfo.srcX) != null ? ref : 0, (ref1 = resizeInfo.srcY) != null ? ref1 : 0, resizeInfo.srcWidth, resizeInfo.srcHeight, (ref2 = resizeInfo.trgX) != null ? ref2 : 0, (ref3 = resizeInfo.trgY) != null ? ref3 : 0, resizeInfo.trgWidth, resizeInfo.trgHeight);
            thumbnail = canvas.toDataURL("image/png");
            if (callback != null) {
              return callback(thumbnail, canvas);
            }
          });
        };
      })(this);
      if (callback != null) {
        img.onerror = callback;
      }
      return img.src = file.dataURL;
    };

    Dropzone.prototype.processQueue = function() {
      var i, parallelUploads, processingLength, queuedFiles;
      parallelUploads = this.options.parallelUploads;
      processingLength = this.getUploadingFiles().length;
      i = processingLength;
      if (processingLength >= parallelUploads) {
        return;
      }
      queuedFiles = this.getQueuedFiles();
      if (!(queuedFiles.length > 0)) {
        return;
      }
      if (this.options.uploadMultiple) {
        return this.processFiles(queuedFiles.slice(0, parallelUploads - processingLength));
      } else {
        while (i < parallelUploads) {
          if (!queuedFiles.length) {
            return;
          }
          this.processFile(queuedFiles.shift());
          i++;
        }
      }
    };

    Dropzone.prototype.processFile = function(file) {
      return this.processFiles([file]);
    };

    Dropzone.prototype.processFiles = function(files) {
      var file, j, len;
      for (j = 0, len = files.length; j < len; j++) {
        file = files[j];
        file.processing = true;
        file.status = Dropzone.UPLOADING;
        this.emit("processing", file);
      }
      if (this.options.uploadMultiple) {
        this.emit("processingmultiple", files);
      }
      return this.uploadFiles(files);
    };

    Dropzone.prototype._getFilesWithXhr = function(xhr) {
      var file, files;
      return files = (function() {
        var j, len, ref, results;
        ref = this.files;
        results = [];
        for (j = 0, len = ref.length; j < len; j++) {
          file = ref[j];
          if (file.xhr === xhr) {
            results.push(file);
          }
        }
        return results;
      }).call(this);
    };

    Dropzone.prototype.cancelUpload = function(file) {
      var groupedFile, groupedFiles, j, k, len, len1, ref;
      if (file.status === Dropzone.UPLOADING) {
        groupedFiles = this._getFilesWithXhr(file.xhr);
        for (j = 0, len = groupedFiles.length; j < len; j++) {
          groupedFile = groupedFiles[j];
          groupedFile.status = Dropzone.CANCELED;
        }
        file.xhr.abort();
        for (k = 0, len1 = groupedFiles.length; k < len1; k++) {
          groupedFile = groupedFiles[k];
          this.emit("canceled", groupedFile);
        }
        if (this.options.uploadMultiple) {
          this.emit("canceledmultiple", groupedFiles);
        }
      } else if ((ref = file.status) === Dropzone.ADDED || ref === Dropzone.QUEUED) {
        file.status = Dropzone.CANCELED;
        this.emit("canceled", file);
        if (this.options.uploadMultiple) {
          this.emit("canceledmultiple", [file]);
        }
      }
      if (this.options.autoProcessQueue) {
        return this.processQueue();
      }
    };

    resolveOption = function() {
      var args, option;
      option = arguments[0], args = 2 <= arguments.length ? slice.call(arguments, 1) : [];
      if (typeof option === 'function') {
        return option.apply(this, args);
      }
      return option;
    };

    Dropzone.prototype.uploadFile = function(file) {
      return this.uploadFiles([file]);
    };

    Dropzone.prototype.uploadFiles = function(files) {
      var doneCounter, doneFunction, file, formData, handleError, headerName, headerValue, headers, i, input, inputName, inputType, j, k, key, l, len, len1, len2, len3, m, method, o, option, progressObj, ref, ref1, ref2, ref3, ref4, ref5, response, results, updateProgress, url, value, xhr;
      xhr = new XMLHttpRequest();
      for (j = 0, len = files.length; j < len; j++) {
        file = files[j];
        file.xhr = xhr;
      }
      method = resolveOption(this.options.method, files);
      url = resolveOption(this.options.url, files);
      xhr.open(method, url, true);
      xhr.timeout = resolveOption(this.options.timeout, files);
      xhr.withCredentials = !!this.options.withCredentials;
      response = null;
      handleError = (function(_this) {
        return function() {
          var k, len1, results;
          results = [];
          for (k = 0, len1 = files.length; k < len1; k++) {
            file = files[k];
            results.push(_this._errorProcessing(files, response || _this.options.dictResponseError.replace("{{statusCode}}", xhr.status), xhr));
          }
          return results;
        };
      })(this);
      updateProgress = (function(_this) {
        return function(e) {
          var allFilesFinished, k, l, len1, len2, len3, m, progress, results;
          if (e != null) {
            progress = 100 * e.loaded / e.total;
            for (k = 0, len1 = files.length; k < len1; k++) {
              file = files[k];
              file.upload.progress = progress;
              file.upload.total = e.total;
              file.upload.bytesSent = e.loaded;
            }
          } else {
            allFilesFinished = true;
            progress = 100;
            for (l = 0, len2 = files.length; l < len2; l++) {
              file = files[l];
              if (!(file.upload.progress === 100 && file.upload.bytesSent === file.upload.total)) {
                allFilesFinished = false;
              }
              file.upload.progress = progress;
              file.upload.bytesSent = file.upload.total;
            }
            if (allFilesFinished) {
              return;
            }
          }
          results = [];
          for (m = 0, len3 = files.length; m < len3; m++) {
            file = files[m];
            results.push(_this.emit("uploadprogress", file, progress, file.upload.bytesSent));
          }
          return results;
        };
      })(this);
      xhr.onload = (function(_this) {
        return function(e) {
          var error1, ref;
          if (files[0].status === Dropzone.CANCELED) {
            return;
          }
          if (xhr.readyState !== 4) {
            return;
          }
          if (xhr.responseType !== 'arraybuffer' && xhr.responseType !== 'blob') {
            response = xhr.responseText;
            if (xhr.getResponseHeader("content-type") && ~xhr.getResponseHeader("content-type").indexOf("application/json")) {
              try {
                response = JSON.parse(response);
              } catch (error1) {
                e = error1;
                response = "Invalid JSON response from server.";
              }
            }
          }
          updateProgress();
          if (!((200 <= (ref = xhr.status) && ref < 300))) {
            return handleError();
          } else {
            return _this._finished(files, response, e);
          }
        };
      })(this);
      xhr.onerror = (function(_this) {
        return function() {
          if (files[0].status === Dropzone.CANCELED) {
            return;
          }
          return handleError();
        };
      })(this);
      progressObj = (ref = xhr.upload) != null ? ref : xhr;
      progressObj.onprogress = updateProgress;
      headers = {
        "Accept": "application/json",
        "Cache-Control": "no-cache",
        "X-Requested-With": "XMLHttpRequest"
      };
      if (this.options.headers) {
        extend(headers, this.options.headers);
      }
      for (headerName in headers) {
        headerValue = headers[headerName];
        if (headerValue) {
          xhr.setRequestHeader(headerName, headerValue);
        }
      }
      formData = new FormData();
      if (this.options.params) {
        ref1 = this.options.params;
        for (key in ref1) {
          value = ref1[key];
          formData.append(key, value);
        }
      }
      for (k = 0, len1 = files.length; k < len1; k++) {
        file = files[k];
        this.emit("sending", file, xhr, formData);
      }
      if (this.options.uploadMultiple) {
        this.emit("sendingmultiple", files, xhr, formData);
      }
      if (this.element.tagName === "FORM") {
        ref2 = this.element.querySelectorAll("input, textarea, select, button");
        for (l = 0, len2 = ref2.length; l < len2; l++) {
          input = ref2[l];
          inputName = input.getAttribute("name");
          inputType = input.getAttribute("type");
          if (input.tagName === "SELECT" && input.hasAttribute("multiple")) {
            ref3 = input.options;
            for (m = 0, len3 = ref3.length; m < len3; m++) {
              option = ref3[m];
              if (option.selected) {
                formData.append(inputName, option.value);
              }
            }
          } else if (!inputType || ((ref4 = inputType.toLowerCase()) !== "checkbox" && ref4 !== "radio") || input.checked) {
            formData.append(inputName, input.value);
          }
        }
      }
      doneCounter = 0;
      results = [];
      for (i = o = 0, ref5 = files.length - 1; 0 <= ref5 ? o <= ref5 : o >= ref5; i = 0 <= ref5 ? ++o : --o) {
        doneFunction = (function(_this) {
          return function(file, paramName, fileName) {
            return function(transformedFile) {
              formData.append(paramName, transformedFile, fileName);
              if (++doneCounter === files.length) {
                return _this.submitRequest(xhr, formData, files);
              }
            };
          };
        })(this);
        results.push(this.options.transformFile.call(this, files[i], doneFunction(files[i], this._getParamName(i), files[i].upload.filename)));
      }
      return results;
    };

    Dropzone.prototype.submitRequest = function(xhr, formData, files) {
      return xhr.send(formData);
    };

    Dropzone.prototype._finished = function(files, responseText, e) {
      var file, j, len;
      for (j = 0, len = files.length; j < len; j++) {
        file = files[j];
        file.status = Dropzone.SUCCESS;
        this.emit("success", file, responseText, e);
        this.emit("complete", file);
      }
      if (this.options.uploadMultiple) {
        this.emit("successmultiple", files, responseText, e);
        this.emit("completemultiple", files);
      }
      if (this.options.autoProcessQueue) {
        return this.processQueue();
      }
    };

    Dropzone.prototype._errorProcessing = function(files, message, xhr) {
      var file, j, len;
      for (j = 0, len = files.length; j < len; j++) {
        file = files[j];
        file.status = Dropzone.ERROR;
        this.emit("error", file, message, xhr);
        this.emit("complete", file);
      }
      if (this.options.uploadMultiple) {
        this.emit("errormultiple", files, message, xhr);
        this.emit("completemultiple", files);
      }
      if (this.options.autoProcessQueue) {
        return this.processQueue();
      }
    };

    return Dropzone;

  })(Emitter);

  Dropzone.version = "5.1.1";

  Dropzone.options = {};

  Dropzone.optionsForElement = function(element) {
    if (element.getAttribute("id")) {
      return Dropzone.options[camelize(element.getAttribute("id"))];
    } else {
      return void 0;
    }
  };

  Dropzone.instances = [];

  Dropzone.forElement = function(element) {
    if (typeof element === "string") {
      element = document.querySelector(element);
    }
    if ((element != null ? element.dropzone : void 0) == null) {
      throw new Error("No Dropzone found for given element. This is probably because you're trying to access it before Dropzone had the time to initialize. Use the `init` option to setup any additional observers on your Dropzone.");
    }
    return element.dropzone;
  };

  Dropzone.autoDiscover = true;

  Dropzone.discover = function() {
    var checkElements, dropzone, dropzones, j, len, results;
    if (document.querySelectorAll) {
      dropzones = document.querySelectorAll(".dropzone");
    } else {
      dropzones = [];
      checkElements = function(elements) {
        var el, j, len, results;
        results = [];
        for (j = 0, len = elements.length; j < len; j++) {
          el = elements[j];
          if (/(^| )dropzone($| )/.test(el.className)) {
            results.push(dropzones.push(el));
          } else {
            results.push(void 0);
          }
        }
        return results;
      };
      checkElements(document.getElementsByTagName("div"));
      checkElements(document.getElementsByTagName("form"));
    }
    results = [];
    for (j = 0, len = dropzones.length; j < len; j++) {
      dropzone = dropzones[j];
      if (Dropzone.optionsForElement(dropzone) !== false) {
        results.push(new Dropzone(dropzone));
      } else {
        results.push(void 0);
      }
    }
    return results;
  };

  Dropzone.blacklistedBrowsers = [/opera.*Macintosh.*version\/12/i];

  Dropzone.isBrowserSupported = function() {
    var capableBrowser, j, len, ref, regex;
    capableBrowser = true;
    if (window.File && window.FileReader && window.FileList && window.Blob && window.FormData && document.querySelector) {
      if (!("classList" in document.createElement("a"))) {
        capableBrowser = false;
      } else {
        ref = Dropzone.blacklistedBrowsers;
        for (j = 0, len = ref.length; j < len; j++) {
          regex = ref[j];
          if (regex.test(navigator.userAgent)) {
            capableBrowser = false;
            continue;
          }
        }
      }
    } else {
      capableBrowser = false;
    }
    return capableBrowser;
  };

  Dropzone.dataURItoBlob = function(dataURI) {
    var ab, byteString, i, ia, j, mimeString, ref;
    byteString = atob(dataURI.split(',')[1]);
    mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
    ab = new ArrayBuffer(byteString.length);
    ia = new Uint8Array(ab);
    for (i = j = 0, ref = byteString.length; 0 <= ref ? j <= ref : j >= ref; i = 0 <= ref ? ++j : --j) {
      ia[i] = byteString.charCodeAt(i);
    }
    return new Blob([ab], {
      type: mimeString
    });
  };

  without = function(list, rejectedItem) {
    var item, j, len, results;
    results = [];
    for (j = 0, len = list.length; j < len; j++) {
      item = list[j];
      if (item !== rejectedItem) {
        results.push(item);
      }
    }
    return results;
  };

  camelize = function(str) {
    return str.replace(/[\-_](\w)/g, function(match) {
      return match.charAt(1).toUpperCase();
    });
  };

  Dropzone.createElement = function(string) {
    var div;
    div = document.createElement("div");
    div.innerHTML = string;
    return div.childNodes[0];
  };

  Dropzone.elementInside = function(element, container) {
    if (element === container) {
      return true;
    }
    while (element = element.parentNode) {
      if (element === container) {
        return true;
      }
    }
    return false;
  };

  Dropzone.getElement = function(el, name) {
    var element;
    if (typeof el === "string") {
      element = document.querySelector(el);
    } else if (el.nodeType != null) {
      element = el;
    }
    if (element == null) {
      throw new Error("Invalid `" + name + "` option provided. Please provide a CSS selector or a plain HTML element.");
    }
    return element;
  };

  Dropzone.getElements = function(els, name) {
    var e, el, elements, error1, j, k, len, len1, ref;
    if (els instanceof Array) {
      elements = [];
      try {
        for (j = 0, len = els.length; j < len; j++) {
          el = els[j];
          elements.push(this.getElement(el, name));
        }
      } catch (error1) {
        e = error1;
        elements = null;
      }
    } else if (typeof els === "string") {
      elements = [];
      ref = document.querySelectorAll(els);
      for (k = 0, len1 = ref.length; k < len1; k++) {
        el = ref[k];
        elements.push(el);
      }
    } else if (els.nodeType != null) {
      elements = [els];
    }
    if (!((elements != null) && elements.length)) {
      throw new Error("Invalid `" + name + "` option provided. Please provide a CSS selector, a plain HTML element or a list of those.");
    }
    return elements;
  };

  Dropzone.confirm = function(question, accepted, rejected) {
    if (window.confirm(question)) {
      return accepted();
    } else if (rejected != null) {
      return rejected();
    }
  };

  Dropzone.isValidFile = function(file, acceptedFiles) {
    var baseMimeType, j, len, mimeType, validType;
    if (!acceptedFiles) {
      return true;
    }
    acceptedFiles = acceptedFiles.split(",");
    mimeType = file.type;
    baseMimeType = mimeType.replace(/\/.*$/, "");
    for (j = 0, len = acceptedFiles.length; j < len; j++) {
      validType = acceptedFiles[j];
      validType = validType.trim();
      if (validType.charAt(0) === ".") {
        if (file.name.toLowerCase().indexOf(validType.toLowerCase(), file.name.length - validType.length) !== -1) {
          return true;
        }
      } else if (/\/\*$/.test(validType)) {
        if (baseMimeType === validType.replace(/\/.*$/, "")) {
          return true;
        }
      } else {
        if (mimeType === validType) {
          return true;
        }
      }
    }
    return false;
  };

  if (typeof jQuery !== "undefined" && jQuery !== null) {
    jQuery.fn.dropzone = function(options) {
      return this.each(function() {
        return new Dropzone(this, options);
      });
    };
  }

  if (typeof module !== "undefined" && module !== null) {
    module.exports = Dropzone;
  } else {
    window.Dropzone = Dropzone;
  }

  Dropzone.ADDED = "added";

  Dropzone.QUEUED = "queued";

  Dropzone.ACCEPTED = Dropzone.QUEUED;

  Dropzone.UPLOADING = "uploading";

  Dropzone.PROCESSING = Dropzone.UPLOADING;

  Dropzone.CANCELED = "canceled";

  Dropzone.ERROR = "error";

  Dropzone.SUCCESS = "success";


  /*
  
  Bugfix for iOS 6 and 7
  Source: http://stackoverflow.com/questions/11929099/html5-canvas-drawimage-ratio-bug-ios
  based on the work of https://github.com/stomita/ios-imagefile-megapixel
   */

  detectVerticalSquash = function(img) {
    var alpha, canvas, ctx, data, ey, ih, iw, py, ratio, sy;
    iw = img.naturalWidth;
    ih = img.naturalHeight;
    canvas = document.createElement("canvas");
    canvas.width = 1;
    canvas.height = ih;
    ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0);
    data = ctx.getImageData(1, 0, 1, ih).data;
    sy = 0;
    ey = ih;
    py = ih;
    while (py > sy) {
      alpha = data[(py - 1) * 4 + 3];
      if (alpha === 0) {
        ey = py;
      } else {
        sy = py;
      }
      py = (ey + sy) >> 1;
    }
    ratio = py / ih;
    if (ratio === 0) {
      return 1;
    } else {
      return ratio;
    }
  };

  drawImageIOSFix = function(ctx, img, sx, sy, sw, sh, dx, dy, dw, dh) {
    var vertSquashRatio;
    vertSquashRatio = detectVerticalSquash(img);
    return ctx.drawImage(img, sx, sy, sw, sh, dx, dy, dw, dh / vertSquashRatio);
  };

  ExifRestore = (function() {
    function ExifRestore() {}

    ExifRestore.KEY_STR = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';

    ExifRestore.encode64 = function(input) {
      var chr1, chr2, chr3, enc1, enc2, enc3, enc4, i, output;
      output = '';
      chr1 = void 0;
      chr2 = void 0;
      chr3 = '';
      enc1 = void 0;
      enc2 = void 0;
      enc3 = void 0;
      enc4 = '';
      i = 0;
      while (true) {
        chr1 = input[i++];
        chr2 = input[i++];
        chr3 = input[i++];
        enc1 = chr1 >> 2;
        enc2 = (chr1 & 3) << 4 | chr2 >> 4;
        enc3 = (chr2 & 15) << 2 | chr3 >> 6;
        enc4 = chr3 & 63;
        if (isNaN(chr2)) {
          enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
          enc4 = 64;
        }
        output = output + this.KEY_STR.charAt(enc1) + this.KEY_STR.charAt(enc2) + this.KEY_STR.charAt(enc3) + this.KEY_STR.charAt(enc4);
        chr1 = chr2 = chr3 = '';
        enc1 = enc2 = enc3 = enc4 = '';
        if (!(i < input.length)) {
          break;
        }
      }
      return output;
    };

    ExifRestore.restore = function(origFileBase64, resizedFileBase64) {
      var image, rawImage, segments;
      if (!origFileBase64.match('data:image/jpeg;base64,')) {
        return resizedFileBase64;
      }
      rawImage = this.decode64(origFileBase64.replace('data:image/jpeg;base64,', ''));
      segments = this.slice2Segments(rawImage);
      image = this.exifManipulation(resizedFileBase64, segments);
      return 'data:image/jpeg;base64,' + this.encode64(image);
    };

    ExifRestore.exifManipulation = function(resizedFileBase64, segments) {
      var aBuffer, exifArray, newImageArray;
      exifArray = this.getExifArray(segments);
      newImageArray = this.insertExif(resizedFileBase64, exifArray);
      aBuffer = new Uint8Array(newImageArray);
      return aBuffer;
    };

    ExifRestore.getExifArray = function(segments) {
      var seg, x;
      seg = void 0;
      x = 0;
      while (x < segments.length) {
        seg = segments[x];
        if (seg[0] === 255 & seg[1] === 225) {
          return seg;
        }
        x++;
      }
      return [];
    };

    ExifRestore.insertExif = function(resizedFileBase64, exifArray) {
      var array, ato, buf, imageData, mae, separatePoint;
      imageData = resizedFileBase64.replace('data:image/jpeg;base64,', '');
      buf = this.decode64(imageData);
      separatePoint = buf.indexOf(255, 3);
      mae = buf.slice(0, separatePoint);
      ato = buf.slice(separatePoint);
      array = mae;
      array = array.concat(exifArray);
      array = array.concat(ato);
      return array;
    };

    ExifRestore.slice2Segments = function(rawImageArray) {
      var endPoint, head, length, seg, segments;
      head = 0;
      segments = [];
      while (true) {
        if (rawImageArray[head] === 255 & rawImageArray[head + 1] === 218) {
          break;
        }
        if (rawImageArray[head] === 255 & rawImageArray[head + 1] === 216) {
          head += 2;
        } else {
          length = rawImageArray[head + 2] * 256 + rawImageArray[head + 3];
          endPoint = head + length + 2;
          seg = rawImageArray.slice(head, endPoint);
          segments.push(seg);
          head = endPoint;
        }
        if (head > rawImageArray.length) {
          break;
        }
      }
      return segments;
    };

    ExifRestore.decode64 = function(input) {
      var base64test, buf, chr1, chr2, chr3, enc1, enc2, enc3, enc4, i, output;
      output = '';
      chr1 = void 0;
      chr2 = void 0;
      chr3 = '';
      enc1 = void 0;
      enc2 = void 0;
      enc3 = void 0;
      enc4 = '';
      i = 0;
      buf = [];
      base64test = /[^A-Za-z0-9\+\/\=]/g;
      if (base64test.exec(input)) {
        console.warning('There were invalid base64 characters in the input text.\n' + 'Valid base64 characters are A-Z, a-z, 0-9, \'+\', \'/\',and \'=\'\n' + 'Expect errors in decoding.');
      }
      input = input.replace(/[^A-Za-z0-9\+\/\=]/g, '');
      while (true) {
        enc1 = this.KEY_STR.indexOf(input.charAt(i++));
        enc2 = this.KEY_STR.indexOf(input.charAt(i++));
        enc3 = this.KEY_STR.indexOf(input.charAt(i++));
        enc4 = this.KEY_STR.indexOf(input.charAt(i++));
        chr1 = enc1 << 2 | enc2 >> 4;
        chr2 = (enc2 & 15) << 4 | enc3 >> 2;
        chr3 = (enc3 & 3) << 6 | enc4;
        buf.push(chr1);
        if (enc3 !== 64) {
          buf.push(chr2);
        }
        if (enc4 !== 64) {
          buf.push(chr3);
        }
        chr1 = chr2 = chr3 = '';
        enc1 = enc2 = enc3 = enc4 = '';
        if (!(i < input.length)) {
          break;
        }
      }
      return buf;
    };

    return ExifRestore;

  })();


  /*
   * contentloaded.js
   *
   * Author: Diego Perini (diego.perini at gmail.com)
   * Summary: cross-browser wrapper for DOMContentLoaded
   * Updated: 20101020
   * License: MIT
   * Version: 1.2
   *
   * URL:
   * http://javascript.nwbox.com/ContentLoaded/
   * http://javascript.nwbox.com/ContentLoaded/MIT-LICENSE
   */

  contentLoaded = function(win, fn) {
    var add, doc, done, init, poll, pre, rem, root, top;
    done = false;
    top = true;
    doc = win.document;
    root = doc.documentElement;
    add = (doc.addEventListener ? "addEventListener" : "attachEvent");
    rem = (doc.addEventListener ? "removeEventListener" : "detachEvent");
    pre = (doc.addEventListener ? "" : "on");
    init = function(e) {
      if (e.type === "readystatechange" && doc.readyState !== "complete") {
        return;
      }
      (e.type === "load" ? win : doc)[rem](pre + e.type, init, false);
      if (!done && (done = true)) {
        return fn.call(win, e.type || e);
      }
    };
    poll = function() {
      var e, error1;
      try {
        root.doScroll("left");
      } catch (error1) {
        e = error1;
        setTimeout(poll, 50);
        return;
      }
      return init("poll");
    };
    if (doc.readyState !== "complete") {
      if (doc.createEventObject && root.doScroll) {
        try {
          top = !win.frameElement;
        } catch (undefined) {}
        if (top) {
          poll();
        }
      }
      doc[add](pre + "DOMContentLoaded", init, false);
      doc[add](pre + "readystatechange", init, false);
      return win[add](pre + "load", init, false);
    }
  };

  Dropzone._autoDiscoverFunction = function() {
    if (Dropzone.autoDiscover) {
      return Dropzone.discover();
    }
  };

  contentLoaded(window, Dropzone._autoDiscoverFunction);

}).call(this);


/* Chosen v1.7.0 | (c) 2011-2017 by Harvest | MIT License, https://github.com/harvesthq/chosen/blob/master/LICENSE.md */
(function(){var a,b,c,d,e,f=function(a,b){return function(){return a.apply(b,arguments)}},g={}.hasOwnProperty,h=function(a,b){function c(){this.constructor=a}for(var d in b)g.call(b,d)&&(a[d]=b[d]);return c.prototype=b.prototype,a.prototype=new c,a.__super__=b.prototype,a};d=function(){function a(){this.options_index=0,this.parsed=[]}return a.prototype.add_node=function(a){return"OPTGROUP"===a.nodeName.toUpperCase()?this.add_group(a):this.add_option(a)},a.prototype.add_group=function(a){var b,c,d,e,f,g;for(b=this.parsed.length,this.parsed.push({array_index:b,group:!0,label:this.escapeExpression(a.label),title:a.title?a.title:void 0,children:0,disabled:a.disabled,classes:a.className}),f=a.childNodes,g=[],d=0,e=f.length;e>d;d++)c=f[d],g.push(this.add_option(c,b,a.disabled));return g},a.prototype.add_option=function(a,b,c){return"OPTION"===a.nodeName.toUpperCase()?(""!==a.text?(null!=b&&(this.parsed[b].children+=1),this.parsed.push({array_index:this.parsed.length,options_index:this.options_index,value:a.value,text:a.text,html:a.innerHTML,title:a.title?a.title:void 0,selected:a.selected,disabled:c===!0?c:a.disabled,group_array_index:b,group_label:null!=b?this.parsed[b].label:null,classes:a.className,style:a.style.cssText})):this.parsed.push({array_index:this.parsed.length,options_index:this.options_index,empty:!0}),this.options_index+=1):void 0},a.prototype.escapeExpression=function(a){var b,c;return null==a||a===!1?"":/[\&\<\>\"\'\`]/.test(a)?(b={"<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},c=/&(?!\w+;)|[\<\>\"\'\`]/g,a.replace(c,function(a){return b[a]||"&amp;"})):a},a}(),d.select_to_array=function(a){var b,c,e,f,g;for(c=new d,g=a.childNodes,e=0,f=g.length;f>e;e++)b=g[e],c.add_node(b);return c.parsed},b=function(){function a(b,c){this.form_field=b,this.options=null!=c?c:{},this.label_click_handler=f(this.label_click_handler,this),a.browser_is_supported()&&(this.is_multiple=this.form_field.multiple,this.set_default_text(),this.set_default_values(),this.setup(),this.set_up_html(),this.register_observers(),this.on_ready())}return a.prototype.set_default_values=function(){var a=this;return this.click_test_action=function(b){return a.test_active_click(b)},this.activate_action=function(b){return a.activate_field(b)},this.active_field=!1,this.mouse_on_container=!1,this.results_showing=!1,this.result_highlighted=null,this.is_rtl=this.options.rtl||/\bchosen-rtl\b/.test(this.form_field.className),this.allow_single_deselect=null!=this.options.allow_single_deselect&&null!=this.form_field.options[0]&&""===this.form_field.options[0].text?this.options.allow_single_deselect:!1,this.disable_search_threshold=this.options.disable_search_threshold||0,this.disable_search=this.options.disable_search||!1,this.enable_split_word_search=null!=this.options.enable_split_word_search?this.options.enable_split_word_search:!0,this.group_search=null!=this.options.group_search?this.options.group_search:!0,this.search_contains=this.options.search_contains||!1,this.single_backstroke_delete=null!=this.options.single_backstroke_delete?this.options.single_backstroke_delete:!0,this.max_selected_options=this.options.max_selected_options||1/0,this.inherit_select_classes=this.options.inherit_select_classes||!1,this.display_selected_options=null!=this.options.display_selected_options?this.options.display_selected_options:!0,this.display_disabled_options=null!=this.options.display_disabled_options?this.options.display_disabled_options:!0,this.include_group_label_in_selected=this.options.include_group_label_in_selected||!1,this.max_shown_results=this.options.max_shown_results||Number.POSITIVE_INFINITY,this.case_sensitive_search=this.options.case_sensitive_search||!1,this.hide_results_on_select=null!=this.options.hide_results_on_select?this.options.hide_results_on_select:!0},a.prototype.set_default_text=function(){return this.form_field.getAttribute("data-placeholder")?this.default_text=this.form_field.getAttribute("data-placeholder"):this.is_multiple?this.default_text=this.options.placeholder_text_multiple||this.options.placeholder_text||a.default_multiple_text:this.default_text=this.options.placeholder_text_single||this.options.placeholder_text||a.default_single_text,this.default_text=this.escape_html(this.default_text),this.results_none_found=this.form_field.getAttribute("data-no_results_text")||this.options.no_results_text||a.default_no_result_text},a.prototype.choice_label=function(a){return this.include_group_label_in_selected&&null!=a.group_label?"<b class='group-name'>"+a.group_label+"</b>"+a.html:a.html},a.prototype.mouse_enter=function(){return this.mouse_on_container=!0},a.prototype.mouse_leave=function(){return this.mouse_on_container=!1},a.prototype.input_focus=function(a){var b=this;if(this.is_multiple){if(!this.active_field)return setTimeout(function(){return b.container_mousedown()},50)}else if(!this.active_field)return this.activate_field()},a.prototype.input_blur=function(a){var b=this;return this.mouse_on_container?void 0:(this.active_field=!1,setTimeout(function(){return b.blur_test()},100))},a.prototype.label_click_handler=function(a){return this.is_multiple?this.container_mousedown(a):this.activate_field()},a.prototype.results_option_build=function(a){var b,c,d,e,f,g,h;for(b="",e=0,h=this.results_data,f=0,g=h.length;g>f&&(c=h[f],d="",d=c.group?this.result_add_group(c):this.result_add_option(c),""!==d&&(e++,b+=d),(null!=a?a.first:void 0)&&(c.selected&&this.is_multiple?this.choice_build(c):c.selected&&!this.is_multiple&&this.single_set_selected_text(this.choice_label(c))),!(e>=this.max_shown_results));f++);return b},a.prototype.result_add_option=function(a){var b,c;return a.search_match&&this.include_option_in_results(a)?(b=[],a.disabled||a.selected&&this.is_multiple||b.push("active-result"),!a.disabled||a.selected&&this.is_multiple||b.push("disabled-result"),a.selected&&b.push("result-selected"),null!=a.group_array_index&&b.push("group-option"),""!==a.classes&&b.push(a.classes),c=document.createElement("li"),c.className=b.join(" "),c.style.cssText=a.style,c.setAttribute("data-option-array-index",a.array_index),c.innerHTML=a.search_text,a.title&&(c.title=a.title),this.outerHTML(c)):""},a.prototype.result_add_group=function(a){var b,c;return(a.search_match||a.group_match)&&a.active_options>0?(b=[],b.push("group-result"),a.classes&&b.push(a.classes),c=document.createElement("li"),c.className=b.join(" "),c.innerHTML=a.search_text,a.title&&(c.title=a.title),this.outerHTML(c)):""},a.prototype.results_update_field=function(){return this.set_default_text(),this.is_multiple||this.results_reset_cleanup(),this.result_clear_highlight(),this.results_build(),this.results_showing?this.winnow_results():void 0},a.prototype.reset_single_select_options=function(){var a,b,c,d,e;for(d=this.results_data,e=[],b=0,c=d.length;c>b;b++)a=d[b],a.selected?e.push(a.selected=!1):e.push(void 0);return e},a.prototype.results_toggle=function(){return this.results_showing?this.results_hide():this.results_show()},a.prototype.results_search=function(a){return this.results_showing?this.winnow_results():this.results_show()},a.prototype.winnow_results=function(){var a,b,c,d,e,f,g,h,i,j,k,l;for(this.no_results_clear(),e=0,g=this.get_search_text(),a=g.replace(/[-[\]{}()*+?.,\\^$|#\s]/g,"\\$&"),d=this.get_search_regex(a),b=this.get_highlight_regex(a),l=this.results_data,j=0,k=l.length;k>j;j++)c=l[j],c.search_match=!1,f=null,this.include_option_in_results(c)&&(c.group&&(c.group_match=!1,c.active_options=0),null!=c.group_array_index&&this.results_data[c.group_array_index]&&(f=this.results_data[c.group_array_index],0===f.active_options&&f.search_match&&(e+=1),f.active_options+=1),c.search_text=c.group?c.label:c.html,(!c.group||this.group_search)&&(c.search_match=this.search_string_match(c.search_text,d),c.search_match&&!c.group&&(e+=1),c.search_match?(g.length&&(h=c.search_text.search(b),i=c.search_text.substr(0,h+g.length)+"</em>"+c.search_text.substr(h+g.length),c.search_text=i.substr(0,h)+"<em>"+i.substr(h)),null!=f&&(f.group_match=!0)):null!=c.group_array_index&&this.results_data[c.group_array_index].search_match&&(c.search_match=!0)));return this.result_clear_highlight(),1>e&&g.length?(this.update_results_content(""),this.no_results(g)):(this.update_results_content(this.results_option_build()),this.winnow_results_set_highlight())},a.prototype.get_search_regex=function(a){var b,c;return b=this.search_contains?"":"^",c=this.case_sensitive_search?"":"i",new RegExp(b+a,c)},a.prototype.get_highlight_regex=function(a){var b,c;return b=this.search_contains?"":"\\b",c=this.case_sensitive_search?"":"i",new RegExp(b+a,c)},a.prototype.search_string_match=function(a,b){var c,d,e,f;if(b.test(a))return!0;if(this.enable_split_word_search&&(a.indexOf(" ")>=0||0===a.indexOf("["))&&(d=a.replace(/\[|\]/g,"").split(" "),d.length))for(e=0,f=d.length;f>e;e++)if(c=d[e],b.test(c))return!0},a.prototype.choices_count=function(){var a,b,c,d;if(null!=this.selected_option_count)return this.selected_option_count;for(this.selected_option_count=0,d=this.form_field.options,b=0,c=d.length;c>b;b++)a=d[b],a.selected&&(this.selected_option_count+=1);return this.selected_option_count},a.prototype.choices_click=function(a){return a.preventDefault(),this.activate_field(),this.results_showing||this.is_disabled?void 0:this.results_show()},a.prototype.keydown_checker=function(a){var b,c;switch(b=null!=(c=a.which)?c:a.keyCode,this.search_field_scale(),8!==b&&this.pending_backstroke&&this.clear_backstroke(),b){case 8:this.backstroke_length=this.get_search_field_value().length;break;case 9:this.results_showing&&!this.is_multiple&&this.result_select(a),this.mouse_on_container=!1;break;case 13:this.results_showing&&a.preventDefault();break;case 27:this.results_showing&&a.preventDefault();break;case 32:this.disable_search&&a.preventDefault();break;case 38:a.preventDefault(),this.keyup_arrow();break;case 40:a.preventDefault(),this.keydown_arrow()}},a.prototype.keyup_checker=function(a){var b,c;switch(b=null!=(c=a.which)?c:a.keyCode,this.search_field_scale(),b){case 8:this.is_multiple&&this.backstroke_length<1&&this.choices_count()>0?this.keydown_backstroke():this.pending_backstroke||(this.result_clear_highlight(),this.results_search());break;case 13:a.preventDefault(),this.results_showing&&this.result_select(a);break;case 27:this.results_showing&&this.results_hide();break;case 9:case 16:case 17:case 18:case 38:case 40:case 91:break;default:this.results_search()}},a.prototype.clipboard_event_checker=function(a){var b=this;if(!this.is_disabled)return setTimeout(function(){return b.results_search()},50)},a.prototype.container_width=function(){return null!=this.options.width?this.options.width:""+this.form_field.offsetWidth+"px"},a.prototype.include_option_in_results=function(a){return this.is_multiple&&!this.display_selected_options&&a.selected?!1:!this.display_disabled_options&&a.disabled?!1:a.empty?!1:!0},a.prototype.search_results_touchstart=function(a){return this.touch_started=!0,this.search_results_mouseover(a)},a.prototype.search_results_touchmove=function(a){return this.touch_started=!1,this.search_results_mouseout(a)},a.prototype.search_results_touchend=function(a){return this.touch_started?this.search_results_mouseup(a):void 0},a.prototype.outerHTML=function(a){var b;return a.outerHTML?a.outerHTML:(b=document.createElement("div"),b.appendChild(a),b.innerHTML)},a.prototype.get_single_html=function(){return'<a class="chosen-single chosen-default">\n  <span>'+this.default_text+'</span>\n  <div><b></b></div>\n</a>\n<div class="chosen-drop">\n  <div class="chosen-search">\n    <input class="chosen-search-input" type="text" autocomplete="off" />\n  </div>\n  <ul class="chosen-results"></ul>\n</div>'},a.prototype.get_multi_html=function(){return'<ul class="chosen-choices">\n  <li class="search-field">\n    <input class="chosen-search-input" type="text" autocomplete="off" value="'+this.default_text+'" />\n  </li>\n</ul>\n<div class="chosen-drop">\n  <ul class="chosen-results"></ul>\n</div>'},a.prototype.get_no_results_html=function(a){return'<li class="no-results">\n  '+this.results_none_found+" <span>"+a+"</span>\n</li>"},a.browser_is_supported=function(){return"Microsoft Internet Explorer"===window.navigator.appName?document.documentMode>=8:/iP(od|hone)/i.test(window.navigator.userAgent)||/IEMobile/i.test(window.navigator.userAgent)||/Windows Phone/i.test(window.navigator.userAgent)||/BlackBerry/i.test(window.navigator.userAgent)||/BB10/i.test(window.navigator.userAgent)||/Android.*Mobile/i.test(window.navigator.userAgent)?!1:!0},a.default_multiple_text="Select Some Options",a.default_single_text="Select an Option",a.default_no_result_text="No results match",a}(),a=jQuery,a.fn.extend({chosen:function(d){return b.browser_is_supported()?this.each(function(b){var e,f;return e=a(this),f=e.data("chosen"),"destroy"===d?void(f instanceof c&&f.destroy()):void(f instanceof c||e.data("chosen",new c(this,d)))}):this}}),c=function(b){function c(){return e=c.__super__.constructor.apply(this,arguments)}return h(c,b),c.prototype.setup=function(){return this.form_field_jq=a(this.form_field),this.current_selectedIndex=this.form_field.selectedIndex},c.prototype.set_up_html=function(){var b,c;return b=["chosen-container"],b.push("chosen-container-"+(this.is_multiple?"multi":"single")),this.inherit_select_classes&&this.form_field.className&&b.push(this.form_field.className),this.is_rtl&&b.push("chosen-rtl"),c={"class":b.join(" "),title:this.form_field.title},this.form_field.id.length&&(c.id=this.form_field.id.replace(/[^\w]/g,"_")+"_chosen"),this.container=a("<div />",c),this.container.width(this.container_width()),this.is_multiple?this.container.html(this.get_multi_html()):this.container.html(this.get_single_html()),this.form_field_jq.hide().after(this.container),this.dropdown=this.container.find("div.chosen-drop").first(),this.search_field=this.container.find("input").first(),this.search_results=this.container.find("ul.chosen-results").first(),this.search_field_scale(),this.search_no_results=this.container.find("li.no-results").first(),this.is_multiple?(this.search_choices=this.container.find("ul.chosen-choices").first(),this.search_container=this.container.find("li.search-field").first()):(this.search_container=this.container.find("div.chosen-search").first(),this.selected_item=this.container.find(".chosen-single").first()),this.results_build(),this.set_tab_index(),this.set_label_behavior()},c.prototype.on_ready=function(){return this.form_field_jq.trigger("chosen:ready",{chosen:this})},c.prototype.register_observers=function(){var a=this;return this.container.bind("touchstart.chosen",function(b){a.container_mousedown(b)}),this.container.bind("touchend.chosen",function(b){a.container_mouseup(b)}),this.container.bind("mousedown.chosen",function(b){a.container_mousedown(b)}),this.container.bind("mouseup.chosen",function(b){a.container_mouseup(b)}),this.container.bind("mouseenter.chosen",function(b){a.mouse_enter(b)}),this.container.bind("mouseleave.chosen",function(b){a.mouse_leave(b)}),this.search_results.bind("mouseup.chosen",function(b){a.search_results_mouseup(b)}),this.search_results.bind("mouseover.chosen",function(b){a.search_results_mouseover(b)}),this.search_results.bind("mouseout.chosen",function(b){a.search_results_mouseout(b)}),this.search_results.bind("mousewheel.chosen DOMMouseScroll.chosen",function(b){a.search_results_mousewheel(b)}),this.search_results.bind("touchstart.chosen",function(b){a.search_results_touchstart(b)}),this.search_results.bind("touchmove.chosen",function(b){a.search_results_touchmove(b)}),this.search_results.bind("touchend.chosen",function(b){a.search_results_touchend(b)}),this.form_field_jq.bind("chosen:updated.chosen",function(b){a.results_update_field(b)}),this.form_field_jq.bind("chosen:activate.chosen",function(b){a.activate_field(b)}),this.form_field_jq.bind("chosen:open.chosen",function(b){a.container_mousedown(b)}),this.form_field_jq.bind("chosen:close.chosen",function(b){a.close_field(b)}),this.search_field.bind("blur.chosen",function(b){a.input_blur(b)}),this.search_field.bind("keyup.chosen",function(b){a.keyup_checker(b)}),this.search_field.bind("keydown.chosen",function(b){a.keydown_checker(b)}),this.search_field.bind("focus.chosen",function(b){a.input_focus(b)}),this.search_field.bind("cut.chosen",function(b){a.clipboard_event_checker(b)}),this.search_field.bind("paste.chosen",function(b){a.clipboard_event_checker(b)}),this.is_multiple?this.search_choices.bind("click.chosen",function(b){a.choices_click(b)}):this.container.bind("click.chosen",function(a){a.preventDefault()})},c.prototype.destroy=function(){return a(this.container[0].ownerDocument).unbind("click.chosen",this.click_test_action),this.form_field_label.length>0&&this.form_field_label.unbind("click.chosen"),this.search_field[0].tabIndex&&(this.form_field_jq[0].tabIndex=this.search_field[0].tabIndex),this.container.remove(),this.form_field_jq.removeData("chosen"),this.form_field_jq.show()},c.prototype.search_field_disabled=function(){return this.is_disabled=this.form_field.disabled||this.form_field_jq.parents("fieldset").is(":disabled"),this.container.toggleClass("chosen-disabled",this.is_disabled),this.search_field[0].disabled=this.is_disabled,this.is_multiple||this.selected_item.unbind("focus.chosen",this.activate_field),this.is_disabled?this.close_field():this.is_multiple?void 0:this.selected_item.bind("focus.chosen",this.activate_field)},c.prototype.container_mousedown=function(b){var c;if(!this.is_disabled)return!b||"mousedown"!==(c=b.type)&&"touchstart"!==c||this.results_showing||b.preventDefault(),null!=b&&a(b.target).hasClass("search-choice-close")?void 0:(this.active_field?this.is_multiple||!b||a(b.target)[0]!==this.selected_item[0]&&!a(b.target).parents("a.chosen-single").length||(b.preventDefault(),this.results_toggle()):(this.is_multiple&&this.search_field.val(""),a(this.container[0].ownerDocument).bind("click.chosen",this.click_test_action),this.results_show()),this.activate_field())},c.prototype.container_mouseup=function(a){return"ABBR"!==a.target.nodeName||this.is_disabled?void 0:this.results_reset(a)},c.prototype.search_results_mousewheel=function(a){var b;return a.originalEvent&&(b=a.originalEvent.deltaY||-a.originalEvent.wheelDelta||a.originalEvent.detail),null!=b?(a.preventDefault(),"DOMMouseScroll"===a.type&&(b=40*b),this.search_results.scrollTop(b+this.search_results.scrollTop())):void 0},c.prototype.blur_test=function(a){return!this.active_field&&this.container.hasClass("chosen-container-active")?this.close_field():void 0},c.prototype.close_field=function(){return a(this.container[0].ownerDocument).unbind("click.chosen",this.click_test_action),this.active_field=!1,this.results_hide(),this.container.removeClass("chosen-container-active"),this.clear_backstroke(),this.show_search_field_default(),this.search_field_scale(),this.search_field.blur()},c.prototype.activate_field=function(){return this.is_disabled?void 0:(this.container.addClass("chosen-container-active"),this.active_field=!0,this.search_field.val(this.search_field.val()),this.search_field.focus())},c.prototype.test_active_click=function(b){var c;return c=a(b.target).closest(".chosen-container"),c.length&&this.container[0]===c[0]?this.active_field=!0:this.close_field()},c.prototype.results_build=function(){return this.parsing=!0,this.selected_option_count=null,this.results_data=d.select_to_array(this.form_field),this.is_multiple?this.search_choices.find("li.search-choice").remove():this.is_multiple||(this.single_set_selected_text(),this.disable_search||this.form_field.options.length<=this.disable_search_threshold?(this.search_field[0].readOnly=!0,this.container.addClass("chosen-container-single-nosearch")):(this.search_field[0].readOnly=!1,this.container.removeClass("chosen-container-single-nosearch"))),this.update_results_content(this.results_option_build({first:!0})),this.search_field_disabled(),this.show_search_field_default(),this.search_field_scale(),this.parsing=!1},c.prototype.result_do_highlight=function(a){var b,c,d,e,f;if(a.length){if(this.result_clear_highlight(),this.result_highlight=a,this.result_highlight.addClass("highlighted"),d=parseInt(this.search_results.css("maxHeight"),10),f=this.search_results.scrollTop(),e=d+f,c=this.result_highlight.position().top+this.search_results.scrollTop(),b=c+this.result_highlight.outerHeight(),b>=e)return this.search_results.scrollTop(b-d>0?b-d:0);if(f>c)return this.search_results.scrollTop(c)}},c.prototype.result_clear_highlight=function(){return this.result_highlight&&this.result_highlight.removeClass("highlighted"),this.result_highlight=null},c.prototype.results_show=function(){return this.is_multiple&&this.max_selected_options<=this.choices_count()?(this.form_field_jq.trigger("chosen:maxselected",{chosen:this}),!1):(this.container.addClass("chosen-with-drop"),this.results_showing=!0,this.search_field.focus(),this.search_field.val(this.get_search_field_value()),this.winnow_results(),this.form_field_jq.trigger("chosen:showing_dropdown",{chosen:this}))},c.prototype.update_results_content=function(a){return this.search_results.html(a)},c.prototype.results_hide=function(){return this.results_showing&&(this.result_clear_highlight(),this.container.removeClass("chosen-with-drop"),this.form_field_jq.trigger("chosen:hiding_dropdown",{chosen:this})),this.results_showing=!1},c.prototype.set_tab_index=function(a){var b;return this.form_field.tabIndex?(b=this.form_field.tabIndex,this.form_field.tabIndex=-1,this.search_field[0].tabIndex=b):void 0},c.prototype.set_label_behavior=function(){return this.form_field_label=this.form_field_jq.parents("label"),!this.form_field_label.length&&this.form_field.id.length&&(this.form_field_label=a("label[for='"+this.form_field.id+"']")),this.form_field_label.length>0?this.form_field_label.bind("click.chosen",this.label_click_handler):void 0},c.prototype.show_search_field_default=function(){return this.is_multiple&&this.choices_count()<1&&!this.active_field?(this.search_field.val(this.default_text),this.search_field.addClass("default")):(this.search_field.val(""),this.search_field.removeClass("default"))},c.prototype.search_results_mouseup=function(b){var c;return c=a(b.target).hasClass("active-result")?a(b.target):a(b.target).parents(".active-result").first(),c.length?(this.result_highlight=c,this.result_select(b),this.search_field.focus()):void 0},c.prototype.search_results_mouseover=function(b){var c;return c=a(b.target).hasClass("active-result")?a(b.target):a(b.target).parents(".active-result").first(),c?this.result_do_highlight(c):void 0},c.prototype.search_results_mouseout=function(b){return a(b.target).hasClass("active-result")?this.result_clear_highlight():void 0},c.prototype.choice_build=function(b){var c,d,e=this;return c=a("<li />",{"class":"search-choice"}).html("<span>"+this.choice_label(b)+"</span>"),b.disabled?c.addClass("search-choice-disabled"):(d=a("<a />",{"class":"search-choice-close","data-option-array-index":b.array_index}),d.bind("click.chosen",function(a){return e.choice_destroy_link_click(a)}),c.append(d)),this.search_container.before(c)},c.prototype.choice_destroy_link_click=function(b){return b.preventDefault(),b.stopPropagation(),this.is_disabled?void 0:this.choice_destroy(a(b.target))},c.prototype.choice_destroy=function(a){return this.result_deselect(a[0].getAttribute("data-option-array-index"))?(this.active_field?this.search_field.focus():this.show_search_field_default(),this.is_multiple&&this.choices_count()>0&&this.get_search_field_value().length<1&&this.results_hide(),a.parents("li").first().remove(),this.search_field_scale()):void 0},c.prototype.results_reset=function(){return this.reset_single_select_options(),this.form_field.options[0].selected=!0,this.single_set_selected_text(),this.show_search_field_default(),this.results_reset_cleanup(),this.trigger_form_field_change(),this.active_field?this.results_hide():void 0},c.prototype.results_reset_cleanup=function(){return this.current_selectedIndex=this.form_field.selectedIndex,this.selected_item.find("abbr").remove()},c.prototype.result_select=function(a){var b,c;return this.result_highlight?(b=this.result_highlight,this.result_clear_highlight(),this.is_multiple&&this.max_selected_options<=this.choices_count()?(this.form_field_jq.trigger("chosen:maxselected",{chosen:this}),!1):(this.is_multiple?b.removeClass("active-result"):this.reset_single_select_options(),b.addClass("result-selected"),c=this.results_data[b[0].getAttribute("data-option-array-index")],c.selected=!0,this.form_field.options[c.options_index].selected=!0,this.selected_option_count=null,this.is_multiple?this.choice_build(c):this.single_set_selected_text(this.choice_label(c)),(!this.is_multiple||this.hide_results_on_select&&!a.metaKey&&!a.ctrlKey)&&(this.results_hide(),this.show_search_field_default()),(this.is_multiple||this.form_field.selectedIndex!==this.current_selectedIndex)&&this.trigger_form_field_change({selected:this.form_field.options[c.options_index].value}),this.current_selectedIndex=this.form_field.selectedIndex,a.preventDefault(),this.search_field_scale())):void 0},c.prototype.single_set_selected_text=function(a){return null==a&&(a=this.default_text),a===this.default_text?this.selected_item.addClass("chosen-default"):(this.single_deselect_control_build(),this.selected_item.removeClass("chosen-default")),this.selected_item.find("span").html(a)},c.prototype.result_deselect=function(a){var b;return b=this.results_data[a],this.form_field.options[b.options_index].disabled?!1:(b.selected=!1,this.form_field.options[b.options_index].selected=!1,this.selected_option_count=null,this.result_clear_highlight(),this.results_showing&&this.winnow_results(),this.trigger_form_field_change({deselected:this.form_field.options[b.options_index].value}),this.search_field_scale(),!0)},c.prototype.single_deselect_control_build=function(){return this.allow_single_deselect?(this.selected_item.find("abbr").length||this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>'),this.selected_item.addClass("chosen-single-with-deselect")):void 0},c.prototype.get_search_field_value=function(){return this.search_field.val()},c.prototype.get_search_text=function(){return this.escape_html(a.trim(this.get_search_field_value()))},c.prototype.escape_html=function(b){return a("<div/>").text(b).html()},c.prototype.winnow_results_set_highlight=function(){var a,b;return b=this.is_multiple?[]:this.search_results.find(".result-selected.active-result"),a=b.length?b.first():this.search_results.find(".active-result").first(),null!=a?this.result_do_highlight(a):void 0},c.prototype.no_results=function(a){var b;return b=this.get_no_results_html(a),this.search_results.append(b),this.form_field_jq.trigger("chosen:no_results",{chosen:this})},c.prototype.no_results_clear=function(){return this.search_results.find(".no-results").remove()},c.prototype.keydown_arrow=function(){var a;return this.results_showing&&this.result_highlight?(a=this.result_highlight.nextAll("li.active-result").first())?this.result_do_highlight(a):void 0:this.results_show()},c.prototype.keyup_arrow=function(){var a;return this.results_showing||this.is_multiple?this.result_highlight?(a=this.result_highlight.prevAll("li.active-result"),a.length?this.result_do_highlight(a.first()):(this.choices_count()>0&&this.results_hide(),this.result_clear_highlight())):void 0:this.results_show()},c.prototype.keydown_backstroke=function(){var a;return this.pending_backstroke?(this.choice_destroy(this.pending_backstroke.find("a").first()),this.clear_backstroke()):(a=this.search_container.siblings("li.search-choice").last(),a.length&&!a.hasClass("search-choice-disabled")?(this.pending_backstroke=a,this.single_backstroke_delete?this.keydown_backstroke():this.pending_backstroke.addClass("search-choice-focus")):void 0)},c.prototype.clear_backstroke=function(){return this.pending_backstroke&&this.pending_backstroke.removeClass("search-choice-focus"),this.pending_backstroke=null},c.prototype.search_field_scale=function(){var b,c,d,e,f,g,h,i;if(this.is_multiple){for(e={position:"absolute",left:"-1000px",top:"-1000px",display:"none",whiteSpace:"pre"},f=["fontSize","fontStyle","fontWeight","fontFamily","lineHeight","textTransform","letterSpacing"],h=0,i=f.length;i>h;h++)d=f[h],e[d]=this.search_field.css(d);return c=a("<div />").css(e),c.text(this.get_search_field_value()),a("body").append(c),g=c.width()+25,c.remove(),b=this.container.outerWidth(),g=Math.min(b-10,g),this.search_field.width(g)}},c.prototype.trigger_form_field_change=function(a){return this.form_field_jq.trigger("input",a),this.form_field_jq.trigger("change",a)},c}(b)}).call(this);

// Ion.RangeSlider | version 2.2.0 | https://github.com/IonDen/ion.rangeSlider
;(function(f){"function"===typeof define&&define.amd?define(["jquery"],function(n){return f(n,document,window,navigator)}):"object"===typeof exports?f(require("jquery"),document,window,navigator):f(jQuery,document,window,navigator)})(function(f,n,k,r,p){var t=0,m=function(){var a=r.userAgent,b=/msie\s\d+/i;return 0<a.search(b)&&(a=b.exec(a).toString(),a=a.split(" ")[1],9>a)?(f("html").addClass("lt-ie9"),!0):!1}();Function.prototype.bind||(Function.prototype.bind=function(a){var b=this,d=[].slice;if("function"!=
typeof b)throw new TypeError;var c=d.call(arguments,1),e=function(){if(this instanceof e){var g=function(){};g.prototype=b.prototype;var g=new g,l=b.apply(g,c.concat(d.call(arguments)));return Object(l)===l?l:g}return b.apply(a,c.concat(d.call(arguments)))};return e});Array.prototype.indexOf||(Array.prototype.indexOf=function(a,b){if(null==this)throw new TypeError('"this" is null or not defined');var d=Object(this),c=d.length>>>0;if(0===c)return-1;var e=+b||0;Infinity===Math.abs(e)&&(e=0);if(e>=c)return-1;
for(e=Math.max(0<=e?e:c-Math.abs(e),0);e<c;){if(e in d&&d[e]===a)return e;e++}return-1});var q=function(a,b,d){this.VERSION="2.2.0";this.input=a;this.plugin_count=d;this.old_to=this.old_from=this.update_tm=this.calc_count=this.current_plugin=0;this.raf_id=this.old_min_interval=null;this.no_diapason=this.force_redraw=this.dragging=!1;this.has_tab_index=!0;this.is_update=this.is_key=!1;this.is_start=!0;this.is_click=this.is_resize=this.is_active=this.is_finish=!1;b=b||{};this.$cache={win:f(k),body:f(n.body),
input:f(a),cont:null,rs:null,min:null,max:null,from:null,to:null,single:null,bar:null,line:null,s_single:null,s_from:null,s_to:null,shad_single:null,shad_from:null,shad_to:null,edge:null,grid:null,grid_labels:[]};this.coords={x_gap:0,x_pointer:0,w_rs:0,w_rs_old:0,w_handle:0,p_gap:0,p_gap_left:0,p_gap_right:0,p_step:0,p_pointer:0,p_handle:0,p_single_fake:0,p_single_real:0,p_from_fake:0,p_from_real:0,p_to_fake:0,p_to_real:0,p_bar_x:0,p_bar_w:0,grid_gap:0,big_num:0,big:[],big_w:[],big_p:[],big_x:[]};
this.labels={w_min:0,w_max:0,w_from:0,w_to:0,w_single:0,p_min:0,p_max:0,p_from_fake:0,p_from_left:0,p_to_fake:0,p_to_left:0,p_single_fake:0,p_single_left:0};var c=this.$cache.input;a=c.prop("value");var e;d={type:"single",min:10,max:100,from:null,to:null,step:1,min_interval:0,max_interval:0,drag_interval:!1,values:[],p_values:[],from_fixed:!1,from_min:null,from_max:null,from_shadow:!1,to_fixed:!1,to_min:null,to_max:null,to_shadow:!1,prettify_enabled:!0,prettify_separator:" ",prettify:null,force_edges:!1,
keyboard:!0,grid:!1,grid_margin:!0,grid_num:4,grid_snap:!1,hide_min_max:!1,hide_from_to:!1,prefix:"",postfix:"",max_postfix:"",decorate_both:!0,values_separator:" \u2014 ",input_values_separator:";",disable:!1,block:!1,extra_classes:"",scope:null,onStart:null,onChange:null,onFinish:null,onUpdate:null};"INPUT"!==c[0].nodeName&&console&&console.warn&&console.warn("Base element should be <input>!",c[0]);c={type:c.data("type"),min:c.data("min"),max:c.data("max"),from:c.data("from"),to:c.data("to"),step:c.data("step"),
min_interval:c.data("minInterval"),max_interval:c.data("maxInterval"),drag_interval:c.data("dragInterval"),values:c.data("values"),from_fixed:c.data("fromFixed"),from_min:c.data("fromMin"),from_max:c.data("fromMax"),from_shadow:c.data("fromShadow"),to_fixed:c.data("toFixed"),to_min:c.data("toMin"),to_max:c.data("toMax"),to_shadow:c.data("toShadow"),prettify_enabled:c.data("prettifyEnabled"),prettify_separator:c.data("prettifySeparator"),force_edges:c.data("forceEdges"),keyboard:c.data("keyboard"),
grid:c.data("grid"),grid_margin:c.data("gridMargin"),grid_num:c.data("gridNum"),grid_snap:c.data("gridSnap"),hide_min_max:c.data("hideMinMax"),hide_from_to:c.data("hideFromTo"),prefix:c.data("prefix"),postfix:c.data("postfix"),max_postfix:c.data("maxPostfix"),decorate_both:c.data("decorateBoth"),values_separator:c.data("valuesSeparator"),input_values_separator:c.data("inputValuesSeparator"),disable:c.data("disable"),block:c.data("block"),extra_classes:c.data("extraClasses")};c.values=c.values&&c.values.split(",");
for(e in c)c.hasOwnProperty(e)&&(c[e]!==p&&""!==c[e]||delete c[e]);a!==p&&""!==a&&(a=a.split(c.input_values_separator||b.input_values_separator||";"),a[0]&&a[0]==+a[0]&&(a[0]=+a[0]),a[1]&&a[1]==+a[1]&&(a[1]=+a[1]),b&&b.values&&b.values.length?(d.from=a[0]&&b.values.indexOf(a[0]),d.to=a[1]&&b.values.indexOf(a[1])):(d.from=a[0]&&+a[0],d.to=a[1]&&+a[1]));f.extend(d,b);f.extend(d,c);this.options=d;this.update_check={};this.validate();this.result={input:this.$cache.input,slider:null,min:this.options.min,
max:this.options.max,from:this.options.from,from_percent:0,from_value:null,to:this.options.to,to_percent:0,to_value:null};this.init()};q.prototype={init:function(a){this.no_diapason=!1;this.coords.p_step=this.convertToPercent(this.options.step,!0);this.target="base";this.toggleInput();this.append();this.setMinMax();a?(this.force_redraw=!0,this.calc(!0),this.callOnUpdate()):(this.force_redraw=!0,this.calc(!0),this.callOnStart());this.updateScene()},append:function(){this.$cache.input.before('<span class="irs js-irs-'+
this.plugin_count+" "+this.options.extra_classes+'"></span>');this.$cache.input.prop("readonly",!0);this.$cache.cont=this.$cache.input.prev();this.result.slider=this.$cache.cont;this.$cache.cont.html('<span class="irs"><span class="irs-line" tabindex="0"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min">0</span><span class="irs-max">1</span><span class="irs-from">0</span><span class="irs-to">0</span><span class="irs-single">0</span></span><span class="irs-grid"></span><span class="irs-bar"></span>');
this.$cache.rs=this.$cache.cont.find(".irs");this.$cache.min=this.$cache.cont.find(".irs-min");this.$cache.max=this.$cache.cont.find(".irs-max");this.$cache.from=this.$cache.cont.find(".irs-from");this.$cache.to=this.$cache.cont.find(".irs-to");this.$cache.single=this.$cache.cont.find(".irs-single");this.$cache.bar=this.$cache.cont.find(".irs-bar");this.$cache.line=this.$cache.cont.find(".irs-line");this.$cache.grid=this.$cache.cont.find(".irs-grid");"single"===this.options.type?(this.$cache.cont.append('<span class="irs-bar-edge"></span><span class="irs-shadow shadow-single"></span><span class="irs-slider single"></span>'),
this.$cache.edge=this.$cache.cont.find(".irs-bar-edge"),this.$cache.s_single=this.$cache.cont.find(".single"),this.$cache.from[0].style.visibility="hidden",this.$cache.to[0].style.visibility="hidden",this.$cache.shad_single=this.$cache.cont.find(".shadow-single")):(this.$cache.cont.append('<span class="irs-shadow shadow-from"></span><span class="irs-shadow shadow-to"></span><span class="irs-slider from"></span><span class="irs-slider to"></span>'),this.$cache.s_from=this.$cache.cont.find(".from"),
this.$cache.s_to=this.$cache.cont.find(".to"),this.$cache.shad_from=this.$cache.cont.find(".shadow-from"),this.$cache.shad_to=this.$cache.cont.find(".shadow-to"),this.setTopHandler());this.options.hide_from_to&&(this.$cache.from[0].style.display="none",this.$cache.to[0].style.display="none",this.$cache.single[0].style.display="none");this.appendGrid();this.options.disable?(this.appendDisableMask(),this.$cache.input[0].disabled=!0):(this.$cache.input[0].disabled=!1,this.removeDisableMask(),this.bindEvents());
this.options.disable||(this.options.block?this.appendDisableMask():this.removeDisableMask());this.options.drag_interval&&(this.$cache.bar[0].style.cursor="ew-resize")},setTopHandler:function(){var a=this.options.max,b=this.options.to;this.options.from>this.options.min&&b===a?this.$cache.s_from.addClass("type_last"):b<a&&this.$cache.s_to.addClass("type_last")},changeLevel:function(a){switch(a){case "single":this.coords.p_gap=this.toFixed(this.coords.p_pointer-this.coords.p_single_fake);this.$cache.s_single.addClass("state_hover");
break;case "from":this.coords.p_gap=this.toFixed(this.coords.p_pointer-this.coords.p_from_fake);this.$cache.s_from.addClass("state_hover");this.$cache.s_from.addClass("type_last");this.$cache.s_to.removeClass("type_last");break;case "to":this.coords.p_gap=this.toFixed(this.coords.p_pointer-this.coords.p_to_fake);this.$cache.s_to.addClass("state_hover");this.$cache.s_to.addClass("type_last");this.$cache.s_from.removeClass("type_last");break;case "both":this.coords.p_gap_left=this.toFixed(this.coords.p_pointer-
this.coords.p_from_fake),this.coords.p_gap_right=this.toFixed(this.coords.p_to_fake-this.coords.p_pointer),this.$cache.s_to.removeClass("type_last"),this.$cache.s_from.removeClass("type_last")}},appendDisableMask:function(){this.$cache.cont.append('<span class="irs-disable-mask"></span>');this.$cache.cont.addClass("irs-disabled")},removeDisableMask:function(){this.$cache.cont.remove(".irs-disable-mask");this.$cache.cont.removeClass("irs-disabled")},remove:function(){this.$cache.cont.remove();this.$cache.cont=
null;this.$cache.line.off("keydown.irs_"+this.plugin_count);this.$cache.body.off("touchmove.irs_"+this.plugin_count);this.$cache.body.off("mousemove.irs_"+this.plugin_count);this.$cache.win.off("touchend.irs_"+this.plugin_count);this.$cache.win.off("mouseup.irs_"+this.plugin_count);m&&(this.$cache.body.off("mouseup.irs_"+this.plugin_count),this.$cache.body.off("mouseleave.irs_"+this.plugin_count));this.$cache.grid_labels=[];this.coords.big=[];this.coords.big_w=[];this.coords.big_p=[];this.coords.big_x=
[];cancelAnimationFrame(this.raf_id)},bindEvents:function(){if(!this.no_diapason){this.$cache.body.on("touchmove.irs_"+this.plugin_count,this.pointerMove.bind(this));this.$cache.body.on("mousemove.irs_"+this.plugin_count,this.pointerMove.bind(this));this.$cache.win.on("touchend.irs_"+this.plugin_count,this.pointerUp.bind(this));this.$cache.win.on("mouseup.irs_"+this.plugin_count,this.pointerUp.bind(this));this.$cache.line.on("touchstart.irs_"+this.plugin_count,this.pointerClick.bind(this,"click"));
this.$cache.line.on("mousedown.irs_"+this.plugin_count,this.pointerClick.bind(this,"click"));this.$cache.line.on("focus.irs_"+this.plugin_count,this.pointerFocus.bind(this));this.options.drag_interval&&"double"===this.options.type?(this.$cache.bar.on("touchstart.irs_"+this.plugin_count,this.pointerDown.bind(this,"both")),this.$cache.bar.on("mousedown.irs_"+this.plugin_count,this.pointerDown.bind(this,"both"))):(this.$cache.bar.on("touchstart.irs_"+this.plugin_count,this.pointerClick.bind(this,"click")),
this.$cache.bar.on("mousedown.irs_"+this.plugin_count,this.pointerClick.bind(this,"click")));"single"===this.options.type?(this.$cache.single.on("touchstart.irs_"+this.plugin_count,this.pointerDown.bind(this,"single")),this.$cache.s_single.on("touchstart.irs_"+this.plugin_count,this.pointerDown.bind(this,"single")),this.$cache.shad_single.on("touchstart.irs_"+this.plugin_count,this.pointerClick.bind(this,"click")),this.$cache.single.on("mousedown.irs_"+this.plugin_count,this.pointerDown.bind(this,
"single")),this.$cache.s_single.on("mousedown.irs_"+this.plugin_count,this.pointerDown.bind(this,"single")),this.$cache.edge.on("mousedown.irs_"+this.plugin_count,this.pointerClick.bind(this,"click")),this.$cache.shad_single.on("mousedown.irs_"+this.plugin_count,this.pointerClick.bind(this,"click"))):(this.$cache.single.on("touchstart.irs_"+this.plugin_count,this.pointerDown.bind(this,null)),this.$cache.single.on("mousedown.irs_"+this.plugin_count,this.pointerDown.bind(this,null)),this.$cache.from.on("touchstart.irs_"+
this.plugin_count,this.pointerDown.bind(this,"from")),this.$cache.s_from.on("touchstart.irs_"+this.plugin_count,this.pointerDown.bind(this,"from")),this.$cache.to.on("touchstart.irs_"+this.plugin_count,this.pointerDown.bind(this,"to")),this.$cache.s_to.on("touchstart.irs_"+this.plugin_count,this.pointerDown.bind(this,"to")),this.$cache.shad_from.on("touchstart.irs_"+this.plugin_count,this.pointerClick.bind(this,"click")),this.$cache.shad_to.on("touchstart.irs_"+this.plugin_count,this.pointerClick.bind(this,
"click")),this.$cache.from.on("mousedown.irs_"+this.plugin_count,this.pointerDown.bind(this,"from")),this.$cache.s_from.on("mousedown.irs_"+this.plugin_count,this.pointerDown.bind(this,"from")),this.$cache.to.on("mousedown.irs_"+this.plugin_count,this.pointerDown.bind(this,"to")),this.$cache.s_to.on("mousedown.irs_"+this.plugin_count,this.pointerDown.bind(this,"to")),this.$cache.shad_from.on("mousedown.irs_"+this.plugin_count,this.pointerClick.bind(this,"click")),this.$cache.shad_to.on("mousedown.irs_"+
this.plugin_count,this.pointerClick.bind(this,"click")));if(this.options.keyboard)this.$cache.line.on("keydown.irs_"+this.plugin_count,this.key.bind(this,"keyboard"));m&&(this.$cache.body.on("mouseup.irs_"+this.plugin_count,this.pointerUp.bind(this)),this.$cache.body.on("mouseleave.irs_"+this.plugin_count,this.pointerUp.bind(this)))}},pointerFocus:function(a){if(!this.target){var b="single"===this.options.type?this.$cache.single:this.$cache.from;a=b.offset().left;a+=b.width()/2-1;this.pointerClick("single",
{preventDefault:function(){},pageX:a})}},pointerMove:function(a){this.dragging&&(this.coords.x_pointer=(a.pageX||a.originalEvent.touches&&a.originalEvent.touches[0].pageX)-this.coords.x_gap,this.calc())},pointerUp:function(a){this.current_plugin===this.plugin_count&&this.is_active&&(this.is_active=!1,this.$cache.cont.find(".state_hover").removeClass("state_hover"),this.force_redraw=!0,m&&f("*").prop("unselectable",!1),this.updateScene(),this.restoreOriginalMinInterval(),(f.contains(this.$cache.cont[0],
a.target)||this.dragging)&&this.callOnFinish(),this.dragging=!1)},pointerDown:function(a,b){b.preventDefault();var d=b.pageX||b.originalEvent.touches&&b.originalEvent.touches[0].pageX;2!==b.button&&("both"===a&&this.setTempMinInterval(),a||(a=this.target||"from"),this.current_plugin=this.plugin_count,this.target=a,this.dragging=this.is_active=!0,this.coords.x_gap=this.$cache.rs.offset().left,this.coords.x_pointer=d-this.coords.x_gap,this.calcPointerPercent(),this.changeLevel(a),m&&f("*").prop("unselectable",
!0),this.$cache.line.trigger("focus"),this.updateScene())},pointerClick:function(a,b){b.preventDefault();var d=b.pageX||b.originalEvent.touches&&b.originalEvent.touches[0].pageX;2!==b.button&&(this.current_plugin=this.plugin_count,this.target=a,this.is_click=!0,this.coords.x_gap=this.$cache.rs.offset().left,this.coords.x_pointer=+(d-this.coords.x_gap).toFixed(),this.force_redraw=!0,this.calc(),this.$cache.line.trigger("focus"))},key:function(a,b){if(!(this.current_plugin!==this.plugin_count||b.altKey||
b.ctrlKey||b.shiftKey||b.metaKey)){switch(b.which){case 83:case 65:case 40:case 37:b.preventDefault();this.moveByKey(!1);break;case 87:case 68:case 38:case 39:b.preventDefault(),this.moveByKey(!0)}return!0}},moveByKey:function(a){var b=this.coords.p_pointer,d=(this.options.max-this.options.min)/100,d=this.options.step/d;this.coords.x_pointer=this.toFixed(this.coords.w_rs/100*(a?b+d:b-d));this.is_key=!0;this.calc()},setMinMax:function(){if(this.options)if(this.options.hide_min_max)this.$cache.min[0].style.display=
"none",this.$cache.max[0].style.display="none";else{if(this.options.values.length)this.$cache.min.html(this.decorate(this.options.p_values[this.options.min])),this.$cache.max.html(this.decorate(this.options.p_values[this.options.max]));else{var a=this._prettify(this.options.min),b=this._prettify(this.options.max);this.result.min_pretty=a;this.result.max_pretty=b;this.$cache.min.html(this.decorate(a,this.options.min));this.$cache.max.html(this.decorate(b,this.options.max))}this.labels.w_min=this.$cache.min.outerWidth(!1);
this.labels.w_max=this.$cache.max.outerWidth(!1)}},setTempMinInterval:function(){var a=this.result.to-this.result.from;null===this.old_min_interval&&(this.old_min_interval=this.options.min_interval);this.options.min_interval=a},restoreOriginalMinInterval:function(){null!==this.old_min_interval&&(this.options.min_interval=this.old_min_interval,this.old_min_interval=null)},calc:function(a){if(this.options){this.calc_count++;if(10===this.calc_count||a)this.calc_count=0,this.coords.w_rs=this.$cache.rs.outerWidth(!1),
this.calcHandlePercent();if(this.coords.w_rs){this.calcPointerPercent();a=this.getHandleX();"both"===this.target&&(this.coords.p_gap=0,a=this.getHandleX());"click"===this.target&&(this.coords.p_gap=this.coords.p_handle/2,a=this.getHandleX(),this.target=this.options.drag_interval?"both_one":this.chooseHandle(a));switch(this.target){case "base":var b=(this.options.max-this.options.min)/100;a=(this.result.from-this.options.min)/b;b=(this.result.to-this.options.min)/b;this.coords.p_single_real=this.toFixed(a);
this.coords.p_from_real=this.toFixed(a);this.coords.p_to_real=this.toFixed(b);this.coords.p_single_real=this.checkDiapason(this.coords.p_single_real,this.options.from_min,this.options.from_max);this.coords.p_from_real=this.checkDiapason(this.coords.p_from_real,this.options.from_min,this.options.from_max);this.coords.p_to_real=this.checkDiapason(this.coords.p_to_real,this.options.to_min,this.options.to_max);this.coords.p_single_fake=this.convertToFakePercent(this.coords.p_single_real);this.coords.p_from_fake=
this.convertToFakePercent(this.coords.p_from_real);this.coords.p_to_fake=this.convertToFakePercent(this.coords.p_to_real);this.target=null;break;case "single":if(this.options.from_fixed)break;this.coords.p_single_real=this.convertToRealPercent(a);this.coords.p_single_real=this.calcWithStep(this.coords.p_single_real);this.coords.p_single_real=this.checkDiapason(this.coords.p_single_real,this.options.from_min,this.options.from_max);this.coords.p_single_fake=this.convertToFakePercent(this.coords.p_single_real);
break;case "from":if(this.options.from_fixed)break;this.coords.p_from_real=this.convertToRealPercent(a);this.coords.p_from_real=this.calcWithStep(this.coords.p_from_real);this.coords.p_from_real>this.coords.p_to_real&&(this.coords.p_from_real=this.coords.p_to_real);this.coords.p_from_real=this.checkDiapason(this.coords.p_from_real,this.options.from_min,this.options.from_max);this.coords.p_from_real=this.checkMinInterval(this.coords.p_from_real,this.coords.p_to_real,"from");this.coords.p_from_real=
this.checkMaxInterval(this.coords.p_from_real,this.coords.p_to_real,"from");this.coords.p_from_fake=this.convertToFakePercent(this.coords.p_from_real);break;case "to":if(this.options.to_fixed)break;this.coords.p_to_real=this.convertToRealPercent(a);this.coords.p_to_real=this.calcWithStep(this.coords.p_to_real);this.coords.p_to_real<this.coords.p_from_real&&(this.coords.p_to_real=this.coords.p_from_real);this.coords.p_to_real=this.checkDiapason(this.coords.p_to_real,this.options.to_min,this.options.to_max);
this.coords.p_to_real=this.checkMinInterval(this.coords.p_to_real,this.coords.p_from_real,"to");this.coords.p_to_real=this.checkMaxInterval(this.coords.p_to_real,this.coords.p_from_real,"to");this.coords.p_to_fake=this.convertToFakePercent(this.coords.p_to_real);break;case "both":if(this.options.from_fixed||this.options.to_fixed)break;a=this.toFixed(a+.001*this.coords.p_handle);this.coords.p_from_real=this.convertToRealPercent(a)-this.coords.p_gap_left;this.coords.p_from_real=this.calcWithStep(this.coords.p_from_real);
this.coords.p_from_real=this.checkDiapason(this.coords.p_from_real,this.options.from_min,this.options.from_max);this.coords.p_from_real=this.checkMinInterval(this.coords.p_from_real,this.coords.p_to_real,"from");this.coords.p_from_fake=this.convertToFakePercent(this.coords.p_from_real);this.coords.p_to_real=this.convertToRealPercent(a)+this.coords.p_gap_right;this.coords.p_to_real=this.calcWithStep(this.coords.p_to_real);this.coords.p_to_real=this.checkDiapason(this.coords.p_to_real,this.options.to_min,
this.options.to_max);this.coords.p_to_real=this.checkMinInterval(this.coords.p_to_real,this.coords.p_from_real,"to");this.coords.p_to_fake=this.convertToFakePercent(this.coords.p_to_real);break;case "both_one":if(!this.options.from_fixed&&!this.options.to_fixed){var d=this.convertToRealPercent(a);a=this.result.to_percent-this.result.from_percent;var c=a/2,b=d-c,d=d+c;0>b&&(b=0,d=b+a);100<d&&(d=100,b=d-a);this.coords.p_from_real=this.calcWithStep(b);this.coords.p_from_real=this.checkDiapason(this.coords.p_from_real,
this.options.from_min,this.options.from_max);this.coords.p_from_fake=this.convertToFakePercent(this.coords.p_from_real);this.coords.p_to_real=this.calcWithStep(d);this.coords.p_to_real=this.checkDiapason(this.coords.p_to_real,this.options.to_min,this.options.to_max);this.coords.p_to_fake=this.convertToFakePercent(this.coords.p_to_real)}}"single"===this.options.type?(this.coords.p_bar_x=this.coords.p_handle/2,this.coords.p_bar_w=this.coords.p_single_fake,this.result.from_percent=this.coords.p_single_real,
this.result.from=this.convertToValue(this.coords.p_single_real),this.result.from_pretty=this._prettify(this.result.from),this.options.values.length&&(this.result.from_value=this.options.values[this.result.from])):(this.coords.p_bar_x=this.toFixed(this.coords.p_from_fake+this.coords.p_handle/2),this.coords.p_bar_w=this.toFixed(this.coords.p_to_fake-this.coords.p_from_fake),this.result.from_percent=this.coords.p_from_real,this.result.from=this.convertToValue(this.coords.p_from_real),this.result.from_pretty=
this._prettify(this.result.from),this.result.to_percent=this.coords.p_to_real,this.result.to=this.convertToValue(this.coords.p_to_real),this.result.to_pretty=this._prettify(this.result.to),this.options.values.length&&(this.result.from_value=this.options.values[this.result.from],this.result.to_value=this.options.values[this.result.to]));this.calcMinMax();this.calcLabels()}}},calcPointerPercent:function(){this.coords.w_rs?(0>this.coords.x_pointer||isNaN(this.coords.x_pointer)?this.coords.x_pointer=
0:this.coords.x_pointer>this.coords.w_rs&&(this.coords.x_pointer=this.coords.w_rs),this.coords.p_pointer=this.toFixed(this.coords.x_pointer/this.coords.w_rs*100)):this.coords.p_pointer=0},convertToRealPercent:function(a){return a/(100-this.coords.p_handle)*100},convertToFakePercent:function(a){return a/100*(100-this.coords.p_handle)},getHandleX:function(){var a=100-this.coords.p_handle,b=this.toFixed(this.coords.p_pointer-this.coords.p_gap);0>b?b=0:b>a&&(b=a);return b},calcHandlePercent:function(){this.coords.w_handle=
"single"===this.options.type?this.$cache.s_single.outerWidth(!1):this.$cache.s_from.outerWidth(!1);this.coords.p_handle=this.toFixed(this.coords.w_handle/this.coords.w_rs*100)},chooseHandle:function(a){return"single"===this.options.type?"single":a>=this.coords.p_from_real+(this.coords.p_to_real-this.coords.p_from_real)/2?this.options.to_fixed?"from":"to":this.options.from_fixed?"to":"from"},calcMinMax:function(){this.coords.w_rs&&(this.labels.p_min=this.labels.w_min/this.coords.w_rs*100,this.labels.p_max=
this.labels.w_max/this.coords.w_rs*100)},calcLabels:function(){this.coords.w_rs&&!this.options.hide_from_to&&("single"===this.options.type?(this.labels.w_single=this.$cache.single.outerWidth(!1),this.labels.p_single_fake=this.labels.w_single/this.coords.w_rs*100,this.labels.p_single_left=this.coords.p_single_fake+this.coords.p_handle/2-this.labels.p_single_fake/2):(this.labels.w_from=this.$cache.from.outerWidth(!1),this.labels.p_from_fake=this.labels.w_from/this.coords.w_rs*100,this.labels.p_from_left=
this.coords.p_from_fake+this.coords.p_handle/2-this.labels.p_from_fake/2,this.labels.p_from_left=this.toFixed(this.labels.p_from_left),this.labels.p_from_left=this.checkEdges(this.labels.p_from_left,this.labels.p_from_fake),this.labels.w_to=this.$cache.to.outerWidth(!1),this.labels.p_to_fake=this.labels.w_to/this.coords.w_rs*100,this.labels.p_to_left=this.coords.p_to_fake+this.coords.p_handle/2-this.labels.p_to_fake/2,this.labels.p_to_left=this.toFixed(this.labels.p_to_left),this.labels.p_to_left=
this.checkEdges(this.labels.p_to_left,this.labels.p_to_fake),this.labels.w_single=this.$cache.single.outerWidth(!1),this.labels.p_single_fake=this.labels.w_single/this.coords.w_rs*100,this.labels.p_single_left=(this.labels.p_from_left+this.labels.p_to_left+this.labels.p_to_fake)/2-this.labels.p_single_fake/2,this.labels.p_single_left=this.toFixed(this.labels.p_single_left)),this.labels.p_single_left=this.checkEdges(this.labels.p_single_left,this.labels.p_single_fake))},updateScene:function(){this.raf_id&&
(cancelAnimationFrame(this.raf_id),this.raf_id=null);clearTimeout(this.update_tm);this.update_tm=null;this.options&&(this.drawHandles(),this.is_active?this.raf_id=requestAnimationFrame(this.updateScene.bind(this)):this.update_tm=setTimeout(this.updateScene.bind(this),300))},drawHandles:function(){this.coords.w_rs=this.$cache.rs.outerWidth(!1);if(this.coords.w_rs){this.coords.w_rs!==this.coords.w_rs_old&&(this.target="base",this.is_resize=!0);if(this.coords.w_rs!==this.coords.w_rs_old||this.force_redraw)this.setMinMax(),
this.calc(!0),this.drawLabels(),this.options.grid&&(this.calcGridMargin(),this.calcGridLabels()),this.force_redraw=!0,this.coords.w_rs_old=this.coords.w_rs,this.drawShadow();if(this.coords.w_rs&&(this.dragging||this.force_redraw||this.is_key)){if(this.old_from!==this.result.from||this.old_to!==this.result.to||this.force_redraw||this.is_key){this.drawLabels();this.$cache.bar[0].style.left=this.coords.p_bar_x+"%";this.$cache.bar[0].style.width=this.coords.p_bar_w+"%";if("single"===this.options.type)this.$cache.s_single[0].style.left=
this.coords.p_single_fake+"%";else{this.$cache.s_from[0].style.left=this.coords.p_from_fake+"%";this.$cache.s_to[0].style.left=this.coords.p_to_fake+"%";if(this.old_from!==this.result.from||this.force_redraw)this.$cache.from[0].style.left=this.labels.p_from_left+"%";if(this.old_to!==this.result.to||this.force_redraw)this.$cache.to[0].style.left=this.labels.p_to_left+"%"}this.$cache.single[0].style.left=this.labels.p_single_left+"%";this.writeToInput();this.old_from===this.result.from&&this.old_to===
this.result.to||this.is_start||(this.$cache.input.trigger("change"),this.$cache.input.trigger("input"));this.old_from=this.result.from;this.old_to=this.result.to;this.is_resize||this.is_update||this.is_start||this.is_finish||this.callOnChange();if(this.is_key||this.is_click)this.is_click=this.is_key=!1,this.callOnFinish();this.is_finish=this.is_resize=this.is_update=!1}this.force_redraw=this.is_click=this.is_key=this.is_start=!1}}},drawLabels:function(){if(this.options){var a=this.options.values.length,
b=this.options.p_values;if(!this.options.hide_from_to)if("single"===this.options.type){if(a)a=this.decorate(b[this.result.from]);else{var d=this._prettify(this.result.from);a=this.decorate(d,this.result.from)}this.$cache.single.html(a);this.calcLabels();this.$cache.min[0].style.visibility=this.labels.p_single_left<this.labels.p_min+1?"hidden":"visible";this.$cache.max[0].style.visibility=this.labels.p_single_left+this.labels.p_single_fake>100-this.labels.p_max-1?"hidden":"visible"}else{a?(this.options.decorate_both?
(a=this.decorate(b[this.result.from]),a+=this.options.values_separator,a+=this.decorate(b[this.result.to])):a=this.decorate(b[this.result.from]+this.options.values_separator+b[this.result.to]),d=this.decorate(b[this.result.from]),b=this.decorate(b[this.result.to])):(d=this._prettify(this.result.from),b=this._prettify(this.result.to),this.options.decorate_both?(a=this.decorate(d,this.result.from),a+=this.options.values_separator,a+=this.decorate(b,this.result.to)):a=this.decorate(d+this.options.values_separator+
b,this.result.to),d=this.decorate(d,this.result.from),b=this.decorate(b,this.result.to));this.$cache.single.html(a);this.$cache.from.html(d);this.$cache.to.html(b);this.calcLabels();a=Math.min(this.labels.p_single_left,this.labels.p_from_left);d=this.labels.p_single_left+this.labels.p_single_fake;var b=this.labels.p_to_left+this.labels.p_to_fake,c=Math.max(d,b);this.labels.p_from_left+this.labels.p_from_fake>=this.labels.p_to_left?(this.$cache.from[0].style.visibility="hidden",this.$cache.to[0].style.visibility=
"hidden",this.$cache.single[0].style.visibility="visible",this.result.from===this.result.to?("from"===this.target?this.$cache.from[0].style.visibility="visible":"to"===this.target?this.$cache.to[0].style.visibility="visible":this.target||(this.$cache.from[0].style.visibility="visible"),this.$cache.single[0].style.visibility="hidden",c=b):(this.$cache.from[0].style.visibility="hidden",this.$cache.to[0].style.visibility="hidden",this.$cache.single[0].style.visibility="visible",c=Math.max(d,b))):(this.$cache.from[0].style.visibility=
"visible",this.$cache.to[0].style.visibility="visible",this.$cache.single[0].style.visibility="hidden");this.$cache.min[0].style.visibility=a<this.labels.p_min+1?"hidden":"visible";this.$cache.max[0].style.visibility=c>100-this.labels.p_max-1?"hidden":"visible"}}},drawShadow:function(){var a=this.options,b=this.$cache,d="number"===typeof a.from_min&&!isNaN(a.from_min),c="number"===typeof a.from_max&&!isNaN(a.from_max),e="number"===typeof a.to_min&&!isNaN(a.to_min),g="number"===typeof a.to_max&&!isNaN(a.to_max);
"single"===a.type?a.from_shadow&&(d||c)?(d=this.convertToPercent(d?a.from_min:a.min),c=this.convertToPercent(c?a.from_max:a.max)-d,d=this.toFixed(d-this.coords.p_handle/100*d),c=this.toFixed(c-this.coords.p_handle/100*c),d+=this.coords.p_handle/2,b.shad_single[0].style.display="block",b.shad_single[0].style.left=d+"%",b.shad_single[0].style.width=c+"%"):b.shad_single[0].style.display="none":(a.from_shadow&&(d||c)?(d=this.convertToPercent(d?a.from_min:a.min),c=this.convertToPercent(c?a.from_max:a.max)-
d,d=this.toFixed(d-this.coords.p_handle/100*d),c=this.toFixed(c-this.coords.p_handle/100*c),d+=this.coords.p_handle/2,b.shad_from[0].style.display="block",b.shad_from[0].style.left=d+"%",b.shad_from[0].style.width=c+"%"):b.shad_from[0].style.display="none",a.to_shadow&&(e||g)?(e=this.convertToPercent(e?a.to_min:a.min),a=this.convertToPercent(g?a.to_max:a.max)-e,e=this.toFixed(e-this.coords.p_handle/100*e),a=this.toFixed(a-this.coords.p_handle/100*a),e+=this.coords.p_handle/2,b.shad_to[0].style.display=
"block",b.shad_to[0].style.left=e+"%",b.shad_to[0].style.width=a+"%"):b.shad_to[0].style.display="none")},writeToInput:function(){"single"===this.options.type?(this.options.values.length?this.$cache.input.prop("value",this.result.from_value):this.$cache.input.prop("value",this.result.from),this.$cache.input.data("from",this.result.from)):(this.options.values.length?this.$cache.input.prop("value",this.result.from_value+this.options.input_values_separator+this.result.to_value):this.$cache.input.prop("value",
this.result.from+this.options.input_values_separator+this.result.to),this.$cache.input.data("from",this.result.from),this.$cache.input.data("to",this.result.to))},callOnStart:function(){this.writeToInput();if(this.options.onStart&&"function"===typeof this.options.onStart)if(this.options.scope)this.options.onStart.call(this.options.scope,this.result);else this.options.onStart(this.result)},callOnChange:function(){this.writeToInput();if(this.options.onChange&&"function"===typeof this.options.onChange)if(this.options.scope)this.options.onChange.call(this.options.scope,
this.result);else this.options.onChange(this.result)},callOnFinish:function(){this.writeToInput();if(this.options.onFinish&&"function"===typeof this.options.onFinish)if(this.options.scope)this.options.onFinish.call(this.options.scope,this.result);else this.options.onFinish(this.result)},callOnUpdate:function(){this.writeToInput();if(this.options.onUpdate&&"function"===typeof this.options.onUpdate)if(this.options.scope)this.options.onUpdate.call(this.options.scope,this.result);else this.options.onUpdate(this.result)},
toggleInput:function(){this.$cache.input.toggleClass("irs-hidden-input");this.has_tab_index?this.$cache.input.prop("tabindex",-1):this.$cache.input.removeProp("tabindex");this.has_tab_index=!this.has_tab_index},convertToPercent:function(a,b){var d=this.options.max-this.options.min;return d?this.toFixed((b?a:a-this.options.min)/(d/100)):(this.no_diapason=!0,0)},convertToValue:function(a){var b=this.options.min,d=this.options.max,c=b.toString().split(".")[1],e=d.toString().split(".")[1],g,l,f=0,h=0;
if(0===a)return this.options.min;if(100===a)return this.options.max;c&&(f=g=c.length);e&&(f=l=e.length);g&&l&&(f=g>=l?g:l);0>b&&(h=Math.abs(b),b=+(b+h).toFixed(f),d=+(d+h).toFixed(f));a=(d-b)/100*a+b;(b=this.options.step.toString().split(".")[1])?a=+a.toFixed(b.length):(a/=this.options.step,a*=this.options.step,a=+a.toFixed(0));h&&(a-=h);h=b?+a.toFixed(b.length):this.toFixed(a);h<this.options.min?h=this.options.min:h>this.options.max&&(h=this.options.max);return h},calcWithStep:function(a){var b=
Math.round(a/this.coords.p_step)*this.coords.p_step;100<b&&(b=100);100===a&&(b=100);return this.toFixed(b)},checkMinInterval:function(a,b,d){var c=this.options;if(!c.min_interval)return a;a=this.convertToValue(a);b=this.convertToValue(b);"from"===d?b-a<c.min_interval&&(a=b-c.min_interval):a-b<c.min_interval&&(a=b+c.min_interval);return this.convertToPercent(a)},checkMaxInterval:function(a,b,d){var c=this.options;if(!c.max_interval)return a;a=this.convertToValue(a);b=this.convertToValue(b);"from"===
d?b-a>c.max_interval&&(a=b-c.max_interval):a-b>c.max_interval&&(a=b+c.max_interval);return this.convertToPercent(a)},checkDiapason:function(a,b,d){a=this.convertToValue(a);var c=this.options;"number"!==typeof b&&(b=c.min);"number"!==typeof d&&(d=c.max);a<b&&(a=b);a>d&&(a=d);return this.convertToPercent(a)},toFixed:function(a){a=a.toFixed(20);return+a},_prettify:function(a){return this.options.prettify_enabled?this.options.prettify&&"function"===typeof this.options.prettify?this.options.prettify(a):
this.prettify(a):a},prettify:function(a){return a.toString().replace(/(\d{1,3}(?=(?:\d\d\d)+(?!\d)))/g,"$1"+this.options.prettify_separator)},checkEdges:function(a,b){if(!this.options.force_edges)return this.toFixed(a);0>a?a=0:a>100-b&&(a=100-b);return this.toFixed(a)},validate:function(){var a=this.options,b=this.result,d=a.values,c=d.length,e;"string"===typeof a.min&&(a.min=+a.min);"string"===typeof a.max&&(a.max=+a.max);"string"===typeof a.from&&(a.from=+a.from);"string"===typeof a.to&&(a.to=+a.to);
"string"===typeof a.step&&(a.step=+a.step);"string"===typeof a.from_min&&(a.from_min=+a.from_min);"string"===typeof a.from_max&&(a.from_max=+a.from_max);"string"===typeof a.to_min&&(a.to_min=+a.to_min);"string"===typeof a.to_max&&(a.to_max=+a.to_max);"string"===typeof a.grid_num&&(a.grid_num=+a.grid_num);a.max<a.min&&(a.max=a.min);if(c)for(a.p_values=[],a.min=0,a.max=c-1,a.step=1,a.grid_num=a.max,a.grid_snap=!0,e=0;e<c;e++){var g=+d[e];isNaN(g)?g=d[e]:(d[e]=g,g=this._prettify(g));a.p_values.push(g)}if("number"!==
typeof a.from||isNaN(a.from))a.from=a.min;if("number"!==typeof a.to||isNaN(a.to))a.to=a.max;"single"===a.type?(a.from<a.min&&(a.from=a.min),a.from>a.max&&(a.from=a.max)):(a.from<a.min&&(a.from=a.min),a.from>a.max&&(a.from=a.max),a.to<a.min&&(a.to=a.min),a.to>a.max&&(a.to=a.max),this.update_check.from&&(this.update_check.from!==a.from&&a.from>a.to&&(a.from=a.to),this.update_check.to!==a.to&&a.to<a.from&&(a.to=a.from)),a.from>a.to&&(a.from=a.to),a.to<a.from&&(a.to=a.from));if("number"!==typeof a.step||
isNaN(a.step)||!a.step||0>a.step)a.step=1;"number"===typeof a.from_min&&a.from<a.from_min&&(a.from=a.from_min);"number"===typeof a.from_max&&a.from>a.from_max&&(a.from=a.from_max);"number"===typeof a.to_min&&a.to<a.to_min&&(a.to=a.to_min);"number"===typeof a.to_max&&a.from>a.to_max&&(a.to=a.to_max);if(b){b.min!==a.min&&(b.min=a.min);b.max!==a.max&&(b.max=a.max);if(b.from<b.min||b.from>b.max)b.from=a.from;if(b.to<b.min||b.to>b.max)b.to=a.to}if("number"!==typeof a.min_interval||isNaN(a.min_interval)||
!a.min_interval||0>a.min_interval)a.min_interval=0;if("number"!==typeof a.max_interval||isNaN(a.max_interval)||!a.max_interval||0>a.max_interval)a.max_interval=0;a.min_interval&&a.min_interval>a.max-a.min&&(a.min_interval=a.max-a.min);a.max_interval&&a.max_interval>a.max-a.min&&(a.max_interval=a.max-a.min)},decorate:function(a,b){var d="",c=this.options;c.prefix&&(d+=c.prefix);d+=a;c.max_postfix&&(c.values.length&&a===c.p_values[c.max]?(d+=c.max_postfix,c.postfix&&(d+=" ")):b===c.max&&(d+=c.max_postfix,
c.postfix&&(d+=" ")));c.postfix&&(d+=c.postfix);return d},updateFrom:function(){this.result.from=this.options.from;this.result.from_percent=this.convertToPercent(this.result.from);this.result.from_pretty=this._prettify(this.result.from);this.options.values&&(this.result.from_value=this.options.values[this.result.from])},updateTo:function(){this.result.to=this.options.to;this.result.to_percent=this.convertToPercent(this.result.to);this.result.to_pretty=this._prettify(this.result.to);this.options.values&&
(this.result.to_value=this.options.values[this.result.to])},updateResult:function(){this.result.min=this.options.min;this.result.max=this.options.max;this.updateFrom();this.updateTo()},appendGrid:function(){if(this.options.grid){var a=this.options,b;var d=a.max-a.min;var c=a.grid_num,e=4,g="";this.calcGridMargin();if(a.grid_snap)if(50<d){c=50/a.step;var f=this.toFixed(a.step/.5)}else c=d/a.step,f=this.toFixed(a.step/(d/100));else f=this.toFixed(100/c);4<c&&(e=3);7<c&&(e=2);14<c&&(e=1);28<c&&(e=0);
for(d=0;d<c+1;d++){var k=e;var h=this.toFixed(f*d);100<h&&(h=100);this.coords.big[d]=h;var m=(h-f*(d-1))/(k+1);for(b=1;b<=k&&0!==h;b++){var n=this.toFixed(h-m*b);g+='<span class="irs-grid-pol small" style="left: '+n+'%"></span>'}g+='<span class="irs-grid-pol" style="left: '+h+'%"></span>';b=this.convertToValue(h);b=a.values.length?a.p_values[b]:this._prettify(b);g+='<span class="irs-grid-text js-grid-text-'+d+'" style="left: '+h+'%">'+b+"</span>"}this.coords.big_num=Math.ceil(c+1);this.$cache.cont.addClass("irs-with-grid");
this.$cache.grid.html(g);this.cacheGridLabels()}},cacheGridLabels:function(){var a,b=this.coords.big_num;for(a=0;a<b;a++){var d=this.$cache.grid.find(".js-grid-text-"+a);this.$cache.grid_labels.push(d)}this.calcGridLabels()},calcGridLabels:function(){var a;var b=[];var d=[],c=this.coords.big_num;for(a=0;a<c;a++)this.coords.big_w[a]=this.$cache.grid_labels[a].outerWidth(!1),this.coords.big_p[a]=this.toFixed(this.coords.big_w[a]/this.coords.w_rs*100),this.coords.big_x[a]=this.toFixed(this.coords.big_p[a]/
2),b[a]=this.toFixed(this.coords.big[a]-this.coords.big_x[a]),d[a]=this.toFixed(b[a]+this.coords.big_p[a]);this.options.force_edges&&(b[0]<-this.coords.grid_gap&&(b[0]=-this.coords.grid_gap,d[0]=this.toFixed(b[0]+this.coords.big_p[0]),this.coords.big_x[0]=this.coords.grid_gap),d[c-1]>100+this.coords.grid_gap&&(d[c-1]=100+this.coords.grid_gap,b[c-1]=this.toFixed(d[c-1]-this.coords.big_p[c-1]),this.coords.big_x[c-1]=this.toFixed(this.coords.big_p[c-1]-this.coords.grid_gap)));this.calcGridCollision(2,
b,d);this.calcGridCollision(4,b,d);for(a=0;a<c;a++)b=this.$cache.grid_labels[a][0],this.coords.big_x[a]!==Number.POSITIVE_INFINITY&&(b.style.marginLeft=-this.coords.big_x[a]+"%")},calcGridCollision:function(a,b,d){var c,e=this.coords.big_num;for(c=0;c<e;c+=a){var g=c+a/2;if(g>=e)break;var f=this.$cache.grid_labels[g][0];f.style.visibility=d[c]<=b[g]?"visible":"hidden"}},calcGridMargin:function(){this.options.grid_margin&&(this.coords.w_rs=this.$cache.rs.outerWidth(!1),this.coords.w_rs&&(this.coords.w_handle=
"single"===this.options.type?this.$cache.s_single.outerWidth(!1):this.$cache.s_from.outerWidth(!1),this.coords.p_handle=this.toFixed(this.coords.w_handle/this.coords.w_rs*100),this.coords.grid_gap=this.toFixed(this.coords.p_handle/2-.1),this.$cache.grid[0].style.width=this.toFixed(100-this.coords.p_handle)+"%",this.$cache.grid[0].style.left=this.coords.grid_gap+"%"))},update:function(a){this.input&&(this.is_update=!0,this.options.from=this.result.from,this.options.to=this.result.to,this.update_check.from=
this.result.from,this.update_check.to=this.result.to,this.options=f.extend(this.options,a),this.validate(),this.updateResult(a),this.toggleInput(),this.remove(),this.init(!0))},reset:function(){this.input&&(this.updateResult(),this.update())},destroy:function(){this.input&&(this.toggleInput(),this.$cache.input.prop("readonly",!1),f.data(this.input,"ionRangeSlider",null),this.remove(),this.options=this.input=null)}};f.fn.ionRangeSlider=function(a){return this.each(function(){f.data(this,"ionRangeSlider")||
f.data(this,"ionRangeSlider",new q(this,a,t++))})};(function(){for(var a=0,b=["ms","moz","webkit","o"],d=0;d<b.length&&!k.requestAnimationFrame;++d)k.requestAnimationFrame=k[b[d]+"RequestAnimationFrame"],k.cancelAnimationFrame=k[b[d]+"CancelAnimationFrame"]||k[b[d]+"CancelRequestAnimationFrame"];k.requestAnimationFrame||(k.requestAnimationFrame=function(b,d){var c=(new Date).getTime(),e=Math.max(0,16-(c-a)),f=k.setTimeout(function(){b(c+e)},e);a=c+e;return f});k.cancelAnimationFrame||(k.cancelAnimationFrame=
function(a){clearTimeout(a)})})()});

/*
 * jQuery liMarquee v 6.3.0
 *
 * Copyright 2013, Linnik Yura | LI MASS CODE | http://masscode.ru
 *
 * Last Update 27.08.2016
 */
(function ($) {
	"use strict";
	var methods = {
		
		/* === Default Settings === */
		init: function (options) {
			
			var p = {
				width:'auto',					//Sets width of the Marquee.
				height:'auto',					//Sets height of the Marquee.
				direction:'left',				//Sets the direction of the Marquee. 
												//It may take the values: "left", "right", "top", "bottom"
				scrollDelay:85,					//Sets the interval between each scroll movement in milliseconds. 
												//The default value is 85. 
												//Note that any value smaller than 60 is ignored and the value 60 is used instead, unless truespeed is specified.
				scrollAmount:6,					//Sets the amount of scrolling at each interval in pixels. 
												//The default value is 6.
				circular:true,					//Creates the effect of an infinite line.
												//It may take the values: true, false
				dragAndDrop:true,				//Enable the opportunity to drag the Marquee by the mouse. 
												//It may take the values: true, false
				hoverStop:true,					//Enable the opportunity to pause the Marquee when mouse hover. 
												//It may take the values: true, false
				scrollStop:true,				//Enable the opportunity to pause the Marquee when scroll page. 
												//It may take the values: true, false
				startShow:true,				//If it is true - the content of marquee appears immediately if the lie - gradually	
				xml:false,						//Path to XML file or false
				touchEvent:true,				//This parameter determines if the ticker responds to touch events or not
												//It may take the values: true or false
				stopOutScreen: true,			//This parameter specifies, the ticker will stop outside the screen or not
												//It may take the values: true or false
												
				create:function(){},			//Triggered when the liMarquee is created.
				
				moveStart:function(){},			//Triggered when motion starts.
				moveStop:function(){},			//Triggered when motion stops.
				
				drag:function(){},				//Triggered while the string is moved during the dragging.
				dragStart:function(){},			//Triggered when dragging starts.
				dragStop:function(){},			//Triggered when dragging stops.
				wayEnd:function(){},			//Triggered when way ended.
				removeContentFadeDuration:300	//The duration of fading when removing the content of marquee
				
			};
			
			if (options) {
				$.extend(p, options);
			}
			
			
			
			
			return this.each(function () {
				var mEl = $(this).addClass('mWrap');
				
				var mElIndex = $('*').index($(this));
				mEl.data().mElIndex = mElIndex;
				
				/*== Extend Standart jQuery Method .position() ==*/
				var pos = function(el,parent){
					var oldObj = el.position();
					var wrapper = parent || $(document);

					var rightVal = (wrapper.outerWidth() - (el.position().left + el.outerWidth()));
					var bottomVal = (wrapper.outerHeight() - (el.position().top + el.outerHeight()));
					var newObj = {right:rightVal,bottom:bottomVal};

					$.extend(newObj, oldObj);
					return newObj;	
				};
				mEl.data().style = mEl.attr('style');
				
				/*== Combine Options ==*/
				$.extend(p, mEl.data());
				$.extend(mEl.data(), p);
				
				/*== Create Moveing Container ==*/
				if(!$('.mMove',mEl).length){
					mEl.wrapInner('<div class="mMove"></div>');
				}
				var mMove = $('.mMove',mEl);
				
				/*== Set Base Style ==*/
				mEl.css({position:'relative',overflow:'hidden',maxWidth:'100%',height:mEl.data().height,width:mEl.data().width});
				
				if(mEl.data().scrollDelay <= 0 ){
					mEl.data().scrollDelay = 85;
				}
				
				/*== Creat Custom Size Function ==*/
				mEl.data().outerSizeFunc = function(el){
					if(mEl.data().direction === 'top' || mEl.data().direction === 'bottom'){
						return el.outerHeight();
					}else{
						return el.outerWidth();	
					}
				}

				mMove.data().style = mMove.attr('style');
				if((!mEl.data().updateCont)){
					mMove.css({position:'absolute',left:'auto',right:'auto',top:'auto',bottom:'auto',float:'left'});
				}
				mEl.data().mMove = mMove;

				var createMarquee = function (){
					
					
					mEl.data().clickEventFlag = true;
					
					if(mEl.data().outerSizeFunc(mMove) > 0){
						
						/*== Splitting a String into Parts ==*/
						var mItem = $('.mItem',mEl);
						mItem.each(function(){
							$(this).data().style = $(this).attr('style');
							$(this).css({display:'inline', zoom:1 });	
						});
						
						var splittingString = function(splitSide,mItem){
							mItem.css({paddingLeft:0, paddingRight:0, paddingTop:0, paddingBottom:0});
							if(mItem.length && !mEl.data().circular){
								var paddingVal = {};
								var paddingValFirst = {};
								paddingVal['padding-'+splitSide] = mEl.data().outerSizeFunc(mEl);
								paddingValFirst['padding-'+splitSide] = 0;
								mItem.css(paddingVal);
								mItem.eq(0).css(paddingValFirst);
							}
						};
						mEl.data().splittingString = splittingString;
						
						
						
						var contentString = $('<div>').addClass('cloneContent').html(mMove.html());
						
						if(mEl.data().direction === 'left' || mEl.data().direction === 'right'){
							mMove.css({whiteSpace:'nowrap'});
							mEl.data().splittingString('left',mItem);
							mEl.css({minHeight:mMove.outerHeight()});	
							contentString.css({display:'inline-block'});
							mEl.data().axis = 'hor';
						}else{
							mMove.css({whiteSpace:'normal'});
							mItem.css({display:'block'});
							mEl.data().splittingString('top',mItem);
							if(mEl.outerHeight() === 0) {alert('Set Height Parametr for Plugin liMarquee');}
							contentString.css({display:'block'});
							mEl.data().axis = 'vert';
						}
						
						/*== Unselectable for IE ==*/
						var isIE = /*@cc_on!@*/false || document.documentMode; 
						if(isIE){
							mEl.add(mEl.find('*')).attr('unselectable','on');
						}
						
						/*== Change Events ==*/
						var moveEvent = 'mousemove.'+mEl.data().mElIndex;
						var mousedownEvent = 'mousedown.'+mEl.data().mElIndex;
						var mouseupEvent = 'mouseup.'+mEl.data().mElIndex;
						var clickEvent = 'click.'+mEl.data().mElIndex;
						mEl.data({
							touchScreen:false,
							teleport:false,
							dragging:false,
							pause:false
						});
						if('ontouchstart' in window){
							moveEvent = 'touchmove.'+mEl.data().mElIndex;
							mousedownEvent = 'touchstart.'+mEl.data().mElIndex;
							mouseupEvent = 'touchend.'+mEl.data().mElIndex;
							mEl.data().touchScreen = true;
							if($(window).width() < 1000){
								mEl.data().hoverStop = false;
							}
						}	
						
						mEl.data({
							moveEvent:moveEvent,	
							mousedownEvent:mousedownEvent,
							mouseupEvent:mouseupEvent,
							clickEvent:clickEvent
						});
						
						/*== Creating Correct Amount of Contents ==*/
						var cloneContent = function(mMove){
							if(mEl.data().outerSizeFunc(mMove) !== 0){
								if(mEl.data().outerSizeFunc(mMove) < mEl.data().outerSizeFunc(mEl) && mEl.data().circular){
									contentString.clone().appendTo(mMove);
									mEl.data().cloneContent(mMove);				
								}
							}else{
								console.log('The string is empty or contains invalid style');
							}
						};
						mEl.data().cloneContent = cloneContent;
						mEl.data().cloneContent(mMove);
						
						/*== This Function Creates Motion Animation Line ==*/
						var anim = function(sPos, ePos){
							if(!mEl.data().pause){
								
								if(sPos === undefined) {sPos = mEl.data().startPos;}
								if(ePos === undefined) {ePos = mEl.data().endPos;}
									if(ePos !== 0 && ePos !== -0){
									
									/*Calculate the Time for Animation to the formula (t = s/v)*/
									var way = (ePos - sPos);
									if(way < 0) {
										way = way * -1;
									}
									//var duration = (way/mEl.data().speed) * 1000;
									var duration = (way * mEl.data().scrollDelay)/mEl.data().scrollAmount;
									var directTypeStart = {};
									var directTypeEnd = {};
	
									directTypeStart[mEl.data().direction] = sPos;
									directTypeEnd[mEl.data().direction] = ePos;
									
									mMove.css(directTypeStart);
									mEl.addClass('mIni');
									
									//Triggered when motion starts.
									mEl.data().stopped = false;
									if (mEl.data().moveStart !== undefined) {mEl.data().moveStart();}	

									mMove.stop(true).animate(directTypeEnd,duration,'linear',function(){
										//Triggered when motion stop.
										if (mEl.data().moveStop !== undefined) {mEl.data().moveStop();}	
										if (mEl.data().wayEnd !== undefined) {mEl.data().wayEnd();}	
										mEl.data().teleport = true;		
										anim();
									});
								}
							}
						};
						mEl.data().anim = anim;
						
						/*== Caching String and Creatin Clones of String ==*/
						var addClone = function(){
							var mMoveClone = mMove.clone().addClass('clone').css({position:'absolute', width:'100%', height:'100%',opacity:0});
							if(mEl.data().direction === 'top' || mEl.data().direction === 'bottom'){mMoveClone.css({left:0});}else{mMoveClone.css({top:0});}
							var value = {};
							var value2 = {};
							value[mEl.data().direction] = '-100%';		
							value2[mEl.data().direction] = '100%';									
							var cloneBefore = mMoveClone.clone().addClass('cloneBefore').css(value).appendTo(mMove);
							var cloneAfter = mMoveClone.clone().addClass('cloneAfter').css(value2).appendTo(mMove);
							if(mEl.data().circular){
								cloneBefore.add(cloneAfter).css({opacity:1});
							}
						};
						if(mEl.data().circular){
							addClone();
						}
						
						//Triggered when the liMarquee is created.
						if (mEl.data().create !== undefined) {mEl.data().create();}
		
						/*== This Function Determines the Coordinate of the Moving Line ==*/
						var nowPos = function(){
							return pos(mMove,mEl)[mEl.data().direction];
						};
						mEl.data().nowPos = nowPos;
						
						/*== This Function Determines the Coordinate of the Touch Event ==*/
						var correctEvent = function(e){
							var eventType = e;
							if(mEl.data().touchScreen){
								if (e.originalEvent.targetTouches.length === 1) {
									eventType = e.originalEvent.targetTouches[0];
								}
							}
	
							/*== Extend Standart jQuery Object of Event Coordinates ==*/
							var newParam = {
								left: eventType.pageX,
								top: eventType.pageY,
								right: ($(window).width() - eventType.pageX),
								bottom: ($(window).height() - eventType.pageY)
							};
							$.extend(eventType, newParam);
							return eventType;
						};
						
						if(mEl.data().hoverStop){
							mEl.on('mouseenter.'+mEl.data().mElIndex,function(){
								mEl.off('mouseleave.'+mEl.data().mElIndex);
								if(mEl.data().dragAndDrop){
									$('html').addClass('grab');
								}
								if(!mEl.data().stopped){
									mMove.stop(true);
									mEl.data().stopped = true;
									//Triggered when motion stop.
									if (mEl.data().moveStop !== undefined) {mEl.data().moveStop();}	
								}
								mEl.on('mouseleave.'+mEl.data().mElIndex,function(){
									$(document).off(moveEvent);
									$('html').removeClass('grab');
									$('html').removeClass('grabbing');
									anim(mEl.data().nowPos());							
								});
							});
						}
						if(!mEl.data().touchScreen && mEl.data().dragAndDrop || mEl.data().touchScreen && mEl.data().touchEvent){
							mEl.on(mousedownEvent, function (e) {
								$(document).off(moveEvent);
								$(document).off(mouseupEvent);
								mEl.off('mouseleave.'+mEl.data().mElIndex);
								$('html').addClass('grabbing');
								
								if(!mEl.data().stopped){
									mMove.stop(true);
									mEl.data().stopped = true;
									//Triggered when motion stop.
									if (mEl.data().moveStop !== undefined) {mEl.data().moveStop();}	
								}
			
								/*== Start Drag and Drop of String ==*/
								var startMouseCoord = correctEvent(e)[mEl.data().direction];
								var startMouseY = correctEvent(e)['top'];
								var startMouseX = correctEvent(e)['left'];
								var vertSum = 0;
								var horSum = 0;
								var dir = 1;
								$(document).on(moveEvent,function(e){
									mEl.data().clickEventFlag = false;
									mEl.off('mouseleave.'+mEl.data().mElIndex);
									$('html').addClass('grabbing');
									if(!mEl.data().dragging){
										//Triggered when dragging starts.
										if (mEl.data().dragStart !== undefined) {mEl.data().dragStart();}
										mEl.data().dragging = true;
									}
									var nowPosVal = mEl.data().nowPos();
									if(!mEl.data().stopped){
										mMove.stop(true);
										mEl.data().stopped = true;
									}
							
									var newMouseCoord = correctEvent(e)[mEl.data().direction];

									var dragTrue = function(){
									
										if(newMouseCoord > startMouseCoord) {dir = 1;}
										if(newMouseCoord < startMouseCoord) {dir = -1;}

										var shiftVal =  (startMouseCoord - newMouseCoord);
										startMouseCoord = newMouseCoord;
										
										var value = {};
										value[mEl.data().direction] = '-='+shiftVal;	

										/*== Calculate Drag Position ==*/
										if(mEl.data().circular){
											if(nowPosVal <= mEl.data().outerSizeFunc(mMove) && !mEl.data().teleport){
												mEl.data().teleport = true;
											}
											if(nowPosVal <= (mEl.data().outerSizeFunc(mEl) - mEl.data().outerSizeFunc(mMove)) && dir < 0 && mEl.data().teleport){
												if (mEl.data().wayEnd !== undefined) {mEl.data().wayEnd();}	
												value[mEl.data().direction] = '+='+mEl.data().outerSizeFunc(mMove);										
											}
											if(nowPosVal >= 0 && dir > 0 && mEl.data().teleport){
												if (mEl.data().wayEnd !== undefined) {mEl.data().wayEnd();}	
												value[mEl.data().direction] = '-='+mEl.data().outerSizeFunc(mMove);										
											}
										}else{	
	
											if(nowPosVal <= -mEl.data().outerSizeFunc(mMove) && dir < 0){
												if (mEl.data().wayEnd !== undefined) {mEl.data().wayEnd();}	
												value[mEl.data().direction] = '+='+(mEl.data().outerSizeFunc(mMove)+mEl.data().outerSizeFunc(mEl));		
											}
											if(nowPosVal >= mEl.data().outerSizeFunc(mEl) && dir > 0){
												if (mEl.data().wayEnd !== undefined) {mEl.data().wayEnd();}	
												value[mEl.data().direction] = '-='+(mEl.data().outerSizeFunc(mMove)+mEl.data().outerSizeFunc(mEl));	
											}	
	
										}
										//Triggered while the string is dragging.
										if (mEl.data().drag !== undefined) {mEl.data().drag();}	
	
										mMove.css(value);
										if(mEl.data().touchEvent){
											return false;
										}
									}
									
									//Detecting swipe direction
									if(mEl.data().axis == 'hor'){
										var newMouseY = correctEvent(e)['top'];	
										var newMouseX = correctEvent(e)['left'];	
										var vertDif = Math.abs(newMouseY - startMouseY);
										var horDif = Math.abs(newMouseX - startMouseX);
										vertSum += vertDif;
										horSum += horDif;
										if(vertSum > horSum){
											$(document).trigger(mouseupEvent);
										}else{
											dragTrue();
										}
									}else{
										dragTrue();	
									}

								});
								
								$(document).on(mouseupEvent, function (e) {
									if(mEl.data().dragging){
										//Triggered when dragging starts.
										if (mEl.data().dragStop !== undefined) {mEl.data().dragStop();}	
										mEl.data().dragging = false;
									}
									if($(e.target).is(mEl) || $(e.target).closest(mEl).length){
										$(document).off(moveEvent);	
										$('html').removeClass('grabbing');	
										if(mEl.data().hoverStop){
											mEl.trigger('mouseenter.'+mEl.data().mElIndex);
										}else{
											anim(mEl.data().nowPos());	
										}
									}else{
										$(document).off(moveEvent);
										anim(mEl.data().nowPos());	
										$('html').removeClass('grab');
										$('html').removeClass('grabbing');	
									}
									$(document).off(mouseupEvent);
									setTimeout(function(){
										mEl.data().clickEventFlag = true;	
									},300);
								});
								if(!mEl.data().touchScreen/* && !mEl.data().touchEvent*/){
									return false;
								}
							});
						}
	
						/*== Set the Starting Position of the String ==*/				
						var getPosition = function(mEl){
							var mMove = mEl.data().mMove;
							var startPos = mEl.data().outerSizeFunc(mEl);
							var endPos = -mEl.data().outerSizeFunc(mMove);
							mEl.data().startPos = startPos;
							mEl.data().endPos = endPos;
							if(mEl.data().circular){
								endPos = - (mEl.data().outerSizeFunc(mMove) + (mEl.data().outerSizeFunc(mMove) - mEl.data().outerSizeFunc(mEl)));	
								mEl.data().endPos = endPos;
								var circularPos = mEl.data().startShow ? mEl.data().outerSizeFunc(mMove) : (mEl.data().outerSizeFunc(mEl) + mEl.data().outerSizeFunc(mMove));
								anim(circularPos);	
							}else{
								var tempStartPos = mEl.data().startShow ? 0 : startPos;
								anim(tempStartPos);	
							}
						};
						mEl.data().getPosition = getPosition;
						
						var setPosition = function(mEl){	
							var mMove = mEl.data().mMove;
							var startPos = mEl.data().outerSizeFunc(mEl);
							var endPos = -mEl.data().outerSizeFunc(mMove);
							mEl.data().startPos = startPos;
							mEl.data().endPos = endPos;
							
							if(mEl.data().circular){
								endPos = - (mEl.data().outerSizeFunc(mMove) + (mEl.data().outerSizeFunc(mMove) - mEl.data().outerSizeFunc(mEl)));	
								mEl.data().endPos = endPos;
							}
						};
						
						mEl.data().setPosition = setPosition;
						if(!mEl.data().updateCont){
							mEl.data().getPosition(mEl);
						}

						/*== This function stops a marquee into an inactive browser tab ==*/
						var visibilityChanged = function(){
							if(document.hidden){
								if(!mEl.data().stopped){
									mMove.stop(true);
									mEl.data().stopped = true;
									//Triggered when motion stop.
									if (mEl.data().moveStop !== undefined) {mEl.data().moveStop();}	
								}
							}else{
								anim(mEl.data().nowPos());
							}
						}
						$(document).on('visibilitychange',function(){
							visibilityChanged();	
						})

						/*== When you change size of the screen - recalculate animation coordinates of  marquee. ==*/
						var resizeId = function(){};
						$(window).on('resize.'+mEl.data().mElIndex,function(){
							clearTimeout(resizeId);
							resizeId = setTimeout(function(){
								mEl.liMarquee('resetPosition');
							},300);
							
						});
						
						/*== If marquee outside the screen, it stops and does not use CPU ==*/
						var scrollPageId = function(){};
						var detectStringPos = function(){
							if(mEl.data().stopOutScreen){
								if((mEl.offset().top + mEl.outerHeight()) < $(window).scrollTop() || mEl.offset().top > ($(window).scrollTop() + $(window).height())){
									if(!mEl.data().stopped){
										mMove.stop(true);
										mEl.data().stopped = true;
										//Triggered when motion stop.
										if (mEl.data().moveStop !== undefined) {mEl.data().moveStop();}	
									}
								}else{
									anim(mEl.data().nowPos());	
								}
							}else{
								anim(mEl.data().nowPos());	
							}
						};
						$(window).on('scroll.'+mEl.data().mElIndex,function(){
							if(mEl.data().scrollStop && !mEl.data().stopped){
								mMove.stop(true);
								mEl.data().stopped = true;
								//Triggered when motion stop.
								if (mEl.data().moveStop !== undefined) {mEl.data().moveStop();}	
							}
							clearTimeout(scrollPageId);
							scrollPageId = setTimeout(function(){
								detectStringPos();
							},100);
						});
						mEl.find('a').on('click',function(){
							if(!mEl.data().clickEventFlag){
								return false;	
							}	
						});
						detectStringPos();
						
					}else{
						mMove.text('marquee "'+mEl.attr('class')+'" elements is hidden or missing');
						createMarquee();
						mEl.liMarquee('stop');
						mEl.liMarquee('removeContent');
					}
				};
				
				/*== Loading XML Content ==*/
				if (mEl.data().xml){
					$.ajax({
						url: mEl.data().xml,
						dataType: "xml",
						success: function (xml) {
							var xmlItem = $(xml).find('item');
							var xmlItemLength = xmlItem.length;
							for(var i = 0; i < xmlItemLength; i++){
								var xmlItemActive = xmlItem.eq(i);
								var xmlItemContent = xmlItemActive.find('title').text();
								var xmlItemLink = xmlItemActive.find('link').text();
								
								if(xmlItemActive.find('link').length){
									$('<div class="mItem"><a href="'+xmlItemLink+'">'+xmlItemContent+'</a></div>').appendTo(mMove);									
								}else{
									$('<div class="mItem">').text(xmlItemContent).appendTo(mMove);	
								}
							}
							createMarquee();
						}
					});
				}else{
					createMarquee();	
				}
			});
		},
		/*== Get Content ==*/
		getContent: function () {
			var mMove = $(this).data().mMove;
			var content;
			if(!mMove.is(':empty')){
				var moveContent = mMove.html();
				var tempEl = $('<div>').html(moveContent);		
				tempEl.find('.clone').remove();
				tempEl.find('.cloneContent').remove();
				content = $.trim(tempEl.html());
			}else{
				content = false;
			}
			return content;
		},
		
		/*== Add Content ==*/
		addContent: function (per) {
			return this.each(function () {
				var mEl = $(this);
				var mMove = mEl.data().mMove;
				var addingFunc = function(){
					if(!mEl.data().removing){
						/*== Cashing Vars ==*/
						var newHtml = '<div class="mItem">'+per+'</div>';
						
						/*== Get old Content ==*/
						var oldCont = mEl.liMarquee('getContent');
						var newCont = oldCont;

						//Correct old Content
						if(!mMove.find('.mItem').length && oldCont){
							oldCont = '<div class="mItem">'+oldCont+'</div>';
						}
						
						//Create Combine Content
						if(per){
							if(mEl.data().direction === 'left' || mEl.data().direction === 'top') {newCont = oldCont ? oldCont+newHtml : newHtml;}
							if(mEl.data().direction === 'right' || mEl.data().direction === 'bottom') {newCont = oldCont ? newHtml+oldCont : newHtml;}
						}
			
						/*== Remove old Content ==*/
						mEl.liMarquee('removeContent');
						
						var addNewContFunc = function(){
							if(!mEl.data().removing){
							
								//Add New Content
								mMove.html(newCont);
								
							
								//Update Initialization
								if(oldCont){
									mEl.data().updateCont = true;		
								}
					
								mEl.liMarquee(mEl.data());
								
								//Set End Position and Start Animation
								if(mEl.data().updateCont){
									mEl.data().setPosition(mEl);
									mEl.data().anim(mEl.data().nowPos());
								}
							}else{
								setTimeout(function(){
									addNewContFunc();
								},mEl.data().removeContentFadeDuration);
							}
						};
						addNewContFunc();
					
					}else{
						setTimeout(function(){
							addingFunc();
						},mEl.data().removeContentFadeDuration);
					}
				
				};
				addingFunc();
			});
		},
		
		/*== Remove Content ==*/
		removeContent: function () {
			return this.each(function () {
				$(this).data().removing = true;
				var mEl = $(this);
				var mMove = mEl.data().mMove;
				
				mMove.children().animate({opacity:0},mEl.data().removeContentFadeDuration);
				
				setTimeout(function(){
					mEl.data().updateCont = true;
					if(!mEl.data().stopped){
						mMove.stop(true);
						mEl.data().stopped = true;
					}
					mEl.off('mouseenter.'+mEl.data().mElIndex);
					mEl.off('mouseleave.'+mEl.data().mElIndex);
					mEl.off($(this).data().mousedownEvent);
					$(window).off('resize.'+mEl.data().mElIndex);
					$(window).off('scroll.'+mEl.data().mElIndex);
					$(document).off(mEl.data().moveEvent);
					$(document).off(mEl.data().mouseupEvent);	
					if(!mEl.data().stopped){
						mMove.stop(true);
						mEl.data().stopped = true;
					}
					mMove.empty();
					mEl.data().removing = false;
				},mEl.data().removeContentFadeDuration);
			});
		},
		changeOptions: function (options) {
			return this.each(function () {
				var mEl = $(this);
				$.extend(mEl.data(), options);
				mEl.liMarquee('destroy');
				mEl.data().updateCont = false;
				mEl.liMarquee(mEl.data());
			});
		},
		
		/* === Function of Destroy Marquee === */
		destroy: function () {
			var mEl = $(this);
			var mMove = mEl.data().mMove;
			mEl.removeAttr('style').attr('style',mEl.data().style);
			if(!mEl.data().stopped){
				mMove.stop(true);
				mEl.data().stopped = true;
			}
			mMove.removeAttr('style').attr('style',mMove.data().style).removeData();
			$('.mItem',mEl).each(function(){
				$(this).removeAttr('style').attr('style',$(this).data().style).removeData();
			});
			mEl.off('mouseenter.'+mEl.data().mElIndex);
			mEl.off('mouseleave.'+mEl.data().mElIndex);
			mEl.off(mEl.data().mousedownEvent);
			$(window).off('resize.'+mEl.data().mElIndex);
			$(window).off('scroll.'+mEl.data().mElIndex);
			if(mEl.data().moveEvent){
				$(document).off(mEl.data().moveEvent);
			}
			if(mEl.data().mouseupEvent){
				$(document).off(mEl.data().mouseupEvent);	
			}
			$('.clone',mEl).remove();
			$('.cloneContent',mEl).remove();
			var mMoveContent = mMove.html();
			mMove.remove();
			mEl.html(mMoveContent).removeClass('mIni').css({opacity:1});
		},
		
		/* === Function of Pause Marquee === */
		stop: function(){
			return this.each(function () {
				var mEl = $(this);
				if(mEl.is('.mIni')){
					var mMove = mEl.data().mMove;
					if(!mEl.data().pause){
						mEl.data().pause = true;
						if(!mEl.data().stopped){
							mMove.stop(true);
							mEl.data().stopped = true;
							//Triggered when motion stop.
							if (mEl.data().moveStop !== undefined) {mEl.data().moveStop();}
						}
					}
				}
			})
			
		}, 
		
		/* === Function of Play Marquee === */
		start: function(delayNew){
			return this.each(function () {
				var mEl = $(this);
				if(mEl.data().pause){
					var delayVal = delayNew? delayNew : 0;
					setTimeout(function(){
						mEl.data().pause = false;
						mEl.data().setPosition(mEl);
						mEl.data().anim(mEl.data().nowPos());
						$(window).trigger('scroll.'+mEl.data().mElIndex);
					},delayVal);
				}
			});
		},
		
		/* === Reset Position === */
		resetPosition: function () {
			return this.each(function () {
				var mEl = $(this);
					if(mEl.is(':visible')){
					var mMove = mEl.data().mMove;
					if(!mEl.data().stopped){
						mMove.stop(true);
						mEl.data().stopped = true;
					}
					
					if(mEl.data().direction === 'left' || mEl.data().direction === 'right'){
						mEl.css({minHeight:mMove.outerHeight()});	
					}
					mEl.data().setPosition(mEl);
					mEl.data().anim(mEl.data().nowPos());
					$(window).trigger('scroll.'+mEl.data().mElIndex);
				}
			});
		}
	};
	$.fn.liMarquee = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error("Метод " + method + " в jQuery.liMarquee doesn't exist");
		}
	};
})(jQuery);




/*!
 * Validator v0.11.9 for Bootstrap 3, by @1000hz
 * Copyright 2017 Cina Saffary
 * Licensed under http://opensource.org/licenses/MIT
 *
 * https://github.com/1000hz/bootstrap-validator
 */

+function(a){"use strict";function b(b){return b.is('[type="checkbox"]')?b.prop("checked"):b.is('[type="radio"]')?!!a('[name="'+b.attr("name")+'"]:checked').length:b.is("select[multiple]")?(b.val()||[]).length:b.val()}function c(b){return this.each(function(){var c=a(this),e=a.extend({},d.DEFAULTS,c.data(),"object"==typeof b&&b),f=c.data("bs.validator");(f||"destroy"!=b)&&(f||c.data("bs.validator",f=new d(this,e)),"string"==typeof b&&f[b]())})}var d=function(c,e){this.options=e,this.validators=a.extend({},d.VALIDATORS,e.custom),this.$element=a(c),this.$btn=a('button[type="submit"], input[type="submit"]').filter('[form="'+this.$element.attr("id")+'"]').add(this.$element.find('input[type="submit"], button[type="submit"]')),this.update(),this.$element.on("input.bs.validator change.bs.validator focusout.bs.validator",a.proxy(this.onInput,this)),this.$element.on("submit.bs.validator",a.proxy(this.onSubmit,this)),this.$element.on("reset.bs.validator",a.proxy(this.reset,this)),this.$element.find("[data-match]").each(function(){var c=a(this),d=c.attr("data-match");a(d).on("input.bs.validator",function(){b(c)&&c.trigger("input.bs.validator")})}),this.$inputs.filter(function(){return b(a(this))&&!a(this).closest(".has-error").length}).trigger("focusout"),this.$element.attr("novalidate",!0)};d.VERSION="0.11.9",d.INPUT_SELECTOR=':input:not([type="hidden"], [type="submit"], [type="reset"], button)',d.FOCUS_OFFSET=120,d.DEFAULTS={delay:500,html:!1,disable:!0,focus:!0,custom:{},errors:{match:"Does not match",minlength:"Not long enough"},feedback:{success:"glyphicon-ok",error:"glyphicon-remove"}},d.VALIDATORS={"native":function(a){var b=a[0];return b.checkValidity?!b.checkValidity()&&!b.validity.valid&&(b.validationMessage||"error!"):void 0},match:function(b){var c=b.attr("data-match");return b.val()!==a(c).val()&&d.DEFAULTS.errors.match},minlength:function(a){var b=a.attr("data-minlength");return a.val().length<b&&d.DEFAULTS.errors.minlength}},d.prototype.update=function(){var b=this;return this.$inputs=this.$element.find(d.INPUT_SELECTOR).add(this.$element.find('[data-validate="true"]')).not(this.$element.find('[data-validate="false"]').each(function(){b.clearErrors(a(this))})),this.toggleSubmit(),this},d.prototype.onInput=function(b){var c=this,d=a(b.target),e="focusout"!==b.type;this.$inputs.is(d)&&this.validateInput(d,e).done(function(){c.toggleSubmit()})},d.prototype.validateInput=function(c,d){var e=(b(c),c.data("bs.validator.errors"));c.is('[type="radio"]')&&(c=this.$element.find('input[name="'+c.attr("name")+'"]'));var f=a.Event("validate.bs.validator",{relatedTarget:c[0]});if(this.$element.trigger(f),!f.isDefaultPrevented()){var g=this;return this.runValidators(c).done(function(b){c.data("bs.validator.errors",b),b.length?d?g.defer(c,g.showErrors):g.showErrors(c):g.clearErrors(c),e&&b.toString()===e.toString()||(f=b.length?a.Event("invalid.bs.validator",{relatedTarget:c[0],detail:b}):a.Event("valid.bs.validator",{relatedTarget:c[0],detail:e}),g.$element.trigger(f)),g.toggleSubmit(),g.$element.trigger(a.Event("validated.bs.validator",{relatedTarget:c[0]}))})}},d.prototype.runValidators=function(c){function d(a){return c.attr("data-"+a+"-error")}function e(){var a=c[0].validity;return a.typeMismatch?c.attr("data-type-error"):a.patternMismatch?c.attr("data-pattern-error"):a.stepMismatch?c.attr("data-step-error"):a.rangeOverflow?c.attr("data-max-error"):a.rangeUnderflow?c.attr("data-min-error"):a.valueMissing?c.attr("data-required-error"):null}function f(){return c.attr("data-error")}function g(a){return d(a)||e()||f()}var h=[],i=a.Deferred();return c.data("bs.validator.deferred")&&c.data("bs.validator.deferred").reject(),c.data("bs.validator.deferred",i),a.each(this.validators,a.proxy(function(a,d){var e=null;!b(c)&&!c.attr("required")||void 0===c.attr("data-"+a)&&"native"!=a||!(e=d.call(this,c))||(e=g(a)||e,!~h.indexOf(e)&&h.push(e))},this)),!h.length&&b(c)&&c.attr("data-remote")?this.defer(c,function(){var d={};d[c.attr("name")]=b(c),a.get(c.attr("data-remote"),d).fail(function(a,b,c){h.push(g("remote")||c)}).always(function(){i.resolve(h)})}):i.resolve(h),i.promise()},d.prototype.validate=function(){var b=this;return a.when(this.$inputs.map(function(){return b.validateInput(a(this),!1)})).then(function(){b.toggleSubmit(),b.focusError()}),this},d.prototype.focusError=function(){if(this.options.focus){var b=this.$element.find(".has-error :input:first");0!==b.length&&(a("html, body").animate({scrollTop:b.offset().top-d.FOCUS_OFFSET},250),b.focus())}},d.prototype.showErrors=function(b){var c=this.options.html?"html":"text",d=b.data("bs.validator.errors"),e=b.closest(".form-group"),f=e.find(".help-block.with-errors"),g=e.find(".form-control-feedback");d.length&&(d=a("<ul/>").addClass("list-unstyled").append(a.map(d,function(b){return a("<li/>")[c](b)})),void 0===f.data("bs.validator.originalContent")&&f.data("bs.validator.originalContent",f.html()),f.empty().append(d),e.addClass("has-error has-danger"),e.hasClass("has-feedback")&&g.removeClass(this.options.feedback.success)&&g.addClass(this.options.feedback.error)&&e.removeClass("has-success"))},d.prototype.clearErrors=function(a){var c=a.closest(".form-group"),d=c.find(".help-block.with-errors"),e=c.find(".form-control-feedback");d.html(d.data("bs.validator.originalContent")),c.removeClass("has-error has-danger has-success"),c.hasClass("has-feedback")&&e.removeClass(this.options.feedback.error)&&e.removeClass(this.options.feedback.success)&&b(a)&&e.addClass(this.options.feedback.success)&&c.addClass("has-success")},d.prototype.hasErrors=function(){function b(){return!!(a(this).data("bs.validator.errors")||[]).length}return!!this.$inputs.filter(b).length},d.prototype.isIncomplete=function(){function c(){var c=b(a(this));return!("string"==typeof c?a.trim(c):c)}return!!this.$inputs.filter("[required]").filter(c).length},d.prototype.onSubmit=function(a){this.validate(),(this.isIncomplete()||this.hasErrors())&&a.preventDefault()},d.prototype.toggleSubmit=function(){this.options.disable&&this.$btn.toggleClass("disabled",this.isIncomplete()||this.hasErrors())},d.prototype.defer=function(b,c){return c=a.proxy(c,this,b),this.options.delay?(window.clearTimeout(b.data("bs.validator.timeout")),void b.data("bs.validator.timeout",window.setTimeout(c,this.options.delay))):c()},d.prototype.reset=function(){return this.$element.find(".form-control-feedback").removeClass(this.options.feedback.error).removeClass(this.options.feedback.success),this.$inputs.removeData(["bs.validator.errors","bs.validator.deferred"]).each(function(){var b=a(this),c=b.data("bs.validator.timeout");window.clearTimeout(c)&&b.removeData("bs.validator.timeout")}),this.$element.find(".help-block.with-errors").each(function(){var b=a(this),c=b.data("bs.validator.originalContent");b.removeData("bs.validator.originalContent").html(c)}),this.$btn.removeClass("disabled"),this.$element.find(".has-error, .has-danger, .has-success").removeClass("has-error has-danger has-success"),this},d.prototype.destroy=function(){return this.reset(),this.$element.removeAttr("novalidate").removeData("bs.validator").off(".bs.validator"),this.$inputs.off(".bs.validator"),this.options=null,this.validators=null,this.$element=null,this.$btn=null,this.$inputs=null,this};var e=a.fn.validator;a.fn.validator=c,a.fn.validator.Constructor=d,a.fn.validator.noConflict=function(){return a.fn.validator=e,this},a(window).on("load",function(){a('form[data-toggle="validator"]').each(function(){var b=a(this);c.call(b,b.data())})})}(jQuery);
