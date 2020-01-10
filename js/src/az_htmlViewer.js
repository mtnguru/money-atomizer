/**
 * @file - az_htmlViewer.js
 */



(function ($) {

  Drupal.atomizer.htmlViewerC = function (atomizer) {

    var viewer = {
      items: {},  // icosafaces, couple other things - need to look into what this is for.,
      context: $('#azid-' + atomizer.atomizerId.toLowerCase()),
      atomizer: atomizer,
    };
    viewer.wrapper = $('az-atomizer-wrapper', viewer.context);
    var fullScreen = false;
    var displayMode = ''; // mobile, tablet, desktop

    /**
     * Search backward up the parent chaa
     *
     * @param canvasContainer
     */
    function getDataAttr(canvasContainer) {
      var attr = {};
      // Look for a parent with class 'az-atomizer'
      var el = viewer.canvasContainer;
      while ((el = el.parentElement) && !el.classList.contains('az-embed'));
      if (el && el.attributes['data-az']) {
        var value = el.attributes['data-az'].value;
        var pairs = value.split(' ');
        for (var pair in pairs) {
          if (!pairs.hasOwnProperty(pair)) continue;
          var pieces = pairs[pair].split('=');
          attr[pieces[0]] = pieces[1];
        }
      }
      return attr;
    }

    /**
     * Set size of the canvas based on screen size
     */
    function setSize() {
      viewer.context.removeClass('display-mode-mobile');
      viewer.context.removeClass('display-mode-table');
      viewer.context.removeClass('display-mode-desktop');

      // Get container width and calculate new height
      if (fullScreen) {
        viewer.canvas.height = window.innerHeight;
        viewer.canvas.width = window.innerWidth;
      } else {
        var $toolbarHeight = 0;

        var $bar = $('.toolbar-bar');
        if ($bar.length) {
          $toolbarHeight += ($bar.length) ? $bar.height() : 0;
        }

        var $tray = $('.toolbar-tray');
        if ($tray.length && $tray.hasClass('is-active')) {
          $toolbarHeight += ($tray.length) ? $tray.height() : 0;
        }

        if (window.innerWidth < 960) {
          displayMode = 'mobile';
          viewer.context.addClass('display-mode-' + displayMode);
        }
        else {
          displayMode = 'desktop';
          viewer.context.addClass('display-mode-' + displayMode);

          if (!viewer.atomizer.windowRatio || viewer.atomizer.windowRatio === 'window') {
            viewer.wrapper.height = window.innerHeight - 40 - $toolbarHeight;
          } else {
            viewer.wrapper.height = viewer.wrapper.width * viewer.atomizer.windowRatio;
          }
          viewer.wrapperContainer.style.height = viewer.canvas.height + 'px';
          // Now that we set the height, set the width again,
          // the page scrollbar changes the container width.
          viewer.canvas.width = viewer.canvasContainer.clientWidth;
        }
      }
    }

    viewer.init = function () {

      // Retrieve canvas dimensions and set renderer size to that.

//    viewer.theme.addDataAttr();
//    viewer.canvas = viewer.canvasContainer.getElementsByTagName('canvas')[0];

//    setSize();

      // Make controls
//    viewer.controls = Drupal.atomizer.controlsC(viewer);
    }

      /*
    viewer.context.hover(function () {
      var x = window.scrollX;
      var y = window.scrollY;
      this.focus();
      window.scrollTo(x, y);
    }, function () {
      this.blur();
    }).keyup(function (event) {
      switch (event.keyCode) {
        case 70: // F
          fullScreen = !fullScreen;
          screenfull.toggle(viewer.context[0]);
          if (fullScreen) {
            viewer.context.addClass('az-fullscreen');
          }
          else {
            viewer.context.removeClass('az-fullscreen');
          }
          event.preventDefault();
          break;

//    case 88: // X
//    case 27: // escape
//      if (localStorage.imagerFullScreen === 'TRUE') {
//        screenfull.exit();
//      }
//      else {
//      }
//      event.preventDefault();
//      break;
      }
    });
    */

    /*
    viewer.context.dblclick(function () {
      if (fullScreen) {
        fullScreen = false;
        viewer.context.removeClass('az-fullscreen');
        screenfull.exit();
      }
    });

    Drupal.behaviors.atomizer_viewer = {
      // Attach functions are executed by Drupal upon page load or ajax loads.
      attach: function (context, settings) {
        var $dialogs = $('.az-dialog', viewer.context);
        $dialogs.each(function ($dialog) {
          if (!$(this).hasClass('az-dialog-processed')) {
            $(this).addClass('az-dialog-processed');
//          viewer.context.append($(this));
          }
        });
      }
    };

    viewer.buttonClicked = function buttonClicked (event) {
      if (event.target.id === 'viewer--fullScreen') {
        fullScreen = !fullScreen;
        if (fullScreen) {
          screenfull.request(viewer.context[0]);
          viewer.context.addClass('az-fullscreen');
        }
        else {
          screenfull.exit();
          viewer.context.removeClass('az-fullscreen');
        }
      }
    };
    */

    return viewer;
  };

})(jQuery);
