<?php
namespace Arlequin;

require_once __DIR__ . '/../init.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authentication {
  public static function generate(array $data = [], integer $exp = 60) {
    $time = time();
    $token = null;

    $payload_init = [
      'iat' => $time,
      'exp' => $time + $exp, // segundos * minutos * horas * dias ...
      'aud' => $_ENV['TOKEN_AUD'],
      'iss' => $_ENV['TOKEN_ISS'],
    ];

    $payload = array_merge($payload_init, ['user' => $data]);

    try {
      $token = JWT::encode($payload, $_ENV['TOKEN_KEY'], 'HS256');
    } catch (\Exception $e) {
      throw new \Exception('Token | No se pudo crear el token');
    }

    return $token;
  }

  public static function decode($token) {
    if (isset($token) && !empty($token)) {
      return JWT::decode($token, new Key($_ENV['TOKEN_KEY'], 'HS256'));
    }
  }
}
