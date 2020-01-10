/**
 * @file - az_base.js
 *
 * File that generates nuclets  proton, helium, lithium, helium - 1, 4, 7, 11
 */

(function ($) {

  Array.prototype.contains = function ( needle ) {
    for (var i in this) {
      if (this[i] == needle) return true;
    }
    return false;
  };

  Drupal.atomizer = {};

  Drupal.atomizer.logit = false;

  Drupal.atomizer.baseC = function () {

    Drupal.AjaxCommands.prototype.loadYmlCommand = function(ajax, response, status) {
      Drupal.atomizer[response.component].loadYml(response);
    };

    Drupal.AjaxCommands.prototype.saveYmlCommand = function(ajax, response, status) {
      Drupal.atomizer[response.component].saveYml(response);
    };

    Drupal.AjaxCommands.prototype.renderNodeCommand = function(ajax, response, status) {
      Drupal.atomizer[response.component].renderNode(response);
    };

    /**
     * Execute an AJAX command - fire callbacks on completion.
     * @param url
     * @param data
     * @param successCallback
     * @param errorCallback
     */
    var doAjax = function doAjax (url, data, successCallback, errorCallback) {
      $.ajax({
        url: url,
        type: 'POST',
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        processData: false,
        success: function (response) {
          if (Array.isArray(response) && response.length > 0) {
            if (response[0].data && response[0].data.message) {
              alert(response[0].data.message);
            }
            if (successCallback) successCallback(response);
          } else {
            if (errorCallback) {
              errorCallback(response);
            } else if (successCallback) {
              successCallback(response);
            }
          }
          return false;
        },
        error: function (response) {
          alert('atomizer_base doAjax: ' + response.responseText);
          if (errorCallback) {
            errorCallback(response);
          } else if (successCallback) {
            successCallback(response);
          }
        }
      });
    };

    const requestP = (command, url, data) => {
      return new Promise(function (resolve, reject) {
        $.ajax({
          url: url,
          type: 'POST',
          data: JSON.stringify(data),
          contentType: "application/json; charset=utf-8",
          processData: false,
          success: function (response) {
            if (Array.isArray(response) && response.length > 0) {
              for (let r = 0; r < response.length; r++) {
                if (response[r].command == command) {
                  // Somehow this detects an error?
                  if (response[r].data && response[r].data.message) {
                    alert(`base/requestP:WTF: ${response[r].data.message}`);
                  }
                  resolve(response[r]);
                }
              }
//            reject(`ERROR: Command not returned -- ${command}`);
            } else {
              reject(`ERROR: ${response}`);
            }
          },
          error: function (response) {
            alert('atomizer_base doAjax: ' + response.responseText);
            reject(response);
          }
        });
      });
    };

    /**
     * Execute an AJAX command - use a promise to return result.
     *
     * @param url
     * @param data
     * @param successCallback
     * @param errorCallback
     */
    function promiseAjax(url, data) {
      return new Promise(function(resolve, reject) {
        $.ajax({
          url: url,
          type: 'POST',
          data: JSON.stringify(data),
          contentType: "application/json; charset=utf-8",
          processData: false,
          success: function (response) {
            if (Array.isArray(response) && response.length > 0) {
              if (response[0].data && response[0].data.message) {
                alert(response[0].data.message);
              }
              resolve(response);
            } else {
              reject(response);
            }
          },
          error: function (response) {
            alert('atomizer_base doAjax: ' + response.responseText);
            reject(response);
          }
        });
      });
    }

    /**
     * Make an object - sphere, tetrahedron, line, etc.
     *
     * @param name
     * @param mat
     * @param geo
     * @param pos
     * @returns {*}
     */
    function makeObject(name, mat, shapeConf, pos, geo) {
      // Set the geometry
      var receiveShadow = false;
      if (!geo) {
        switch (name) {
          case 'plane':
            receiveShadow = true;
            geo = new THREE.PlaneGeometry(
              shapeConf.width || 1000,
              shapeConf.depth || 1000
            );
            break;
          case 'proton':
          case 'sphere':
            geo = new THREE.SphereGeometry(
              shapeConf.radius || viewer.theme.get('proton--radius'),
              shapeConf.widthSegments || 36,
              shapeConf.heightSegments || 36
            );
            break;
          case 'electron':
            geo = new THREE.SphereGeometry(
              shapeConf.radius || viewer.theme.get('electron--radius'),
              shapeConf.widthSegments || 20,
              shapeConf.heightSegments || 20
            );
            break;
          case 'octahedron':
            geo = new THREE.OctahedronGeometry(shapeConf.length || 3);
            break;
          case 'tetrahedron':
            geo = new THREE.TetrahedronGeometry(shapeConf.length || 3);
            break;
          case 'icosahedron':
            geo = new THREE.IcosahedronGeometry(shapeConf.length || 3);
            break;
          case 'dodecahedron':
            geo = new THREE.DodecahedronGeometry(shapeConf.length || 3);
            break;
          case 'cube':
            var l = shapeConf.length || 4;
            geo = new THREE.BoxGeometry(l, l, l);
            break;
          case 'pentagonalBipyramid':
            geo = createBiPyramid(5, shapeConf.length, shapeConf.height, 35);
            break;
        }
      }

      // Set the Mesh material
      var materials = [];
      if (mat.wireframe) {
        materials.push(new THREE.MeshBasicMaterial(mat.wireframe));
      }
      if (mat.lambert) {
        materials.push(new THREE.MeshLambertMaterial(mat.lambert));
      }
      if (mat.phong) {
        materials.push(new THREE.MeshPhongMaterial(mat.phong));
      }
      if (mat.doubleSided) {
        materials[0].side = THREE.DoubleSide;
      }

      // Create the object
      var object;
      if (materials.length == 1) {
        if (name == 'proton') {
//      object = new Physijs.SphereMesh(geo, materials[0]);
          object = new THREE.Mesh(geo, materials[0]);
        } else {
          object = new THREE.Mesh(geo, materials[0]);
        }
      } else {
        object = new THREE.SceneUtils.createMultiMaterialObject(geo, materials);
      }

      // Set scale
      if (shapeConf.scale) {
        object.scale.set(shapeConf.scale, shapeConf.scale, shapeConf.scale);
      }

      // Set rotation
      if (pos) {
        if (pos.rotation) {
          if (pos.rotation.x) { object.rotation.x = pos.rotation.x; }
          if (pos.rotation.y) { object.rotation.y = pos.rotation.y; }
          if (pos.rotation.z) { object.rotation.z = pos.rotation.z; }
        }

        // Position the object
        object.position.x = pos.x || 0;
        object.position.y = pos.y || 0;
        object.position.z = pos.z || 0;
      }

      object.name = name;

      return object;
    }

    /**
     * Align the axis of an object to another axis.
     *
     * @param object
     * @param objectAxis
     * @param finalAxis
     * @param negate
     */
    function alignObjectToAxis(object, objectAxis, finalAxis, negate) {
      // Find the rotation axis.
      var rotationAxis = new THREE.Vector3();
      rotationAxis.crossVectors( objectAxis, finalAxis ).normalize();

      // calculate the angle between the element axis vector and rotation vector
      var radians = Math.acos(objectAxis.dot(finalAxis) );

      // set the quaternion
      object.quaternion.setFromAxisAngle(rotationAxis, (negate) ? -radians : radians);
    }

    /*
    var mouseX,mouseY,windowWidth,windowHeight;
    var  popupLeft,popupTop;

      $(document).mousemove(function(e){
        mouseX = e.pageX;
        mouseY = e.pageY;
        //To Get the relative position
        if( this.offsetLeft !=undefined)
          mouseX = e.pageX - this.offsetLeft;
        if( this.offsetTop != undefined)
          mouseY = e.pageY; - this.offsetTop;

        if(mouseX < 0)
          mouseX =0;
        if(mouseY < 0)
          mouseY = 0;

        windowWidth  = $(window).width()+$(window).scrollLeft();
        windowHeight = $(window).height()+$(window).scrollTop();
      });

      $('html').click(function(){
        $('div').show();
        var popupWidth  = $('div').outerWidth();
        var popupHeight =  $('div').outerHeight();

        if(mouseX+popupWidth > windowWidth)
          popupLeft = mouseX-popupWidth;
        else
          popupLeft = mouseX;

        if(mouseY+popupHeight > windowHeight)
          popupTop = mouseY-popupHeight;
        else
          popupTop = mouseY;

        if( popupLeft < $(window).scrollLeft()){
          popupLeft = $(window).scrollLeft();
        }

        if( popupTop < $(window).scrollTop()){
          popupTop = $(window).scrollTop();
        }

        if(popupLeft < 0 || popupLeft == undefined)
          popupLeft = 0;
        if(popupTop < 0 || popupTop == undefined)
          popupTop = 0;

        $('div').offset({top:popupTop,left:popupLeft});
      });
    */

    function initDraggable($elem,name,initLeft,initTop) {
      /*
      var left;
      var top;
      var ww = window.innerWidth;
      var wh = window.innerHeight;
      var dw = ($elem.outerWidth() < 40) ? 200 : $elem.outerWidth();
      var dh = ($elem.outerHeight() < 40) ? 100 : $elem.outerHeight();
      if (localStorage[name]) {
        var pt = localStorage[name].split(",");
        left = parseInt(pt[0]);
        top  = parseInt(pt[1]);
        if (left + dw > ww) {
          left = ww - dw;
        }
        if (top + dh > wh) {
          top = wh - dh;
        }
      } else {
        left = initLeft;
        top  = initTop;
        if (initLeft < 0) {
          left = ww - dw + initLeft;
        }
        if (initTop < 0) {
          top = wh - dh + initTop;
        }
      }
      $.elem.css({'left': left,
        'top':  top});
      $.elem.draggable({
        stop: function(evt,ui) {
          var left = $(this).css('left');
          var top = $(this).css('top');
          if (parseInt(top) < 0)  top  = "0px";
          if (parseInt(left) < 0) left = "0px";
          localStorage[name] = left + ',' + top;
        }
      });
      */
    }

    function toRadians(degrees) {
      return degrees * (Math.PI / 180);
    }

    function toDegrees(radians) {
      return radians * (180 / Math.PI);
    }


    return {
      toDegrees,
      toRadians,
      doAjax,
      promiseAjax,
      requestP,
      alignObjectToAxis,
      initDraggable,
      makeObject,
      constants: {
        visibleThresh: .01,
        transparentThresh: .99
      }
    };

  };

})(jQuery);
