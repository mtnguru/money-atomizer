<?php

namespace Drupal\atomizer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\atomizer\AtomizerControlBlock;


/**
 * Class AtomizerControlsForm.
 *
 * @package Drupal\atomizer\Form
 */
class AtomizerControlsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atomizer_controls_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $controlSet = array()) {

    ////////// Create empty style set - as controls get built this gets initialized.
    $theme = array(
      'name' => 'Default',
      'description' => 'Initial style set',
      'settings' => array(),
    );

    ////////// Create the control blocks
    if (isset($controlSet['blocks'])) {
      $form['blocks'] = array(
        '#type' => 'container',
        '#attributes' => array('id' => 'controls-blocks'),
      );

      foreach ($controlSet['blocks'] as $blockName => $blockConf) {
        $form['blocks'][$blockName] = AtomizerControlBlock::create('blocks', $blockName, $blockConf, $theme['settings']);
        $form['blocks'][$blockName]['#az-hidden'] = true;
        if (!empty($blockConf['destination'])) {
          $dest = $blockConf['destination'];
          if ($dest == 'dialog') {
            // Nothing really to do here.  JavaScript will deal with it.
            // Link button to popup the dialog
            // Link
          }
        }
      }
    }

    // Set up the sidebar.
    if (isset($controlSet['sidebar'])) {
      // Loop through sidebar blocks
      foreach ($controlSet['sidebar'] as $sectionName => $section) {
        foreach ($section as $blockName => $block) {
          if (!empty($block['show']) && $block['show']) {
            // Mark blocks that are displayed initially.
            $form['blocks'][$blockName]['#az-hidden'] = false;
            if (!empty($form['blocks']['buttons']['toggle--' . $blockName])) {
              $form['blocks']['buttons']['toggle--' . $blockName]['#attributes']['class'][] = 'az-selected';
            }
          }
        }
      }
    }

    if (isset($controlSet['sidebar'])) {

    }

    // Set options for the theme block selector control
    if (isset($controlSet['blocks']['theme']) &&
        isset($controlSet['blocks']['theme']['controls']['theme--selectBlock'])) {
      $options = $controlSet['blocks']['theme']['controls']['theme--selectBlock'][3];
      foreach ($options as $key) {
        $form['blocks']['theme']['theme--selectBlock']['#options'][$key] = $controlSet['blocks'][$key]['title'];
      }
    }

    // Mark all hidden blocks.
    foreach ($controlSet['blocks'] as $blockName => $block) {
      if ($form['blocks'][$blockName]['#az-hidden']) {
//      if ($blockName != 'animations') {
          $form['blocks'][$blockName]['#attributes']['class'][] = 'az-hidden';
//      }
      }
    }

    $form['#attributes'] = array('name' => 'az-controls-form');

    // Attach the theme to form for later use.
    $form['#az-theme'] =  $theme;
//  file_put_contents(drupal_get_path('module', 'atomizer') . '/config/style/base2.yml', Yaml::encode($theme));
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
