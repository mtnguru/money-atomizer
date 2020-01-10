/**
 * File: az_board.js
 *
 * Create the game board.  Give it it's own viewer, etc.monopoly like cards on the game board.
 */

(function ($) {

  Drupal.atomizer.boardC = function (_viewer) {
    let viewer = _viewer;
    let $container;
    let $board;
    let config;

    /**
     * User has clicked on a cell in the periodic table.
     *
     * @param event
     */
    const onMouseDown = function (event) {
      let $element = $(event.target).hasClass('element') ? $(event.target) : $(event.target).parents('.element');
//    let nid = $element.data('nid');
      console.log(`onMouseDown - ${$element[0].className}`);
      switch (event.button) {
        case 0:     // Select element as new atom.
          // If it doesn't exist then create it.
          break;
        case 1:     // Nothing?
          break;
        case 2:     // Display the elementPopup
          break;
      }
    };

    const createPr = ($_container, _config) => {
      $container = $_container;
      $board = $('.az-html', viewer.context);
      config = _config;

      // Create the board - Is there anything in the config for this?
      // Find the board in the html - az-html is the board James.
      // Get the board size as defined by the CSS or whatever is happening
      // Set the size and location of each object


      // Create objects on the board
      for (let id in config['scene-layout'].objects) {
        let object = config['scene-layout'].objects[id];

        let type = id.split("--")[0];
        let element = viewer[type].create(id, object, viewer);
        $board.append(element);
      }

      onResize();
    };

    function findWindowSize() {
      let width, height;
      let aspect = 2.10;
      $container.css({
        'width': 'auto',
        'height': 'auto',
      });
      width = $container.width();
      height = width / aspect + 36;
      return {width, height};
    }

    function onResize() {
      let {width, height} = findWindowSize();
      console.log(`onResize: ${width} ${height}`);
    }

    return {
      createPr,
      onResize,
    }
  }
})(jQuery);


