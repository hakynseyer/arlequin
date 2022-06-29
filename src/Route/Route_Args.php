<?php
namespace Arlequin\Route;

class Route_Args {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Route_Args();
    }

    return self::$instance;
  }

  private function __construct() {
  }

  // Se evita la clonación
  private function __clone() {
  }

  private $theRoutes = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'DELETE' => [],
  ];

  private $firstReq = [
    'route' => null,
    'middlewares' => null,
    'view' => null,
    'method' => null,
    'params' => null,
    'data' => null,
    'files' => null,
    'auth' => null,
  ];

  public function getTheRoutes() {
    return $this->theRoutes;
  }

  public function pushTheRoutes($method, $theRoutes) {
    array_push($this->theRoutes[$method], $theRoutes);
  }

  public function getFirstReq() {
    return $this->firstReq;
  }

  public function setKeyFirstReq($key, $data) {
    $this->firstReq[$key] = $data;
  }

  public function clearFirstReq() {
    foreach ($this->firstReq as $key => $value) {
      $this->firstReq[$key] = null;
    }
  }

  public function removeFirstMiddleware() {
    if (count($this->firstReq['middlewares'])) {
      array_shift($this->firstReq['middlewares']);
    }
  }
}
