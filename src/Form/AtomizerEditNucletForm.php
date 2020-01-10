<?php

namespace Drupal\atomizer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Yaml;
use Drupal\atomizer\AtomizerFiles;


/**
 * Class AtomizerEditNucletForm.
 *
 * @package Drupal\atomizer\Form
 */
class AtomizerEditNucletForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atomizer_edit_nuclet_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $controlSet = array()) {
    $form = [
      'nuclet_label' => [ '#markup' => t('Selected Nuclet') ],
      'nucletId' => [
        '#type' => 'textfield',
        '#title' => 'Nuclet Id',
        '#description' => 'Name which identifies this nuclet (and therefore it\'s location) - 0, 00, 01, 001, ...',
        '#default_value' => '0',
      ],
      'nuclet_type' => [
        '#type' => 'select',
        '#title' => 'Type of nuclet',
        '#options' => [
          'lithium' => 'Lithium',
          'carbon' => 'Carbon',
          'backbone' => 'Backbone',
        ],
      ],
      'nuclet_state' => [
        '#type' => 'select',
        '#title' => 'Nuclet State',
        '#default_value' => 'final',
        '#options' => [
          'initial' => 'Initial',
          'final' => 'Final',
        ],
      ],
      'nuclet_attach_angle' => [
        '#type' => 'number',
        '#title' => 'Attach Angle',
        '#required' => true,
        '#default_value' => 1,
        '#min' => 1,
        '#max' => 5
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  public function loadYml(array &$form, FormStateInterface $form_state) {
    return;
  }
}
