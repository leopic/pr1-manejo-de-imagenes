<?php

/**
 * bootstrap.php
 * Inicio, no visual, de la aplicación.
 * Carga todas las dependencias.
 */

// Dependencias de composer
require "vendor/autoload.php";

// Muestra todos los errores de PHP, evita un pantallazo blanco
// http://stackoverflow.com/questions/1053424/how-do-i-get-php-errors-to-display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
