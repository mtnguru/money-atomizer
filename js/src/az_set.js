/**
 * File: az_set.js
 */

(function ($) {

  Drupal.atomizer.setC = function (_viewer) {
    let viewer = _viewer;
    let $wrapper = $('.az-html-wrapper', viewer.context);
    let $container = $('.az-html', viewer.context);

    const display = (set) => {
      if (set.cssId) {
        $wrapper[0].id = set.cssId;
      }
      if (set.cssClass) {
        $wrapper.addClass(set.cssClass);
      }
      switch (set.type) {
        case 'form-dialog':
          break;
        case 'node-popup':
          break;
        case 'video-player':
          break;
      }
    };

    return {
      display,
    }
  }
})(jQuery);


