/**
 * File: az_step_video.js
 */

(function ($) {

  Drupal.atomizer.step_videoC = function (_viewer) {
    let viewer = _viewer;

    const startP = (step) => {
      // read in the node with a promise.
      let data = {
        nid: step.nid,
        viewMode: step.viewMode,
      };
      return new Promise (function(resolve, reject) {
        // How do we do this?  Should we render media, or render a node.  I could do this totally through the render node function.
        let p = viewer.base.requestP('renderNodeCommand', '/ajax/renderNode', data);
        p.then(function fulfilled(results) {
          console.log('step_render_node.start - promise fulfilled');
          $(step.container, viewer.content).html(results.htmlContents);
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


