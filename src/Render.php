<?php
namespace Arlequin;

require_once __DIR__ . '/../init.php';

class Render {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Render();
    }

    return self::$instance;
  }

  public static function clearInstance() {
    self::$instance = null;
  }

  private function __clone() {
  }

  private function __construct() {
  }

  public function view($view, $args = []) {
    $isView = gettype($view) === 'string';
    $isArgs = gettype($args) === 'array';

    if ($isView && $isArgs) {
      $loaderTemplates = new \Twig\Loader\FilesystemLoader([
        __DIR__ . '/../../../../' . $_ENV['DIR_PUBLIC'],
        __DIR__ . '/Templates/Twig/',
      ]);
      /* $loaderTemplates = new \Twig\Loader\ArrayLoader([$_ENV['DIR_PUBLIC']]); */

      $twig = new \Twig\Environment($loaderTemplates);

      $server = array_merge($args, [
        'title' => isset($args['title']) ? $args['title'] : 'BACKEND DEVELOPER',
        'creador' => $_ENV['CREATOR'],
        'creadorEmail' => $_ENV['CREATOR_EMAIL'],
      ]);

      echo $twig->render($view . '.html', ['server' => $server]);
    } else {
      throw new \Exception(
        'Error en los parametros del render... (string, array)'
      );
    }
  }
}
