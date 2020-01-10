<?php

/**
 * @file
 * Contains \Drupal\atomizer\Plugin\Field\FieldFormatter\CodeFormatter
 */

namespace Drupal\atomizer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormStateInterface;
use Drupal\atomizer\AtomizerFiles;
use Drupal\Component\Serialization\Yaml;

/**
 * Plugin implementation of the 'code' formatter.
 *
 * @FieldFormatter(
 *   id = "code_formatter",
 *   label = @Translation("Code embed"),
 *   description = @Translation("Display text in <pre><code> elements."),
 *   field_types = {
 *     "string", "string_long"
 *   }
 * )
 */
class CodeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
//public static function defaultSettings() {
//  return array(
//    'atomizer' => 'atom_builder',
//  );
//}

  /**
   * {@inheritdoc}
   */
//public function settingsForm(array $form, FormStateInterface $form_state) {
//  return $elements;
//}

  /**
   * {@inheritdoc}
   */
//public function settingsSummary() {
//  return $summary;
//}


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    foreach ($items as $delta => $entity) {
      // Read in the config/atomizer file
      $elements[$delta] = [
        '#markup' => '<pre><code>' . Xss::filter($entity->value) . '</pre></code>',
      ];
    }
    return $elements;
  }

}
