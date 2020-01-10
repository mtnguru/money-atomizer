/**
 * File: az_step_webform.js
 *
 * Display a form which user fills in.
 */

(function ($) {

  Drupal.atomizer.step_webformC = function (_viewer) {
    let viewer = _viewer;

    const startP = (step) => {
      // read in the node with a promise.
      let data = {
        formName: step['form-name'],
      };
      return new Promise (function(resolve, reject) {
        let p = viewer.base.requestP('renderWebformCommand', '/ajax/renderWebform', data);
        p.then(function fulfilled(results) {
          console.log('step_render_node.start - promise fulfilled');
          let $container = $(step.container, viewer.context);
          if (!$container) {
            console.log(`container not defined`);
          }
          $(step.container, viewer.context).html(results.htmlContents);
          resolve();
          // Display the node here.
        });
      })
    };

    return {
      startP,
    }
  }
})(jQuery);


