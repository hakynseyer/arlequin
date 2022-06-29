<?php
namespace Arlequin\Memory;

use Arlequin\Route\Route_Args;
use Arlequin\Middleware;

class Memory_Next {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Memory_Next();
    }

    return self::$instance;
  }

  public static function clearInstance() {
    self::$instance = null;
  }

  // Evitación de la clonación
  private function __clone() {
  }

  private function __construct() {
  }

  public function __get($name) {
    if ($name === 'mw') {
      Middleware::init()->nextMiddleware();
      die();
    } else {
      throw new \Exception(
        'Solo existen las siguientes opciónes: <br/><b>mw</b> = Mover al siguiente middleware<br/>'
      );
    }
  }
}
