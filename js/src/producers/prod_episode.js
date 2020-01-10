/**
 * @file - prod_episode.js
 *
 * This 'producer' creates the view with a single atom for editing
 * with the atom_builder.
 */

(function ($) {

  Drupal.atomizer.prod_episodeC = function (atomizer) {

    var viewer = Drupal.atomizer.htmlViewerC(atomizer);
    let sceneNid;
    var $sceneName = $('.scene--name, .az-scene-name, .az-canvas-labels', viewer.context);


    /**
     * Callback for when an object has been loaded.
     *
     * @param object
     */
    var objectLoaded = function (object) {
      object.az.id = object.az.conf.id;

      if (object.az.conf.rotation) {
        object.rotation.x = parseInt(Drupal.atomizer.base.toRadians(object.az.conf.rotation[0]));
        object.rotation.y = parseInt(Drupal.atomizer.base.toRadians(object.az.conf.rotation[1]));
        object.rotation.z = parseInt(Drupal.atomizer.base.toRadians(object.az.conf.rotation[2]));
      }

      let objectShell = new THREE.Object3D();
      objectShell.name = 'objectShell';
      if (object.az.conf.position) {
        objectShell.position.set(...object.az.conf.position);
      }
      objectShell.add(object);

      addObject(object);

      // @TODO Cycle through list of directors.  All directors are registered as they start up.
      viewer.dir_molecule.objectLoaded(object);
      viewer.dir_atom.objectLoaded(object);

      if (object.az.conf.needsPosition) {
        grabObject(viewer.controls.mouse, object);
      }
    };

    /**
     * Create the view - add any objects to scene.
     *
     * @TODO Call the directors to do this.
     */
    let createScene = function () {

      // Add the trackball and page controls
//    viewer.controls = Drupal.atomizer.controlsC(viewer);

      // Initialize the viewer
      viewer.init();
//    viewer.controls.init();

      viewer.act              = Drupal.atomizer.actC(viewer);
      viewer.arrow            = Drupal.atomizer.arrowC(viewer);
      viewer.board            = Drupal.atomizer.boardC(viewer);
      viewer.base             = Drupal.atomizer.baseC(viewer);
      viewer.card             = Drupal.atomizer.cardC(viewer);
      viewer.episode          = Drupal.atomizer.episodeC(viewer);
      viewer.scene            = Drupal.atomizer.sceneC(viewer);
      viewer.set              = Drupal.atomizer.setC(viewer);
      viewer.step_webform     = Drupal.atomizer.step_webformC(viewer);
      viewer.step_render_node = Drupal.atomizer.step_render_nodeC(viewer);
      viewer.step_video       = Drupal.atomizer.step_videoC(viewer);

      // Read in the episode, this defines the sequence of events
      let p1 = viewer.episode.loadP('v1');
      p1
      .then(function (_episode) {
        // start the episode
        episode = _episode;
        viewer.episode.startP();
      })
      .catch(function (err) {
        console.log(`ERROR - prod_episode:createScene:catch: ${err}`);
      })
    };

    function getAtomizerGroup(atomizerClass) {
      if (atomizerClass == 'money-builder') {
        return 'money';
      }
      if (atomizerClass == 'atom-builder' ||
          atomizerClass == 'atom-viewer' ||
          atomizerClass == 'molecule-builder') {
        return 'atom';
      }
      return null;
    }

    const initMouse = () => {
      return;
      var $mouseBlock = $('#blocks--mouse-mode', viewer.context);
      var group = getAtomizerGroup(viewer.atomizer.atomizerClass);
      if ($mouseBlock.length) {
        viewer.controls.mouse.mode = $mouseBlock.find('input[name=mouse]:checked').val();
        // Add event listeners to mouse mode form radio buttons
        var $mouseRadios = $mouseBlock.find('#edit-mouse--wrapper input');
        $mouseRadios.click(function (event) {
          console.log('mode: ' + event.target.value);
          mouse.mode = event.target.value;
        });
      }
    };

    // Load the theme, createView is called back on completion
//  viewer.theme = Drupal.atomizer.themeC(viewer,createView);

    initMouse();
    createScene();


    return {
      createScene,
      objectLoaded,
    };
  };

})(jQuery);
