<?php

namespace Drupal\atomizer\Ajax;
use Drupal\Core\Ajax\CommandInterface;

class LoadYmlCommand implements CommandInterface {
  public function __construct($data, $ymlContents) {
    $this->data = $data;
    $this->ymlContents = $ymlContents;
  }

  public function render() {
    return array(
      'command' => 'loadYmlCommand',
      'data' => $this->data,
      'ymlContents' => $this->ymlContents,
    );
  }
}


