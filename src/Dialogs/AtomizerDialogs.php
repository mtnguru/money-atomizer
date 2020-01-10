<?php

namespace Drupal\atomizer\Dialogs;

use Drupal\atomizer\Dialogs\AtomizerBessel;

/**
 * Class AtomizerDialogs.
 *
 * @package Drupal\atomizer
 */
class AtomizerDialogs {

  /**
   * Build all Dialogs.
   *
   * @param array $config
   *   Configuration which defines attrpopup.
   *
   * @return mixed
   *   Return the render array for all popups.
   */
  static public function buildDialog(array $config) {

    // Define the dialog contents.
    switch ($config['dialogName']) {
      case 'Bessel':
        $build = AtomizerBessel::build($config);
        break;

    }

    // If the dialog has buttons than create them.
    if ($build['buttons']) {
      $build['buttonpane'] = [
        '#type' => 'container',
      ];
      foreach ($build['buttons'] as $name) {
        $build['buttonpane'][$name] = [
          '#type' => 'button',
          '#value' => $name,
          '#attributes' => [
            'class' => ['atomizer-button'],
            'id' => 'atomizer-' . strtolower($config['dialogName']) . '-' . str_replace(' ', '-', strtolower($name)),
          ],
        ];
      }
    }

    return $build;
  }

}
