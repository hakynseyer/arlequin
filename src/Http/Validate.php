<?php
namespace Arlequin\Http;

class Validate {
  public static function empty($data) {
    if (isset($data) && !empty($data)) {
      return null;
    }
    return 'No puede estar vacio';
  }

  public static function type($data, $option) {
    $error = null;

    switch ($option) {
      case 'string':
        if (gettype($data) !== 'string') {
          $error = 'Debe de ser un texto';
        }
        break;
      case 'integer':
        if (gettype($data) !== 'integer') {
          $error = 'Debe de ser un numero';
        }
        break;
      case 'decimal':
        if (gettype($data) !== 'double') {
          $error = 'Debe de ser un decimal';
        }
        break;
      case 'email':
        if (filter_var($data, FILTER_VALIDATE_EMAIL) === false) {
          $error = 'Debe de ser un email Valido';
        }
        break;
      default:
        $error = 'No se pudo identificar el tipo de dato';
    }

    return $error;
  }

  public static function min($data, $min) {
    if (gettype($min) !== 'integer') {
      throw new \Exception(
        'Solo se permiten valores enteros para el valor del <b>Minímo</b>'
      );
    }

    $sizeData = strlen($data);

    if ($min > 0) {
      if ($sizeData < $min) {
        return 'Te faltan ' . ($min - $sizeData) . ' carácteres';
      }
    }

    return null;
  }

  public static function max($data, $max) {
    if (gettype($max) !== 'integer') {
      throw new \Exception(
        'Solo se permiten valores enteros para el valor del <b>Máximo</b>'
      );
    }

    $sizeData = strlen($data);

    if ($max > 0 && gettype($max) === 'integer') {
      if ($sizeData > $max) {
        return 'Te sobran ' . ($sizeData - $max) . ' carácteres';
      }
    }

    return null;
  }

  public static function bigger($data, $bigger) {
    if (gettype($bigger) !== 'integer') {
      throw new \Exception(
        "Solo se permiten valores enteros para el valor del <b>Bigger $bigger</b>"
      );
    }
    if (gettype($data) !== 'integer' && gettype($data) !== 'double') {
      throw new \Exception(
        "Solo se permiten valores enteros o decimales para el valor del <b>Data $data</b>"
      );
    }

    if ($bigger >= 0) {
      if ($data <= $bigger) {
        return 'Debe de ser mayor a ' . $bigger;
      }
    }

    return null;
  }

  public static function lower($data, $lower) {
    if (gettype($lower) !== 'integer') {
      throw new \Exception(
        "Solo se permiten valores enteros para el valor del <b>Lower $lower</b>"
      );
    }
    if (gettype($data) !== 'integer' && gettype($data) !== 'double') {
      throw new \Exception(
        "Solo se permiten valores enteros o decimales para el valor del <b>Data $data</b>"
      );
    }

    if ($lower > 0) {
      if ($data >= $lower) {
        return 'Debe de ser menor a ' . $lower;
      }
    }

    return null;
  }

  public static function image_type($data) {
    $error =
      'Tu imagen es de tipo [' .
      explode('/', $data)[1] .
      ']; solo se permiten imagenes de tipo [JPEG, PNG o GIF]';

    $isJPEG = $data === 'image/jpeg';
    $isPNG = $data === 'image/png';
    $isGIF = $data === 'image/gif';

    if ($isJPEG || $isPNG || $isGIF) {
      $error = null;
    }

    return $error;
  }

  public static function file_size($data, $weightValid) {
    $error = null;

    if ($data > $weightValid) {
      $error =
        'Tu archivo es demasiado pesado para nuestros servidores. Solo podemos almacenar archivos cuyo peso máximo sea de ' .
        ($weightValid * 9.7656) / 10000 .
        ' KB';
    }

    return $error;
  }

  public static function val_Empty_Type_Min_Max($data, $type, $min, $max) {
    $error = Validate::empty($data);

    if ($error === null) {
      $error = Validate::type($data, $type);
      if ($error === null) {
        $error = Validate::min($data, $min);
        if ($error === null) {
          $error = Validate::max($data, $max);
          if ($error === null) {
            return null;
          }
        }
      }
    }

    return $error;
  }

  public static function val_EmptyOptional_Type_Min_Max(
    $data,
    $type,
    $min,
    $max
  ) {
    $error = null;

    if (Validate::empty($data) === null) {
      $error = Validate::type($data, $type);
      if ($error === null) {
        $error = Validate::min($data, $min);
        if ($error === null) {
          $error = Validate::max($data, $max);
          if ($error === null) {
            return null;
          }
        }
      }
    }

    return $error;
  }
}
