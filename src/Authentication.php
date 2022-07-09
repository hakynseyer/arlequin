<?php
namespace Arlequin;

require_once __DIR__ . '/../init.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authentication {
  private static function getAud(): string {
    $aud = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $aud = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $aud = $_SERVER['REMOTE_ADDR'];
    }

    $aud .= @$_SERVER['HTTP_USER_AGENT'];
    $aud .= gethostname();

    return sha1($aud);
  }

  public static function generate(array $data = [], int $exp = 60) {
    $time = time();
    $token = null;

    $payload_init = [
      'iat' => $time,
      'exp' => $time + $exp, // segundos * minutos * horas * dias ...
      'aud' => self::getAud(),
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
    $decode = null;

    try {
      if (isset($token) && !empty($token)) {
        $decode = JWT::decode($token, new Key($_ENV['TOKEN_KEY'], 'HS256'));

        if ($decode->aud === self::getAud()) {
          return $decode;
        }
      }
    } catch (\Exception $e) {
      throw new \Exception('TOKEN | No se pudo obtener el token');
    }

    return $decode;
  }
}
