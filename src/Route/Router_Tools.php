<?php
namespace Arlequin\Route;

use Arlequin\Route\Route_Args;
use Arlequin\Headers;

class Router_Tools {
  protected static function validateRoute($route) {
    if (!is_null($route) && !empty($route)) {
      if (!preg_match("/[áÁéÉóÓúÚñ\\\!$%&()=|<>?¡¿,.#'*+-]/", $route)) {
        return true;
      }
    }

    return false;
  }

  protected static function parseURL($url) {
    $newURL = null;

    if (isset($url) && !empty($url)) {
      $newURL = self::clearURL($url);
      $newURL = trim($newURL, '/');
      $newURL = filter_var($newURL, FILTER_SANITIZE_URL);
      $newURL = explode('/', $newURL);
    }

    return $newURL;
  }

  private static function clearURL($url) {
    $forbidden = [
      '*',
      '<script>',
      '</script>',
      '<script type=',
      'script',
      '?',
      '=',
      '<?php',
      '?>',
      '< ',
      '>',
      '.',
      '^',
      '´',
      '+',
      '$',
    ];

    return str_replace($forbidden, '', strtolower($url));
  }

  protected static function valNumberParts($urlBrowser, $urlSystem) {
    if (count($urlBrowser) === count($urlSystem)) {
      return true;
    }

    return false;
  }

  protected static function valNumberParams($urlBrowser, $urlSystem) {
    $numberParams = 0;

    foreach ($urlSystem as $paramSystem) {
      if ($paramSystem === '@{param}') {
        $numberParams++;
      }
    }

    if ($numberParams === count($urlBrowser)) {
      return false;
    }

    return true;
  }

  protected static function valEqualParts($urlBrowser, $urlSystem, $route) {
    Route_Args::init()->clearFirstReq();

    $success = 0;
    $arrayParams = [];

    foreach ($urlBrowser as $index => $ub) {
      if ($ub === $urlSystem[$index]) {
        $success++;
      } elseif ($urlSystem[$index] === '@{param}') {
        array_push($arrayParams, $ub);
        $success++;
      }
    }

    if ($success === count($urlBrowser)) {
      Route_Args::init()->setKeyFirstReq('route', $route['route']);
      Route_Args::init()->setKeyFirstReq('middlewares', $route['middlewares']);
      Route_Args::init()->setKeyFirstReq('view', $route['view']);
      Route_Args::init()->setKeyFirstReq('method', $route['method']);
      Route_Args::init()->setKeyFirstReq('params', $arrayParams);

      return true;
    }

    return false;
  }

  protected static function valMethod() {
    $success = false;

    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        if (Route_Args::init()->getFirstReq()['method'] === 'get') {
          $success = true;

          Route_Args::init()->setKeyFirstReq('data', null);
        }
        break;
      case 'POST':
        if (Route_Args::init()->getFirstReq()['method'] === 'post') {
          $theHeaders = Headers::getHeaders();

          if (
            array_key_exists('content-type', $theHeaders) &&
            $theHeaders['content-type'] === 'application/json'
          ) {
            $success = true;

            $data = json_decode(file_get_contents('php://input'), true);

            Route_Args::init()->setKeyFirstReq('data', $data);
          } elseif (
            array_key_exists('accept', $theHeaders) &&
            $theHeaders['accept'] === 'multipart/form-data'
          ) {
            $success = true;

            Route_Args::init()->setKeyFirstReq('data', $_POST);

            if (isset($_FILES) && count($_FILES) > 0) {
              Route_Args::init()->setKeyFirstReq('files', $_FILES);
            }
          }
        }
        break;
      case 'PUT':
      case 'DELETE':
        if (
          Route_Args::init()->getFirstReq()['method'] === 'put' ||
          Route_Args::init()->getFirstReq()['method'] === 'delete'
        ) {
          $theHeaders = Headers::getHeaders();

          if (
            array_key_exists('content-type', $theHeaders) &&
            $theHeaders['content-type'] === 'application/json'
          ) {
            $success = true;

            $data = json_decode(file_get_contents('php://input'), true);

            Route_Args::init()->setKeyFirstReq('data', $data);
          } else {
            throw new \Exception(
              'Unicamente se permite el envio de data por application/json'
            );
          }
        }
        break;
      default:
        echo 'ELIMINAR';
        break;
    }

    return $success;
  }

  protected static function getSessionToken() {
    $theHeaders = Headers::getHeaders();

    foreach ($theHeaders as $header => $value) {
      if (strtolower($header) === 'authorization') {
        $auth_explode = explode(' ', $value);

        if (strtolower(auth_explode[0]) === 'token') {
          Route_Args::init()->setKeyFirstReq('auth', $auth_explode[1]);
          break;
        }
      }
    }
  }
}
