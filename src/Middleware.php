<?php
namespace Arlequin;

use Arlequin\Route\Route_Args;
use Arlequin\Memory\{Memory_Req, Memory_Res, Memory_Next};

class Middleware {
  public static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Middleware();
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

  private function validateFirstReq() {
    if (
      !empty(Route_Args::init()->getFirstReq()) &&
      is_array(Route_Args::init()->getFirstReq())
    ) {
      return true;
    } else {
      throw new \Exception(
        'No se logró encontrar los parametros del primero REQ'
      );
    }
  }

  public function nextMiddleware() {
    if (!$this->validateFirstReq()) {
      return;
    }

    Memory_Req::clearInstance();
    Memory_Res::clearInstance();
    Memory_Next::clearInstance();

    if (count(Route_Args::init()->getFirstReq())) {
      $midd = null;

      if (is_callable(Route_Args::init()->getFirstReq()['middlewares'][0])) {
        $midd = new \ReflectionFunction(
          Route_Args::init()->getFirstReq()['middlewares'][0]
        );

        // Eliminando el primer middleware
        Route_Args::init()->removeFirstMiddleware();

        switch (count($midd->getParameters())) {
          case 2:
            // Validando el nombre de los parametros
            $isReq = $midd->getParameters()[0]->getName() === 'req';
            $isRes = $midd->getParameters()[1]->getName() === 'res';

            if ($isReq && $isRes) {
              // Llamo a la función middleware
              $midd->invoke(Memory_Req::init(), Memory_Res::init());
            } else {
              throw new \Exception(
                'Los nombres de los parametros deben de ser (req, res)'
              );
            }
            break;
          case 3:
            // Validando el nombre de los parametros
            $isReq = $midd->getParameters()[0]->getName() === 'req';
            $isRes = $midd->getParameters()[1]->getName() === 'res';
            $isNext = $midd->getParameters()[2]->getName() === 'next';

            if ($isReq && $isRes && $isNext) {
              // Llamo a la función middleware
              if (count(Route_Args::init()->getFirstReq()['middlewares'])) {
                $midd->invoke(
                  Memory_Req::init(),
                  Memory_Res::init(),
                  Memory_Next::init()
                );
              } else {
                throw new \Exception(
                  'Este es el último middleware registrado, no se puede usar el parametro next'
                );
              }
            } else {
              throw new \Exception(
                'Los nombres de los parametros deben de ser (req, res, next)'
              );
            }
            break;
          default:
            throw new \Exception(
              'Los middlewares deben de contener entre 2 o 3 parametros (req, res, next)'
            );
            break;
        }
      } elseif (
        method_exists(
          Route_Args::init()->getFirstReq()['middlewares'][0],
          'middleware'
        )
      ) {
        $midd = new \ReflectionClass(
          Route_Args::init()->getFirstReq()['middlewares'][0]
        );

        // Limpiando singleton
        $midd->getMethod('clearInstance')->invoke(null);

        // Eliminando el primer middleware
        Route_Args::init()->removeFirstMiddleware();

        switch (count($midd->getMethod('middleware')->getParameters())) {
          case 2:
            // Validando el nombre de los parametros
            $isReq =
              $midd
                ->getMethod('middleware')
                ->getParameters()[0]
                ->getName() === 'req';
            $isRes =
              $midd
                ->getMethod('middleware')
                ->getParameters()[1]
                ->getName() === 'res';

            if ($isReq && $isRes) {
              // Llamo a la función middleware
              $midd
                ->getMethod('middleware')
                ->invoke(
                  $midd->getMethod('init')->invoke(null),
                  Memory_Req::init(),
                  Memory_Res::init()
                );
            } else {
              throw new \Exception(
                'Los nombres de los parametros deben de ser (req, res)'
              );
            }
            break;
          case 3:
            // Validando el nombre de los parametros
            $isReq =
              $midd
                ->getMethod('middleware')
                ->getParameters()[0]
                ->getName() === 'req';
            $isRes =
              $midd
                ->getMethod('middleware')
                ->getParameters()[1]
                ->getName() === 'res';
            $isNext =
              $midd
                ->getMethod('middleware')
                ->getParameters()[2]
                ->getName() === 'next';

            if ($isReq && $isRes && $isNext) {
              // Llamo a la función middleware
              if (count(Route_Args::init()->getFirstReq()['middlewares'])) {
                $midd
                  ->getMethod('middleware')
                  ->invoke(
                    $midd->getMethod('init')->invoke(null),
                    Memory_Req::init(),
                    Memory_Res::init(),
                    Memory_Next::init()
                  );
              } else {
                throw new \Exception(
                  'Este es el último middleware registrado, no se puede usar el parametro next'
                );
              }
            } else {
              throw new \Exception(
                'Los nombres de los parametros deben de ser (req, res, next)'
              );
            }
            break;
          default:
            throw new \Exception(
              'Los middlewares deben de contener entre 2 o 3 parametros (req, res, next)'
            );
            break;
        }
      }
    }
  }
}
