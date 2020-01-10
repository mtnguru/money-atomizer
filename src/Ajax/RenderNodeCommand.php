<?php

namespace Drupal\atomizer\Ajax;
use Drupal\Core\Ajax\CommandInterface;

class RenderNodeCommand implements CommandInterface {
  public function __construct($data, $htmlContents) {
    $this->data = $data;
    $this->htmlContents = $htmlContents;
  }

  public function render() {
    return array(
      'command' => 'renderNodeCommand',
      'data' => $this->data,
      'htmlContents' => $this->htmlContents,
    );
  }
}


