<?php

namespace Drupal\atomizer;

use Drupal\core\Url;
use Drupal\Component\Serialization\Json;
// use Drupal\Component\Serialization\Yaml;
//use Drupal\atomizer\AtomizerFiles;

/**
 * Class AtomizerControlBlock.
 *
 * @package Drupal\atomizer
 */
class AtomizerControlBlock {

  static private function makeOpacityControl($id, $name, $defaultValue, $min, $max, $step) {
    $opacityClass = 'az-opacity';
    $opacityId = $id . '--' . $opacityClass;
    $colorClass = 'az-color';
    $colorId = $id . '--' . $colorClass;
    $sliderClass = 'az-slider';
    $sliderId = $id . '--'  . $sliderClass;
    $valueClass = 'az-value';
    $valueId = $id . '--' . $valueClass;
    return [
      '#type' => 'container',
      '#attributes' => [
        'id' => $opacityId,
        'class' => [$opacityClass, $opacityId, 'az-opacity'],
      ],
      'title' => [
        '#markup' => '<div class="az-name">' . $name . '</div>'
      ],
      'color' => [
        '#type' => 'container',
        '#attributes' => [
          'id' => $colorId,
          'class' => [$colorClass, 'opacity--color'],
        ],
      ],
      'value' => [
        '#type' => 'textfield',
        '#default_value' => $defaultValue,
        '#attributes' => [
          'id' => $valueId,
          'class' => [$valueClass, 'opacity--value'],
        ],
      ],
      'range' => [
        '#type' => 'range',
        '#default_value' => $defaultValue,
        '#min' => $min,
        '#max' => $max,
        '#step' => $step,
        '#attributes' => [
          'id' => $sliderId,
          'class' => [$sliderClass, 'opacity--slider'],
        ],
      ],
    ];
  }

  static private function makeRangeControl($id, $name, $defaultValue, $min, $max, $step) {
    $sliderClass = 'az-slider';
    $sliderId = $id . '--'  . $sliderClass;
    $valueClass = 'az-value';
    $valueId = $id . '--' . $valueClass;

    return [
      '#type' => 'container',
      'title' => [
        '#markup' => '<div class="az-name">' . $name . '</div>'
      ],
      'value' => [
        '#type' => 'textfield',
        '#default_value' => $defaultValue,
        '#attributes' => [
          'id' => $valueId,
          'class' => [$valueClass, $valueId],
        ],
      ],
      'range' => [
        '#type' => 'range',
        '#default_value' => $defaultValue,
        '#min' => $min,
        '#max' => $max,
        '#step' => $step,
        '#attributes' => [
          'id' => $sliderId,
          'class' => [$sliderClass, $sliderId],
        ],
      ],
    ];
  }

  static function makeControl($controlName, $controlConf, &$theme) {
//  $id = str_replace('_', '-', $controlName);
    $id = $controlName;
    $control = [];
    $containerClasses = ['az-control', 'az-control-' . $controlConf[1]];
    $defaultValue = (isset($controlConf[2])) ? $controlConf[2] : '';
    $addValue = false;
    switch ($controlConf[1]) {

      case 'selectyml':

        $fileList = AtomizerFiles::createFileList(drupal_get_path('module', 'atomizer') . '/' . $controlConf[4], '/\.yml/');
        $control = [
          '#type' => 'container',
          '#attributes' => ['class' => ['selectyml']],
          'selectyml' => [
            '#type' => 'select',
            '#title' => $controlConf[0],
            '#default_value' => $controlConf[2],
            '#attributes' => [
              'id' => $id . '--select',
              'class' => [$id . '--select'],
            ],
            '#options' => $fileList,
          ],
          /*          'overwrite_button' => array(
                        '#type' => 'item',
                        '#prefix' => '<div id="' . $id . '--button" class="button-wrapper">',
                        '#suffix' => '</div>',
                        '#markup' => t('Overwrite'),
                      ), */
          /*          'overwrite_message' => array(
                        '#type' => 'item',
                        '#prefix' => '<div id="' . $id . '--message" class="message-wrapper">',
                        '#suffix' => '</div>',
                        '#markup' => t(''),
                      ) */
        ];
        $containerClasses[] = 'selectyml';
        if (count($fileList) <= 1) {
          $containerClasses[] = 'az-hidden';
        }
        $addValue = true;
        break;

      case 'saveyml':
        $control = [
          '#type' => 'container',
          '#attributes' => ['class' => ['saveyml']],
          /*          'name' => array(
                        '#type' => 'textfield',
                        '#maxlength' => 24,
                        '#title' => 'Name',
                      ), */
          /*          'filename' => array(
                        '#type' => 'textfield',
                        '#maxlength' => 16,
                        '#title' => 'File name',
                        '#description' => t('Only characters, numbers, and underscores allowed - Leave blank to have it automatically generated.'),
                      ), */
          /*          'description' => array(
                        '#type' => 'textarea',
                        '#maxlength' => 30,
                        '#title' => 'Description',
                        '#maxlength' => 64,
                        '#rows' => 4,
                      ), */
          'save_button' => [
            '#type' => 'item',
            '#prefix' => '<div class="' . $id . ' button-wrapper">',
            '#suffix' => '</div>',
            '#markup' => t('Save Atom'),
          ],
          'overwrite_message' => [
            '#type' => 'item',
            '#prefix' => '<div class="' . $id . ' message-wrapper">',
            '#suffix' => '</div>',
            '#markup' => t(''),
          ]
        ];
        $addValue = true;
        break;

      case 'link':
        if (empty($controlConf[4])) {
          $controlConf[4] = [];
        }
        $control = [
          '#type' => 'container',
          'link' => [
            '#type' => 'link',
            '#title' => $controlConf[0],
            '#url' => Url::fromRoute($controlConf[3], $controlConf[4]),
            '#attached' => ['library' => ['core/drupal.dialog.ajax']],
            '#attributes' => [
              'class' => ['use-ajax'],
              'data-dialog-type' => $controlConf[2],
              'data-dialog-options' => Json::encode([
                'dialogClass' => 'az-dialog az-atom-form',
                'width' => '700px',
                'draggable' => TRUE,
                'autoResize' => FALSE,
                'position' => [
                  'my' => 'center top-20',
                  'at' => 'center top'
                ],
              ]),
            ],
          ],
        ];
        break;

      case 'button':
      case 'text-button':
      case 'snapshot':
      case 'popup-node':
        $control = [
          '#type' => 'button',
          '#value' => $controlConf[0],
        ];
        if ($controlConf[1] == 'popup-node') {
          $control['popup'] = [
            '#type' => 'container',
            '#attributes' => ['class' => ['az-popup']],
          ];
        }
        $addValue = true;
        break;

      case 'toggle':
      case 'toggleDialog':
        $control = [
          '#type' => 'button',
          '#value' => $controlConf[0],
        ];
        if (!empty($controlConf[2]['font-awesome'])) {
          $control = [
            '#type' => 'container',
            '#attributes' => [
              'title' => $controlConf[0],
            ],
          ];
          $containerClasses[] = 'fa';
          $containerClasses[] = $controlConf[2]['font-awesome'];
          $addValue = TRUE;
        }
        break;

      case 'checkbox':
        $control = [
          '#type' => 'checkbox',
//          '#attributes' => array('onclick' => 'return (false);'),
          '#title' => $controlConf[0],
          '#default_value' => $defaultValue,
        ];
        $addValue = true;
        break;

      case 'checkboxes':
        $options = [];
        $defaults = [];
        foreach ($controlConf[2] as $key => $conf) {
          $options[$key] = $conf[0];
          if ($conf[1]) {
            $defaults[] = $key;
          }
        };
        $control = [
          '#type' => 'checkboxes',
          '#title' => $controlConf[0],
          '#options' => $options,
          '#default_value' => $defaults,
        ];
        $addValue = true;
        break;

      case 'range':
        $control = AtomizerControlBlock::makeRangeControl(
          $id,
          $controlConf[0],
          $defaultValue,
          $controlConf[3][0],
          $controlConf[3][1],
          $controlConf[3][2]
        );
        $addValue = true;
        break;

      case 'opacity':
      case 'opacityMaster':
        $control = AtomizerControlBlock::makeOpacityControl(
          $id,
          $controlConf[0],
          $defaultValue,
          $controlConf[3][0],
          $controlConf[3][1],
          $controlConf[3][2]
        );
        $addValue = true;
        break;

      case 'radios':
        $control = [
          '#type' => 'radios',
          '#title' => $controlConf[0],
          '#default_value' => $defaultValue,
          '#options' => $controlConf[3],
        ];
        if (!empty($controlConf[4]['directory'])) {
          $control['#attributes']['data-directory'] = $controlConf[4]['directory'];
        }
//      // Hide the control -- #proton--color--style
//      // @TODO - make this general and apply to all types
//      if (!empty($controlConf[4])) {
//        if (!empty($controlConf[4]['hidden'])) {
//          $containerClasses[] = 'az-hidden';
//        }
//      }
        $addValue = true;
        break;

      case 'select':
        $control = [
          '#type' => 'select',
          '#title' => $controlConf[0],
          '#default_value' => $defaultValue,
          '#options' => $controlConf[3],
        ];
        $addValue = true;
        break;

      case 'selectblock':
        $control = [
          '#type' => 'select',
          '#title' => $controlConf[0],
          '#default_value' => $defaultValue,
        ];
        $addValue = true;
        break;

      case 'rotation':
      case 'position':
        $control = [
          '#type' => 'container',
          '#attributes' => ['class' => ['multi-slider']],
          'title' => [
            '#markup' => '<div class="az-label">' . $controlConf[0] . '</div>',
          ],
        ];
        foreach (['x', 'y', 'z'] as $ind => $axis) {
          $control[$id . '--' . $axis] = AtomizerControlBlock::makeRangeControl(
            $id . '--' . $axis,
            strtoupper($axis),
            $controlConf[2][$ind],
            $controlConf[3][0],
            $controlConf[3][1],
            $controlConf[3][2]
          );
          $control[$id . '--' . $axis]['#attributes']['class'][] = 'az-indent';
          $theme[$id . '--' . $axis] = [
            'type' => $controlConf[1],
            'setting' => $defaultValue[$ind],
          ];
        }
        break;

      case 'color':
        $control = [
          '#type' => 'color',
          '#title' => $controlConf[0],
          '#default_value' => $defaultValue,
        ];
        $addValue = true;
        break;

      case 'textfield':
        $control = [
          '#type' => 'textfield',
          '#title' => $controlConf[0],
          '#maxlength' => $controlConf[3],
        ];
        $addValue = true;
        break;

      case 'html':
        $control = [
          '#type' => 'item',
          '#title' => $controlConf[0],
          'value' => [
            '#type' => 'item',
            '#markup' => $controlConf[2],
          ],
        ];
        $addValue = true;
        break;

      case 'textarea':
        $control = [
          '#type' => 'textarea',
          '#title' => 'Description',
          '#maxlength' => $controlConf[3],
          '#rows' => $controlConf[4],
        ];
        $addValue = true;
        break;

      case 'text':
        $control = [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['text-wrapper'],
          ],
          'label' => ['#markup' => "<div class='text-label " . $id . "--label'>$controlConf[0]</div>"],
          'value' => ['#markup' => "<div class='text-value " . $id . "--value'>$controlConf[2]</div"],
        ];
        break;

      case 'number':
        $control = [
          '#type' => 'number',
          '#title' => $controlConf[0],
          '#default_value' => $controlConf[2],
          '#min' => $controlConf[3][0],
          '#max' => $controlConf[3][1],
          '#maxlength' => $controlConf[3][2],
        ];
        $addValue = true;
        break;

      case 'container':
        $control = ['#type' => 'container'];
        $addValue = true;
        break;

      case 'header':
        if (!empty($controlConf[2])) {
          $control = ['#markup' => "<div class='az-block-header'><i class='fas $controlConf[2]'></i> $controlConf[0]</div>"];
        } else {
          $control = ['#markup' => "<div class='az-block-header'>$controlConf[0]</div>"];
        }
        break;

      case 'label':
        $control = ['#markup' => "<div class='az-label $controlName'>$controlConf[0]</div>"];
        break;

      case 'hr':
        $control = ['#markup' => '<hr>'];
        break;

      default:
        break;
    }

    if (!empty($controlConf[2]) && is_array($controlConf[2])) {
      foreach ($controlConf[2] as $key => $value) {
        if (preg_match('/^data\-/', $key)) {
          $control['#attributes'][$key] = $value;
          if ($key == 'data-blockid') {
            $containerClasses[] = 'toggle-block';
          }
        } else if ($key == 'class') {
          $containerClasses[] = $value;
        } else if ($key == 'sound-file') {
          $containerClasses[] = 'sound-file';
          if ($wrapper = \Drupal::service('stream_wrapper_manager')->getViaUri('public://atomizer/' . $value)) {
            $fileUrl = $wrapper->getExternalUrl();
            //              $filepath = \Drupal::service('file_system')->realpath(file_default_scheme() . "://") . '/atomizer/' . $value;
          }

          //            $filepath = \Drupal::service('file_system')->realpath(file_default_scheme() . "://") . '/atomizer/' . $value;
          $soundId = str_replace ('.', '-', $value);
          $control['sound_file'] = [
            '#type' => 'inline_template',
            '#template' => '{{ embed_sound|raw }}',
            '#context' => [
              //                'embed_sound' => '<embed src="' . $filepath . '" autostart="false" width="0" height="0" id="' . $soundId . '" enablejavascript="true">',
              'embed_sound' => '<audio src="' . $fileUrl . '" preload></audio>',
            ],
          ];
        } else if ($key == 'font-awesome') {
          $control = [
            '#type' => 'container',
            '#attributes' => [
              'title' => $controlConf[0],
            ],
          ];
          if (!empty($controlConf[2]['label'])) {
            $control['label'] = ['#markup' => $controlConf[2]['label']];
          }
          $containerClasses[] = 'az-button';
          $containerClasses[] = 'fa';
          $containerClasses[] = $controlConf[2]['font-awesome'];
        }
      }
    }
    if ($addValue) {
      $theme[$id] = [
        'type' => $controlConf[1],
        'setting' => $defaultValue,
      ];
    }
    $containerClasses[] = $id;
    $control['#attributes']['name'] = $id;
    $control['#attributes']['class'] = $containerClasses;
    $control['#attributes']['id'] = $id;

    return $control;
  }

  /**
   * @param $type
   * @param $blockName
   * @param $blockConf
   * @param $theme
   * @param $showTitle
   * @return mixed
   */
  static public function create($type, $blockName, $blockConf, &$theme) {
    // Create a container for the block
    $block = [
      '#type' => 'container',
      '#attributes' => [
        'id' => $type . '--' . $blockName,
        'class' => ['control-block', $type . '--' . $blockName],
      ],
    ];
    if (!empty($blockConf['title'])) {
      $block['#title'] = $blockConf['title'];
    }

    if (!empty($blockConf['controls'])) {
      foreach ($blockConf['controls'] as $controlName => $controlConf) {
        $idArgs = explode('--', $controlName);
        if (!empty($idArgs[1]) && $idArgs[1] == 'wrapper') {
          $block[$controlName] = AtomizerControlBlock::create('blocks', $controlName, $controlConf, $theme);
        }
        else {
          $block[$controlName] = AtomizerControlBlock::makeControl($controlName, $controlConf, $theme);
        }
      }
    }

    return $block;
  }
}

