<?php

namespace Drupal\atomizer;

use Drupal\az_content\AzContentQuery;
use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Utility\Xss;
use Drupal\node\Entity\Node;

/**
 * Class AtomizerFiles.
 *
 * @package Drupal\atomizer
 */
class AtomizerInit {
  public static $idNum = 0;

  static function build($config) {
    $atomizerId = str_replace(['_', ' '], '-', strtolower($config['atomizerId'])) . '--' . self::$idNum;
    self::$idNum++;

// Read in the config/atomizer file
    if (empty($config['atomizer_configuration'])) {
      $atomizer_config = Yaml::decode(file_get_contents(
        drupal_get_path('module', 'atomizer') . '/config/producers/' . $config['atomizerFile']
      ));
      $atomizer_config['filename']   = $config['atomizerFile'];
      $atomizer_config['atomizerId'] = $atomizerId;
      $atomizer_config['atomizerClass'] = $config['atomizerId'];
      if (!empty($config['nid'])) {
        $atomizer_config['nid'] = $config['nid'];
      }
    } else { // When displaying an atomizer node override block settings,
             // read configuration from atomizer_config file - Set in atomizer_preprocess_node()
      $atomizer_config = $config['atomizer_configuration'];
    }

//  Read in the objects - nuclets for Atom Builder/Viewer - solids for Platonic Solids Viewer
    $objects = [];
    if (!empty($atomizer_config)) {
      if (!empty($atomizer_config['objects'])) {
        foreach ($atomizer_config['objects'] as $object) {
          $files = file_scan_directory(drupal_get_path('module', 'atomizer') . '/config/objects/' . $object, '/\.yml/');
          foreach ($files as $file) {
            $objects[str_replace('nuclet_', '', $file->name)] = Yaml::decode(
              file_get_contents(drupal_get_path('module', 'atomizer') . '/config/objects/' . $object . '/' . $file->filename)
            );
          }
        }
      }
    }

    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['az-wrapper'],
        'tabindex' => 1,
      ],
      '#cache' => [
        'max-age' => 0,
      ],
      'content' => [
        '#type' => 'container',
        '#attributes' => [
          'id' => 'azid-' . $atomizerId,
          'class' => ['az-atomizer-wrapper'],
        ],
      ],
    ];

    // Build the Header region - used for mobile only?

    $build['content']['header'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['az-header']],
      'sceneName' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['az-scene-name']],
      ],
      'selectAtom' => [
        '#type' => 'button',
        '#value' => 'Select Atom',
        '#attributes' => ['id' => 'atom-select-enable'],
      ],
    ];

    // Build atomizer container with html container - attach drupalSettings
    $build['content']['atomizer'] = [
      $atomizer_config['atomizerId'] => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['az-html-wrapper', $config['atomizerId'] . '-container'],
        ],
        'html-wrapper' => [
          '#markup' => '<div class="az-html"></div>',
        ],
        'html-popup' => [
          '#markup' => '<div class="az-popup"></div>',
        ],
        'labels' => [
          '#markup' => '<div class="az-html-labels">Title Overlay</div>',
        ],
        'copyright' => [
          '#markup' => '<div title="Copyright 2019 by EcoSleuth, LLC" class="az-fa-copyright copyright"></div>'
        ],
      ],
      '#attached' => [
        'drupalSettings' => [
          'atomizer' => [
            $atomizerId => $atomizer_config,
          ],
          'atomizer_config' => [
            'objects' => $objects,
          ],
        ],
      ],
    ];

    // Build the controls
    if (isset($config['controlFile'])) {
      $controlFile = $config['controlFile'];
    } else if (isset($config['controlFile'])) {
      $controlFile = $atomizer_config['controlFile'];
    }
    if (isset($controlFile)) {
      $controlSet = Yaml::decode(file_get_contents(drupal_get_path('module', 'atomizer') . '/config/controls/' . $controlFile));
      $controlSet['filename'] = $controlFile;

      // Create the controls form
      $controls['form'] = \Drupal::formBuilder()->getForm('Drupal\atomizer\Form\AtomizerControlsForm', $controlSet);
      $controls['form']['#attributes'] = [
        'class' => [
          'az-controls-form',
          str_replace(['_', '.'], '-', $atomizer_config['controlFile']),
        ]
      ];
      $controls['form']['#attached'] = [
        'drupalSettings' => [
          'atomizer' => [
            $atomizerId => [
              'atomizerId' => $atomizerId,
              'controlSet' => $controlSet,
              'theme' => $controls['form']['#az-theme'],
            ],
          ],
        ],
      ];

      $build['content']['controls'] = [
        '#type' => 'container',
//      '#weight' => -10,
        '#attributes' => ['class' => ['az-controls-wrapper']],
        'controls' => $controls,
      ];

    }

    if (!empty($atomizer_config['classes'])) {
      foreach ($atomizer_config['classes'] as $class) {
        $build['content']['#attributes']['class'][] = $class;
      }
    }

    // Attach the atomizer js libraries if we haven't already done it.
    $js_loaded = &drupal_static('atomizer_js_loaded', false);
    if (!$js_loaded) {
      $js_loaded = true;
      // Load raw JavaScript files if on host az or user has permission.

      $server = \Drupal::service('request_stack')->getCurrentRequest()->server->get('SERVER_NAME');
      if ($server == 'money' ||
          $server == 'az' ||
          \Drupal::currentUser()->hasPermission('atomizer load raw js')) {
        if (!empty($atomizer_config['librariesDev'])) {
          $build['#attached']['library'] = $atomizer_config['librariesDev'];
        } else {
          if (!empty($atomizer_config['libraries'])) {
            $build['#attached']['library'] = $atomizer_config['libraries'];
          }
        }
      }
      else { // else load the obfuscated JavaScript files
        if (!empty($atomizer_config['libraries'])) {
          $build['#attached']['library'] = $atomizer_config['libraries'];
        }
      }
    }

    return $build;
  }
}

