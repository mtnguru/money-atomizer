<?php

namespace Drupal\atomizer\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the atomizer module.
 */
class AtomizerControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "atomizer AtomizerController's controller functionality",
      'description' => 'Test Unit for module atomizer and controller AtomizerController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests atomizer functionality.
   */
  public function testAtomizerController() {
    // Check that the basic functions of module atomizer.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
