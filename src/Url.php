<?php
namespace Arlequin;

class Url {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Url();
    }

    return self::$instance;
  }

  public static function clearInstance() {
    self::$instance = null;
  }

  private function __clone() {
  }

  private array $URL = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'DELETE' => [],
  ];

  private function __construct() {
    $this->URL = [
      'GET' => [],
      'POST' => [],
      'PUT' => [],
      'DELETE' => [],
    ];
  }

  public function add_to_url(string $method, string $route) {
    $the_method = strtoupper($method);

    if (array_key_exists($the_method, $this->URL)) {
      if (!empty($route)) {
        array_push($this->URL[$the_method], $route);
      } else {
        throw new \Exception('Redirect | Se necesita de una ruta a almacenar');
      }
    } else {
      throw new \Exception(
        'Redirect | No es valido el método que se esta intentando ingresar'
      );
    }
  }

  public function get_GET_url(): array {
    return $this->URL['GET'];
  }
}
