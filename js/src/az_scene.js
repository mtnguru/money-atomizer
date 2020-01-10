/**
 * File: az_scene.js
 *
 * Create the game board.  Give it it's own viewer, etc.monopoly like cards on the game board.
 */

(function ($) {

  Drupal.atomizer.sceneC = function (_viewer) {
    let scene;
    let sceneId;
    let step;
    let stepNum;
    let stepId;
    let viewer = _viewer;

    const loadP = (episodeId, actName) => {
      let data = {
        component: 'episode',
        directory: `config/episodes/${episodeId}/${actName}`,
        filename: 'act.yml',
      };

      return new Promise (function (resolve, reject) {
        var p = viewer.base.requestP('/ajax/loadyml', data);
        p.then(
          function (results) {
            episode = results[0].ymlContents;
            resolve(episode);
          },
          function (error) {
            console.log(`Error: ${error}`);
            reject(error);
          }
        );
      });
    };



    const setButtonEvents = (step) => {
      buttons = [];
      for (let id in step.buttons) {
        let button = step.buttons[id];
        button.element = document.getElementById(id);
        if (!button.element) {
          console.log(`Button not found - ${id}`);
          continue;
        }
        button.element.addEventListener("click", function (event) {
          event.preventDefault();
          let action = step.buttons[event.target.id].action || 'none';
          switch (action) {
            case 'next':
              if (scene.stepKeys.length > stepNum + 1) {
                stepNum++;
                step = scene.steps[scene.stepKeys[stepNum]];

                // Start the step
                viewer[`step_${step.type.replace('-', '_')}`].startP(step)
                  .then(
                    function () {
                      if (step.buttons) {
                        setButtonEvents(step);
                      }
                    },
                    function () {
                      console.log('Could not start step');
                    }
                  );
              } else {
                viewer.act.next();
              }
              break;

            default:
              throw(`Button action not found - $(action)`);
          }
        })
      }
    };

    /**
     * Start a scene
     *
     * @param scene
     * @returns {*}
     */
    const start = (_scene) => {
      // Display the set.
      scene = _scene;
      viewer.set.display(scene.set);
      scene.stepKeys = Object.keys(scene.steps);
      stepNum = 0;
      step = scene.steps[scene.stepKeys[stepNum]];

      // Start the step
      let stepClassName = `step_${step.type.replace('-', '_')}`;
      let stepClass = viewer[stepClassName];
      if (stepClass == null) {
        throw(`stepClass ${stepClassName} not found`);
      }
      viewer[`step_${step.type.replace('-', '_')}`].startP(step)
        .then(
          function () {
            if (step.buttons) {
              setButtonEvents(step);
            }
          },
          function () {
            console.log('Could not start step');
          }
        );
    };

    return {
      loadP,
      start,
      stepId,
      id: sceneId,
    }
  }
})(jQuery);


