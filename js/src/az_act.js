/**
 * File: az_act.js
 *
 * Create the game board.  Give it it's own viewer, etc.monopoly like cards on the game board.
 */

(function ($) {

  Drupal.atomizer.actC = function (_viewer) {
    let viewer = _viewer;
    let act;
    let actId;
    let scene;
    let sceneNum;

    const loadP = (episodeId, _actId) => {
      actId = _actId;
      let data = {
        component: 'act',
        directory: `config/episodes/${episodeId}/${actId}`,
        filename: 'act.yml',
      };
      return new Promise (function (resolve, reject) {
        var p = viewer.base.requestP('loadYmlCommand', '/ajax/loadyml', data);
        p.then(function fulfilled(results) {
          act = results.ymlContents.act;
          act.sceneKeys = Object.keys(act.scenes);
          resolve(act);
        }).catch(function reject (error) {
          console.log(`ERROR: ${error}`);
          reject(error);
        });
      });
    };

    const next = () => {
      if (act.sceneKeys.length > sceneNum + 1) {
        sceneNum++;
        scene = act.scenes[act.sceneKeys[sceneNum]];
        viewer.scene.start(scene);
      } else {
        viewer.episode.next();
      }
    };

    const start = (act) => {
      sceneNum = act.firstScene || 0;
      scene = act.scenes[act.sceneKeys[sceneNum]];
      viewer.scene.start(scene);
    };

    return {
      loadP,
      next,
      start,
      id: actId,
    }
  }
})(jQuery);


