/**
 * File: az_card.js
 *
 * Create the monopoly like cards on the game board.
 */

(function ($) {

  Drupal.atomizer.cardC = function (_board) {
    let viewer;
    let board = _board;

    const create = (id, conf, viewer) => {
      let html;

      let className = "card";
      // Create the card contents
      html = `<div class="${className}">`;
      for (let i in conf.objects) {
        let object = conf.objects[i];
         switch (object.type || i) {
           case 'header':
             html += `<div class="header"><h3>${object.label}</h3></div>`;
             break;

           case 'content':
             let rotate = `rotate-${object.rotation || 'none'}`;
             html += `<div class="content ${rotate}"><h3>${object.label}</h3></div>`;
             break;
         }
      }
      html += `</div>`;

      // Create the .card-wrapper div
      let card = document.createElement('div');
      let classes = ['card-wrapper'];
      if (conf.rotate) {
        classes.push(`rotate-${conf.rotate}`);
      }
      card.className = classes.join(' ');
      card.id = id.toLowerCase();
      card.innerHTML = html;

      let styles = [];

      // Position the card
      if (conf.position) {

        // Set top: and bottom:
        if (conf.anchor[0] == 'T') {
          styles.push('top:' + ` ${conf.position[1]}%;`);
          styles.push('bottom:' + ` ${100 - (conf.position[1] + conf.size[1])}%;`);
        } else if (conf.anchor[0] == 'B') {
          styles.push('bottom:' + ` ${conf.position[1]}%;`);
          styles.push('top:' + ` ${100 - (conf.position[1] + conf.size[1])}%;`);
        }

        // Set right: and left:
        if (conf.anchor[1] == 'L') {
          styles.push('left:' + ` ${conf.position[0]}%;`);
          styles.push('right:' + ` ${100 - (conf.position[0] + conf.size[0])}%;`);
        } else if (conf.anchor[0] == 'B') {
          styles.push('right:' + ` ${conf.position[0]}%;`);
          styles.push('left:' + ` ${100 - (conf.position[0] + conf.size[0])}%;`);
        }
      }

      card.style = styles.join(' ');
      card.az = {
        board: board,
      };


      return card;
    };

    return {
      create,
    }
  }
})(jQuery);


