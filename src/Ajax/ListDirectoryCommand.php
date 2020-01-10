<?php

namespace Drupal\atomizer\Ajax;
use Drupal\Core\Ajax\CommandInterface;

class ListDirectoryCommand implements CommandInterface {
  public function __construct($data, $filelist) {
    $this->data = $data;
    $this->filelist = $filelist;
  }

  public function render() {
    return array(
      'command' => 'listDirectoryCommand',
      'data' => $this->data,
      'filelist' => $this->filelist,
    );
  }
}


