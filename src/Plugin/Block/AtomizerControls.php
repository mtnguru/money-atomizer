<?php

namespace Drupal\atomizer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Yaml;

/**
 * Provides a 'AtomizerControls' block.
 *
 * @Block(
 *  id = "atomizer_controls",
 *  admin_label = @Translation("Atomizer controls"),
 * )
 */
class AtomizerControls extends BlockBase {

  public function blockForm($form, FormStateInterface $form_state) {

    // Give the viewer a name so the controls block can connect to it.
    $form['atomizerId'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Atomizer ID'),
      '#description' => $this->t('Enter the same name as that given the Atomizer Viewer block'),
      '#default_value' => isset($this->configuration['atomizerId']) ? $this->configuration['atomizerId'] : 'Atomizer',
    );

    $control_files = file_scan_directory(drupal_get_path('module','atomizer') . '/config/controls', '/\.yml/');
    foreach ($control_files as $file) {
      $control_options[$file->filename] = $file->name;
    }
    $form['controlFile'] = array(
      '#type' => 'select',
      '#title' => $this->t('Control Set file'),
      '#description' => $this->t(''),
      '#default_value' => isset($this->configuration['controlFile']) ? $this->configuration['controlFile'] : 'base',
      '#options' => $control_options,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['atomizerId'] = $form_state->getValue('atomizerId');
    $this->configuration['controlFile'] = $form_state->getValue('controlFile');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    // Read in the controls file
    $controlSet = Yaml::decode(file_get_contents(drupal_get_path('module', 'atomizer') . '/config/controls/' . $config['controlFile']));
    $controlSet['filename'] = $config['controlFile'];

    // Create the form
    $render['form'] = \Drupal::formBuilder()->getForm('Drupal\atomizer\Form\AtomizerControlsForm', $controlSet);

    // Pass the atomizer id and control set on to JavaScript.
    $render['form']['#attached'] =  array(
      'library' => array('atomizer/atomizer-js'),
      'drupalSettings' => array(
        'atomizer' => array(
          $config['atomizerId'] => array(
            'atomizerId' =>  $config['atomizerId'],
            'controlSet' =>  $controlSet,
            'theme' => $render['form']['#az-theme'],
          ),
        ),
      ),
    );
    return $render;
  }
}
