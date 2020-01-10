<?php

namespace Drupal\atomizer\Plugin\Block;

use Drupal\atomizer\AtomizerFiles;
use Drupal\atomizer\AtomizerInit;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Yaml;

/**
 * Provides a 'Atomizer' block.
 *
 * @Block(
 *  id = "atomizer_block",
 *  admin_label = @Translation("Atomizer block dude - controls and viewer in one block"),
 * )
 */
class AtomizerBlock extends BlockBase {

  public function blockForm($form, FormStateInterface $form_state) {

    // Give the viewer a name so the controls block can connect to it.
    $form['atomizerId'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Atomizer ID'),
      '#description' => $this->t('Give this instance of the atomizer an ID - not needed in this instance?'),
      '#default_value' => isset($this->configuration['atomizerId']) ? $this->configuration['atomizerId'] : 'Atomizer',
    ];

    $control_files = file_scan_directory(drupal_get_path('module','atomizer') . '/config/controls', '/\.yml/');
    $control_options = [];
    foreach ($control_files as $file) {
      $control_options[$file->filename] = $file->name;
    }
    $form['controlFile'] = [
      '#type' => 'select',
      '#title' => $this->t('Control Set file'),
      '#description' => $this->t(''),
      '#default_value' => isset($this->configuration['controlFile']) ? $this->configuration['controlFile'] : 'base',
      '#options' => $control_options,
    ];

    $form['atomizerFile'] = [
      '#type' => 'select',
      '#title' => $this->t('Atomizer file'),
      '#description' => $this->t(''),
      '#default_value' => isset($this->configuration['atomizerFile']) ? $this->configuration['atomizerFile'] : 'default',
      '#options' => AtomizerFiles::createFileList(drupal_get_path('module', 'atomizer') . '/config/producers', '/\.yml/'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['atomizerId'] = $form_state->getValue('atomizerId');
    $this->configuration['controlFile'] = $form_state->getValue('controlFile');
    $this->configuration['atomizerFile'] = $form_state->getValue('atomizerFile');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    return AtomizerInit::build($config);
  }
}
