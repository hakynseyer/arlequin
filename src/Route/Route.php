<?php
namespace Arlequin\Route;

require_once __DIR__ . '/../../init.php';

use Arlequin\Route\{Router_Tools, Route_Args};
use Arlequin\{Middleware, Redirect, Url};

class Route extends Router_Tools {
  public static function init() {
    $baseDir = __DIR__ . '/../../../../../' . $_ENV['ARLEQUIN_VIEWS_PATH'];

    $views = scandir($baseDir);

    if (count($views)) {
      foreach ($views as $view) {
        if ($view !== '.' && $view !== '..') {
          $controller =
            $baseDir . '/' . $view . '/' . $view . '_controller.php';
          if (is_file($controller)) {
            require_once $controller;
          }
        }
      }
    }

    // Arrancador
    try {
      if (!isset($_GET['url'])) {
        Route::launchRoute('/');
      } else {
        Route::launchRoute($_GET['url']);
      }
    } catch (\Exception $error) {
      echo $error;
    }
  }

  public static function launchRoute($route) {
    Route_Args::init()->clearFirstReq();

    $success = false;

    $url = self::parseURL($route);

    if (
      array_key_exists(
        $_SERVER['REQUEST_METHOD'],
        Route_Args::init()->getTheRoutes()
      )
    ) {
      foreach (
        Route_Args::init()->getTheRoutes()[$_SERVER['REQUEST_METHOD']]
        as $routeSystem
      ) {
        $urlSystem = self::parseURL($routeSystem['route']);

        if (self::valNumberParts($url, $urlSystem)) {
          if (self::valNumberParams($url, $urlSystem)) {
            if (self::valEqualParts($url, $urlSystem, $routeSystem)) {
              if (self::valMethod()) {
                //TODO: Crear el manejador de sesiones
                $success = true;
                break;
              }
            }
          }
        }
      }
    } else {
      throw new \Exception('La petición HTTP es invalida para este sistema');
    }

    if (!$success) {
      // TODO: Enviar una respuesta 500 cuando los metodos no sean GET
      /* echo 'ENVIAR A LA VISTA 404'; */
      Redirect::route('/');
    } else {
      if (count(Route_Args::init()->getFirstReq()['middlewares'])) {
        Middleware::clearInstance();
        Middleware::init()->nextMiddleware();
      } else {
        throw new \Exception(
          'Es necesario al menos tenero un middleware en la definición de la ruta'
        );
      }
    }
  }

  private static function buildingRouteArgs(
    $route,
    $middlewares,
    $method,
    $view
  ) {
    if (self::validateRoute($route)) {
      return [
        'route' => $route,
        'middlewares' => count($middlewares) ? $middlewares : [],
        'method' => $method,
        'view' => $view,
      ];
    } else {
      throw new \Exception(
        'RouteArgs | No se pudo construir los argumentos iniciales de la ruta</br>'
      );
    }
  }

  private static function methodHTTP($method, $route, $middlewares) {
    $debug = debug_backtrace();

    $file = explode(DIRECTORY_SEPARATOR, $debug[1]['file']);
    array_pop($file);
    $view = array_pop($file);

    Route_Args::init()->pushTheRoutes(
      strtoupper($method),
      self::buildingRouteArgs($route, $middlewares, strtolower($method), $view)
    );

    Url::init()->add_to_url($method, $route);
  }

  public static function get($route, ...$middlewares) {
    self::methodHTTP('get', $route, $middlewares);
  }

  public static function post($route, ...$middlewares) {
    self::methodHTTP('post', $route, $middlewares);
  }

  public static function put($route, ...$middlewares) {
    self::methodHTTP('put', $route, $middlewares);
  }

  public static function delete($route, ...$middlewares) {
    self::methodHTTP('delete', $route, $middlewares);
  }
}