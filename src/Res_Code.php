<?php
namespace Arlequin;

require_once __DIR__ . '/../init.php';

use Arlequin\Templates\Error404;
use Arlequin\Redirect;

class Res_Code {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Res_Code();
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

  public function response(int $code, array $message = []) {
    header($this->protocol() . ' ' . $code . ' ' . $this->codes($code));

    $this->actions($code);
    $this->message($code, $message);

    die();
  }

  private function protocol() {
    return isset($_SERVER['SERVER_PROTOCOL'])
      ? $_SERVER['SERVER_PROTOCOL']
      : 'HTTP/1.0';
  }

  private function codes(int $code) {
    $txtCode = null;

    switch ($code) {
      case 200:
        $txtCode = 'OK'; // Vistas cargadas o respuestas satisfactorias
        break;
      case 201:
        $txtCode = 'Created'; // Crear recursos
        break;
      case 204:
        $txtCode = 'No Content'; // Respuestas satisfactorias POST, PUT, DELETE
        break;
      case 302:
        $txtCode = 'Moved Temporarily'; // Mantenimientos
        break;
      case 401:
        $txtCode = 'Unauthorized'; // Vistas protegidas
        break;
      case 404:
        $txtCode = 'Not Found'; // Vistas no encontradas
        break;
      case 406:
        $txtCode = 'Not Acceptable'; // Datos no admitidos por POST, PUT, DELTE
        break;
      case 500:
        $txtCode = 'Internal Server Error'; // Errores graves del servidor
        break;
      default:
        exit('Unknow http status code "' . htmlentities($code) . '"');
        break;
    }

    return $txtCode;
  }

  private function actions(int $code) {
    switch ($code) {
      case 404:
        if ($_ENV['MODE'] == 'dev') {
          print Error404::template_error404();
        } elseif ($_ENV['MODE'] == 'prod') {
          echo 'Enviando al Error404';
          /* Redirect::route('ERROR404'); */
        } else {
          throw new \Exception('No se encontró una acción para el rescode');
        }
        break;
    }
  }

  private function message(int $code, $message) {
    switch ($code) {
      case 201:
      case 406:
      case 500:
        if (count($message)) {
          echo json_encode($message);
        } else {
          throw new \Exception(
            'Se necesita de un array no vacío para enviar mensajes'
          );
        }
        break;
    }
  }
}
