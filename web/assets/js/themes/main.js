/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "./";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function($, jQuery) {Dropzone.autoDiscover = false; //To fixed "Dropzone already attached".
/* Global vars --------------------------------------------------------------------------- */
var scrollticker; // Scroll Timer | don't need to set this in every scroll
var lightbox_attr = false;
var frmValidator = $('[data-toggle="validator"]').validator();
$.fn.validator.Constructor.FOCUS_OFFSET = '150';
var myDropzone;

/* API Config --------------------------------------------------------------------------- */
var config = {
    //'apiUrl': '/api/public',
    'apiUrl': '/api',
    'route': '/send'

    /* AJAX Submit Form ------------------------------------------------------------------------ */
};jQuery(document).ready(function ($) {

    /*frmValidator.on('submit', function (event) {
        if (event.isDefaultPrevented()) {
            // handle the invalid form...
            console.log('error');
        } else {
            event.preventDefault();
            var form = $(this),
                formData = false,
                url = config.apiUrl + config.route;
            if (window.FormData) {
                formData = new FormData(form[0]);
            }
            if ($('#careerDetail__form').length) {
                var files = myDropzone.files;
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    //console.log(file);
                    // Add the file to the request.
                    formData.append('file', file, file.name);
                }
            }

            $('.fc-loading').show();
            $.ajax({
                url: url,
                data: formData ? formData : form.serialize(),
                cache: false,
                dataType: 'json',
                contentType: false,
                processData: false,
                type: 'POST',
                success: function success(data, textStatus, jqXHR) {
                    window.location.href = '/thank-you';
                    // $('html, body').animate({
                    //     scrollTop: $(form.parents('section')).offset().top
                    // }, 1000);
                    // $('.fc-loading').hide();
                    // $('.alert-success').removeClass('hidden');
                    // $('.alert-success').addClass('in');
                    // setTimeout(function() {
                    //     $('.alert-success').removeClass('in');
                    //     $('.alert-success').addClass('hidden');
                    // }, 5000);
                    // form[0].reset();
                    // if ($('#careerDetail__form').length) {
                    //     myDropzone.removeAllFiles();
                    // }
                },
                error: function error(request, status, _error) {
                    $('html, body').animate({
                        scrollTop: $(form.parents('section')).offset().top
                    }, 1000);
                    $('.fc-loading').hide();
                    $('.alert-danger').removeClass('hidden');
                    $('.alert-danger').addClass('in');
                    setTimeout(function () {
                        $('.alert-danger').removeClass('in');
                        $('.alert-danger').addClass('hidden');
                    }, 5000);
                }
            });
        }
    });*/
});
/* Menu Slide --------------------------------------------------------------------------- */
function headerSlide() {
    var button = $('.header__tools__menu'),
        menu = $('.slideMenu'),
        main = $('.main, .footer, .bottom'),
        sub = menu.find('.sub');
    body = $('body');
    link = '';
    menu.find('.hasChild a').after('<span class="showChild"></span>');
    link = menu.find('.showChild');
    button.bind('click', function (e) {
        var el = $(this);
        el.toggleClass('active');
        menu.toggleClass('show');
        body.toggleClass('show-slideMenu');
        e.preventDefault();
    });
    main.bind('click', function () {
        if (button.hasClass('active')) {
            button.removeClass('active');
            menu.removeClass('show');
            body.removeClass('show-slideMenu');
        }
    });
    link.bind("click", function (e) {
        var el = $(this);
        sub.slideUp(200).parent('li').removeClass('active');
        if (el.next('.sub').is(":hidden")) {
            el.next('.sub').slideDown(200).parent('li').addClass('active');
        }
    });
}

  /* Home Page ---------------------------------------------------------------------------- */
  function slideMain() {
    $('.slideMain').slick({
      dots: false,
      infinite: true,
      speed: 500,
      fade: true,
      cssEase: 'linear',
      autoplay: true,
      autoplaySpeed: 6000,
      arrows: true,
      adaptiveHeight: true
    });

    $('.section-slide, .section-strengths, .section-business, .boxBusiness__item, .section-description, .section-subheader--down, .section-subheader--up, .box-aboutOurStory').waypoint({
      offset: '100%',
      triggerOnce: true,
      handler: function handler() {

        var el = $(this.element).length ? $(this.element) : $(this);

        el.addClass('active');

        if (typeof this.destroy !== 'undefined' && $.isFunction(this.destroy)) {
          this.destroy();
        }
      }
    });

    if ($('.navigation').length) {
      var navLink = $('.navigation ul a'),
        html = $('.navigation__center h2');
      navLink.hover(function() {
        var el = $(this),
          navText = el.data('text');
        html.html(navText);
      });
      $('.section-callback__readmore').mouseenter(function() {
        anime({
          targets: '.navigation__bg img',
          rotate: function rotate() {
            return anime.random(-360 * 4, 360 * 3);
          },
          duration: function duration() {
            return anime.random(2200, 2800);
          },
          delay: function delay() {
            return anime.random(0, 100);
          },
          direction: 'alternate',
          easing: 'linear',
          loop: true
        });
      }).mouseleave(function() {
        anime.remove('.navigation__bg img');
      });
    }
  }

  function slidePartner() {
    if ($('.boxPartnerWapper').length) {
      $('.boxPartnerWapper').liMarquee();
    }
  }

slidePartner();

/* Lightbox | Magnific Popup ------------------------------------------------------------ */

function lightbox() {

    var galleries = [];

    // init -----------

    var init = function init() {

        if (lightbox_attr) {
            compatibility();
            set_type();
            constructor();
        }
    };

    // backward compatibility for prettyPhoto ----------

    var compatibility = function compatibility() {

        $('a[rel^="prettyphoto"], a.prettyphoto, a[rel^="prettyphoto"]').each(function () {

            var el = $(this);
            var rel = el.attr('rel');

            if (rel) {
                rel = rel.replace('prettyphoto', 'lightbox');
            } else {
                rel = 'lightbox';
            }

            // Visual Composer prettyPhoto
            var data_rel = el.attr('data-rel');
            if (data_rel) {
                rel = data_rel.replace('prettyPhoto', 'lightbox');
                el.removeAttr('data-rel');
            }

            el.removeClass('prettyphoto').attr('rel', rel);
        });
    };

    // check if item is a part of gallery ----------

    var is_gallery = function is_gallery(rel) {

        if (!rel) {
            return false;
        }

        var reg_exp = /\[(?:.*)\]/;
        var gallery = reg_exp.exec(rel);

        if (gallery) {
            gallery = gallery[0];
            gallery = gallery.replace('[', '').replace(']', '');
            return gallery;
        }

        return false;
    };

    // set array of names of galleries ----------

    var set_gallery = function set_gallery(gallery) {

        if (galleries.indexOf(gallery) == -1) {
            galleries.push(gallery);
            return true;
        }

        return false;
    };

    // get file type by link ----------

    var get_type = function get_type(src) {

        if (src.match(/youtube\.com\/watch/i) || src.match(/youtu\.be/i)) {
            return 'iframe';
        } else if (src.match(/vimeo\.com/i)) {
            return 'iframe';
        } else if (src.match(/\biframe=true\b/i)) {
            return 'ajax';
        } else if (src.match(/\bajax=true\b/i)) {
            return 'ajax';
        } else if (src.substr(0, 1) == '#') {
            return 'inline';
        } else {
            return 'image';
        }
    };

    // set file type ----------

    var set_type = function set_type() {

        $('a[rel^="lightbox"]').each(function () {

            var el = $(this);
            var href = el.attr('href');
            var rel = el.attr('rel');

            if (href) {

                // gallery
                var gallery = is_gallery(rel);
                if (gallery) {
                    el.attr('data-type', 'gallery');
                    set_gallery(gallery);
                    return true;
                }

                el.attr('data-type', get_type(href));
            }
        });

        //				console.log( galleries );
    };

    // constructor ----------

    var constructor = function constructor() {

        var attr = {
            autoFocusLast: false,
            removalDelay: 160,
            image: {
                titleSrc: function titleSrc(item) {
                    return lightbox_attr.title ? item.img.attr('alt') : false;
                }
            }
        };

        // image

        $('a[rel^="lightbox"][data-type="image"]').magnificPopup({
            autoFocusLast: attr.autoFocusLast,
            removalDelay: attr.removalDelay,
            type: 'image',
            image: attr.image
        });

        // iframe | video

        $('a[rel^="lightbox"][data-type="iframe"]').magnificPopup({
            autoFocusLast: attr.autoFocusLast,
            removalDelay: attr.removalDelay,
            type: 'iframe'
        });

        // inline

        $('a[rel^="lightbox"][data-type="inline"]').magnificPopup({
            autoFocusLast: attr.autoFocusLast,
            type: 'inline',
            midClick: true,
            callbacks: {
                open: function open() {
                    $('.mfp-content').children().addClass('mfp-inline');
                },
                beforeClose: function beforeClose() {
                    $('.mfp-content').children().removeClass('mfp-inline');
                }
            }
        });

        // gallery

        for (var i = 0, len = galleries.length; i < len; i++) {

            var gallery = '[' + galleries[i] + ']';
            gallery = 'a[rel^="lightbox' + gallery + '"]';

            $(gallery).magnificPopup({
                autoFocusLast: attr.autoFocusLast,
                removalDelay: attr.removalDelay,
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        }
    };

    // Visual Composer prettyPhoto | unbind ----------

    var unbindPretty = function unbindPretty() {

        if (lightbox_attr) {
            $('a[data-rel^="prettyPhoto"], a[rel^="lightbox"]').each(function () {
                $(this).unbind('click.prettyphoto');
            });
        }
    };

    // init ----------

    init();

    // onload ----------

    $(window).on('load', unbindPretty);
}

lightbox();

/* Scroll | niceScroll ------------------------------------------------------------------ */
if ($('body').hasClass('nice-scroll-on') && window.innerWidth >= 768 && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) {
    $('html').niceScroll({
        autohidemode: false,
        cursorborder: 0,
        cursorborderradius: 5,
        cursorcolor: '#222222',
        cursorwidth: 10,
        horizrailenabled: false,
        mousescrollstep: window.mfn.nicescroll ? window.mfn.nicescroll : 40,
        scrollspeed: 60
    });

    $('body').removeClass('nice-scroll-on').addClass('nice-scroll');
    niceScrollFix();
}

/* Animate Math | Counter, Quick Fact, etc ---------------------------------------------- */

$('.animate-math .number').waypoint({

    offset: '100%',
    triggerOnce: true,
    handler: function handler() {

        var el = $(this.element).length ? $(this.element) : $(this);
        var duration = Math.floor(Math.random() * 1000 + 1000);
        var to = el.attr('data-to');

        $({ property: 0 }).animate({ property: to }, {
            duration: duration,
            easing: 'linear',
            step: function step() {
                el.text(Math.floor(this.property));
            },
            complete: function complete() {
                el.text(this.property);
            }
        });

        if (typeof this.destroy !== 'undefined' && $.isFunction(this.destroy)) {
            this.destroy();
        }
    }
});

/* Animate ------------------------------------------------------------------------------ */
$('.animate').waypoint({
    offset: '90%',
    triggerOnce: true,
    handler: function handler() {

        var el = $(this.element).length ? $(this.element) : $(this);
        if ($(window).width() >= 960) {
            el.each(function (i, elem) {
                var elem = $(elem),
                    type = $(this).attr('data-anim-type'),
                    delay = $(this).attr('data-anim-delay');

                if (elem.is(':visible')) {
                    setTimeout(function () {
                        elem.addClass(type);
                    }, delay);
                }
            });
        } else {
            el.each(function (i, elem) {
                var elem = $(elem),
                    type = $(this).attr('data-anim-type'),
                    delay = $(this).attr('data-anim-delay');

                setTimeout(function () {
                    elem.addClass(type);
                }, delay);
            });
        }

        if (typeof this.destroy !== 'undefined' && $.isFunction(this.destroy)) {
            this.destroy();
        }
    }
});
$('.animate50').waypoint({
    offset: '70%',
    triggerOnce: true,
    handler: function handler() {

        var el = $(this.element).length ? $(this.element) : $(this);
        if ($(window).width() >= 960) {
            el.each(function (i, elem) {
                var elem = $(elem),
                    type = $(this).attr('data-anim-type'),
                    delay = $(this).attr('data-anim-delay');

                if (elem.is(':visible')) {
                    setTimeout(function () {
                        elem.addClass(type);
                    }, delay);
                }
            });
        } else {
            el.each(function (i, elem) {
                var elem = $(elem),
                    type = $(this).attr('data-anim-type'),
                    delay = $(this).attr('data-anim-delay');

                setTimeout(function () {
                    elem.addClass(type);
                }, delay);
            });
        }

        if (typeof this.destroy !== 'undefined' && $.isFunction(this.destroy)) {
            this.destroy();
        }
    }
});

/* Sticky Call --------------------------------------------------------------------------- */
$('.footer').waypoint({

    offset: '100%',
    triggerOnce: true,
    handler: function handler(direction) {

        var el = $(this.element).length ? $(this.element) : $(this);

        if (direction == "down") {
            el.find('.footer__call').addClass('fixed');
        } else if (direction == "up") {
            el.find('.footer__call').removeClass('fixed');
        }
    }
});

$('#box-aboutTeam').gridrotator({
    rows: 6,
    columns: 12,
    maxStep: 7,
    interval: 1500,
    w1400: {
        rows: 6,
        columns: 11
    },
    w1200: {
        rows: 6,
        columns: 9
    },
    w1024: {
        rows: 6,
        columns: 8
    },
    w768: {
        rows: 6,
        columns: 7
    },
    w480: {
        rows: 6,
        columns: 6
    },
    w320: {
        rows: 6,
        columns: 5
    },
    w240: {
        rows: 4,
        columns: 4
    }
});

/* About Page ----------------------------------------------------------------------------- */
$(".box-aboutOurStory").slick({
    // autoplay: true,
    initialSlide: 4,
    dots: true,
    prevArrow: '<button type="button" class="slick-prev"></button>',
    nextArrow: '<button type="button" class="slick-next"></button>',
    customPaging: function customPaging(slider, i) {
        var date = $(slider.$slides[i]).data('date');
        return '<span>' + date + '</span><span>' + date + '</span>';
    }
});
/* FAQs Search form */
$('#faqsSearch').hideseek({
    highlight: true,
    ignore: '.no-search'
});

/* Case Studies ------------------------------------------------------------------------------- */
$('.CaseStudieSlide').slick({
    dots: false,
    infinite: true,
    speed: 500,
    fade: true,
    cssEase: 'linear',
    autoplay: true,
    autoplaySpeed: 10000,
    arrows: true,
    prevArrow: '<button type="button" class="slick-prev"></button>',
    nextArrow: '<button type="button" class="slick-next"></button>',
    adaptiveHeight: true
});

/* Careers ------------------------------------------------------------------------------- */
if ($('#careerDetail__form').length) {
    myDropzone = new Dropzone("#careerDetail__form", {
        //myDropzone = $("#careerDetail__form").dropzone({
        url: config.apiUrl + config.route,
        maxFilesize: 5,
        maxFiles: 1,
        dictResponseError: 'Server not Configured',
        acceptedFiles: ".doc, .docx, .pdf, .png, .jpg, .gif,.bmp, .jpeg",
        autoProcessQueue: false,
        init: function init() {
            var self = this;
            // config
            self.options.addRemoveLinks = true;
            self.options.dictRemoveFile = "Delete";
            //New file added
            self.on("addedfile", function (file) {
                console.log('new file added ', file);
            });
            // Send file starts
            self.on("sending", function (file) {
                console.log('upload started', file);
                $('.meter').show();
            });

            // File upload Progress
            self.on("totaluploadprogress", function (progress) {
                console.log("progress ", progress);
                $('.roller').width(progress + '%');
            });

            self.on("queuecomplete", function (progress) {
                $('.meter').delay(999).slideUp(999);
            });

            // On removing file
            self.on("removedfile", function (file) {
                console.log(file);
            });
        }
    });
}

/* Contact ------------------------------------------------------------------------------- */
if ($('.select').length) {
    $(".select").chosen({
        no_results_text: "Oops, nothing found!"
    });
}
$("#duration").ionRangeSlider({
    grid: true,
    from: 2,
    values: ["3 months", "6 months", "9 months", "12 months", "15 months", "18 months"],
    hide_min_max: true,
    hide_from_to: true
});

/* $(window).scroll ---------------------------------------------------------------------- */
$(window).scroll(function () {});

/* $(window).Load ------------------------------------------------------------------------ */
$(window).load(function () {
    slideMain();
    headerSlide();
});

// $(window).on("load resize", function (e) {

// });
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(0), __webpack_require__(0)))

/***/ }),
/* 2 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(2);


/***/ })
/******/ ]);