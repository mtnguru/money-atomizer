/**
 * File: az_episode.js
 *
 * Create the game board.  Give it it's own viewer, etc.monopoly like cards on the game board.
 */

(function ($) {

  Drupal.atomizer.episodeC = function (_viewer) {
    let viewer = _viewer;
    let episodeId;
    let episode;
    let act;
    let actNum;

    const loadP = (_episodeId) => {
      episodeId = _episodeId;
      let data = {
        component: 'episode',
        directory: `config/episodes/${episodeId}`,
        filename: 'episode.yml',
      };

      return new Promise (function (resolve, reject) {
        var p = viewer.base.requestP('loadYmlCommand', '/ajax/loadyml', data);
        p.then(function fulfilled(results) {
          episode = results.ymlContents;
          episode.keys = Object.keys(results.ymlContents.acts);
          resolve(episode);
        }).catch(function reject (error) {
          console.log(`ERROR: ${error}`);
          reject(error);
        });
      });
    };

    const startP = () => {
      // The episode
      // Step through each scene - or do we just start the first one?
      actNum = 0;
      let actId = episode.keys[actNum];
      let p1 = viewer.act.loadP(episodeId, actId);
      p1
        .then(function (_act) {
          // Save and start the episode
          act = _act;
          viewer.act.start(act);
        })
        .catch(function (err) {
          console.log(`ERROR - prod_episode:createScene:catch: ${err}`);
        });
    };

    const next = () => {
      let fart = 5;
    };

    return {
      loadP,
      startP,
      next,
      id: episodeId,
    }
  }
})(jQuery);


