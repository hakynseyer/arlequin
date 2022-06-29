<?php
namespace Arlequin;

class Headers {
  public static function getHeaders() {
    $headers = [];

    foreach (getallheaders() as $header => $value) {
      $headers[strtolower($header)] = $value;
    }

    return $headers;
  }
}
