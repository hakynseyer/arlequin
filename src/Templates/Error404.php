<?php
namespace Arlequin\Templates;

class Error404 {
  public static function template_error404() {
    return <<<ERROR404
  <div style="display:flex; align-items:flex-end;">
  	<h1 style="width: auto">Error 404</h1>
  	<i style="font-size: 15px; margin: 15px; padding-bottom: 15px; ">Funcionando en modo Dev-Back</i>
  </div>
  <p> No se pudo encontrar el recurso deseado :(</p>
ERROR404;
  }
}
