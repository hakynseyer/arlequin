<?php
namespace Arlequin;

require_once __DIR__ . '/../init.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class Database {
  private static $instance = null;

  public static function init() {
    if (is_null(self::$instance)) {
      self::$instance = new Database();
    }

    return self::$instance;
  }

  public static function clearInstance() {
    self::$instance = null;
  }

  private function __clone() {
  }

  private $DB = null;
  private $Entities = null;
  private $isDevMode = null;

  private $Connection = null;

  private function __construct() {
    $this->Connection = null;

    $this->Entities = [$_ENV['DOCTRINE_ENTITIES']];
    $this->isDevMode = $_ENV['MODE'] === 'dev' ? true : false;

    $this->DB = [
      'host' => $_ENV['DB_HOST'],
      'user' => $_ENV['DB_USERNAME'],
      'password' => $_ENV['DB_PASSWORD'],
      'dbname' => $_ENV['DB_DATABASE'],
      'driver' => $_ENV['DB_DRIVER'],
    ];

    $this->Connection = EntityManager::create(
      $this->DB,
      ORMSetup::createAnnotationMetadataConfiguration(
        $this->Entities,
        $this->isDevMode
      )
    );
  }

  public function conn(): EntityManager {
    return $this->Connection;
  }

  public function query() {
    return $this->Connection->createQueryBuilder();
  }
}
