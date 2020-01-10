/**
 * @file - az_atom.js
 *
 * Manage atom's - load, save, create
 */

(function ($) {

  Drupal.atomizer.birkelandC = function (_viewer) {

//  labels = [
//      [X, "Chart Label",jz,jθ,Delta x,"Vector Angle",Magnitude,"Assymp ch"],
//      [x, "fig. 1", J0,J1,0.2,"°cw from +z", "of j vector,k/sqrt(x)"]
//  ];

    // Bessel numbers - X, chrt pt, j0, J1
    // Pt
    // J0
    // J1
    // Vector Angle - cw from +z
    // Magnitude of J vector
    // Assymp Chk - k/sqrt(x)
    var bessel = {
      0.1:  [1,     1.000,  0.000,   0, 1.00, null],
      0.2:  [null,  0.990,  0.100,   6, 1.00, 1.82],
      0.4:  [null,  0.960,  0.196,  12, 0.98, 1.29],
      0.6:  [null,  0.912,  0.287,  17, 0.96, 1.05],
      0.8:  [null,  0.846,  0.369,  24, 0.92, 0.91],
      1:    [null,  0.765,  0.440,  30, 0.88, 0.82],
      1.2:  [null,  0.671,  0.498,  37, 0.84, 0.74],
      1.4:  [null,  0.567,  0.542,  44, 0.78, 0.69],
      1.6:  [null,  0.455,  0.570,  51, 0.73, 0.64],
      1.8:  [null,  0.340,  0.582,  60, 0.67, 0.61],
      2:    [null,  0.224,  0.577,  69, 0.62, 0.58],
      2.2:  [null,  0.110,  0.556,  79, 0.57, 0.55],
      2.4:  [2,     0.003,  0.520,  90, 0.52, 0.53],
      2.6:  [null, -0.097,  0.471, 102, 0.48, 0.51],
      2.8:  [null, -0.185,  0.410, 114, 0.45, 0.49],
      3:    [null, -0.260,  0.339, 127, 0.43, 0.47],
      3.2:  [null, -0.320,  0.261, 141, 0.41, 0.46],
      3.4:  [null, -0.364,  0.179, 154, 0.41, 0.44],
      3.6:  [null, -0.392,  0.095, 166, 0.40, 0.43],
      3.8:  [3,    -0.403,  0.013, 178, 0.40, 0.42],
      4:    [null, -0.397, -0.066, 189, 0.40, 0.41],
      4.2:  [null, -0.377, -0.139, 200, 0.40, 0.40],
      4.4:  [null, -0.342, -0.203, 211, 0.40, 0.39],
      4.6:  [null, -0.296, -0.257, 221, 0.39, 0.38],
      4.8:  [null, -0.240, -0.298, 231, 0.38, 0.37],
      5:    [null, -0.178, -0.328, 242, 0.37, 0.36],
      5.2:  [null, -0.110, -0.343, 252, 0.36, 0.36],
      5.4:  [null, -0.041, -0.345, 263, 0.35, 0.35],
      5.6:  [4,     0.027, -0.334, 275, 0.34, 0.34],
      5.8:  [null,  0.092, -0.311, 286, 0.32, 0.34],
      6:    [null,  0.151, -0.277, 299, 0.32, 0.33],
      6.2:  [null,  0.202, -0.233, 311, 0.31, 0.33],
      6.4:  [null,  0.243, -0.182, 323, 0.30, 0.32],
      6.6:  [null,  0.274, -0.125, 335, 0.30, 0.32],
      6.8:  [null,  0.293, -0.065, 347, 0.30, 0.31],
      7:    [5,     0.300, -0.005,  -1, 0.30, 0.31],
      7.2:  [null,  0.295,  0.054,  10, 0.30, 0.30],
      7.4:  [null,  0.279,  0.110,  21, 0.30, 0.30],
      7.6:  [null,  0.252,  0.159,  32, 0.30, 0.30],
      7.8:  [null,  0.215,  0.201,  43, 0.29, 0.29],
      8:    [null,  0.172,  0.235,  54, 0.29, 0.29],
      8.2:  [null,  0.122,  0.258,  65, 0.29, 0.28],
      8.4:  [null,  0.069,  0.271,  76, 0.28, 0.28],
      8.6:  [6,     0.015,  0.273,  87, 0.27, 0.28],
      8.8:  [null, -0.039,  0.264,  98, 0.27, 0.27],
      9:    [null, -0.090,  0.245, 110, 0.26, 0.27],
      9.2:  [null, -0.137,  0.217, 122, 0.26, 0.27],
      9.4:  [null, -0.177,  0.182, 134, 0.25, 0.27],
      9.6:  [null, -0.209,  0.140, 146, 0.25, 0.26],
      9.8:  [null, -0.232,  0.093, 158, 0.25, 0.26],
     10:    [null, -0.246,  0.043, 170, 0.25, 0.26],
     10.2:  [7,    -0.250, -0.007, 182, 0.25, 0.26],
     10.4:  [null, -0.243, -0.055, 193, 0.25, 0.25],
     10.6:  [null, -0.228, -0.101, 204, 0.25, 0.25],
     10.8:  [null, -0.203, -0.142, 215, 0.25, 0.25]
    };

    var viewer = _viewer;
    var cylinders = {};
    var points;

    var loadCallback;

    /**
     * Execute AJAX command to load a new birkeland current.
     *
     * @param nid
     */
    var loadObject = function loadObject (conf, callback) {
      loadCallback = callback;
      Drupal.atomizer.base.doAjax(
        '/ajax/loadNode',
        {conf},
        doCreateBirkeland
      );
    };

    /**
     * Callback from ajax call to load and display a birkeland current.
     *
     * @param results
     */
    var doCreateBirkeland = function doCreateBirkeland (results) {
      for (var i = 0; i < results.length; i++) {
        var result = results[i];
        if (result.command == 'loadDataCommand') {
          if (viewer.scene.az && viewer.scene.az.bc) {
            // Remove any birkeland current's currently displayed
            deleteScene(bc);
            viewer.scene.az.bc = null;
          }

          var bc = createBirkeland(result.data.nodeConf);
          bc.az = {
            nid: result.data.nid,
            name: result.data.nodeName,
            title: result.data.nodeTitle,
            conf: result.data.conf,
          };

          // Move bc position
          if (viewer.dataAttr['object--position--x']) {
            if (!object.position) object.position = new THREE.Vector3();
            bc.position.x = viewer.dataAttr['bc--position--x'];
          }
          if (viewer.dataAttr['bc--position--y']) {
            if (!bc.position) bc.position = new THREE.Vector3();
            bc.position.y = viewer.dataAttr['bc--position--y'];
          }
          if (viewer.dataAttr['bc--position--z']) {
            if (!bc.position) bc.position = new THREE.Vector3();
            bc.position.z = viewer.dataAttr['bc--position--z'];
          }

          // Rotate bc
          if (viewer.dataAttr['bc--rotation--x']) {
            if (!bc.rotation) bc.rotation = new THREE.Vector3();
            bc.rotation.x = Drupal.atomizer.base.toRadians(viewer.dataAttr['bc--rotation--x']);
          }
          if (viewer.dataAttr['bc--rotation--y']) {
            if (!bc.rotation) bc.rotation = new THREE.Vector3();
            bc.rotation.y = Drupal.atomizer.base.toRadians(viewer.dataAttr['bc--rotation--y']);
          }
          if (viewer.dataAttr['bc--rotation--z']) {
            if (!bc.rotation) bc.rotation = new THREE.Vector3();
            bc.rotation.z = Drupal.atomizer.base.toRadians(viewer.dataAttr['bc--rotation--z']);
          }
          viewer.producer.objectLoaded(bc);

          if (loadCallback) {
            loadCallback();
          }
        }
      }
    };

    function makeCylinder(radius, height, color, opacity) {
      var cylinderGeometry = new THREE.CylinderGeometry(radius, radius, height, 50, 10, true);
      var cylinderMaterial = new THREE.MeshLambertMaterial({
        color: color,
        transparent: true,
        visible: true,
        opacity: opacity
      });
      cylinderMaterial.side = THREE.DoubleSide;
      cylinder = new THREE.Mesh(cylinderGeometry, cylinderMaterial);
      return cylinder;
    }

    /**
     * Create a birkeland current
     *
     * @param conf
     * @returns {THREE.Group|*}
     */
    var createBirkeland = function createBirkeland (conf) {
      var parms;
      var ptsGeometry;  // Geometry with all points for all arrows
      var ptsMaterial;
      var cyl;
      var ptConf;

      function plotParticle(a) {
        var radius = parms.radius * parms.radiusZoom + 5;
        // Calculate the rotate distance in degrees and the z distance
        switch (parms.markerType) {
          case 'arrows':
            ptConf.y += parms.length / parms.numArrows;
            // Calculate the rotate distance in degrees and the z distance

            var point;
            var y = ptConf.y;
            var theta = ptConf.radians;
            for (var p = 0; p < parms.markerPts; p += parms.markerSize) {
              // Create a point
              var x = radius * Math.sin(theta);
              var z = radius * Math.cos(theta);

              point = new THREE.Vector3(x, y, z);
              point = movePoint(cyl, point, 0);
              ptsGeometry.vertices.push(point);

              theta += parms.thetaStep;
              y += parms.yStep;
            }
            break;

          case 'particles':
            ptConf.y += parms.length / parms.numArrows;
            point = new THREE.Vector3(ptConf.x, ptConf.y, ptConf.z);
            point = movePoint(cyl, point, 0);
            ptsGeometry.vertices.push(point);
            break;

          case 'lines':
            var point;
            var y = ptConf.y += parms.length / parms.numArrows;
            var theta = ptConf.radians;
            var geometry = new THREE.Geometry();
            for (var p = 0; p < parms.markerPts; p += parms.markerSize) {
              // Create a point
              var x = radius * Math.sin(theta);
              var z = radius * Math.cos(theta);

              point = new THREE.Vector3(x, y, z);
//            point = moveSegment(cyl, point, 0);
              geometry.vertices.push(point);

              theta += parms.thetaStep;
              y += parms.yStep;
            }

            var line = new THREE.Line( geometry, ptsMaterial );
            return line;
        }
      } // function plotParticle

      // Create the birkeland current group/wrapper
      var bc = new THREE.Group();
      if (conf.object.rotation) {
        bc.rotation = new THREE.Vector3();
        bc.rotation.set(
          (conf.object.rotation.x) ? Drupal.atomizer.base.toRadians(conf.object.rotation.x) : 0,
          (conf.object.rotation.y) ? Drupal.atomizer.base.toRadians(conf.object.rotation.y) : 0,
          (conf.object.rotation.z) ? Drupal.atomizer.base.toRadians(conf.object.rotation.z) : 0
        );
      }
      // Add the birkeland current to the scene.
      viewer.scene.add(bc);

      // Create each cylinder
      bc.cylinders = {};
      for (var cylinder in conf.cylinders) {
        if (cylinder == 'defaults') continue;

        // Merge parms - defaults and this cylinder
        parms = [];
        for (var parm in conf.cylinders.defaults) {
          parms[parm] = conf.cylinders.defaults[parm];
        }
        for (var parm in conf.cylinders[cylinder]) {
          parms[parm] = conf.cylinders[cylinder][parm];
        }

        var radius = parms.radius * parms.radiusZoom;

        // Create the geometry and material for this cylinder
        ptsGeometry = new THREE.Geometry();
        ptsMaterial = new THREE.PointsMaterial({
          size: parms.markerSize,
          sizeAttenuation: false,
          opacity: viewer.theme.get('particles-' + cylinder + '--opacity'),
          transparent: true,
          visible: true,
          color: parms.color
        });

        conf.color = parms.color;

        // Create the cylinder
        cyl = makeCylinder(
          radius,
          parms.length + 20,
          parms.color,
          viewer.theme.get('cylinders-' + cylinder + '--opacity')
        );

        cyl.name = 'cylinders-' + cylinder.replace(' ', '_');
        cyl.conf = parms;
        bc.add(cyl);
        bc.cylinders[cylinder] = cyl;

        // Initialize variables for this cylinder.
        cyl.conf.circumference = 2 * Math.PI * radius;
        cyl.conf.J0 = bessel[parms.radius][1];  // vector down the Z axis
        cyl.conf.J1 = bessel[parms.radius][2];  // total rotation distance in 'units
        cyl.conf.theta = cyl.conf.J1 * parms.markerLength / cyl.conf.circumference * 6.28;  // total rotation in theta
        cyl.conf.thetaStep = cyl.conf.theta / parms.markerPts; // Calculate step in theta
        cyl.conf.yStep = cyl.conf.J0 * parms.markerLength / parms.markerPts;        // Calculate step in 'y'
        cyl.conf.spacing = parms.length / (parms.numArrows);

        // Calculate the degrees rotation and plot each arrow path.
        var rotate = 6.28 / parms.numArrowPaths;
        var random = viewer.theme.get('particles--random');
        ptConf = {};

        // Create arrows for each arrowPath
        for (var path = 0; path < parms.numArrowPaths; path++) {
          var radians = path * rotate;
          var y = -parms.length / 2;

          // Create each of the arrows
          var stagger = 0;
          for (var a = 0; a < parms.numArrows; a++) {
            if (random) {
              ptConf.radians = radians + .75 * cyl.conf.theta * Math.random() * Math.random();
              ptConf.y = y + .85 * cyl.conf.spacing * Math.random() * Math.random();
            } else {
              ptConf.radians = radians;
              ptConf.y = y + .85 * cyl.conf.spacing * Math.random();
              if (++stagger == 3) {
                stagger = -2;
              }
            }
            var particle = plotParticle(a);
            if (parms.markerType == 'lines') {
              cyl.add(particle);
            }
            y += cyl.conf.spacing
          }
        }

        if (parms.markerType == 'arrows') {
          points = new THREE.Points(ptsGeometry, ptsMaterial);
          points.name = 'particles-' + cylinder.replace(' ', '_');
          cyl.add(points);
        }
      }

      extrudeCylinders(bc, viewer.theme.get('cylinders--extrude'));

      viewer.render();
      return bc;
    };

    /**
     * Extrude the cylinders
     * @param val
     */
    function extrudeCylinders (bc, val) {
      var cn = 0;
      for (var c in bc.cylinders) {
        if (bc.cylinders.hasOwnProperty(c)) {
          bc.cylinders[c].position.y = val * cn;
          cn++;
        }
      }
    }

    function movePoint (cyl, point, distance) {
      point.y += distance;
      if (distance >= 0) {
        if (point.y > cyl.conf.length / 2) {
          point.y -= cyl.conf.length;
        }
      } else {
        if (point.y < -cyl.conf.length / 2) {
          point.y += cyl.conf.length;
        }
      }
      return point;
    }

    function moveSegment (cyl, point, distance) {
      point.y += distance;

      if (distance >= 0) {
        if (point.hidden) {
          point.hidden = false;
          point.x = point.cx;
          point.z = point.cz;
        } else {
          if (point.y > cyl.conf.length / 2) {
            point.cx = point.x;
            point.cz = point.z;
            point.y -= cyl.conf.length;
            point.hidden = true;
            point.x = null;
            point.z = null;
          }
        }
      } else {
        if (point.y < -cyl.conf.length / 2) {
          point.y += cyl.conf.length;
        }
      }
      return point;
    }

    let animateCycle = 0;
    function animate(animateConf) {
      let speed = viewer.theme.get('animation--speed') / 2;
      let smooth = viewer.theme.get('animation--smooth');
      animateCycle = (animateCycle > 5 - smooth) ? 0 : animateCycle + 1;
      let cn = 0;
      for (var c in viewer.scene.az.bc.cylinders) {
        if (cn++ % 2 != animateCycle) continue;
        var cyl = viewer.scene.az.bc.cylinders[c];
        if (cyl) {
          var conf = cyl.conf;
          switch (cyl.conf.markerType) {
            case 'arrows':
              var vertices = cyl.children[0].geometry.vertices;
              cyl.children[0].geometry.verticesNeedUpdate = true;
              for (var v = 0; v < vertices.length; v++) {
                vertices[v] = movePoint(cyl, vertices[v], conf.yStep * speed);
              }
              break;

            case 'lines':
              for (var p = 0; p < cyl.children.length; p++) {
                var particle = cyl.children[p];
                particle.geometry.verticesNeedUpdate = true;
                var vertices = particle.geometry.vertices;
                for (var v = 0; v < vertices.length; v++) {
                  vertices[v] = moveSegment(cyl, vertices[v], conf.yStep * speed);
                }
              }
              break;
          }
          cyl.rotation.y += conf.thetaStep * speed;
        }
      }
      viewer.render();
    }

    /**
     * Delete a birkeland current
     * @param bc
     */
    var deleteScene = function deleteScene (bc) {
      viewer.scene.remove(bc);
    };

    /**
     * User pressed a button on the main form - act on it.
     *
     * @param id
     */
    var buttonClicked = function buttonClicked(event) {
      if (event.target.id == 'atom--select') {
        $(viewer.context).toggleClass('select-atom-enabled');
      }
    };

    // Set up event handler when user closes atom-select button
    $('.atom--select-close').click(function () {
      $(viewer.context).removeClass('select-atom-enabled');
    });

    /**
     * Interface to this birkelandC.
     */
    return {
      az: function () { return atom.az; },
      buttonClicked: buttonClicked,
      createBirkeland: createBirkeland,
      deleteScene: deleteScene,
      extrudeCylinders: extrudeCylinders,
      loadObject: loadObject,
      animate: animate
    };
  };

})(jQuery);
