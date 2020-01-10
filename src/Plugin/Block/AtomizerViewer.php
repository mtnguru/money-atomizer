<?php

namespace Drupal\atomizer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\atomizer\AtomizerInit;
use Drupal\atomizer\AtomizerFiles;

/**
 * Provides a 'Atomizer' block.
 *
 * @Block(
 *  id = "atomizer_viewer",
 *  admin_label = @Translation("Atomizer viewer"),
 * )
 */
class AtomizerViewer extends BlockBase {

  public function blockForm($form, FormStateInterface $form_state) {

    // Give the atomizer the controls block can connect to it.
    $form['atomizerId'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Atomizer ID'),
      '#description' => $this->t('Give this atomizer the same id as that assigned the Atomizer Control block'),
      '#default_value' => isset($this->configuration['atomizerId']) ? $this->configuration['atomizerId'] : 'Atomizer',
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
    $this->configuration['atomizerFile'] = $form_state->getValue('atomizerFile');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $build = AtomizerInit::start($config);
    return $build;
  }

}
