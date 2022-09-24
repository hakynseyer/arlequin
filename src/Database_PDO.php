<?php
namespace Arlequin;

require_once __DIR__ . '/../init.php';

class Database_PDO {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Database_PDO();
    }

    return self::$instance;
  }

  public static function clearInstance() {
    self::$instance = null;
  }

  private function __clone() {
  }

  private $HOST = null;
  private $PORT = null;
  private $DBNAME = null;
  private $USER = null;
  private $PASS = null;


  private $Connection = null;

  private function __construct() {
    $this->Connection = null;
    
    $this -> HOST = $_ENV['DB_HOST'];
    $this -> PORT = $_ENV['DB_PORT'];
    $this -> DBNAME = $_ENV['DB_DATABASE'];
    $this -> USER = $_ENV['DB_USERNAME'];
    $this -> PASS = $_ENV['DB_PASSWORD'];
  }

  private function connection_mysql(): void {
    try {
      $this -> Connection = new \PDO('mysql:host='.$this -> HOST.';port='.$this->PORT.';dbname='.$this->DBNAME, $this->USER, $this->PASS);
      $this -> Connection -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
      throw new \Exception($e -> getMessage());
    }
  }

  public function conn(): \PDO {
    try {
      switch ($_ENV['DB_DRIVER']) {
        case "mysql":
          $this -> connection_mysql(); 
          break;
        default:
          throw new \Exception("No se pudo conectar con ninguna base de datos... Drivers disponibles (mysql)");
          break;
      }
    } catch (\Exception $e) {
      throw new \Exception($e -> getMessage());
    }

    return $this->Connection;
  }

  public function close(): void {
    $this -> Connection = null;
  }
}
