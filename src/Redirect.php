<?php
namespace Arlequin;

use Arlequin\Url;
use Arlequin\Route\Route_Args;

class Redirect {
  public static function route(string $route, ...$params) {
    if (!empty($route)) {
      if (in_array($route, Url::init()->get_GET_url())) {
        $toRoute = $route;
        $explodeURL = explode('/', $route);
        $countURL = array_count_values($explodeURL);

        if (isset($countURL['@{param}'])) {
          if (count($params) === $countURL['@{param}']) {
            for ($i = 0; $i < $countURL['@{param}']; $i++) {
              $toRoute = preg_replace('/@{param}/', $params[$i], $toRoute, 1);
            }
          } else {
            throw new \Exception(
              "La ruta require de {$countURL['@{param}']} parametros"
            );
          }
        }

        header('Location:' . $_ENV['HOST'] . $toRoute);
        /* die(); */
      } else {
        throw new \Exception(
          'Redirect | Es necesario establecer una ruta existente'
        );
      }
    } else {
      throw new \Exception('Redirect | Es necesario establecer una ruta');
    }
  }
}
