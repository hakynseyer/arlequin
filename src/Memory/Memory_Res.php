<?php
namespace Arlequin\Memory;

use Arlequin\{Res_Code, Render};
use Arlequin\Route\Route_Args;

class Memory_Res {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Memory_Res();
    }

    return self::$instance;
  }

  public static function clearInstance() {
    self::$instance = null;
  }

  // Evitacion de la clonación
  private function __clone() {
  }

  private function __construct() {
  }

  public function __get($name) {
    throw new \Exception('No hay soporte de get para el Response');
  }

  public function __set($name, $value) {
    throw new \Exception('No se permite sobrescribir el Response');
  }

  public function code($code, $message = []) {
    $isCode = gettype($code) === 'integer';
    $isMessage = gettype($message) === 'array';

    if ($isCode && $isMessage) {
      Res_Code::clearInstance();
      Res_Code::init()->response($code, $message);
    } else {
      throw new \Exception('Error en los parametros Code... (entero, array)');
    }
  }

  public function render($args) {
    $isArgs = gettype($args) === 'array';

    if ($isArgs) {
      Render::init()->view(Route_Args::init()->getFirstReq()['view'], $args);
    } else {
      throw new \Exception('Error en los parametros Render... (array)');
    }
  }
}
