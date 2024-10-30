/* globals jQuery */

(function ($) {
  // Selector to select only not already processed elements
  $.expr[":"].notmdproc = function (obj) {
    if ($(obj).data("mdproc")) {
      return false;
    } else {
      return true;
    }
  };

  function _isChar(evt) {
    if (typeof evt.which == "undefined") {
      return true;
    } else if (typeof evt.which == "number" && evt.which > 0) {
      return (
        !evt.ctrlKey
        && !evt.metaKey
        && !evt.altKey
        && evt.which != 8  // backspace
        && evt.which != 9  // tab
        && evt.which != 13 // enter
        && evt.which != 16 // shift
        && evt.which != 17 // ctrl
        && evt.which != 20 // caps lock
        && evt.which != 27 // escape
      );
    }
    return false;
  }

  function _addFormGroupFocus(element) {
    var $element = $(element);
    if (!$element.prop('disabled')) {  // this is showing as undefined on chrome but works fine on firefox??
      $element.closest("label").addClass("is-focused");
    }
  }

  function _toggleTypeFocus($input) {
    $input.closest('label').hover(function () {
      var $i = $(this).find('input');
      if (!$i.prop('disabled')) { // hack because the _addFormGroupFocus() wasn't identifying the property on chrome
        _addFormGroupFocus($i);     // need to find the input so we can check disablement
      }
    }, function () {
      _removeFormGroupFocus($(this).find('input'));
    });
  }

  function _removeFormGroupFocus(element) {
    $(element).closest("label").removeClass("is-focused"); // remove class from form-group
  }

  $.material = {
    "options": {
      // These options set what will be started by $.material.init()
      "ripples": true,
      "checkbox": true,
      "togglebutton": true,
      "radio": true,
      "arrive": true,
      "autofill": false,

      "withRipples": [
        ".button",
        ".button-secondary",
        ".button-primary",
        ".wrap .page-title-action",
        "#add-new-comment a", 
        ".tablenav .tablenav-pages a",
        "#wpadminbar .quicklinks>ul>li>a",
        "#adminmenu a.menu-top"
      ].join(","),
      "inputElements": "input[type=text], input[type=email], input[type=url],input[type=search], input[type=password], input[type=tel], input[type=number], textarea, select",
      "checkboxElements": "input[type=checkbox]",
      "radioElements": "input[type=radio]"
    },
    "checkbox": function (selector) {
      // Add fake-checkbox to material checkboxes
      var $input = $((selector) ? selector : this.options.checkboxElements)
        .filter(":notmdproc")
        .data("mdproc", true)
        .wrap("<label class='mfat-wrapping'></label>")
        .after("<span class='checkbox-material'><span class='check'></span></span>");

      _toggleTypeFocus($input);
    },
    "togglebutton": function (selector) {
      // Add fake-checkbox to material checkboxes
      var $input = $((selector) ? selector : this.options.togglebuttonElements)
        .filter(":notmdproc")
        .data("mdproc", true)
        .after("<span class='toggle'></span>");

      _toggleTypeFocus($input);
    },
    "radio": function (selector) {
      // Add fake-radio to material radios
      var $input = $((selector) ? selector : this.options.radioElements)
        .filter(":notmdproc")
        .filter(function(){return $(this).css('display') != 'none';})
        .data("mdproc", true)
        .wrap("<label class='mfat-wrapping'></label>")
        .after("<span class='circle'></span><span class='check'></span>");

      _toggleTypeFocus($input);
    },
    "ripples": function (selector) {
      $((selector) ? selector : this.options.withRipples).ripples();
    },
    "init": function (options) {
      this.options = $.extend({}, this.options, options);
      var $document = $(document);

      if ($.fn.ripples && this.options.ripples) {
        this.ripples();
      }
      if (this.options.checkbox) {
        this.checkbox();
      }
      if (this.options.togglebutton) {
        this.togglebutton();
      }
      if (this.options.radio) {
        this.radio();
      }
      if (document.arrive && this.options.arrive) {
        if ($.fn.ripples && this.options.ripples) {
          $document.arrive(this.options.withRipples, function () {
            $.material.ripples($(this));
          });
        }
        if (this.options.checkbox) {
          $document.arrive(this.options.checkboxElements, function () {
            $.material.checkbox($(this));
          });
        }
        if (this.options.radio) {
          $document.arrive(this.options.radioElements, function () {
            $.material.radio($(this));
          });
        }
        if (this.options.togglebutton) {
          $document.arrive(this.options.togglebuttonElements, function () {
            $.material.togglebutton($(this));
          });
        }

      }
    }
  };

})(jQuery);
jQuery(document).ready(function($){
  $.material.init();
});