<?php

/**
 * @file
 * Contains \Drupal\atomizer\Plugin\Field\FieldFormatter\AtomFormatter 
 */

namespace Drupal\atomizer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\atomizer\AtomizerFiles;
use Drupal\atomizer\AtomizerInit;

/**
 * Plugin implementation of the 'atom' formatter.
 *
 * @FieldFormatter(
 *   id = "atomizer_formatter",
 *   label = @Translation("Atomizer embed"),
 *   description = @Translation("Display an atomizer."),
 *   field_types = {
 *     "string", "string_long"
 *   }
 * )
 */
class AtomizerFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'atomizer' => 'atomizer_viewer',
      'atomizerClass' => '',
      'control' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $atomizerFiles = AtomizerFiles::createFileList(drupal_get_path('module', 'atomizer') . '/config/producers', '/\.yml/');
    $atomizer = $this->getSetting('atomizer');
    $atomizerClass = $this->getSetting('atomizerClass');

    $controlFiles = AtomizerFiles::createFileList(drupal_get_path('module', 'atomizer') . '/config/controls', '/\.yml/');
    $control = $this->getSetting('control');

    $elements['atomizer'] = array(
      '#type' => 'select',
      '#options' => $atomizerFiles,
      '#title' => t('Atomizer mode'),
      '#default_value' => ($atomizer) ? $atomizer : $this->defaultSettings()['atomizer'],
      '#required' => TRUE,
    );
    $elements['control'] = array(
      '#type' => 'select',
      '#options' => $controlFiles,
      '#title' => t('Control file'),
      '#default_value' => ($control) ? $control : $this->defaultSettings()['control'],
      '#required' => TRUE,
    );
    $elements['atomizerClass'] = array(
      '#type' => 'textfield',
      '#title' => t('Class'),
      '#default_value' => ($atomizerClass) ? $atomizerClass : $this->defaultSettings()['atomizerClass'],
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $atomizerFiles = AtomizerFiles::createFileList(drupal_get_path('module', 'atomizer') . '/config/producers', '/\.yml/');
    $atomizer = $this->getSetting('atomizer');
    $atomizer = ($atomizer) ? $atomizer : $this->defaultSettings()['atomizer'];
    if ($atomizer == 'atom_viewer.yml') {
      $atomizer = 'atomizer_atom_viewer.yml';
    }
    $summary[] = t('Atomizer: @atomizer', array('@atomizer' => $atomizerFiles[$atomizer]));

    $controlFiles = AtomizerFiles::createFileList(drupal_get_path('module', 'atomizer') . '/config/controls', '/\.yml/');
    $control = $this->getSetting('control');
    $control = ($control) ? $control : $this->defaultSettings()['control'];
    $summary[] = t('Control file: @control', array('@control' => $controlFiles[$control]));

    $atomizerClass = $this->getSetting('atomizerClass');
    $summary[] = t('Class: @class', array('@class' => $atomizerClass));

    return $summary;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    foreach ($items as $delta => $item) {
      $nid = $item->getEntity()->nid->value;
      $config = [
        'atomizerId' => 'atomizer_formatter_' . $nid,
        'atomizerFile' => $this->getSetting('atomizer'),
        'atomizerClass' => $this->getSetting('atomizerClass'),
        'controlFile' => $this->getSetting('control'),
        'label' => 'Atomizer Formatter',
        'nid' => $nid,
      ];

      $elements[$delta] = AtomizerInit::build($config);
    }

    return $elements;
  }

}
