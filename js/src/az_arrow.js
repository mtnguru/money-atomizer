/**
 * File: az_arrow.js
 *
 * Create an arrow on the game board.
 */

(function ($) {
  Drupal.atomizer.arrowC = function (_board) {
    let viewer;
    let board = _board;

    const create = (id, conf, viewer) => {
      let html;
      let label = conf.label || ' ';
      label = label == 'none' ? ' ' : label;

      html  = `<div class="arrow" style="width: ${conf.position[2] - conf.position[0]}px;">`;
      html += `<div class="arrow-body">${label}</div>`;
      html += `</div>`;

      let arrow = document.createElement('div');
      arrow.className = 'arrow-wrapper';
      arrow.id = id.toLowerCase();
      arrow.innerHTML = html;
      arrow.az = {
        board: board,
      };
      return arrow;
    };

    return {
      create,
    }
  }
})(jQuery);


