<?php
namespace Arlequin\Memory;

use Arlequin\Route\Route_Args;

class Memory_Req {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Memory_Req();
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
    switch ($name) {
      case 'chest':
        return array_filter(
          Route_Args::init()->getFirstReq(),
          function ($data, $key) {
            return $key !== 'middlewares' &&
              $key !== 'route' &&
              $key !== 'view';
          },
          ARRAY_FILTER_USE_BOTH
        );
        break;
      case 'print':
        $this->toString();
        break;
      default:
        throw new \Exception(
          'Solo existen las siguientes opciónes: <br/><b>chest</b> = Devuelte un array de los datos del request<br/> <b>print</b> = Imprime los datos del request<br/>'
        );
        break;
    }
  }

  public function __set($name, $value) {
    throw new \Exception('No se permite sobreescribir la data de Req');
  }

  public function save_chest(string $key, $value) {
    if (!empty($key) && !empty($value)) {
      $forbidden = [
        'route',
        'middlewares',
        'view',
        'method',
        'params',
        'data',
        'files',
        'auth',
      ];
      if (!in_array($key, $forbidden)) {
        Route_Args::init()->setKeyFirstReq($key, $value);
      } else {
        throw new \Exception('Escoge otro nombre para la llave');
      }
    } else {
      throw new \Exception(
        'Es necesario definir la llave y el valor a ingresar'
      );
    }
  }

  private function toString($mainKey = null) {
    $req = array_filter(
      Route_Args::init()->getFirstReq(),
      function ($data, $key) {
        return $key !== 'middlewares' && $key !== 'route' && $key !== 'view';
      },
      ARRAY_FILTER_USE_BOTH
    );

    if (array_key_exists($mainKey, $req)) { ?>
  <h4>Mostrando datos del "chest" Req[<?php echo $mainKey; ?>]</h4>
<?php $req = $req[$mainKey];} else { ?>
  <h4>Mostrando datos del "chest" Req</h4>
<?php }
    ?>
  <div style="padding: 0 20px;">
    <?php if (empty($req)) { ?> 
      <b>VACIO</b>
  <?php } else {$this->recursiveToString($req);} ?>
  </div>
<?php
  }
}
