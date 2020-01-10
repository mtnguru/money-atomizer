<?php

namespace Drupal\atomizer\Ajax;
use Drupal\Core\Ajax\CommandInterface;

class SaveYmlCommand implements CommandInterface {
  public function __construct($data) {
    $this->data = $data;
  }

  public function render() {
    return array(
      'command' => 'saveYmlCommand',
      'data' => $this->data,
    );
  }
}


