window.milestoneArgs = {
  0: function (e, t) {
    e.exports = jQuery
  }, 13: function (e, t, o) {
    e.exports = o(4)
  }, 4: function (e, t, o) {
    (function (e) {
      function t(e, t, o) {
        return t in e ? Object.defineProperty(e, t, {value: o, enumerable: !0, configurable: !0, writable: !0}) : e[t] = o, e
      }

      var o = ".milestone__timeline", s = ".milestone__year";
      e(o).length && function () {
        function i() {
          var t = e(".timeline__item"), o = e(".slick-current").find(".timeline__item");
          t.removeClass("active"), o.waypoint({
            offset: "90%", triggerOnce: !0, handler: function (t) {
              var o = e(e(this.element).length ? this.element : this);
              "down" == t && o.addClass("active"), void 0 !== this.destroy && e.isFunction(this.destroy) && this.destroy()
            }
          })
        }

        var n;
        e(o).on("init.slick", function (e, t) {
          i()
        }).slick((n = {slidesToShow: 1, slidesToScroll: 1, arrows: !1, fade: !0, infinite: !1}, t(n, "infinite", !0), t(n, "adaptiveHeight", !0), t(n, "asNavFor", s), n)).on("afterChange", function (e, t, o, s) {
          i()
        }), e(s).slick({asNavFor: o, focusOnSelect: !0, dots: !1, prevArrow: '<a class="slider_prev" href="#"><span class="button_icon"><i class="arrow_carrot-left"></i></span></a>', nextArrow: '<a class="slider_next" href="#"><span class="button_icon"><i class="arrow_carrot-right"></i></span></a>', infinite: !1, speed: 300, slidesToShow: 3, slidesToScroll: 1, responsive: [{breakpoint: 1200, settings: {slidesToShow: 3, slidesToScroll: 1}}, {breakpoint: 1024, settings: {slidesToShow: 3, slidesToScroll: 1}}, {breakpoint: 767, settings: {slidesToShow: 2, slidesToScroll: 1}}, {breakpoint: 480, settings: {slidesToShow: 2, slidesToScroll: 1}}, {breakpoint: 360, settings: {slidesToShow: 1, slidesToScroll: 1}}]})
      }();
      var i = e(".discover__number");
      !function () {
        function t() {
          i.find(".slick-active .discover__number__item__icon span").waypoint({
            offset: "100%", triggerOnce: !0, handler: function () {
              var t = e(e(this.element).length ? this.element : this), o = Math.floor(1e3 * Math.random() + 1e3), s = t.attr("data-to");
              s < 10 && (o = 400), e({property: 0}).animate({property: s}, {
                duration: o, easing: "linear", step: function () {
                  val = this.property < 10 ? "0" + Math.floor(this.property) : Math.floor(this.property), t.text(val)
                }, complete: function () {
                  val = this.property < 10 ? "0" + this.property : this.property, t.text(val)
                }
              }), void 0 !== this.destroy && e.isFunction(this.destroy) && this.destroy()
            }
          })
        }

        i.on("init.slick", function (e, o) {
          t()
        }).slick({focusOnSelect: !0, dots: !1, prevArrow: '<a class="slider_prev" href="#"><span class="button_icon"><i class="arrow_carrot-left"></i></span></a>', nextArrow: '<a class="slider_next" href="#"><span class="button_icon"><i class="arrow_carrot-right"></i></span></a>', infinite: !1, speed: 300, slidesToShow: 4, slidesToScroll: 1, autoplay: !0, autoplaySpeed: 4e3, responsive: [{breakpoint: 1200, settings: {slidesToShow: 3, slidesToScroll: 1, autoplay: !0, autoplaySpeed: 4e3}}, {breakpoint: 992, settings: {slidesToShow: 2, slidesToScroll: 1, autoplay: !0, autoplaySpeed: 4e3}}, {breakpoint: 480, settings: {slidesToShow: 1, slidesToScroll: 1, autoplay: !0, autoplaySpeed: 4e3}}]}).on("afterChange", function (e, o, s, i) {
          t()
        })
      }();
      var n = e(".value__feature");
      !function () {
        n.slick({focusOnSelect: !0, dots: !1, prevArrow: '<a class="slider_prev" href="#"><span class="button_icon"><i class="arrow_carrot-left"></i></span></a>', nextArrow: '<a class="slider_next" href="#"><span class="button_icon"><i class="arrow_carrot-right"></i></span></a>', infinite: !1, speed: 300, slidesToShow: 3, slidesToScroll: 1, responsive: [{breakpoint: 1200, settings: {slidesToShow: 3, slidesToScroll: 1}}, {breakpoint: 992, settings: {slidesToShow: 2, slidesToScroll: 1, autoplay: !0, autoplaySpeed: 4e3, infinite: !0}}, {breakpoint: 480, settings: {slidesToShow: 1, slidesToScroll: 1, autoplay: !0, autoplaySpeed: 4e3, infinite: !0}}]})
      }()
    }).call(t, o(0))
  }
};
function runMilestone(e) {
  function t(s) {
    if (o[s]) return o[s].exports;
    var i = o[s] = {i: s, l: !1, exports: {}};
    return e[s].call(i.exports, i, i.exports, t), i.l = !0, i.exports
  }

  var o = {};
  t.m = e, t.c = o, t.i = function (e) {
    return e
  }, t.d = function (e, o, s) {
    t.o(e, o) || Object.defineProperty(e, o, {configurable: !1, enumerable: !0, get: s})
  }, t.n = function (e) {
    var o = e && e.__esModule ? function () {
      return e.default
    } : function () {
      return e
    };
    return t.d(o, "a", o), o
  }, t.o = function (e, t) {
    return Object.prototype.hasOwnProperty.call(e, t)
  }, t.p = "./", t(t.s = 13)
}

// runMilestone(window.milestoneArgs);
