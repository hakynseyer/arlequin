<?php
namespace Arlequin;

trait Singleton {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      /* self::$instance = new First(); */

      $theclass = new \ReflectionClass(get_class());

      self::$instance = $theclass->newInstance();
    }

    return self::$instance;
  }

  public static function clearInstance() {
    self::$instance = null;
  }

  private function __clone() {
  }

  public function __construct() {
    // Por alguna razón cuando el singleton esta en un trait, no puede acceder a este constructor si el modificador de acceso esta en private (Cosa Habitual en los Singleton)
  }
}
