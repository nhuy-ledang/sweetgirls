function getSETTINGVar(key) {
  var configs = _.extend({urlPrefix: ''}, window['settings']);
  if (configs[key]) {
    return configs[key];
  } else {
    return '';
  }
}

// Cart add remove functions
var pdCart = {
  'add': function(product_id, quantity) {
  },
  'update': function(key, quantity, element) {
    $.ajax({
      url: '/' + getSETTINGVar('urlPrefix') + 'checkout/cart/edit',
      type: 'post',
      data: 'key=' + key + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
      dataType: 'json',
      beforeSend: function() {
        // $('#cart > button').button('loading');
        $(element).prop('disabled', true);
      },
      complete: function() {
        // $('#cart > button').button('reset');
        $(element).prop('disabled', false);
      },
      success: function(json) {
        console.log(json);
        location.reload();
        /*if (getSETTINGVar('ROUTE') == 'checkout/cart' || getSETTINGVar('ROUTE') == 'checkout/checkout') {
          location = '/checkout/cart';
        } else {
          $('#cart').parent().load('/common/cart/info');
        }*/
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  },
  'remove': function(key, element) {
    $.ajax({
      url: '/' + getSETTINGVar('urlPrefix') + 'checkout/cart/remove',
      type: 'post',
      data: 'key=' + key,
      dataType: 'json',
      beforeSend: function() {
        // $('#cart > button').button('loading');
        $(element).prop('disabled', true);
      },
      complete: function() {
        // $('#cart > button').button('reset');
        $(element).prop('disabled', false);
      },
      success: function(json) {
        location.reload();
        /*if (getSETTINGVar('ROUTE') == 'checkout/cart' || getSETTINGVar('ROUTE') == 'checkout/checkout') {
          location.reload();
        } else {
          $('#cart').parent().load('/common/cart/info');
        }*/
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
};

// var voucher = {
//   'add': function() {
//
//   },
//   'remove': function(key) {
//     $.ajax({
//       url: '/' + getSETTINGVar('urlPrefix') + 'checkout/cart/remove',
//       type: 'post',
//       data: 'key=' + key,
//       dataType: 'json',
//       beforeSend: function() {
//         $('#cart > button').button('loading');
//       },
//       complete: function() {
//         $('#cart > button').button('reset');
//       },
//       success: function(json) {
//         if (getSETTINGVar('ROUTE') == 'checkout/cart' || getSETTINGVar('ROUTE') == 'checkout/checkout') {
//           location = '/checkout/cart';
//         } else {
//           $('#cart').parent().load('/common/cart/info');
//         }
//       },
//       error: function(xhr, ajaxOptions, thrownError) {
//         alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
//       }
//     });
//   }
// };
//
// var wishlist = {
//   'add': function(product_id) {
//     $.ajax({
//       url: '/account/wishlist/add',
//       type: 'post',
//       data: 'product_id=' + product_id,
//       dataType: 'json',
//       success: function(json) {
//         if (json['redirect']) {
//           location = json['redirect'];
//         }
//
//         if (json['success']) {
//           // $.toast({heading: 'Đã thêm yêu thích', text: json['success'], position: 'top-right', icon: 'info', stack: false, hideAfter: 3000});
//           $.toast({heading: '', text: 'Đã thêm yêu thích', position: 'top-right', icon: 'info', stack: false, hideAfter: 3000});
//         }
//
//         $('#wishlist-total span').html(json['total']);
//         $('#wishlist-total').attr('title', json['total']);
//       },
//       error: function(xhr, ajaxOptions, thrownError) {
//         alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
//       }
//     });
//   },
//   'remove': function() {
//
//   }
// };
//
// var compare = {
//   'add': function(product_id) {
//     $.ajax({
//       url: '/' + getSETTINGVar('urlPrefix') + 'product/compare/add',
//       type: 'post',
//       data: 'product_id=' + product_id,
//       dataType: 'json',
//       success: function(json) {
//         if (json['success']) {
//           // $.toast({heading: 'Đã thêm vào so sánh', text: json['success'], position: 'top-right', icon: 'info', stack: false, hideAfter: 3000});
//           $.toast({heading: '', text: 'Đã thêm yêu thích', position: 'top-right', icon: 'info', stack: false, hideAfter: 3000});
//
//           $('#compare-total').html(json['total']);
//         }
//       },
//       error: function(xhr, ajaxOptions, thrownError) {
//         alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
//       }
//     });
//   },
//   'remove': function() {
//
//   }
// };

var exhCart = {
  'add': function(ticket_id, quantity, element) {
    $.ajax({
      url: '/' + getSETTINGVar('urlPrefix') + 'exhibit/checkout/cart/add',
      type: 'post',
      data: 'ticket_id=' + ticket_id + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
      dataType: 'json',
      beforeSend: function() {
        $(element).prop('disabled', true);
      },
      complete: function() {
        $(element).prop('disabled', false);
      },
      success: function(json) {
        console.log(json);
        location.reload();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  },
  'update': function(key, quantity, element) {
    $.ajax({
      url: '/' + getSETTINGVar('urlPrefix') + 'exhibit/checkout/cart/edit',
      type: 'post',
      data: 'key=' + key + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
      dataType: 'json',
      beforeSend: function() {
        $(element).prop('disabled', true);
      },
      complete: function() {
        $(element).prop('disabled', false);
      },
      success: function(json) {
        console.log(json);
        location.reload();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  },
  'remove': function(key, element) {
    $.ajax({
      url: '/' + getSETTINGVar('urlPrefix') + 'exhibit/checkout/cart/remove',
      type: 'post',
      data: 'key=' + key,
      dataType: 'json',
      beforeSend: function() {
        $(element).prop('disabled', true);
      },
      complete: function() {
        $(element).prop('disabled', false);
      },
      success: function(json) {
        location.reload();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
};

var actCart = {
  'add': function(ticket_id, quantity, element) {
    $.ajax({
      url: '/' + getSETTINGVar('urlPrefix') + 'activity/checkout/cart/add',
      type: 'post',
      data: 'ticket_id=' + ticket_id + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
      dataType: 'json',
      beforeSend: function() {
        $(element).prop('disabled', true);
      },
      complete: function() {
        $(element).prop('disabled', false);
      },
      success: function(json) {
        console.log(json);
        location.reload();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  },
  'update': function(key, quantity, element) {
    $.ajax({
      url: '/' + getSETTINGVar('urlPrefix') + 'activity/checkout/cart/edit',
      type: 'post',
      data: 'key=' + key + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
      dataType: 'json',
      beforeSend: function() {
        $(element).prop('disabled', true);
      },
      complete: function() {
        $(element).prop('disabled', false);
      },
      success: function(json) {
        console.log(json);
        location.reload();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  },
  'remove': function(key, element) {
    $.ajax({
      url: '/' + getSETTINGVar('urlPrefix') + 'activity/checkout/cart/remove',
      type: 'post',
      data: 'key=' + key,
      dataType: 'json',
      beforeSend: function() {
        $(element).prop('disabled', true);
      },
      complete: function() {
        $(element).prop('disabled', false);
      },
      success: function(json) {
        location.reload();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
};

// Chain ajax calls.
class Chain {
  constructor() {
    this.start = false;
    this.data = [];
  }

  attach(call) {
    this.data.push(call);

    if (!this.start) {
      this.execute();
    }
  }

  execute() {
    if (this.data.length) {
      this.start = true;

      (this.data.shift())().done(function() {
        chain.execute();
      });
    } else {
      this.start = false;
    }
  }
}

var chain = new Chain();

// Button
(function($) {
  'use strict';

  // BUTTON PUBLIC CLASS DEFINITION
  // ==============================

  var Button = function(element, options) {
    this.$element = $(element)
    this.options = $.extend({}, Button.DEFAULTS, options)
    this.isLoading = false
  }

  Button.VERSION = '3.3.5'

  Button.DEFAULTS = {
    loadingText: 'loading...'
  }

  Button.prototype.setState = function(state) {
    var d = 'disabled'
    var $el = this.$element
    var val = $el.is('input') ? 'val' : 'html'
    var data = $el.data()

    state += 'Text'

    if (data.resetText == null) $el.data('resetText', $el[val]())

    // push to event loop to allow forms to submit
    setTimeout($.proxy(function() {
      $el[val](data[state] == null ? this.options[state] : data[state])

      if (state == 'loadingText') {
        this.isLoading = true
        $el.addClass(d).attr(d, d)
      } else if (this.isLoading) {
        this.isLoading = false
        $el.removeClass(d).removeAttr(d)
      }
    }, this), 0)
  }

  Button.prototype.toggle = function() {
    var changed = true
    var $parent = this.$element.closest('[data-toggle="buttons"]')

    if ($parent.length) {
      var $input = this.$element.find('input')
      if ($input.prop('type') == 'radio') {
        if ($input.prop('checked')) changed = false
        $parent.find('.active').removeClass('active')
        this.$element.addClass('active')
      } else if ($input.prop('type') == 'checkbox') {
        if (($input.prop('checked')) !== this.$element.hasClass('active')) changed = false
        this.$element.toggleClass('active')
      }
      $input.prop('checked', this.$element.hasClass('active'))
      if (changed) $input.trigger('change')
    } else {
      this.$element.attr('aria-pressed', !this.$element.hasClass('active'))
      this.$element.toggleClass('active')
    }
  }

  // BUTTON PLUGIN DEFINITION
  // ========================

  function Plugin(option) {
    return this.each(function() {
      var $this = $(this)
      var data = $this.data('bs.button')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.button', (data = new Button(this, options)))

      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }

  var old = $.fn.button

  $.fn.button = Plugin
  $.fn.button.Constructor = Button


  // BUTTON NO CONFLICT
  // ==================

  $.fn.button.noConflict = function() {
    $.fn.button = old
    return this
  }


  // BUTTON DATA-API
  // ===============

  $(document).on('click.bs.button.data-api', '[data-toggle^="button"]', function(e) {
    var $btn = $(e.target);

    if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn');

    Plugin.call($btn, 'toggle');

    if (!($(e.target).is('input[type="radio"]') || $(e.target).is('input[type="checkbox"]'))) e.preventDefault();
  }).on('focus.bs.button.data-api blur.bs.button.data-api', '[data-toggle^="button"]', function(e) {
    $(e.target).closest('.btn').toggleClass('focus', /^focus(in)?$/.test(e.type));
  });
})(jQuery);

/* ========================================================================
 * Bootstrap: alert.js v3.3.5
 * http://getbootstrap.com/javascript/#alerts
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function($) {
  'use strict';

  // ALERT CLASS DEFINITION
  // ======================

  var dismiss = '[data-dismiss="alert"]'
  var Alert = function(el) {
    $(el).on('click', dismiss, this.close)
  }

  Alert.VERSION = '3.3.5'

  Alert.TRANSITION_DURATION = 150

  Alert.prototype.close = function(e) {
    var $this = $(this)
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = $(selector)

    if (e) e.preventDefault()

    if (!$parent.length) {
      $parent = $this.closest('.alert')
    }

    $parent.trigger(e = $.Event('close.bs.alert'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      // detach from parent, fire event then clean up data
      $parent.detach().trigger('closed.bs.alert').remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent
        .one('bsTransitionEnd', removeElement)
        .emulateTransitionEnd(Alert.TRANSITION_DURATION) :
      removeElement()
  }


  // ALERT PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function() {
      var $this = $(this)
      var data = $this.data('bs.alert')

      if (!data) $this.data('bs.alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.alert

  $.fn.alert = Plugin
  $.fn.alert.Constructor = Alert


  // ALERT NO CONFLICT
  // =================

  $.fn.alert.noConflict = function() {
    $.fn.alert = old
    return this
  }


  // ALERT DATA-API
  // ==============

  $(document).on('click.bs.alert.data-api', dismiss, Alert.prototype.close)

}(jQuery);
