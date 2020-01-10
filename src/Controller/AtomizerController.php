<?php

namespace Drupal\atomizer\Controller;

use Drupal\atomizer\AtomizerInit;
use Drupal\atomizer\Dialogs\AtomizerDialogs;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Url;

use Drupal\Component\Serialization\Yaml;

use Drupal\atomizer\Ajax\RenderNodeCommand;
use Drupal\atomizer\Ajax\RenderWebformCommand;
use Drupal\atomizer\Ajax\LoadYmlCommand;
use Drupal\atomizer\Ajax\SaveYmlCommand;
use Drupal\atomizer\Ajax\LoadDataCommand;
use Drupal\atomizer\Ajax\ListDirectoryCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

// use Symfony\Component\Yaml\Yaml;

/**
 * Class AtomizerController.
 *
 * @package Drupal\atomizer\Controller
 */
class AtomizerController extends ControllerBase {

   /**
   * The Entity Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * AtomizerController constructor.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')
    );
  }

  public function renderWebform()
  {
    $data = json_decode(file_get_contents("php://input"), true);
    $form = $this->entityTypeManager->getStorage('webform')->load($data['formName']);
//  return $this->entityTypeManager->getStorage('webform')->load($this->configuration['webform_id']);
    $build = [
      '#type' => 'webform',
      '#webform' => $form,
//    '#webform' => $this->getWebform(),
      '#default_data' => null,
//    '#default_data' => $this->configuration['default_data'],
    ];

    $html = render($build);

    $response = new AjaxResponse();
    $response->addCommand(new RenderWebformCommand($data, $html));
    return $response;
  }

  /**
   * Load a YML file.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function renderNode() {
    $data = json_decode(file_get_contents("php://input"), true);

    $nid = $data['nid'];
    $snippet = $this->entityTypeManager->getStorage('node')->load($nid);
    $htmlContents = $this->entityTypeManager->getViewBuilder('node')->view($snippet, 'full');

    $response = new AjaxResponse();
    $response->addCommand(new RenderNodeCommand($data, render($htmlContents)));

    return $response;
  }

  /**
   * Load a Node and render it.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function loadYml() {
    $data = json_decode(file_get_contents("php://input"), true);
    $filepath = "$data[directory]/$data[filename]";

    $path = drupal_get_path('module', 'atomizer') . '/' . $filepath;
    if (!file_exists($path)) {
       $path =  dirname($path) . '/Default.yml';
       $data['message'] = "Could not find $filepath -- Default.yml file loaded.";
       $data['filepath'] = $path;
       $data['themeId'] = 'default';
    }
    $response = new AjaxResponse();
    $ymlContents = Yaml::decode(file_get_contents($path));
    $response->addCommand(new LoadYmlCommand($data, $ymlContents));

    return $response;
  }

  /**
   * Save a YML file.
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function saveYml() {
    $data = json_decode(file_get_contents("php://input"), true);

    $response = new AjaxResponse();
    $data['success'] = file_put_contents(drupal_get_path('module', 'atomizer') . '/' . $data['filepath'], Yaml::encode($data['ymlContents']));
    $response->addCommand(new SaveYmlCommand($data));

    return $response;
  }

  /**
   * List the contents of a directory.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function listDirectory() {
    $data = json_decode(file_get_contents("php://input"), true);

    $files = file_scan_directory(drupal_get_path('module', 'atomizer') . '/' . $data['directory'], '/\.yml/');
    foreach ($files as $file) {
      $filelist[$file->filename] = $file->name;
    }
    $response = new AjaxResponse();
    $response->addCommand(new ListDirectoryCommand($data, $filelist));
    return $response;
  }

  /**
   * Load the atom edit form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function loadAtomForm() {
    $node = $this->entityTypeManager()->getStorage('node')->create(array('type' => 'atom'));

    $form = \Drupal::entityTypeManager()
      ->getFormObject('node', 'default')
      ->setEntity($node);
    $content = \Drupal::formBuilder()->getForm($form, 'popup');

    $response = new AjaxResponse();
    $title = 'Atom Edit Form';
    $html = drupal_render($content);

    $is_modal = true;
    if ($is_modal) {
      $dialog = new OpenModalDialogCommand($title, $html);
      $response->addCommand($dialog);
    }
    else {
      $selector = '#ajax-test-dialog-wrapper-1';
      $response->addCommand(new OpenDialogCommand($selector, $title, $html));
    }
    return $response;
  }

  /**
   * Load a atom.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function loadAtom() {
    $response = new AjaxResponse();
    $data = json_decode(file_get_contents("php://input"), true);
    $link = [
      '#type' => 'link',
      '#title' => 'Save Atom',
      '#url' => Url::fromRoute('entity.node.edit_form',['node' => $data['conf']['nid']]),
      '#attached' => ['library' => ['core/drupal.dialog.ajax']],
      '#attributes' => [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'dialogClass' => 'az-dialog az-atom-form',
          'width' => '700px',
          'draggable' => true,
          'autoResize' => false,
          'position' => [
            'my' => 'center top-20',
            'at' => 'center top',
          ],
        ]),
        'data-drupal-selector' => "edit-link",
        'id' =>"edit-link",
      ],
    ];
    $data['link'] = render($link);

    $node = Node::load($data['conf']['nid']);
    $data['element'] = $node->field_element->entity->label();

    // Render the node/atom using teaser atom_viewer mode.
    $data['atomName'] = $node->label();
    $data['atomTitle'] = '<h3 class="scene-name"><a href="/node/' . $node->id() . '">' . $node->label() . '</a></h3>';

    // Build the properties block using the atom_viewer view'.
    $build = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'atom_viewer');

    // Add the create link to Wikipedia Isotopes page for this element
    $element = strtolower($node->field_element->entity->label());
    $isotopeUrl = 'https://en.wikipedia.org/wiki/Isotopes_of_' . $element;
    $build['links'] = [
      '#type' => 'link',
      '#title' => 'Isotopes',
      '#attributes' => [
        'title' => t('Wikipedias Isotope page for this element'),
        'target' => '_blank',
      ],
      '#url' => Url::fromUri($isotopeUrl),
    ];
    // Render the properties block.
    $data['properties'] = render($build);

    // Render the atom information using teaser view.
    $build = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'teaser');
    $data['information'] = render($build);

    // Get the atomic structure field.
    $field = $node->field_atomic_structure;
    $data['atomConf'] = (empty($field)) ? 'Object not found' : Yaml::decode($field->value);

    $response->addCommand(new LoadDataCommand($data));
    return $response;
  }

  /**
   * Load hierarchical list of elements and their isotopes.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function loadAtomList()
  {
    $data = json_decode(file_get_contents("php://input"), true);
    $list = NULL;
    $cid = 'loadAtomList';
    $access = ($data['access']) ? $data['access'] : 'permissions';
    if ($cache = \Drupal::cache()->get($cid . '1')) {   // The . '1' prevents cache from working.
//  if ($cache = \Drupal::cache()->get($cid)) {   // The . '1' prevents cache from working.
      $list = $cache->data;
    } else {
      // Query for the elements and atoms.
      $elements = AtomizerInit::queryElements();
      $atoms = AtomizerInit::queryAtoms($access)['results'];

      // Go through each atom and count number of isotopes per element
      $isobars = [];
      foreach ($atoms as $atom) {
        $eid = $atom->field_element_target_id;
        if (!empty($elements[$eid])) {
          // Count the number of isotopes
          $elements[$eid]['isotopes'][] = $atom;
          $isobars[$atom->field__protons_value][] = $atom;
          if (empty($elements[$eid]['numIsotopes'])) {
            $elements[$eid]['numIsotopes'] = 1;
          } else {
            $elements[$eid]['numIsotopes']++;
          }
        }
      }

      // Create list of elements and their isotopes.
      $list = [];
      foreach ($elements as $element) {
        $defaultAtom = null;
        $imageUrl = '';
        $title = '';

        if ($element['numIsotopes'] > 0) {
          $defaultAtomNid = (!empty($element['defaultAtom'])) ? $element['defaultAtom'] : $element['isotopes'][0]->nid;
          if (empty($atoms[$defaultAtomNid])) {
            $defaultAtom = $atoms[$element['isotopes'][0]->nid];
          } else {
            $defaultAtom = $atoms[$defaultAtomNid];
          }
          $title = $defaultAtom->title;

          // add image url
//        $media = \Drupal::entityManager()->getStorage('media')->load($defaultAtom->field_media_target_id);
          if ($defaultAtom->field_image_target_id) {
            $fid = $defaultAtom->field_image_target_id;
            $file = \Drupal\file\Entity\File::load($fid);
            $style = \Drupal::entityTypeManager()->getStorage('image_style')->load('large');
            $imageUrl = $style->buildUrl($file->getFileUri());
          }
        }

        // if media exists then create the URL for the image.

        $name = strtolower($element['name']);
        $list[$name] = [
          'symbol' => $element['symbol'],
          'name' => $element['name'],
          'defaultAtomName' => $title,
          'nid' => $element['nid'],
          'default_atom_nid' => $defaultAtomNid,
          'stability' => (!empty($defaultAtom->name)) ? $defaultAtom->name : 'Not Set',
          'atomic_number' => $element['atomicNumber'],
          'image_url' => $imageUrl,
          'pte_row' => $element['pte_row'],
          'pte_column' => $element['pte_column'],
          'sam_row' => $element['sam_row'],
          'sam_column' => $element['sam_column'],
          'num_isotopes' => $element['numIsotopes'],
          'num_isobars' => $element['numIsobars'],
          'valence' => $element['valence'],
          'isotopes' => ($element['numIsotopes']) ? $element['isotopes'] : null,
        ];
      }
      \Drupal::cache()->set($cid, $list);
    }

    $data['list'] = &$list;
    $response = new AjaxResponse();
    $response->addCommand(new LoadDataCommand($data));
    return $response;
  }

  /**
   * Load a molecule.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function loadMolecule() {
    $response = new AjaxResponse();
    $data = json_decode(file_get_contents("php://input"), true);
    $link = [
      '#type' => 'link',
      '#title' => 'Save Molecule',
      '#url' => Url::fromRoute('entity.node.edit_form',['node' => $data['conf']['nid']]),
      '#attached' => ['library' => ['core/drupal.dialog.ajax']],
      '#attributes' => [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'dialogClass' => 'az-dialog az-molecule-form',
          'width' => '700px',
          'draggable' => true,
          'autoResize' => false,
          'position' => [
            'my' => 'center top-20',
            'at' => 'center top',
          ],
        ]),
        'data-drupal-selector' => "edit-link",
        'id' =>"edit-link",
      ],
    ];
    $data['link'] = render($link);

    $node = Node::load($data['conf']['nid']);

    // Get the atomic structure field. -- append it to the conf array.
    $field = $node->field_molecule_structure;
    if ($field == empty('field')) {
      $data['conf']['error'][] = 'field_molecule_structure field is empty';
    } else {
      $data['conf'] = array_merge($data['conf'], YAML::decode($field->value));
    }

    // Render the node/atom using teaser atom_viewer mode.
    $data['name'] = $node->label();
    $data['title'] = '<h3 class="scene-name"><a href="/node/' . $node->id() . '">' . $node->label() . '</a></h3>';

    // Build the properties block using the atom_viewer view'.
    $build = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'atom_viewer');

    // Render the properties block.
    $data['properties'] = render($build);

    // Render the atom information using teaser view.
    $build = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'teaser');
    $data['information'] = render($build);


    $response->addCommand(new LoadDataCommand($data));
    return $response;
  }

  /**
   * Load a scene.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function loadScene() {
    $response = new AjaxResponse();
    $data = json_decode(file_get_contents("php://input"), true);

    $atomizer = Node::load($data['conf']['nid']);

    // Get the atomic structure field. -- append it to the conf array.
    if ($atomizer->field_episode->isEmpty()) {
      $data['scene']['error'][] = 'field_molecule_structure field is empty';
    } else {
      $enid = $atomizer->field_episode->target_id;
      $episode = Node::load($enid);
      if (!$episode->field_scene_layout->isEmpty()) {
        $data['scene-layout'] = array_merge($data['conf'], (YAML::decode($episode->field_scene_layout->value))['scene-layout']);
      }
    }
    $data['name'] = $atomizer->label();
    $data['title'] = '<h3 class="scene-name"><a href="/node/' . $atomizer->id() . '">' . $atomizer->label() . '</a></h3>';

    $response->addCommand(new LoadDataCommand($data));
    return $response;
  }

  /**
   * Execute ImageMagick convert command to scale image.
   *
   * @param string $spath
   *   Source path for image.
   * @param string $dpath
   *   Destination path for image.
   * @param string $type
   *   If $type == browser then scale to saved configuration settings.
   */
  private function convertImage($spath, $dpath, $type = NULL) {
    switch ($type) {
      case 'browser':
        $geometry = variable_get('imager_browser_width', 1200) . 'x' .
          variable_get('imager_browser_height', 1200);
        $cmd = "/usr/bin/convert -quality 60 -scale $geometry \"$spath\" \"$dpath\" > /tmp/convert.log 2>&1";
        break;

      default:
        $cmd = "/usr/bin/convert -quality 60 \"$spath\" \"$dpath\" > /tmp/convert.log 2>&1";
        break;
    }
    system($cmd);
    if (!file_exists($dpath)) {
      $cmd = "cp $spath $dpath";
      system($cmd);
    }
//  $cmd = "rm $spath";
//  system($cmd);
  }

  static private $dialogs = NULL;
  /**
   * Render a dialog - why do I need this?  Look at imager code.
   */
  public function loadDialog() {
    $data = json_decode(file_get_contents("php://input"), TRUE);

    if (empty(self::$dialogs)) {
      self::$dialogs = new AtomizerDialogs();
    }

    $dialog = self::$dialogs->buildDialog($data);
    $data['content'] = render($dialog['content']);
    if (!empty($dialog['buttonpane'])) {
      $data['buttonpane'] = render($dialog['buttonpane']);
    }
    $data['id'] = $dialog['id'];

    $response = new AjaxResponse();
    return $response->addCommand(new LoadDataCommand($data));

  }

  /**
   * Write POSTed image to file path.
   *
   * @param string $path
   *   Path to write image to.
   */
  private function writeImage($path, $base64Img) {
    $filtered_data = explode(',', $base64Img);
    $fp = fopen($path, 'w');
    fwrite($fp, base64_decode($filtered_data[1]));
    fclose($fp);
  }

  /**
   * Save data for an atom - currently just saving binding energy.
   */
  public function saveAtom() {
    $data = json_decode(file_get_contents("php://input"), TRUE);
    $atom = Node::load($data['nid']);
    if ($data['be_sam_nuclets'] != '0') {
      $atom->set('field_be_sam_nuclets', $data['be_sam_nuclets']);
    }
    if ($data['be_sam_nuclets_perc'] != '0%') {
      $atom->set('field_be_sam_nuclets_', str_replace('%', '', $data['be_sam_nuclets_perc']));
    }
    $atom->setNewRevision(TRUE);
    $retcode = $atom->save();
    $nuclets = $data['be_sam_nuclets'];
    $perc = $data['be_sam_nuclets_perc'];
    \Drupal::logger('atomizer')->notice("AtomizerController::saveAtom - $retcode  nuclets: $nuclets  nuclets perc: $perc");
    return new AjaxResponse();
  }

  public function _saveAtomImage($data) {

    error_log("_saveAtomImage - enter");
    $atom = $this->entityTypeManager->getStorage('node')->load($data['sceneNid']);
    $filename = $data['filename'] . '.png';
    $tmpPath = file_directory_temp() . '/' . $filename;
    $finalpath = DRUPAL_ROOT . '/sites/default/files/atoms_primary/' . $filename;

    // The atom does not have a image file, create a new one
    if (!$atom->hasField('field_image') || $atom->field_image->isEmpty()) {
      error_log("_saveAtomImage - create new");
      $file = File::create(['uri' => 'public://atoms_primary/' . $filename]);
      $file->save();

      // Set the image
      $image = $atom->field_image->getValue();
      $image[0]['target_id'] = $file->id();
      $atom->field_image->setValue(($image));

      $atom->save();
    } else {
      error_log("_saveAtomImage - overwrite");
      $file = $atom->field_image->entity;
      $file->setFileUri('public://atoms_primary/' . $filename);
      $file->save;
    }

    // Clear the styles directory for all atoms.
    system('rm -r ' . DRUPAL_ROOT . '/sites/default/files/styles/*/*/atoms_primary/*');

    error_log("_saveAtomImage - writeImage " . $tmpPath);
    $this->writeImage($tmpPath, $data['imgBase64']);
    error_log("_saveAtomImage - convertImage " . $finalpath);
    $this->convertImage($tmpPath, $finalpath);

    // @TODO - return confirmation message.
    $response = new AjaxResponse();
    return $response;
  }

  /**
   * Save an edited image into a new media entity.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response.
   */
  public function saveImage() {
    $data = json_decode(file_get_contents("php://input"), TRUE);
    if ($data['type'] == 'atom' && $data['imageType'] == 'primary') {
      return self::_saveAtomImage($data);
    }

    $n = 0;
    $filename = $data['filename'];
    do {
      $newfilename = $filename . '_' . ++$n . '.jpg';
      $newpath = DRUPAL_ROOT . '/sites/default/files/' . $data['directory'] . '/' . $newfilename;
    } while (file_exists($newpath));

    // Save image, process through 'convert' command to reduce file size.
    $tmpPath = file_directory_temp() . '/' . $newfilename;
    $this->writeImage($tmpPath, $data['imgBase64']);
    $this->convertImage($tmpPath, $newpath);

    $file = File::create(['uri' => 'public://atoms/' . $newfilename]);
    $file->save();

    // Create the media entity.
    $media = $this->entityTypeManager->getStorage('media')->create(['bundle' => 'image']);

    // Assign the media name
    $media->name->setValue($data['atomName']);

    // Get the image and thumbnail values - not sure this does anything.
    $image = $media->get('image')->getValue();
    $thumb = $media->get('thumbnail')->getValue();

    // if we are overwriting then duplicate the old media entity.
    $media = ($data['overwrite'] == "TRUE") ? $media : $media->createDuplicate();

    // Set published status and moderation state.
    $media->status->setValue(NODE_PUBLISHED);
    $media->moderation_state->setValue('published');

    // Set the image
    $image[0]['target_id'] = $file->id();
    $media->get('image')->setValue(($image));

    // Set the thumbnail.
    $thumb[0]['target_id'] = $file->id();
    $media->get('thumbnail')->setValue($thumb);

    // Set the changed time to current time.
    $media->get('changed')->setValue(REQUEST_TIME);

    // Save the media entity.
    $media->save();

    // Re-read the media to get the newly assigned mid.
    $media = $this->entityTypeManager->loadEntityByUuid('media', $media->uuid->value);

    // Read in the Atom, assign the media entity and save.
    $atom = $this->entityTypeManager->getStorage('node')->load($data['sceneNid']);
    $atom->field_media->target_id = $media->id();
    $atom->save();

    // @TODO - return confirmation message.
    $response = new AjaxResponse();
    return $response;
  }

  /**
   * Load a Birkland current definition file.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response.
   */
  public function loadNode() {
    $response = new AjaxResponse();
    $data = json_decode(file_get_contents("php://input"), true);

    $node = Node::load($data['conf']['nid']);

    // Render the node using teaser atom_viewer mode.
    $data['nodeName'] = $node->label();
    $data['nodeTitle'] = '<h3 class="scene-name"><a href="/node/' . $node->id() . '">' . $node->label() . '</a></h3>';

    $build = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'atom_viewer');
    $data['properties'] = render($build);

    // Render the node/atom using teaser view mode.
    $build = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'teaser');
    $data['information'] = render($build);

    // Get the atomic structure field.
    $type = $node->getType();
    $field = null;
    switch ($type) {
      case 'atom':
        $field = $node->field_atomic_structure;
        if ($field) {
          $data['nodeConf'] = (empty($field)) ? 'Node not found' : Yaml::decode($field->value);
        }
        break;
      case 'birkeland':
        $field = $node->field_structure;
        $path = drupal_get_path('module', 'atomizer') . '/config/objects/birkeland/' . $data['conf']['nid'] . '.yml';
        if (file_exists($path)) {
          $data['nodeConf'] = Yaml::decode(file_get_contents($path));
        } else {
          if ($field) {
            $data['nodeConf'] = (empty($field)) ? 'Node not found' : Yaml::decode($field->value);
          }
        }
        break;
      case 'episode':
        $scenes = [];
        if (!$node->field_scenes->isEmpty()) {
          foreach($node->field_scenes->getValue() as $scene) {
            $pid = $scene['target_id']; // get scene id;
            $scene = \Drupal::entityTypeManager()->getStorage('paragraph')->load($pid);
            $scenes[] = [
              'title' => $scene->field_title->value,
              'type' => $scene->field_scene_type->target_id,
              'script' => Yaml::decode($scene->field_script->value),
              'description' => $scene->field_description->value,
              'description_format' => $scene->field_description->format,
            ];
          }
        }
        $data['scenes'] = $scenes;
        break;
    }
    $response->addCommand(new LoadDataCommand($data));
    return $response;
  }
}
