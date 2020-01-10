/**
 * File: az_step_render_node.js
 */

(function ($) {

  Drupal.atomizer.step_render_nodeC = function (_viewer) {
    let viewer = _viewer;

    const startP = (step) => {
      // read in the node with a promise.
      let data = {
        nid: step.nid,
        viewMode: step.viewMode,
      };
      return new Promise (function(resolve, reject) {
        let p = viewer.base.requestP('renderNodeCommand', '/ajax/renderNode', data);
        p.then(function fulfilled(results) {
          console.log('step_render_node.start - promise fulfilled');
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


