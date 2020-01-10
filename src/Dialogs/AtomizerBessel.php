<?php

namespace Drupal\atomizer\Dialogs;

use Drupal\atomizer\Dialogs\AtomizerDialogsInterface;
use Drupal\file\Entity\File;

/**
 * Class AtomizerBessel.
 *
 * @package Drupal\atomizer
 */
class AtomizerBessel implements AtomizerDialogsInterface {

  /**
   * Build render array for HSL slider popup.
   *
   * @return array
   *   Render array for Hue/Saturation/Lightness dialog.
   */
  static public function build(array $config) {
    $file = File::load(633);
    $id = 'atomizer-bessel';
    // What is it - just a dialog with a div and canvas
    $content = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['bessel-canvas-wrapper'],
      ],
      'canvas' => [
        '#markup' => '<canvas class="bessel-canvas"></canvas>',
        '#allowed_tags' => ['canvas'],
      ],
      'image' => [
        '#theme' => 'image',
//      '#theme' => 'image_style`',
//      '#style_name' => '640x480',
//      '#width' => 100,
//      '#height' => 100,
        '#uri' => $file->getFileUri(),
      ],
    ];
    return [
      'content' => $content,
      'buttons' => ['Cancel', 'Reset', 'Apply'],
      'id' => $id,
    ];
  }

}
