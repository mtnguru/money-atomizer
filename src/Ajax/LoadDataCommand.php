<?php

namespace Drupal\atomizer\Ajax;
use Drupal\Core\Ajax\CommandInterface;

class LoadDataCommand implements CommandInterface {
  public function __construct($data) {
    $this->data = $data;
  }

  public function render() {
    return array(
      'command' => 'loadDataCommand',
      'data' => $this->data,
    );
  }
}


